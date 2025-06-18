<?php

use App\Services\AlteredApi\GetFactions;
use App\Models\Faction;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

describe('GetFactions', function () {
    beforeEach(function () {
        $this->mockHandler = new MockHandler();
        $this->handlerStack = HandlerStack::create($this->mockHandler);
        $this->client = new Client(['handler' => $this->handlerStack]);
        $this->service = new GetFactions($this->client);
    });

    it('returns correct endpoint', function () {
        $reflection = new ReflectionClass($this->service);
        $method = $reflection->getMethod('getEndpoint');
        $method->setAccessible(true);

        expect($method->invoke($this->service))->toBe('factions');
    });

    it('creates factions from api data', function () {
        $responseData = [
            'hydra:member' => [
                [
                    'id' => 1,
                    'name' => 'Axiom',
                    'reference' => 'AX'
                ],
                [
                    'id' => 2,
                    'name' => 'Bravos',
                    'reference' => 'BR'
                ]
            ]
        ];

        $this->mockHandler->append(new Response(200, [], json_encode($responseData)));

        $this->service->getData();

        expect(Faction::count())->toBe(2)
            ->and(Faction::where('name', 'Axiom')->exists())->toBeTrue()
            ->and(Faction::where('name', 'Bravos')->exists())->toBeTrue();
    });
});
