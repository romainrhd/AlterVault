<?php

namespace App\Services;

use App\Models\Card;
use Illuminate\Database\Eloquent\Builder;

readonly class SearchService
{
    public function __construct(
        private string $search
    ){}

    public function __invoke(): Builder
    {
        $query = Card::with('users');

        if (!empty($this->search)) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        return $query;
    }
}
