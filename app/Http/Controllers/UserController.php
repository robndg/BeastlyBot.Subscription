<?php

namespace App\Http\Controllers;

use App\AlertHelper;
use App\StripeHelper;
use App\User;
use Illuminate\Support\Facades\Log;

class UserController extends Controller {

    public function __construct() {
        $this->middleware('auth');
    }

    public static function getViewWithInvoices(string $view, int $num_of_invoices) {
        $stripe_helper = auth()->user()->getStripeHelper();

        // get last 100 most recent invoices for this customer
        $invoices = \Stripe\Invoice::all([
            'limit' => $num_of_invoices,
            'customer' => auth()->user()->stripe_customer_id
        ]);

        $invoices_array = $invoices->toArray()['data'];

       // sort the invoices in ASC order
        usort($invoices_array, function($a, $b) {
            return $b['created'] <=> $a['created'];
        });

        return view($view)->with('stripe_login_link', $stripe_helper->getLoginURL())->with('invoices', $invoices_array);
    }

    public static function getViewWithSubscriptions(string $view) {
        $stripe_helper = auth()->user()->getStripeHelper();
        $subscriptions = array();
        foreach ($stripe_helper->getSubscriptions() as $subscription) $subscriptions[$subscription->id] = $subscription->toArray();
        return view($view)->with('subscriptions', $subscriptions);
    }

}
