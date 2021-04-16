<?php

namespace App\Http\Controllers;

use App\AlertHelper;

use App\StripeHelper;
use App\Processors;
use App\ProcessorsController;
use App\DiscordOAuth;
use Illuminate\Support\Facades\Log;

class StripeConnectController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
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

        Log::info("Stripe Account ID");
        Log::info($stripe_account->id);

        if (!Processors::where('user_id', $user->id)->where('type', 1)->exists()) {
    
            $processorConnect = new Processors(['user_id' => $user->id, 'store_id' => null, 'type' => 1, 'processor_id' => $stripe_account->id, 'cur'=> 'USD', 'enabled' => 1]);
            $processorConnect->save();

            // Set all stores to processor_id
            $discord_o_auth = DiscordOAuth::where('user_id', $user->id)->first();
            $user_discord_stores = DiscordStore::where('user_id', $discord_o_auth->discord_id)->update(['processor_id' => $processorConnect->id]);
            $user_discord_stores = DiscordStore::where('user_id', $discord_o_auth->discord_id)->update(['live' => 1]); // TODOv2: add toggle
           // $user_discord_stores->save();

            /*$user->StripeConnect->express_id = $stripe_account->id;
            $user->StripeConnect->save();*/
            AlertHelper::alertSuccess('Stripe account created! You can now accept payments.');
            return redirect('/dashboard');
        } else {
            // Set all stores to processor_id
            $discord_o_auth = DiscordOAuth::where('user_id', $user->id)->first();
           
            $processorConnect_id = Processors::where('user_id', $user->id)->where('type', 1)->first()->id; 
            // TODOv3: make stores allow diff processors, ie. paypal 
            $user_discord_stores = DiscordStore::where('user_id', $discord_o_auth->discord_id)->update(['processor_id' => $processorConnect_id]);
            $user_discord_stores = DiscordStore::where('user_id', $discord_o_auth->discord_id)->update(['live' => 1]); // TODOv2: add toggle
            AlertHelper::alertInfo('You have already linked a Stripe account.');
            return redirect('/dashboard');
        }
    }
}
