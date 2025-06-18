<?php

namespace App\Services\AlteredApi;

use App\Models\CardType;

class GetCardTypes extends BaseAlteredApiService
{
    protected function getEndpoint(): string
    {
        return 'card_types';
    }

    protected function getModelClass(): string
    {
        return CardType::class;
    }

    protected function transformApiData(array $apiData): array
    {
        return [
            'name' => $apiData['name'],
            'slug' => $apiData['reference'],
            'altered_api_id' => $apiData['id'],
        ];
    }

    public function getData(?string $email = null, ?string $password = null): void
    {
        $token = $this->getAuthToken($email, $password);
        $headers = ['Authorization' => 'Bearer ' . $token];

        $responseBody = $this->makeApiRequest($this->getEndpoint(), [], $headers);
        $this->processApiData($responseBody);
    }
}
