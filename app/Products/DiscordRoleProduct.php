<?php

namespace App\Products;

use App\AlertHelper;
use App\DiscordStore;
use App\User;
use Exception;

class DiscordRoleProduct extends Product
{

    private $guild_id, $role_id, $billing_cycle;
    private $discord_store;

    public function __construct($guild_id, $role_id, $billing_cycle)
    {
        parent::__construct('discord', $guild_id . '_' . $role_id, $guild_id . '_' . $role_id . '_' . $billing_cycle . '_r');
        $this->guild_id = $guild_id;
        $this->role_id = $role_id;
        $this->billing_cycle = $billing_cycle;
    }
  
    public function validate(): void {
        if(! DiscordStore::where('guild_id', $this->guild_id)->exists())
            throw new ProductMsgException('Discord store not found in database.');

        $this->discord_store = DiscordStore::where('guild_id', $this->guild_id)->first();

        if($this->discord_store->testing)
            throw new ProductMsgException('Sorry, purchases are disabled in testing mode.');

        if (auth()->user()->getStripeHelper()->isSubscribedToProduct($this->guild_id . '_' . $this->role_id)) 
            throw new ProductMsgException('You are already subscribed to that role. You can edit your subscription in the subscriptions page.');
        
    }

    public function getCallbackParams(): array
    {
        return ['guild_id' => $this->guild_id, 'role_id' => $this->role_id, 'billing_cycle' => $this->billing_cycle];
    }

    public function checkoutSuccess()
    {
        // TODO: Create 'order' in Orders table for bot to use to init
        return redirect('/account/subscriptions');
    }

    public function checkoutCancel()
    {
        return redirect('/shop/' . $this->guild_id);
    }

}
