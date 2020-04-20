<?php

namespace App;

class PayoutHelper
{

    /**
     * 1. We hold funds in our Stripe account for at least 7 days or whatever the refund terms are
     * 2. We grab all invoices that are from refund threshold days back until now
     * 3. After the invoice is passed the refund threshold days it will be transferred to
     * the Express users account automatically from the bot
     */

}
