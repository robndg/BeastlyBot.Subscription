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
        return DiscordOAuth::where('user_id', $this->user->id)->exists() ? DiscordOAuth::where('user_id', $this->user->id)->first()->discord_id : null;
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

    public function getGuilds() {
        if(Cache::has('guilds_' . $this->user->id)) {
            return Cache::get('guilds_' . $this->user->id);
        }

        $provider = $this->getDiscordProvider();
        $token = $this->getDiscordAccessToken();
        $guildsRequest = $provider->getAuthenticatedRequest('GET', $provider->getResourceOwnerDetailsUrl($token) . '/guilds', $token);

        $discord_client = new DiscordClient(['token' => env('DISCORD_BOT_TOKEN')]); // Token is required
        $guilds = $provider->getParsedResponse($guildsRequest);
        $guilds_array = array();
        
        foreach($guilds as $guild) {
            try {
                $discord_client->guild->getGuildRoles(['guild.id' => intval($guild['id'])]);
                array_push($guilds_array, $guild);
            } catch(\Exception $e) {
            }
        }

        Cache::put('guilds_' . $this->user->id, $guilds_array, 60 * 5);
        return Cache::get('guilds_' . $this->user->id);
    }

    public function getOwnedGuilds() {
        $guilds = array();
        foreach($this->getGuilds() as $guild) {
            if($guild['owner'] == 'true') {
                array_push($guilds, $guild);
            }
        }
        return $guilds;
    }

    public function isUserBanned(int $guild_id, int $user_id) {
        $discord_client = new DiscordClient(['token' => env('DISCORD_BOT_TOKEN')]); // Token is required
        foreach($discord_client->guild->getGuildBans(['guild.id' => $guild_id]) as $ban) {
            if($ban->user->id == $user_id) return true;
        }
        return false;
    }

    public function isMember(int $guild_id, int $user_id) {
        $discord_client = new DiscordClient(['token' => env('DISCORD_BOT_TOKEN')]); // Token is required
        foreach($discord_client->guild->listGuildMembers(['guild.id' => $guild_id]) as $member) {
            if($member->user->id == $user_id) return true;
        }
        return false;
    }


    public function getRoles(int $guild_id) {
        if(Cache::has('roles_' . $guild_id)) {
            return Cache::get('roles_' . $guild_id);
        }

        $discord_client = new DiscordClient(['token' => env('DISCORD_BOT_TOKEN')]); // Token is required
        Cache::put('roles_' . $guild_id, $discord_client->guild->getGuildRoles(['guild.id' => $guild_id]), 60 * $this->minutes_to_cache);
        return Cache::get('roles_' . $guild_id);
    }

    public function getRole(int $guild_id, int $role_id) {
        if(Cache::has('roles_' . $guild_id)) {
            foreach(Cache::get('roles_' . $guild_id) as $role) {
                if($role->id == $role_id) {
                    return $role;
                }
            }
        }

        $discord_client = new DiscordClient(['token' => env('DISCORD_BOT_TOKEN')]); // Token is required
        Cache::put('roles_' . $guild_id, $discord_client->guild->getGuildRoles(['guild.id' => $guild_id]), 60 * $this->minutes_to_cache);

        foreach(Cache::get('roles_' . $guild_id) as $role) {
            if($role->id == $role_id) {
                return $role;
            }
        }

        return null;
    }

    public function getGuild(int $guild_id) {
        if(Cache::has('guild_' . $guild_id)) {
            return Cache::get('guild_' . $guild_id);
        }

        $discord_client = new DiscordClient(['token' => env('DISCORD_BOT_TOKEN')]); // Token is required
        Cache::put('guild_' . $guild_id, $discord_client->guild->getGuild(['guild.id' => $guild_id]), 60 * $this->minutes_to_cache);
        return Cache::get('guild_' . $guild_id);
    }

    public function ownsGuild(int $guild_id): bool {
        $discord_o_auth = DiscordOAuth::where('user_id', $this->user->id)->first();
        return $this->getGuild($guild_id)->owner_id == $discord_o_auth->discord_id;
    }

    public function getUser(int $discord_id) {
        if(Cache::has('discord_user_' . $discord_id)) {
            return Cache::get('discord_user_' . $discord_id);
        }
        $discord_client = new DiscordClient(['token' => env('DISCORD_BOT_TOKEN')]); // Token is required
        Cache::put('discord_user_' . $discord_id, $discord_client->user->getUser(['user.id' => $discord_id], 60 * 5));
        return Cache::get('discord_user_' . $discord_id);
    }

    public function sendMessage($message) {
        $discord_client = new DiscordClient(['token' => env('DISCORD_BOT_TOKEN')]); // Token is required
        $channel = $discord_client->user->createDm(['recipient_id' => intval($this->getID())]);
        $discord_client->channel->createMessage(['channel.id' => $channel->id, 'content' => $message]);
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
            'clientId' => env('DISCORD_CLIENT_ID'),
            'clientSecret' => env('DISCORD_CLIENT_SECRET'),
            'redirectUri' => env('APP_URL') . '/discord_oauth',
        ]);
    }

}
