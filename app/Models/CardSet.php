<?php

namespace App\Models;

use Database\Factories\CardSetFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CardSet extends Model
{
    /** @use HasFactory<CardSetFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'altered_api_id',
    ];
}
