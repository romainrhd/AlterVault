<?php

use App\Models\Card;
use App\Services\SearchService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Builder;

uses(RefreshDatabase::class);

beforeEach(function () {
    Card::factory()->create(['name' => 'Petit Djinn']);
    Card::factory()->create(['name' => 'Puissant Djinn']);
    Card::factory()->create(['name' => 'Kojo']);
    Card::factory()->create(['name' => 'Skadi']);
});

it('returns a query builder instance', function () {
    $searchService = new SearchService('');
    $result = $searchService();

    expect($result)->toBeInstanceOf(Builder::class);
});

it('includes users relationship in the query', function () {
    $searchService = new SearchService('');
    $query = $searchService();

    expect($query->getEagerLoads())->toHaveKey('users');
});

it('returns all cards when search is empty', function () {
    $searchService = new SearchService('');
    $results = $searchService()->get();

    expect($results)->toHaveCount(4);
});

it('filters cards by name when search term is provided', function () {
    $searchService = new SearchService('Petit');
    $results = $searchService()->get();

    expect($results)->toHaveCount(1)
        ->and($results->first()->name)->toBe('Petit Djinn');
});

it('performs case insensitive partial search', function () {
    $searchService = new SearchService('puiss');
    $results = $searchService()->get();

    expect($results)->toHaveCount(1)
        ->and($results->first()->name)->toBe('Puissant Djinn');
});

it('returns empty collection when no cards match search term', function () {
    $searchService = new SearchService('NonExistentCard');
    $results = $searchService()->get();

    expect($results)->toHaveCount(0);
});

it('handles special characters in search term', function () {
    Card::factory()->create(['name' => 'Test-Card']);

    $searchService = new SearchService('Test-');
    $results = $searchService()->get();

    expect($results)->toHaveCount(1)
        ->and($results->first()->name)->toBe('Test-Card');
});

it('can be chained with additional query methods', function () {
    $searchService = new SearchService('d');
    $query = $searchService();

    $results = $query->orderBy('name')->limit(2)->get();

    expect($results)->toHaveCount(2)
        ->and($results->first()->name)->toBe('Petit Djinn');
});

it('maintains query builder state for further modifications', function () {
    $searchService = new SearchService('di');
    $query = $searchService();

    $results = $query->where('name', '!=', 'Puissant Djinn')->get();

    expect($results)->toHaveCount(1)
        ->and($results->first()->name)->toBe('Skadi');
});
