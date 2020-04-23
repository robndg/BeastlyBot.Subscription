<?php

namespace App\Products;

use App\AlertHelper;
use App\SiteConfig;
use Illuminate\Support\Facades\Log;

class ExpressProduct extends Product
{

    public function __construct(string $product_id = null, string $plan_id)
    {
        parent::__construct('express', $product_id, $plan_id);
    }

    public function checkoutValidate(): void
    {
        $stripe_helper = auth()->user()->getStripeHelper();

         // make sure they have linked their stripe acount
         if (! $stripe_helper->isExpressUser()) 
            throw new ProductMsgException('Please connect or create your stripe Express account.');
         // make sure they do not already have an active express plan with us
         if ($stripe_helper->hasExpressPlan()) 
            throw new ProductMsgException('You already have an active live plan.');
         // this is pretty useless, but it's making sure they have a valid stripe Customer account with us, not Express. That way we can bill their Customer account
         if ($stripe_helper->getStripeEmail() == null) 
            throw new ProductMsgException('You do not have a linked stripe account.');
    }
    
    public function getCallbackParams(): array
    {
        return ['plan_id' => $this->getStripePlan()->id];
    }

    public function checkoutSuccess()
    {
        return redirect('/servers?click-first=true');
    }

    public function checkoutCancel()
    {
        return redirect('/servers?click-first=true');
    }

    public function changePlan(string $new_plan_id)
    {
        // Any time accessing Stripe API this snippet of code must be ran above any preceding API calls
        \Stripe\Stripe::setApiKey(SiteConfig::get('STRIPE_SECRET'));
 
        $stripe_helper = auth()->user()->getStripeHelper();

        if(! $stripe_helper->hasExpressPlan())
            throw new ProductMsgException('You do not have an active Express plan. Nothing to update.');

        if ($stripe_helper->isSubscribedToPlan($new_plan_id)) 
            throw new ProductMsgException('You are already subscribed to that plan.');

        $subscription = $stripe_helper->getExpressSubscription();

        try {
            \Stripe\Subscription::update($subscription->id, [
                'prorate' => true,
                'collection_method' => 'charge_automatically',
                'cancel_at_period_end' => false,
                'items' => [
                    [
                        'id' => $subscription->items->data[0]->id,
                        'plan' => $new_plan_id,
                    ],
                ],
            ]);
            $invoice = \Stripe\Invoice::upcoming(['customer' => $stripe_helper->getCustomerAccount()->id]);
            return response()->json(['success' => true, 'msg' => 'Plan changed. You were billed automatically.', 'invoice_url' => $invoice->invoice_pdf]);
        } catch(\Exception $e) {
            if(env('APP_DEBUG')) Log::error($e);
            return response()->json(['success' => false, 'msg' => 'Failed to change plan.']);
        }
    }

    public function getApplicationFee(): float
    {
        return 1.5;
    }

}
