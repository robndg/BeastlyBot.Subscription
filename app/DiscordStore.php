<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DiscordStore extends Model
{
    protected $fillable = ['guild_id', 'url', 'user_id', 'UUID'];

    public function owner()
    {
        return $this->belongsTo(User::class);
    }
}
