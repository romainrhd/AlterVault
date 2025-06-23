<?php

namespace App\Console\Commands;

use App\Models\CardSet;
use App\Models\User;
use App\Services\AlteredApi\GetAuthToken;
use App\Services\AlteredApi\GetCards;
use App\Services\AlteredApi\GetCardSets;
use App\Services\AlteredApi\GetCardTypes;
use App\Services\AlteredApi\GetFactions;
use App\Services\AlteredApi\GetRarities;
use App\Services\AlteredApi\GetUserCollection;
use Illuminate\Console\Command;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Auth;

class FetchAlteredDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'altered:fetch-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to fetch Altered data like Faction, Type of card, cards, etc...';

    /**
     * Execute the console command.
     * @throws ConnectionException
     */
    public function handle(): void
    {
        $this->info('We need your Altered credentials to fetch all data of Altered API.');
        $altered_email = $this->ask("What's your email of altered account?");
        $altered_password = $this->secret("What's your password of altered account?");

        $token = (new GetAuthToken($altered_email, $altered_password))();

        $this->info('We need your AlterVault credentials to sync your collection.');
        $altervault_email = $this->ask("What's your email of AlterVault account?");
        $altervault_password = $this->secret("What's your password of AlterVault account?");

        if (!Auth::attempt(['email' => $altervault_email, 'password' => $altervault_password])) {
            $this->error('Aucun utilisateur trouvé en base.');
            return;
        }

        $user = User::where('email', $altervault_email)->first();

        $this->info('Starting fetching Factions data...');
        (new GetFactions())->getData();
        $this->info('Finished fetching Factions data...');

        $this->info('Starting fetching Sets of Cards data...');
        (new GetCardSets())->getData();
        $this->info('Finished fetching Sets of Cards data...');

        $this->info('Starting fetching Types of Cards data...');
        (new GetCardTypes())->getData($altered_email, $altered_password);
        $this->info('Finished fetching Types of Cards data...');

        $this->info('Starting fetching Rarities data...');
        (new GetRarities())->getData();
        $this->info('Finished fetching Rarities data...');

        $this->info('Starting fetching Cards data...');
        CardSet::all()->each(function (CardSet $cardSet): void {
            (new GetCards($cardSet->slug))->getData();
        });
        $this->info('Finished fetching Cards data...');

        $this->info('Synchronisation de la collection utilisateur...');
        app(GetUserCollection::class)($user, $token);
        $this->info('Collection synchronisée !');
    }
}
