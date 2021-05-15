<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UserService
{
    /**
     * Create a new user
     *
     * @param $properties
     * @return User
     */
    public static function create($properties)
    {
        Log::info(sprintf('New user "%s" with email "%s"', $properties['name'], $properties['email']));
        $referral_code = Str::random(10);
        $user = new User();
        $user->referrer_id          = $properties['referrer_id'] ?? NULL;
        $user->name                 = $properties['name'];
        $user->email                = $properties['email'];
        $user->role                 = $properties['role'] ?? User::ROLE_USER;
        $user->password             = Hash::make($properties['password']);
        $user->status               = User::STATUS_ACTIVE;
        $user->email_verified_at    = $properties['email_verified_at'] ?? NULL;
        $user->last_login_at        = $properties['last_login_at'] ?? Carbon::now();
        $user->last_login_from      = $properties['last_login_from'] ?? request()->ip();
        $user->referral_code      = $referral_code;
        $user->save();

        return $user;
    }

    /**
     * Get user with necessary related info
     *
     * @param User $user
     * @return User
     */
    public static function user(User $user): User
    {
        return $user
            ->append('two_factor_auth_enabled', 'two_factor_auth_passed', 'is_admin')
            ->loadMissing('account', 'profiles');
    }
}
