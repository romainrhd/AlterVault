<?php

namespace App\Services\AlteredApi;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class GetAuthToken
{
    private Client $client;

    public function __construct(
        private readonly string $email,
        private readonly string $password,
    ) {
        $this->client = $client ?? new Client([
            'base_uri' => 'https://api.altered.gg/',
            'headers' => [
                'Accept' => 'application/ld+json',
            ],
        ]);
    }

    public function __invoke()
    {
        try {
            $response = $this->client->post('login', [
                'json' => [
                    'email' => $this->email,
                    'password' => $this->password,
                ]
            ]);

            $responseBody = json_decode($response->getBody()->getContents(), true);
            return $responseBody['token'];
        } catch (GuzzleException $e) {
            throw new \RuntimeException('Erreur lors de la récupération du token: ' . $e->getMessage());
        }
    }
}
