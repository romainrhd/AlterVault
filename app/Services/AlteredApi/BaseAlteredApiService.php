<?php

namespace App\Services\AlteredApi;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

abstract class BaseAlteredApiService
{
    protected Client $client;

    public function __construct(Client $client = null)
    {
        $this->client = $client ?? new Client([
            'base_uri' => 'https://api.altered.gg/',
            'headers' => [
                'Accept' => 'application/ld+json',
            ],
        ]);
    }

    abstract public function getData(?string $email, ?string $password);

    abstract protected function getEndpoint(): string;

    abstract protected function getModelClass(): string;

    abstract protected function transformApiData(array $apiData): array;

    protected function makeApiRequest(string $endpoint, array $queryParams = [], array $headers = []): array
    {
        try {
            $response = $this->client->get($endpoint, [
                'headers' => $headers,
                'query' => array_merge(['locale' => 'fr-fr'], $queryParams)
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            throw new \RuntimeException(
                sprintf('Erreur lors de la récupération des données depuis %s: %s', $endpoint, $e->getMessage())
            );
        }
    }

    protected function processApiData(array $responseBody): void
    {
        $modelClass = $this->getModelClass();

        foreach ($responseBody['hydra:member'] as $item) {
            if ($this->shouldProcessItem($item)) {
                $transformedData = $this->transformApiData($item);
                $modelClass::updateOrCreate(
                    ['altered_api_id' => $item['id']],
                    $transformedData
                );
            }
        }
    }

    protected function shouldProcessItem(array $item): bool
    {
        return true;
    }

    protected function getAuthToken(string $email, string $password): string
    {
        try {
            $response = $this->client->post('login', [
                'json' => [
                    'email' => $email,
                    'password' => $password
                ]
            ]);

            $responseBody = json_decode($response->getBody()->getContents(), true);
            return $responseBody['token'];
        } catch (GuzzleException $e) {
            throw new \RuntimeException('Erreur lors de la récupération du token: ' . $e->getMessage());
        }
    }
}
