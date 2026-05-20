<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable=[
        'type_id',
        'title',
        'description',
        'tech',
        'features',
        'type'
    ];

    protected $casts = [
        'tech'=>'array',
        'features'=>'array',
    ];
}
