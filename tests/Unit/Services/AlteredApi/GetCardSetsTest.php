<?php

use App\Services\AlteredApi\GetCardSets;
use App\Models\CardSet;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

describe('GetCardSets', function () {
    beforeEach(function () {
        $this->mockHandler = new MockHandler();
        $this->handlerStack = HandlerStack::create($this->mockHandler);
        $this->client = new Client(['handler' => $this->handlerStack]);
        $this->service = new GetCardSets($this->client);
    });

    it('returns correct endpoint', function () {
        $reflection = new ReflectionClass($this->service);
        $method = $reflection->getMethod('getEndpoint');
        $method->setAccessible(true);

        expect($method->invoke($this->service))->toBe('card_sets');
    });

    it('returns correct model class', function () {
        $reflection = new ReflectionClass($this->service);
        $method = $reflection->getMethod('getModelClass');
        $method->setAccessible(true);

        expect($method->invoke($this->service))->toBe(CardSet::class);
    });

    it('transforms api data correctly', function () {
        $apiData = [
            'id' => 456,
            'name' => 'Test Set',
            'reference' => 'test-set'
        ];

        $reflection = new ReflectionClass($this->service);
        $method = $reflection->getMethod('transformApiData');
        $method->setAccessible(true);

        $result = $method->invoke($this->service, $apiData);

        expect($result)->toBe([
            'name' => 'Test Set',
            'slug' => 'test-set',
            'altered_api_id' => 456,
        ]);
    });

    it('creates card sets from api data', function () {
        $responseData = [
            'hydra:member' => [
                [
                    'id' => 1,
                    'name' => 'First Set',
                    'reference' => 'first-set'
                ],
                [
                    'id' => 2,
                    'name' => 'Second Set',
                    'reference' => 'second-set'
                ]
            ]
        ];

        $this->mockHandler->append(new Response(200, [], json_encode($responseData)));

        $this->service->getData();

        expect(CardSet::count())->toBe(2)
            ->and(CardSet::where('name', 'First Set')->exists())->toBeTrue()
            ->and(CardSet::where('name', 'Second Set')->exists())->toBeTrue();
    });
});
