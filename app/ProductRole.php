<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductRole extends Model
{
    protected $fillable = ['id', 'discord_store_id', 'role_id', 'description', 'active'];

    public function store()
    {
        return $this->belongsTo(DiscordStore::class);
    }
    protected $casts = [
        'id' => 'string'
    ];

   /* public function prices()
    {
        return $this->hasMany(Price::class);
    }*/
}
