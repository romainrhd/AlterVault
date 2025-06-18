<?php

namespace App\Models;

use Database\Factories\CardTypeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CardType extends Model
{
    /** @use HasFactory<CardTypeFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'altered_api_id',
    ];
}
