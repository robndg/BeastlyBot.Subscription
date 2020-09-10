<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public $incrementing = false;
    public $primaryKey = 'id';
    public $timestamps = false;
}
