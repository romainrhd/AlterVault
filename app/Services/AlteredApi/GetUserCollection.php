<?php

namespace App\Services\AlteredApi;

use App\Models\Card;
use App\Models\User;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class GetUserCollection
{
    /**
     * @throws ConnectionException
     */
    public function __invoke(User $user, string $token): void
    {
        $page = 1;
        $hasNextPage = true;
    
        while ($hasNextPage) {
            $response = Http::withToken($token)
                ->withHeaders([
                    'Accept' => 'application/ld+json',
                ])
                ->get('https://api.altered.gg/cards/stats', [
                    'collection' => 'true',
                    'locale' => 'fr-fr',
                    'page' => $page,
                ]);

            $body = $response->json();

            $this->processApiData($body['hydra:member'], $user);

            $hasNextPage = isset($body['hydra:view']['hydra:next']);
            $page++;
        }
    }

    private function processApiData(array $cards, User $user): void
    {
        foreach ($cards as $cardData) {
            $slug = str_replace('/cards/', '', $cardData['@id']);
            $quantity = $cardData['inMyCollection'] ?? 0;

            $card = Card::where('slug', $slug)->first();
            if (!$card) {
                continue;
            }

            if ($card->cardType->slug === 'TOKEN') {
                continue;
            }

            $user->cards()->syncWithoutDetaching([
                $card->id => ['quantity' => $quantity]
            ]);
        }
    }
}
