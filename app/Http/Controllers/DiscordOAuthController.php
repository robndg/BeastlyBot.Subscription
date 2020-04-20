<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Wohali\OAuth2\Client\Provider\Discord;

class DiscordOAuthController extends Controller {

    public function __construct() {
        $this->middleware('guest')->except('logout');
    }

    public function connect() {

        if (!\request()->exists('code')) {
            // AlertHelper::alertDanger('Failed to connect Discord account!');
            return redirect('/');
        }

        $provider = self::getProvider();

        try {
            // generate an access token with the oauth2 code provided by Discord
            $token = $provider->getAccessToken('authorization_code', [
                'code' => \request('code'),
            ]);

            // grab the discord user from the access token generated above
            $discord_user = $provider->getResourceOwner($token);

            $user = null;

            // Check if a user already exists in our DB with the discord user.id of the logged in oauth2 user
            if (User::where('discord_id', $discord_user->getId())->exists()) {
                $user = User::where('discord_id', $discord_user->getId())->get()[0];
                auth()->login($user);
            }

            // if the user does not exists we need to create the user and log them in
            if ($user === null) {
                $user = new User();
                $user->discord_id = $discord_user->getId();
                $user->discord_access_token = $token->getToken();
                $user->discord_refresh_token = $token->getRefreshToken();
                $user->discord_token_expiration = $token->getExpires();
                $user->save();
                auth()->login($user);
            } else {
                // if the user does exist we just update their tokens
                $user->discord_access_token = $token->getToken();
                $user->discord_refresh_token = $token->getRefreshToken();
                $user->discord_token_expiration = $token->getExpires();
                $user->save();
            }

            // if the authenticated user does not have a strip account we need to create one for them
            if (auth()->user()->stripe_customer_id === null) {
                // Any time accessing Stripe API this snippet of code must be ran above any preceding API calls
                \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

                $customers_with_email = \Stripe\Customer::all(['limit' => 1, 'email' => $discord_user->getEmail()]);
                $stripe_account = null;

                foreach($customers_with_email as $cus) {
                    if($cus->email === $discord_user->getEmail()) {
                        $stripe_account = $cus;
                        break;
                    }
                }

                if($stripe_account == null) {
                    $stripe_account = \Stripe\Customer::create([
                        "email" => $discord_user->getEmail(),
                        "metadata" => ['discord_id' => $discord_user->getId()]
                    ]);
                } else {
                    \Stripe\Customer::update($stripe_account->id, ['metadata' => ['discord_id' => $discord_user->getId()]]);
                }

                $user->stripe_customer_id = $stripe_account->id;
                $user->save();
            }
        } catch (IdentityProviderException $e) {
            if (env('APP_DEBUG')) Log::error($e);
        }

        return redirect()->intended('/dashboard');
    }

    /**
     * This method is used to get the Discord Provider to access the Discord API
     * @return Discord
     */
    public static function getProvider() {
        return new Discord([
            'clientId' => env('DISCORD_CLIENT_ID'),
            'clientSecret' => env('DISCORD_SECRET'),
            'redirectUri' => env('DISCORD_AUTH_REDIRECT'),
        ]);
    }

}
