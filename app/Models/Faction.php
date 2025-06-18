<?php

namespace App\Models;

use Database\Factories\FactionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faction extends Model
{
    /** @use HasFactory<FactionFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'altered_api_id',
    ];
}
