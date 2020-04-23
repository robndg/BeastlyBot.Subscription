<?php

namespace App\Products\Plans;

class DiscordPlan extends Plan
{

    public function update(\Illuminate\Http\Request $request)
    {
        
    }

    public function getStripeID(): string
    {
        return $this->product->getStripeID() . '_' . $this->interval_cycle . '_r';
    }

}