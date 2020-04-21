<?php

namespace App\Http\Controllers;

use App\AlertHelper;
use App\StripeHelper;
use Illuminate\Support\Facades\Log;

class StripeConnectController extends Controller
{

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function connect()
    {
        $code = \request('code');

        // if there is an error connecting to Stripe, abort and let user know
        if (isset($_GET['error'])) {
            if (env('APP_DEBUG')) Log::error($_GET['error']);
            AlertHelper::alertError('Something went wrong! Open a support ticket.');
            return redirect('/dashboard');
        }

        if ($code == null) return;

        $user = auth()->user();

        $stripe_account = StripeHelper::getAccountFromStripeConnect($code);

        if ($stripe_account->country == 'US' && $user->stripe_express_id == null) {
            $user->stripe_express_id = $stripe_account->id;
            $user->save();
            AlertHelper::alertSuccess('Stripe account created! You can now accept payments.');
            \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
            // Set payout schedule to 7 days automatically by default
            \Stripe\Account::update(
                $stripe_account->id,
                [
                    'settings' =>
                    [
                        'payouts' =>
                        [
                            'schedule' =>
                            [
                                'delay_days' => env('STRIPE_PAYOUT_DELAY_DAYS'),
                                'interval' => 'daily'
                            ]
                        ]
                    ]
                ]
            );
            return redirect('/dashboard#open-servers=true');
        } else {
            AlertHelper::alertError('This is not a US account or you have already connected an account.');
            return redirect('/dashboard');
        }
    }
}
