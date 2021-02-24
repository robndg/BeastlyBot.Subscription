<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Processors extends Model
{
    protected $fillable = ['id', 'user_id', 'store_id', 'type', 'processor_id', 'cur', 'enabled'];

    public function storeProcessor()
    {
        return $this->belongsTo(DiscordStore::class)->select(array('id', 'store_id'));
    }

    public function userProcessor()
    {
        return $this->belongsTo(User::class)->where('user_id');
    }

   // public $incrementing = false;

}