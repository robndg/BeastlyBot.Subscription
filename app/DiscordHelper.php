<?php

namespace App;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use League\OAuth2\Client\Token\AccessToken;
use Wohali\OAuth2\Client\Provider\Discord;
use RestCord\DiscordClient;

class DiscordHelper
{

    private $minutes_to_cache = 10;
    private $user;

    public function __construct(User $user){
        $this->user = $user;
    }

    public function cache(): void {
        $data = $this->getDiscordData();
        $username = $data['username'] . ' #' . $data['discriminator'];

        if(!empty($this->getDiscordData()['avatar'])) {
            $avatar_url = "https://cdn.discordapp.com/avatars/" . $this->user->DiscordOAuth->discord_id . "/" . $this->getDiscordData()['avatar'] . ".png";
        } else {
            $avatar_url = 'https://i.imgur.com/qbVxZbJ.png';
        }

        Cache::put('discord_username_' . $this->user->DiscordOAuth->discord_id, $username, 60 * $this->minutes_to_cache);
        Cache::put('discord_email_' . $this->user->DiscordOAuth->discord_id, $data['email'], 60 * $this->minutes_to_cache);
        Cache::put('discord_avatar_' . $this->user->DiscordOAuth->discord_id, $avatar_url, 60 * $this->minutes_to_cache);
    }

    public function getID(): string {
        return DiscordOAuth::where('user_id', auth()->user()->id)->exists() ? DiscordOAuth::where('user_id', auth()->user()->id)->first()->discord_id : null;
    }

    public function getAvatar(): string {
        if(!Cache::has('discord_avatar_' . $this->user->DiscordOAuth->discord_id)) {
            $this->cache();
        }
        return Cache::get('discord_avatar_' . $this->user->DiscordOAuth->discord_id, 'https://i.imgur.com/qbVxZbJ.png');
    }

    public function getUsername(): string {
        if(!Cache::has('discord_username_' . $this->user->DiscordOAuth->discord_id)) {
            $this->cache();
        }
        return Cache::get('discord_username_' . $this->user->DiscordOAuth->discord_id);
    }

    public function getEmail(): string {
        if(!Cache::has('discord_email_' . $this->user->DiscordOAuth->discord_id)) {
            $this->cache();
        }
        return Cache::get('discord_email_' . $this->user->DiscordOAuth->discord_id);
    }

    // TODO: Cache
    public function getGuilds() {
        $provider = $this->getDiscordProvider();
        $token = $this->getDiscordAccessToken();
        $guildsRequest = $provider->getAuthenticatedRequest('GET', $provider->getResourceOwnerDetailsUrl($token) . '/guilds', $token);

        $guilds = $provider->getParsedResponse($guildsRequest);
        return $guilds;
    }

    // TODO: Cache
    public function getOwnedGuilds() {
        $guilds = array();
        foreach($this->getGuilds() as $guild) {
            if($guild['owner'] == 'true') {
                array_push($guilds, $guild);
            }
        }
        return $guilds;
    }

    public function getRoles(int $guild_id) {
        if(Cache::has('roles_' . $guild_id)) {
            return Cache::get('roles_' . $guild_id);
        }

        $discord_client = new DiscordClient(['token' => env('DISCORD_BOT_TOKEN')]); // Token is required
        Cache::put('roles_' . $guild_id, $discord_client->guild->getGuildRoles(['guild.id' => $guild_id]), 60 * $this->minutes_to_cache);
        return Cache::get('roles_' . $guild_id);
    }

    public function getGuild(int $guild_id) {
        if(Cache::has('guild_' . $guild_id)) {
            return Cache::get('guild_' . $guild_id);
        }

        $discord_client = new DiscordClient(['token' => env('DISCORD_BOT_TOKEN')]); // Token is required
        Cache::put('guild_' . $guild_id, $discord_client->guild->getGuild(['guild.id' => $guild_id]), 60 * $this->minutes_to_cache);
        return Cache::get('guild_' . $guild_id);
    }

    public function ownsGuild($guild_id): bool {
        if(DiscordStore::where('guild_id', $guild_id)->exists()) {
            $store = DiscordStore::where('guild_id', $guild_id)->first();
            if($store->user_id == auth()->user()->id) return true;
        }
        
        return false;
    }

    private function getDiscordData() {
        $provider = $this->getDiscordProvider();
        $authToken = $this->getDiscordAccessToken();
        $req = $provider->getAuthenticatedRequest('GET', $provider->getResourceOwnerDetailsUrl(new AccessToken(['access_token' => $authToken])), $authToken);
        return $provider->getParsedResponse($req);
    }

      // TODO: Have to get new token or whatver it's not working properly so we are getting 401 Unauthent
    private function getDiscordAccessToken(): AccessToken {
        $token = new AccessToken(['access_token' => $this->user->DiscordOAuth->access_token, 'refresh_token' => $this->user->DiscordOAuth->refresh_token, 'expires_in' => $this->user->DiscordOAuth->token_expiration]);
        return $token;
    }

    private function getDiscordProvider(): Discord {
        return new Discord([
            'clientId' => SiteConfig::get('DISCORD_CLIENT_ID'),
            'clientSecret' => SiteConfig::get('DISCORD_SECRET'),
            'redirectUri' => SiteConfig::get('APP_URL') . SiteConfig::get('DISCORD_OAUTH_REDIRECT_URL'),
        ]);
    }

}
