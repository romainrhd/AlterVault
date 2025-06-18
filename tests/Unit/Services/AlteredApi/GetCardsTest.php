<?php

use App\Services\AlteredApi\GetCards;
use App\Models\Card;
use App\Models\Faction;
use App\Models\Rarity;
use App\Models\CardType;
use App\Models\CardSet;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

describe('GetCards', function () {
    beforeEach(function () {
        $this->mockHandler = new MockHandler();
        $this->handlerStack = HandlerStack::create($this->mockHandler);
        $this->client = new Client(['handler' => $this->handlerStack]);
        $this->service = new GetCards($this->client);

        $this->faction = Faction::factory()->create(['altered_api_id' => 1]);
        $this->rarity = Rarity::factory()->create(['altered_api_id' => 1]);
        $this->cardType = CardType::factory()->create(['altered_api_id' => 1]);
        $this->cardSet = CardSet::factory()->create(['altered_api_id' => 1]);
    });

    it('returns correct endpoint', function () {
        $reflection = new ReflectionClass($this->service);
        $method = $reflection->getMethod('getEndpoint');
        $method->setAccessible(true);

        expect($method->invoke($this->service))->toBe('cards');
    });

    it('returns correct model class', function () {
        $reflection = new ReflectionClass($this->service);
        $method = $reflection->getMethod('getModelClass');
        $method->setAccessible(true);

        expect($method->invoke($this->service))->toBe(Card::class);
    });

    it('transforms api data correctly', function () {
        $apiData = [
            'id' => 123,
            'name' => 'Test Card',
            'reference' => 'test-card',
            'imagePath' => '/images/test.jpg',
            'mainFaction' => ['id' => 1],
            'rarity' => ['id' => 1],
            'cardType' => ['id' => 1],
            'cardSet' => ['id' => 1]
        ];

        $reflection = new ReflectionClass($this->service);
        $method = $reflection->getMethod('transformApiData');
        $method->setAccessible(true);

        $result = $method->invoke($this->service, $apiData);

        expect($result)->toBe([
            'name' => 'Test Card',
            'slug' => 'test-card',
            'image' => '/images/test.jpg',
            'altered_api_id' => 123,
            'faction_id' => $this->faction->id,
            'rarity_id' => $this->rarity->id,
            'card_type_id' => $this->cardType->id,
            'card_set_id' => $this->cardSet->id,
        ]);
    });

    it('filters out foiler cards', function () {
        $foilerCard = ['cardType' => ['reference' => 'FOILER']];
        $normalCard = ['cardType' => ['reference' => 'HERO']];

        $reflection = new ReflectionClass($this->service);
        $method = $reflection->getMethod('shouldProcessItem');
        $method->setAccessible(true);

        expect($method->invoke($this->service, $foilerCard))->toBeFalse()
            ->and($method->invoke($this->service, $normalCard))->toBeTrue();
    });

    it('handles pagination correctly', function () {
        $firstPageResponse = [
            'hydra:member' => [
                [
                    'id' => 1,
                    'name' => 'Card 1',
                    'reference' => 'card-1',
                    'imagePath' => '/img1.jpg',
                    'mainFaction' => ['id' => 1],
                    'rarity' => ['id' => 1],
                    'cardType' => ['id' => 1, 'reference' => 'HERO'],
                    'cardSet' => ['id' => 1]
                ]
            ],
            'hydra:view' => ['hydra:next' => '/cards?page=2']
        ];

        $secondPageResponse = [
            'hydra:member' => [
                [
                    'id' => 2,
                    'name' => 'Card 2',
                    'reference' => 'card-2',
                    'imagePath' => '/img2.jpg',
                    'mainFaction' => ['id' => 1],
                    'rarity' => ['id' => 1],
                    'cardType' => ['id' => 1, 'reference' => 'HERO'],
                    'cardSet' => ['id' => 1]
                ]
            ]
        ];

        $this->mockHandler->append(new Response(200, [], json_encode($firstPageResponse)));
        $this->mockHandler->append(new Response(200, [], json_encode($secondPageResponse)));

        $this->service->getData();

        expect(Card::count())->toBe(2)
            ->and(Card::where('name', 'Card 1')->exists())->toBeTrue()
            ->and(Card::where('name', 'Card 2')->exists())->toBeTrue();
    });
});
