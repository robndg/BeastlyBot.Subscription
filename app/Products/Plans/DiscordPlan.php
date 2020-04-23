<?php

namespace App\Products\Plans;

use Illuminate\Support\Facades\Cache;

class DiscordPlan extends Plan
{

    public function update(\Illuminate\Http\Request $request)
    {
        
    }

    public function create(\Illuminate\Http\Request $request)
    {
        try {
            parent::create($request);
            $key = 'price_' . $this->product->getStripeID() . '_' . $this->interval_cycle;
            Cache::put($key, $request['price'], 60 * 5);
        } catch(\Exception $e) {}
    }

    public function getStripeID(): string
    {
        return $this->product->getStripeID() . '_' . $this->interval_cycle . '_r';
    }

}