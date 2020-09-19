<?php

namespace App\Products;

use App\DiscordStore;
use App\SiteConfig;
use Illuminate\Http\Request;

class DiscordRoleProduct extends Product
{

    private $guild_id, $role_id, $billing_cycle;
    private $discord_store;

    public function __construct($guild_id, $role_id, $billing_cycle)
    {
        $this->guild_id = $guild_id;
        $this->role_id = $role_id;
        $this->billing_cycle = $billing_cycle;
        parent::__construct('discord');
    }
  
    public function checkoutValidate(): void {
        if(! DiscordStore::where('guild_id', $this->guild_id)->exists())
            throw new ProductMsgException('Discord store not found in database.');

        $this->discord_store = DiscordStore::where('guild_id', $this->guild_id)->first();

        if(!$this->discord_store->live)
            throw new ProductMsgException('Sorry, purchases are disabled in testing mode.');

        if (auth()->user()->getStripeHelper()->isSubscribedToProduct($this->guild_id . '_' . $this->role_id)) 
            throw new ProductMsgException('You are already subscribed to that role. You can edit your subscription in the subscriptions page.');
        
    }

    public function create(Request $request) {
        \Stripe\Product::create([
            'name' => $request['name'],
            'id' => $this->getStripeID(),
            'type' => 'service',
            'metadata' => ['user_id' => auth()->user()->id],
        ]);

        if(! DiscordStore::where('guild_id', $this->guild_id)->exists()) {
            $store = new DiscordStore();
            $store->guild_id = $this->guild_id;
            $store->user_id = auth()->user()->id;
            $store->url = explode('_', $this->getStripeID())[1];
            $store->live = false;
            $store->save();
        }

        return response()->json(['success' => true, 'msg' => 'Product created!', 'active' => true]);
    }

    public function update(Request $request) {
        try {
            \Stripe\Product::update($this->getStripeID(), ['active' => $request['active']]);
        } catch(\Exception $e) {
        }
    }

    public function getCallbackParams(): array
    {
        return ['guild_id' => $this->guild_id, 'role_id' => $this->role_id, 'billing_cycle' => $this->billing_cycle];
    }

    public function getStripeID(): string
    {
        return 'discord_' . $this->guild_id . '_' . $this->role_id;
    }

    public function checkoutSuccess()
    {
        return redirect('/account/subscriptions');
    }

    public function checkoutCancel()
    {
        return redirect('/shop/' . $this->guild_id);
    }

    public function getStripePlan() {
        \Stripe\Stripe::setApiKey(env('STRIPE_CLIENT_SECRET'));
        return \Stripe\Plan::retrieve($this->getStripeID() . '_' . $this->billing_cycle . '_r');
    }

}
