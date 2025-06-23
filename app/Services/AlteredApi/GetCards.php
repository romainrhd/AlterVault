<?php

namespace App\Services\AlteredApi;

use App\Models\Card;
use App\Models\CardSet;
use App\Models\CardType;
use App\Models\Faction;
use App\Models\Rarity;
use GuzzleHttp\Client;

class GetCards extends BaseAlteredApiService
{
    public function __construct(
        private readonly string $cardSetSlug,
        Client $client = null
    ){
        parent::__construct($client);
    }

    protected function getEndpoint(): string
    {
        return 'cards';
    }

    protected function getModelClass(): string
    {
        return Card::class;
    }

    protected function transformApiData(array $apiData): array
    {
        return [
            'name' => $apiData['name'],
            'slug' => $apiData['reference'],
            'image' => $apiData['imagePath'],
            'altered_api_id' => $apiData['id'],
            'faction_id' => Faction::where('altered_api_id', $apiData['mainFaction']['id'])->first()->id,
            'rarity_id' => Rarity::where('altered_api_id', $apiData['rarity']['id'])->first()->id,
            'card_type_id' => CardType::where('altered_api_id', $apiData['cardType']['id'])->first()->id,
            'card_set_id' => CardSet::where('altered_api_id', $apiData['cardSet']['id'])->first()->id,
        ];
    }

    protected function shouldProcessItem(array $item): bool
    {
        return $item['cardType']['reference'] !== 'FOILER';
    }

    public function getData(?string $email = null, ?string $password = null): void
    {
        $page = 1;
        $hasNextPage = true;

        while ($hasNextPage) {
            $responseBody = $this->makeApiRequest($this->getEndpoint(), ['page' => $page, 'cardSet' => $this->cardSetSlug]);
            $this->processApiData($responseBody);

            $hasNextPage = isset($responseBody['hydra:view']['hydra:next']);
            $page++;
        }
    }
}
