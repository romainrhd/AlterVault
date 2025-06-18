<?php

use App\Services\AlteredApi\GetCards;
use App\Services\AlteredApi\GetFactions;
use App\Models\Card;
use App\Models\CardSet;
use App\Models\Faction;
use App\Models\Rarity;
use App\Models\CardType;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

describe('Altered API Integration', function () {
    it('can create complete card with all dependencies', function () {
        $faction = Faction::factory()->create(['altered_api_id' => 1]);
        $rarity = Rarity::factory()->create(['altered_api_id' => 1]);
        $cardType = CardType::factory()->create(['altered_api_id' => 1]);
        $cardSet = CardSet::factory()->create(['altered_api_id' => 1]);

        $mockHandler = new MockHandler();
        $handlerStack = HandlerStack::create($mockHandler);
        $client = new Client(['handler' => $handlerStack]);

        $cardsResponse = [
            'hydra:member' => [
                [
                    'id' => 1,
                    'name' => 'Integration Test Card',
                    'reference' => 'integration-card',
                    'imagePath' => '/images/integration.jpg',
                    'mainFaction' => ['id' => 1],
                    'rarity' => ['id' => 1],
                    'cardType' => ['id' => 1, 'reference' => 'HERO'],
                    'cardSet' => ['id' => 1]
                ]
            ]
        ];

        $mockHandler->append(new Response(200, [], json_encode($cardsResponse)));

        $getCards = new GetCards($client);
        $getCards->getData();

        expect(Card::count())->toBe(1);

        $card = Card::first();
        expect($card->name)->toBe('Integration Test Card')
            ->and($card->faction_id)->toBe($faction->id)
            ->and($card->rarity_id)->toBe($rarity->id)
            ->and($card->card_type_id)->toBe($cardType->id)
            ->and($card->card_set_id)->toBe($cardSet->id);
    });

    it('handles updateOrCreate correctly', function () {
        $mockHandler = new MockHandler();
        $handlerStack = HandlerStack::create($mockHandler);
        $client = new Client(['handler' => $handlerStack]);

        $responseData = [
            'hydra:member' => [
                [
                    'id' => 1,
                    'name' => 'Test Faction',
                    'reference' => 'TF'
                ]
            ]
        ];

        $mockHandler->append(new Response(200, [], json_encode($responseData)));
        $getFactions = new GetFactions($client);
        $getFactions->getData();

        expect(Faction::count())->toBe(1);
        $originalFaction = Faction::first();

        $responseData['hydra:member'][0]['name'] = 'Updated Faction';
        $mockHandler->append(new Response(200, [], json_encode($responseData)));
        $getFactions->getData();

        expect(Faction::count())->toBe(1);

        $updatedFaction = Faction::first();
        expect($updatedFaction->id)->toBe($originalFaction->id)
            ->and($updatedFaction->name)->toBe('Updated Faction');
    });
});
