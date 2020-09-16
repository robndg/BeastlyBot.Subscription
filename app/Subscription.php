<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    public $incrementing = false;
    public $primaryKey = 'id';
    public $timestamps = false;

    protected $casts = [
        'metadata' => 'array'
    ];
}
