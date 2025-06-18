<?php

namespace App\Services\AlteredApi;

use App\Models\Faction;

class GetFactions extends BaseAlteredApiService
{
    protected function getEndpoint(): string
    {
        return 'factions';
    }

    protected function getModelClass(): string
    {
        return Faction::class;
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
