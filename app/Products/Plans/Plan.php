<?php

namespace App\Products\Plans;

use App\Products\ProductMsgException;

use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use App\StripeHelper;
use Illuminate\Support\Str;
use App\Price;

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


        if(Cache::has('plan_' . $this->getStripeID())) {
            if(Cache::get('plan_' . $this->getStripeID()) != 'null') {
                $this->stripe_plan_obj = Cache::get('plan_' . $this->getStripeID());
            }
        } else {
            StripeHelper::setApiKey();
            try {
                $this->stripe_plan_obj = \Stripe\Price::retrieve($this->getStripeID());
                Cache::put('plan_' . $this->getStripeID(), $this->stripe_plan_obj, 60 * 10);
            } catch (\Exception $e) {
                Cache::put('plan_' . $this->getStripeID(), "null", 60 * 10);
            }
        }
        
    }

    public function getProduct(): \App\Products\Product {
        return $this->product;
    }

    public function create(Request $request) {
        $price = $request['price'];

        $product_id = $request['product_id'];

        if (! $this->validPrice($price)) throw new ProductMsgException('Invalid price for ' . $this->interval_cycle . ' ' . $this->interval . '.');

        // if the plan already exists, delete it.
        try {
            $this->delete($request);
        } catch (\Exception $e) {
        }

        if($price < 1) return; // TODO ROB: If price null set 0
/*
        $plan = \Stripe\Plan::create([
            "amount" => $price * 100,
            "interval" => $this->interval,
            "interval_count" => $this->interval_cycle,
            "product" => $this->product->getStripeID(),
            "currency" => "usd",
            'metadata' => [
                'user_id' => auth()->user()->id 
            ],
            "id" => $this->getStripeID(),
        ]);*/


        $cur = "usd"; // Comes global owner stripe

        if(Price::where('product_id', $product_id)->where('interval', $this->interval)->exists()){ // TODO Rob: might search by UUID
            $product_price = \App\Price::where('product_id', $product_id)->where('interval', $this->interval)->first();
            
        }else{
            $product_price = new \App\Price([
            'id' => Str::uuid(),
            'interval' => $this->interval,
            'product_id' => $product_id,
            'price' => intval($price) * 100,
            ]);
            $product_price->save();
            //Log::info($product->id);
            
        }
        $product_price->price = intval($price) * 100;
        $product_price->cur = $cur;
        $product_price->status = 0;
        $product_price->save();
        
        $product_price = \App\Price::where('product_id', $product_id)->where('interval', $this->interval)->first();

        $plan = \Stripe\Price::create([
            'unit_amount' => intval($price) * 100,
            'currency' => $cur,
            'recurring' => ['interval' => $this->interval],
            'product' => $this->product->getStripeID(), // TODO: change to $product_price->id
            'metadata' => [
                'product_uuid' => $product_price->id, 
            ],
          ]);

       // Cache::put('plan_' . $this->getStripeID(), $product_price, 60 * 10);
        $product_id->product_id = $product_id;
        $product_price->stripe_price_id = $plan->id;
        $product_price->status = 1;
        $product_price->save();

        return response()->json(['success' => true, 'msg' => 'Plan created!1', 'active' => true]);
    }
    
    /* 
        Plans on Stripe are immutable by design, meaning you can't change the price. 
        However, you can delete the plan and re-create it at a new price, with the same name and plan_id. 
        Internally Stripe will continue to use the old plan for existing customers.
    */
    public function delete(Request $request) {
        StripeHelper::setApiKey();
        try {
            if($this->getStripePlan() !== null) {
                Cache::forget('plan_' . $this->getStripeID());
                $this->getStripePlan()->delete();
            }
        } catch(\Exception $e) {}
    }

    abstract public function update(Request $request);

    abstract public function getStripeID(): string;

    public function getStripePlan() {
        return $this->stripe_plan_obj;
    }

    protected function validPrice($input): bool {
        return $input === null || $input === '' || preg_match('/^[0-9]+(?:\.[0-9]{0,2})?$/', $input);
    }
    
}