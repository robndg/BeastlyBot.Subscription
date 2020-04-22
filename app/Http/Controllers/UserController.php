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

    public function getDashboard() {
        $stripe_helper = auth()->user()->getStripeHelper();

    // get all active subscriptions for user and put into cleaned up array
    $subscriptions = array();
    foreach ($stripe_helper->getSubscriptions() as $subscription) {
        $subscriptions[$subscription->id] = $subscription->toArray();
    }

    return view('dashboard')->with('subscriptions', $subscriptions)->with('balance', $stripe_helper->getBalance());
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
    
    public function getPayoutSlide($stripe_account_id) {

        if (auth()->user()->StripeConnect->express_id != $stripe_account_id && !Auth::user()->admin) return response()->json(['success' => false, 'msg' => 'You do not own this Stripe account.']);

        #if (auth()->user()->error == "1") return response()->json(['success' => false, 'msg' => 'Please refresh the page and connect a US Stripe account.']);

        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        $unix_now = time();

    #####
        ## TODO: We should add payout delay to their stripe account, so if they get a dispute it adds 7 days
        /*
        $stripe_account_id = Auth::user()->stripe_account_id;
        $stripe_account = \Stripe\Account::retrieve($stripe_account_id);
        1) Get it from stripe
            $stripe_payout_delay = $stripe_account->settings->payouts->schedule->delay_days;
        2) Get it from user DB
            $stripe_payout_delay = User::where('stripe_account_id', '=' , $stripe_account_id)->value('stripe_delay_days');
        */
        $stripe_payout_delay = BeastlyConfig::get('STRIPE_PAYOUT_DELAY');
    #####

    ####
        ## TODO: can we get the fee percent from Stripe API?
        # so we can add days if they get disputes or lower if they are good.
        $app_fee_percent = 5;
    ####


    #### TODO: we need a faster way to get their paid invoices
        $invoices_pending = \Stripe\Invoice::all([
            'status' => 'paid',
            'created' => [
                'gte' => strtotime('-'.$stripe_payout_delay.' days', $unix_now)
            ],
        ]);

        $earnings = 0;
        $pending_invoices = array();
        foreach($invoices_pending as $invoice) {
            if ($invoice->metadata['paid_out'] != 'true' && $invoice->metadata['refunded'] != 'true') {
                try {
                    if($invoice->lines->data[0]->plan->metadata['app_fee_percent']) {
                        $app_fee_percent =  $invoice->lines->data[0]->plan->metadata['app_fee_percent'];
                    }else{
                        $app_fee_percent = 5;
                    }
                    if($invoice->lines->data[0]->plan->metadata['stripe_account_id'] == $stripe_account_id) {
                        array_push($pending_invoices, $invoice);
                        $earnings += (($invoice->amount_paid / 100) * ((100 - $app_fee_percent)/100));
                    }
                } catch(\Exception $e) {
                    \Log::error($e);
                }
            }
        }

        usort($pending_invoices, function($a, $b) {
            return $a['created'] <=> $b['created'];
        });

        return view('slide.slide-payout')->with('pending_invoices', $pending_invoices)->with('stripe_login_link', \Stripe\Account::createLoginLink($stripe_account_id)->url)->with('earnings', $earnings)->with('stripe_payout_delay', $stripe_payout_delay);
    }
}
