<?php

namespace App\Services\AlteredApi;

use App\Models\CardSet;

class GetCardSets extends BaseAlteredApiService
{
    protected function getEndpoint(): string
    {
        return 'card_sets';
    }

    protected function getModelClass(): string
    {
        return CardSet::class;
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
        $responseBody = $this->makeApiRequest($this->getEndpoint());
        $this->processApiData($responseBody);
    }
}
