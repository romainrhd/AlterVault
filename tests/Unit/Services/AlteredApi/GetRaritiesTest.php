<?php

use App\Services\AlteredApi\GetRarities;
use App\Models\Rarity;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

describe('GetRarities', function () {
    beforeEach(function () {
        $this->mockHandler = new MockHandler();
        $this->handlerStack = HandlerStack::create($this->mockHandler);
        $this->client = new Client(['handler' => $this->handlerStack]);
        $this->service = new GetRarities($this->client);
    });

    it('returns correct endpoint', function () {
        $reflection = new ReflectionClass($this->service);
        $method = $reflection->getMethod('getEndpoint');
        $method->setAccessible(true);

        expect($method->invoke($this->service))->toBe('rarities');
    });

    it('creates rarities from api data', function () {
        $responseData = [
            'hydra:member' => [
                [
                    'id' => 1,
                    'name' => 'Common',
                    'reference' => 'COMMON'
                ],
                [
                    'id' => 2,
                    'name' => 'Rare',
                    'reference' => 'RARE'
                ]
            ]
        ];

        $this->mockHandler->append(new Response(200, [], json_encode($responseData)));

        $this->service->getData();

        expect(Rarity::count())->toBe(2)
            ->and(Rarity::where('name', 'Common')->exists())->toBeTrue()
            ->and(Rarity::where('name', 'Rare')->exists())->toBeTrue();
    });
});
