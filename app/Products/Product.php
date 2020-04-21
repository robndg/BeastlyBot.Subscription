<?php

namespace App\Products;

use Illuminate\Http\Request;

abstract class Product 
{
  
    protected $product_type;
    protected $stripe_product_obj, $stripe_plan_obj;

    public function __construct(string $product_type, string $product_id = null, string $plan_id)
    {
        $this->product_type = $product_type;
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        if($product_id !== null) {
            $this->stripe_product_obj = \Stripe\Product::retrieve($product_id);
            if (! $this->stripe_product_obj->active) throw new ProductMsgException('Product is not active.');
        }
        $this->stripe_plan_obj = \Stripe\Plan::retrieve($plan_id);
        if (! $this->stripe_plan_obj->active) throw new ProductMsgException('Plan is not active.');
    }

    abstract public function checkoutValidate(): void;

    abstract public function checkoutSuccess();

    abstract public function checkoutCancel();

    abstract public function changePlan(string $new_plan_id);

    abstract public function create(Request $request);

    abstract public function delete(Request $request);

    abstract public function update(Request $request);

    abstract public function getCallbackParams(): array;

    abstract public function getApplicationFee(): float;

    public function getCallbackSuccessURL(): string {
        $data = $this->getCallbackParams();
        $data['success'] = true;
        $data['product_type'] = $this->product_type;
        return env('APP_URL') . '/checkout?' . http_build_query($data);
    }

    public function getCallbackCancelURL(): string {
        $data = $this->getCallbackParams();
        $data['success'] = false;
        $data['product_type'] = $this->product_type;
        return env('APP_URL') . '/checkout?' . http_build_query($data);
    }

    public function getStripeProduct(): \Stripe\Product {
        return $this->stripe_product_obj;
    }

    public function getStripePlan(): \Stripe\Plan {
        return $this->stripe_plan_obj;
    }

    public function getExpressOwnerID(): string {
        if($this->stripe_product_obj != null) 
            return $this->stripe_product_obj->metadata['owner_id'];
        return null;
    }

}
