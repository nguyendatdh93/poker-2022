<?php

namespace App\Services;

use App\Models\Account;
use App\Models\AffiliateCommission;
use App\Models\PercentageAffiliateCommission;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use App\Cache\GameRoomCache;

class AffiliateService
{
    private $account; // referral user account

    /**
     * AffiliateService constructor.
     * @param Account $account - referral user account
     */
    public function __construct(Account $account)
    {
        $this->account = $account;
    }

    /**
     * Create affiliate commissions of given type for all tiers
     *
     * @param Model $commissionable
     * @param int $type
     */
    public function createCommissions(Model $commissionable, int $type, $roomId = null): void
    {
        // retrieve user from account
        $tierUser = $this->account->user;

        // loop through tiers
        for ($tier = 0; $tier <= 1; $tier++) {
            // if current tier user has referrer
            if ($tierUser->referrer) {
                // create commission for the given tier account
                $this->createCommission(
                    $commissionable, // game, user, deposit etc
                    $tierUser->referrer, // referrer user
                    $tier + 1,
                    $type,
                    $this->calculateCommissionAmount($commissionable, $type, $tier, $roomId) // amount for the given tier
                );

                $tierUser = $tierUser->referrer;
            } else {
                if ($roomId) {
                    $this->addRemainingToAdminAccount($tier, $roomId);
                } else {
                    break;
                }
            }
        }
    }

    /**
     * add commision into admins account if current user does not have any referrer users
     */
    private function addRemainingToAdminAccount($tier, $roomId)
    {
        $otherPlayersStake = GameRoomCache::getOtherPlayersStake($roomId);

        $totalStake = $otherPlayersStake[1];
        if ($tier == 0) {
            $totalStake = array_sum($otherPlayersStake);
        }
        AccountService::updateUserOrAdminAccount($totalStake);
    }

    /**
     * Create a new affiliate commission
     *
     * @param Model $commissionable
     * @param User $referrer
     * @param int $type
     * @param float $amount
     * @return AffiliateCommission|null
     */
    private function createCommission(Model $commissionable, User $referrer, int $tier, int $type, float $amount): ?AffiliateCommission
    {
        if (floatval($amount) == 0) {
            return NULL;
        }

        // check referrer login IP address is different from current IP (if affiliate commissions from the same IP are not allowed)
        // if (!config('settings.affiliate.allow_same_ip') && $referrer->last_login_from == request()->ip()) {
        //     Log::debug(sprintf('Ignore affiliate commission for referrer user ID %d due to the same IP address.', $referrer->id));
        //     return NULL;
        // }

        // create new affiliate commission
        $commission = new AffiliateCommission();
        $commission->account()->associate($referrer->account);
        $commission->referralAccount()->associate($this->account);
        $commission->tier = $tier;
        $commission->type = $type;
        $commission->status = AffiliateCommission::STATUS_PENDING;
        $commission->amount = $amount;
        $commission->ip = request()->ip();
        $commissionable->commission()->save($commission);

        return $commission;
    }

    /**
     * Get commissions amounts for the given commission type
     *
     * @param int $type
     * @return array
     */
    private function calculateCommissionAmount(Model $commissionable, int $type, int $tier, $roomId): float
    {
        if ($type == AffiliateCommission::TYPE_SIGN_UP) {
            $key = 'sign_up';
        } elseif ($type == AffiliateCommission::TYPE_GAME_LOSS) {
            $key = 'game_loss';
        } elseif ($type == AffiliateCommission::TYPE_GAME_WIN) {
            $key = 'game_win';
            $otherPlayersStake = GameRoomCache::getOtherPlayersStake($roomId);
            return $otherPlayersStake[$tier];
        } elseif ($type == AffiliateCommission::TYPE_DEPOSIT) {
            $key = 'deposit';
        }

        $commission = config('settings.affiliate.commissions.' . $key);
        $rates = $commission['rates'];

        return $commission['type'] == 'percentage' && $commissionable instanceof PercentageAffiliateCommission
            ? $commissionable->getAffiliateCommissionBaseAmount() * $rates[$tier] / 100
            : $rates[$tier];
    }
}
