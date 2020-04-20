<?php

namespace App\Products;

use App\AlertHelper;

class ExpressProduct extends Product
{

    public function validate(): void
    {
        $stripe_helper = auth()->user()->getStripeHelper();

         // make sure they have linked their stripe acount
         if (! $stripe_helper->isExpressUser()) 
            throw new ProductMsgException('Please connect or create your stripe Express account.');
         // make sure they do not already have an active express plan with us
         if ($stripe_helper->hasActivePlan()) 
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
        return redirect('/account/settings');
    }

}
