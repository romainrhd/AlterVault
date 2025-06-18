<?php

namespace App\Models;

use Database\Factories\RarityFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rarity extends Model
{
    /** @use HasFactory<RarityFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'altered_api_id',
    ];
}
