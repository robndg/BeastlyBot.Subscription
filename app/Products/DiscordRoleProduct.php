<?php

namespace App\Products;

use App\DiscordStore;
use Exception;
use Illuminate\Http\Request;

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
  
    public function checkoutValidate(): void {
        if(! DiscordStore::where('guild_id', $this->guild_id)->exists())
            throw new ProductMsgException('Discord store not found in database.');

        $this->discord_store = DiscordStore::where('guild_id', $this->guild_id)->first();

        if($this->discord_store->testing)
            throw new ProductMsgException('Sorry, purchases are disabled in testing mode.');

        if (auth()->user()->getStripeHelper()->isSubscribedToProduct($this->guild_id . '_' . $this->role_id)) 
            throw new ProductMsgException('You are already subscribed to that role. You can edit your subscription in the subscriptions page.');
        
    }

    public function checkoutSuccess()
    {
        return redirect('/account/subscriptions');
    }

    public function checkoutCancel()
    {
        return redirect('/shop/' . $this->guild_id);
    }

    public function create(Request $request) {
        $product_id = 'discord_' . $this->guild_id . '_' . $this->role_id;
        
        \Stripe\Product::create([
            'name' => $request['name'],
            'id' => $product_id,
            'type' => 'service',
            'metadata' => ['owner_id' => auth()->user()->StripeConnect->express_id],
        ]);

        return response()->json(['success' => true, 'msg' => 'Product created!']);
    }

    // can't delete a product if there are any \Stripe\Plan's still with active \Stripe\Subscription's on them.
    public function delete(Request $request) {
        $product_id = 'discord_' . $this->guild_id . '_' . $this->role_id;

        $product = \Stripe\Product::retrieve($product_id);
        $product->delete();

        return response()->json(['success' => true, 'msg' => 'Product deleted!']);
    }

    public function update(Request $request) {
        
    }

    public function getCallbackParams(): array
    {
        return ['guild_id' => $this->guild_id, 'role_id' => $this->role_id, 'billing_cycle' => $this->billing_cycle];
    }

    public function getApplicationFee(): float
    {
        return 5.0;
    }

}
