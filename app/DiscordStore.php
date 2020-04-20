<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DiscordStore extends Model
{
    protected $primaryKey = 'guild_id';
    public $incrementing = false;
}
