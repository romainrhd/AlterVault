<?php

use App\Services\AlteredApi\GetCardTypes;
use App\Models\CardType;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

describe('GetCardTypes', function () {
    beforeEach(function () {
        $this->mockHandler = new MockHandler();
        $this->handlerStack = HandlerStack::create($this->mockHandler);
        $this->client = new Client(['handler' => $this->handlerStack]);
        $this->service = new GetCardTypes($this->client);
    });

    it('returns correct endpoint', function () {
        $reflection = new ReflectionClass($this->service);
        $method = $reflection->getMethod('getEndpoint');
        $method->setAccessible(true);

        expect($method->invoke($this->service))->toBe('card_types');
    });

    it('returns correct model class', function () {
        $reflection = new ReflectionClass($this->service);
        $method = $reflection->getMethod('getModelClass');
        $method->setAccessible(true);

        expect($method->invoke($this->service))->toBe(CardType::class);
    });

    it('requires authentication and creates card types', function () {
        $tokenResponse = ['token' => 'auth-token-123'];
        $cardTypesResponse = [
            'hydra:member' => [
                [
                    'id' => 1,
                    'name' => 'Hero',
                    'reference' => 'HERO'
                ],
                [
                    'id' => 2,
                    'name' => 'Spell',
                    'reference' => 'SPELL'
                ]
            ]
        ];

        $this->mockHandler->append(new Response(200, [], json_encode($tokenResponse)));
        $this->mockHandler->append(new Response(200, [], json_encode($cardTypesResponse)));

        $this->service->getData('test@example.com', 'password');

        expect(CardType::count())->toBe(2)
            ->and(CardType::where('name', 'Hero')->exists())->toBeTrue()
            ->and(CardType::where('name', 'Spell')->exists())->toBeTrue();

        $requests = $this->mockHandler->getLastRequest();
        expect($requests->getHeader('Authorization'))->toContain('Bearer auth-token-123');
    });
});
