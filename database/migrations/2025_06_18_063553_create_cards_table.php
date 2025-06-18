<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cards', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('image');
            $table->string('altered_api_id')->unique();
            $table->foreignId('faction_id')->constrained('factions');
            $table->foreignId('rarity_id')->constrained('rarities');
            $table->foreignId('card_type_id')->constrained('card_types');
            $table->foreignId('card_set_id')->constrained('card_sets');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cards');
    }
};
