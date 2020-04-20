<?php

namespace App;

use Exception;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    protected $primaryKey = 'discord_id';
    public $incrementing = false;

    public function getDiscordHelper() {
        return new DiscordHelper($this);
    }

    public function getStripeHelper() {
        return new StripeHelper($this);
    }

    public function getPlanExpiration() {
        try {
            $subscription = $this->getStripeHelper()->getActivePlan();
            if($subscription == null || $subscription->status != 'active') return null;
            return $subscription->current_period_end;
        }catch(Exception $e) {
            return null;
        }
        return null;
    }

    public function canAcceptPayments() {
        return $this->getStripeHelper()->isExpressUser() && $this->getStripeHelper()->hasActivePlan();
    }

}
