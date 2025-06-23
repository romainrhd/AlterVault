<?php

use App\Models\User;
use App\Models\Card;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Resources\CardResource;
use App\Services\SearchService;

new
#[Layout('components.layouts.app')]
#[Title('My Collection')]
class extends Component {
    public array $cards;

    public string $search = '';

    public function mount(): void
    {
        $this->loadCards();
    }

    public function updatedSearch(): void
    {
        $this->loadCards();
    }

    private function loadCards(): void
    {
        $this->cards = CardResource::collection((new SearchService($this->search))()->get())->toArray(request());
    }

    public function loadQuantityColor(int $quantity): string
    {
        return match (true) {
            $quantity > 2 => 'green',
            $quantity > 0 => 'yellow',
            default => 'red'
        };
    }
}; ?>

<div>
    <div class="mb-6">
        <flux:input
            wire:model.live="search"
            :placeholder="__('Search for a card by name...')"
            icon="magnifying-glass"
        >
            @if(!empty($search))
                <x-slot name="iconTrailing">
                    <flux:button wire:click="$set('search', '')" size="sm" variant="subtle" icon="x-mark" class="-mr-1" />
                </x-slot>
            @endif
        </flux:input>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @forelse($cards as $card)
            <div
                class="flex flex-col rounded-xl border bg-white dark:bg-zinc-900 shadow-sm overflow-hidden transition hover:shadow-md">
                @if($card['image'])
                    <div class="w-full flex items-center justify-center bg-zinc-50 dark:bg-zinc-800"
                         style="aspect-ratio:3/4;">
                        <img src="{{ $card['image'] }}" alt="{{ $card['name'] }}"
                             class="max-h-full max-w-full object-contain" loading="lazy">
                    </div>
                @else
                    <div class="w-full flex items-center justify-center bg-zinc-50 dark:bg-zinc-800 text-zinc-400"
                         style="aspect-ratio:3/4;">
                        <flux:icon.photo class="size-12"/>
                    </div>
                @endif

                <div class="flex flex-col p-4">
                    <flux:heading size="lg" class="mb-1 truncate">{{ $card['name'] }}</flux:heading>
                    <flux:badge class="w-fit" :color="$this->loadQuantityColor($card['quantity'])" size="sm">
                        x{{ $card['quantity'] }}</flux:badge>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="text-center py-8 rounded-xl border bg-white dark:bg-zinc-900 shadow-sm">
                    <flux:icon.magnifying-glass class="mx-auto size-12 text-zinc-400 mb-4"/>
                    <flux:heading size="lg" class="text-zinc-500 mb-2">{{ __('No cards found') }}</flux:heading>
                    <flux:subheading class="text-zinc-400">
                        {{ __('No results for :search', ['search' => $search]) }}
                    </flux:subheading>
                </div>
            </div>
        @endforelse
    </div>
</div>
