<?php

namespace App\Products\Plans;

use App\Products\ProductMsgException;
use App\SiteConfig;
use Illuminate\Http\Request;

abstract class Plan 
{
  
    protected $product;
    protected $stripe_plan_obj;
    public $interval, $interval_cycle;

    public function __construct(\App\Products\Product $product, string $interval, string $interval_cycle = null)
    {
        $this->product = $product;
        $this->interval = $interval;
        $this->interval_cycle = $interval_cycle;
        if(('day' && 'week' && 'year' && 'month') != $interval) throw new ProductMsgException('Invalid interval.');
        try {
            $this->stripe_plan_obj = \Stripe\Plan::retrieve($this->getStripeID());
        } catch (\Exception $e) {}
    }

    public function getProduct(): \App\Products\Product {
        return $this->product;
    }

    public function create(Request $request) {
        $price = $request['price'];

        if (! $this->validPrice($price)) throw new ProductMsgException('Invalid price for ' . $this->interval_cycle . ' ' . $this->interval . '.');

        // if the plan already exists, delete it.
        try {
            $this->delete($request);
        } catch (\Exception $e) {
        }

        if($price < 1) return;

        \Stripe\Plan::create([
            "amount" => $price * 100,
            "interval" => $this->interval,
            "interval_count" => $this->interval_cycle,
            "product" => $this->product->getStripeID(),
            "currency" => "usd",
            'metadata' => [
                'user_id' => auth()->user()->id
            ],
            "id" => $this->getStripeID(),
            "nickname" => $request['nickname'],
        ]);
        
        return response()->json(['success' => true, 'msg' => 'Plan created!', 'active' => true]);
    }
    
    /* 
        Plans on Stripe are immutable by design, meaning you can't change the price. 
        However, you can delete the plan and re-create it at a new price, with the same name and plan_id. 
        Internally Stripe will continue to use the old plan for existing customers.
    */
    public function delete(Request $request) {
        \Stripe\Stripe::setApiKey(SiteConfig::get('STRIPE_SECRET'));
        try {
            if($this->getStripePlan() !== null) {
                $this->getStripePlan()->delete();
            }
        } catch(\Exception $e) {}
    }

    abstract public function update(Request $request);

    abstract public function getStripeID(): string;

    public function getStripePlan() {
        \Stripe\Stripe::setApiKey(SiteConfig::get('STRIPE_SECRET'));
        if($this->stripe_plan_obj == null) {
            try {
                $this->stripe_plan_obj = \Stripe\Plan::retrieve($this->getStripeID());
            } catch (\Exception $e) {}
        }
        return $this->stripe_plan_obj;
    }

    protected function validPrice($input): bool {
        return $input === null || $input === '' || preg_match('/^[0-9]+(?:\.[0-9]{0,2})?$/', $input);
    }
    
}