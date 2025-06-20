<?php

use App\Services\AlteredApi\GetUserCollection;
use App\Models\User;
use App\Models\Card;
use App\Models\CardType;
use Illuminate\Support\Facades\Http;

describe('GetUserCollection', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->token = 'fake-token';

        $this->normalType = CardType::factory()->create(['slug' => 'NORMAL']);
        $this->tokenType = CardType::factory()->create(['slug' => 'TOKEN']);

        $this->card1 = Card::factory()->create([
            'slug' => 'card-1',
            'card_type_id' => $this->normalType->id,
        ]);
        $this->card2 = Card::factory()->create([
            'slug' => 'card-2',
            'card_type_id' => $this->tokenType->id,
        ]);
        $this->card3 = Card::factory()->create([
            'slug' => 'card-3',
            'card_type_id' => $this->normalType->id,
        ]);
    });

    it('synchronizes user collection and ignores TOKEN cards, with pagination', function () {
        Http::fake([
            'https://api.altered.gg/cards/stats*' => Http::sequence()
                ->push([
                    'hydra:member' => [
                        [
                            '@id' => '/cards/card-1',
                            'inMyCollection' => 2,
                        ],
                        [
                            '@id' => '/cards/card-2',
                            'inMyCollection' => 5,
                        ],
                    ],
                    'hydra:view' => [
                        'hydra:next' => '/cards/stats?page=2',
                    ],
                ])
                ->push([
                    'hydra:member' => [
                        [
                            '@id' => '/cards/card-3',
                            'inMyCollection' => 1,
                        ],
                    ],
                ])
        ]);

        $service = new GetUserCollection();
        $service($this->user, $this->token);

        $this->user->refresh();
        $cards = $this->user->cards()->get();

        expect($cards->count())->toBe(2);
        expect($cards->pluck('slug'))->toContain('card-1', 'card-3');
        expect($cards->pluck('slug'))->not()->toContain('card-2');
        expect($cards->firstWhere('slug', 'card-1')->pivot->quantity)->toBe(2);
        expect($cards->firstWhere('slug', 'card-3')->pivot->quantity)->toBe(1);
    });
}); 