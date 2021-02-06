<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductRole extends Model
{
    protected $fillable = ['id', 'discord_store_id', 'role_id', 'description', 'active', 'start_date', 'end_date', 'max_sales'];

    public function store()
    {
        return $this->belongsTo(DiscordStore::class);
    }
    protected $casts = [
        'id' => 'string',
        'start_date' =>  'datetime',
        'end_start' => 'datetime',
    ];

    public function prices()
    {
        return $this->hasMany(Price::class, 'product_id', 'id');
    }
}
