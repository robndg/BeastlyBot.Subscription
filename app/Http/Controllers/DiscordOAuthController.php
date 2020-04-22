<?php

namespace App\Http\Controllers;

use App\BeastlyConfig;
use App\DiscordOAuth;
use App\StripeConnect;
use App\User;
use Illuminate\Support\Facades\Log;
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
        $user = null;
        try {
            // generate an access token with the oauth2 code provided by Discord
            $token = $provider->getAccessToken('authorization_code', [
                'code' => \request('code'),
            ]);
            // grab the discord user from the access token generated above
            $discord_user = $provider->getResourceOwner($token);


            // Check if a user already exists in our DB with the discord user.id of the logged in oauth2 user
            if (DiscordOAuth::where('discord_id', $discord_user->getId())->exists()) {
                $user = DiscordOAuth::where('discord_id', $discord_user->getId())->first()->user;
                auth()->login($user);
            }
            // if the user does not exists we need to create the user and log them in
            if ($user === null) {
                $user = new User();
                $user->save();
                $oauth = new DiscordOAuth
                ([
                    'user_id' => $user->id,
                    'discord_id' => $discord_user->getId(), 
                    'access_token' => $token->getToken(), 
                    'refresh_token' => $token->getRefreshToken(), 
                    'token_expiration' => $token->getExpires()
                ]);
                $user->DiscordOAuth()->save($oauth);
                auth()->login($user);
            } else {
                // if the user does exist we just update their tokens
                $user->DiscordOAuth->access_token = $token->getToken();
                $user->DiscordOAuth->refresh_token = $token->getRefreshToken();
                $user->DiscordOAuth->token_expiration = $token->getExpires();
                $user->DiscordOAuth->save();
            }
            // if the authenticated user does not have a strip account we need to create one for them
            if (! StripeConnect::where('user_id', $user->id)->exists()) {
                // Any time accessing Stripe API this snippet of code must be ran above any preceding API calls
                \Stripe\Stripe::setApiKey(BeastlyConfig::get('STRIPE_SECRET'));

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

                $connect = new StripeConnect(['user_id' => $user->id, 'customer_id' => $stripe_account->id]);
                $user->StripeConnect()->save($connect);
            }
        } catch (IdentityProviderException $e) {
            if (env('APP_DEBUG')) Log::error($e);
            if($user != null) $user->delete();
        }

        return redirect()->intended('/dashboard');
    }

    /**
     * This method is used to get the Discord Provider to access the Discord API
     * @return Discord
     */
    public static function getProvider() {
        return new Discord([
            'clientId' => BeastlyConfig::get('DISCORD_CLIENT_ID'),
            'clientSecret' => BeastlyConfig::get('DISCORD_SECRET'),
            'redirectUri' => BeastlyConfig::get('APP_URL') . BeastlyConfig::get('DISCORD_OAUTH_REDIRECT_URL'),
        ]);
    }

}
