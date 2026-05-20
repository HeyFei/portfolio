<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hero extends Model
{
    protected $table='hero';

    use HasFactory;

    protected $fillable=[
        'title',
        'subtitle',
        'coreStack',
        'cloudPath'
    ];

    protected $casts = [
        'coreStack'=>'array'
    ];
}
