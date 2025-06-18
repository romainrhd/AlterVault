<?php

use App\Services\AlteredApi\GetFactions;
use App\Models\Faction;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;

describe('BaseAlteredApiService', function () {
    beforeEach(function () {
        $this->mockHandler = new MockHandler();
        $this->handlerStack = HandlerStack::create($this->mockHandler);
        $this->client = new Client(['handler' => $this->handlerStack]);
        $this->service = new GetFactions($this->client);
    });

    it('can be instantiated with default client', function () {
        $service = new GetFactions();
        expect($service)->toBeInstanceOf(GetFactions::class);
    });

    it('can make api request successfully', function () {
        $responseData = [
            'hydra:member' => [
                ['id' => 1, 'name' => 'Test Faction', 'reference' => 'TF']
            ]
        ];

        $this->mockHandler->append(new Response(200, [], json_encode($responseData)));

        $reflection = new ReflectionClass($this->service);
        $method = $reflection->getMethod('makeApiRequest');
        $method->setAccessible(true);

        $result = $method->invoke($this->service, 'factions');

        expect($result)->toBe($responseData);
    });

    it('adds locale to query parameters', function () {
        $responseData = ['hydra:member' => []];
        $this->mockHandler->append(new Response(200, [], json_encode($responseData)));

        $reflection = new ReflectionClass($this->service);
        $method = $reflection->getMethod('makeApiRequest');
        $method->setAccessible(true);

        $method->invoke($this->service, 'factions', ['page' => 1]);

        $request = $this->mockHandler->getLastRequest();
        parse_str($request->getUri()->getQuery(), $queryParams);

        expect($queryParams)->toHaveKey('locale', 'fr-fr')
            ->and($queryParams)->toHaveKey('page', '1');
    });

    it('throws runtime exception on http error', function () {
        $this->mockHandler->append(new RequestException(
            'Error Communicating with Server',
            new Request('GET', 'test')
        ));

        $reflection = new ReflectionClass($this->service);
        $method = $reflection->getMethod('makeApiRequest');
        $method->setAccessible(true);

        expect(fn() => $method->invoke($this->service, 'factions'))
            ->toThrow(RuntimeException::class, 'Erreur lors de la récupération des données depuis factions');
    });

    it('can authenticate and get token', function () {
        $tokenResponse = ['token' => 'test-token-123'];
        $this->mockHandler->append(new Response(200, [], json_encode($tokenResponse)));

        $reflection = new ReflectionClass($this->service);
        $method = $reflection->getMethod('getAuthToken');
        $method->setAccessible(true);

        $token = $method->invoke($this->service, 'test@example.com', 'password');

        expect($token)->toBe('test-token-123');

        $request = $this->mockHandler->getLastRequest();
        $body = json_decode($request->getBody()->getContents(), true);

        expect($body)->toBe([
            'email' => 'test@example.com',
            'password' => 'password'
        ]);
    });

    it('throws exception when authentication fails', function () {
        $this->mockHandler->append(new RequestException(
            'Unauthorized',
            new Request('POST', 'login')
        ));

        $reflection = new ReflectionClass($this->service);
        $method = $reflection->getMethod('getAuthToken');
        $method->setAccessible(true);

        expect(fn() => $method->invoke($this->service, 'wrong@example.com', 'wrong'))
            ->toThrow(RuntimeException::class, 'Erreur lors de la récupération du token');
    });

    it('processes api data correctly', function () {
        $responseBody = [
            'hydra:member' => [
                [
                    'id' => 1,
                    'name' => 'Test Faction',
                    'reference' => 'TF'
                ]
            ]
        ];

        $reflection = new ReflectionClass($this->service);
        $method = $reflection->getMethod('processApiData');
        $method->setAccessible(true);

        $method->invoke($this->service, $responseBody);

        expect(Faction::count())->toBe(1)
            ->and(Faction::first()->name)->toBe('Test Faction');
    });
});
