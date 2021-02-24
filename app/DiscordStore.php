<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DiscordStore extends Model
{
    protected $fillable = ['guild_id', 'url', 'user_id', 'UUID', 'payment_processor', 'processor_id'];

    public function owner()
    {
        return $this->belongsTo(User::class);
    }
}
