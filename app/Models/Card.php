<?php

namespace App\Models;

use Database\Factories\CardFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    /** @use HasFactory<CardFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'image',
        'altered_api_id',
        'faction_id',
        'rarity_id',
        'card_type_id',
        'card_set_id',
    ];
}
