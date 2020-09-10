<?php

namespace App\Products;

use App\SiteConfig;
use Illuminate\Http\Request;

abstract class Product 
{
  
    protected $product_type;
    protected $stripe_product_obj;

    public function __construct(string $product_type)
    {
        $this->product_type = $product_type;
        \Stripe\Stripe::setApiKey(SiteConfig::get('STRIPE_SECRET'));
        try {
            $this->stripe_product_obj = \Stripe\Product::retrieve($this->getStripeID());
        } catch (\Exception $e) {}
    }

    abstract public function checkoutValidate(): void;

    abstract public function checkoutSuccess();

    abstract public function checkoutCancel();

    abstract public function create(Request $request);

     // can't delete a product if there are any \Stripe\Plan's still with active \Stripe\Subscription's on them.
    public function delete(Request $request) {
        if($this->stripe_product_obj != null)
            $this->stripe_product_obj->delete();
    }

    public function update(Request $request) {}

    abstract public function getCallbackParams(): array;

    abstract public function getApplicationFee(): float;

    abstract public function getStripeID(): string;

    public function getCallbackSuccessURL(): string {
        $data = $this->getCallbackParams();
        $data['success'] = true;
        $data['product_type'] = $this->product_type;
        return SiteConfig::get('APP_URL') . '/checkout?' . http_build_query($data);
    }

    public function getCallbackCancelURL(): string {
        $data = $this->getCallbackParams();
        $data['success'] = false;
        $data['product_type'] = $this->product_type;
        return SiteConfig::get('APP_URL') . '/checkout?' . http_build_query($data);
    }

    public function getStripeProduct() {
        if($this->stripe_product_obj == null) {
            try {
                $this->stripe_product_obj = \Stripe\Product::retrieve($this->getStripeID());
            } catch (\Exception $e) {}
        }
        return $this->stripe_product_obj;
    }

    public function getExpressOwnerID() {
        if($this->stripe_product_obj != null) 
            return $this->stripe_product_obj->metadata['owner_id'];
        return null;
    }

    protected function validPrice($input): bool {
        return $input === null || $input === '' || preg_match('/^[0-9]+(?:\.[0-9]{0,2})?$/', $input);
    }

    abstract public function getStripePlan();

}
