<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Rules\ReCaptchaValidationPassed;
use App\Services\UserService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Carbon;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * The user has been registered.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function registered(Request $request, User $user)
    {
        return UserService::user($user);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $rules = [
            'name'      => 'required|min:3|max:100|unique:users',
            'email'     => 'required|email|max:255|unique:users',
            'password'  => 'required|min:6|confirmed',
            'password_confirmation' => 'required',
            'referral_code' => 'required|exists:users,referral_code',
        ];

        if (config('services.recaptcha.secret_key')) {
            $rules['recaptcha'] = ['required', new ReCaptchaValidationPassed()];
        }

        return Validator::make($data, $rules);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        // $referrerId = Cookie::has('ref') ? intval(Cookie::get('ref')) : NULL;
        $referral_code = $data['referral_code'];
        $referrer = User::where('referral_code', $referral_code)->active()->first();
        // get the referrer user
        // dd($this->remainingUserForCurrentCycle($referrer));
        $canUserRegister = $this->remainingUserForCurrentCycle($referrer);
        if ($canUserRegister['success']==FALSE) {
            throw new \Exception('We are not accepting any new user from this refferal code');
        }
        Log::info(sprintf('Referrer ID %d, user %s', $referrer->id, ($referrer ? 'exists' : 'does NOT exist')));

        // check that it's found and login IP address is different from current IP (if referrals registrations from the same IP are not allowed)
        if ((!config('settings.affiliate.allow_same_ip') && $referrer->last_login_from == request()->ip())) {
            $referrerId = NULL;
        }


        return UserService::create([
            'referrer_id'   => $referrer->id,
            'name'          => $data['name'],
            'email'         => $data['email'],
            'password'      => $data['password']
        ]);
    }
    public function remainingUserForCurrentCycle($user)
    {
        // print_r($user);
        $format = 'Y-m-d H:i:s';
        // $timeZone = "Asia/Kolkata";
        $allReferredUsers = User::where('referrer_id', $user->id)->count();
        $now = Carbon::now();
        $createdAt = Carbon::createFromFormat($format, $user->created_at);
        $diff = $createdAt->diffInMonths($now);
        if ($diff < 2 && 10 - $allReferredUsers >= 0) {
            return  [
                'availableReferral' => 10 - $allReferredUsers,
                'success' => TRUE
            ];
        }
        if ($diff > 2 && $diff < 6 && 20 - $allReferredUsers >= 0) {
            return  [
                'availableReferral' => 20 - $allReferredUsers,
                'success' => TRUE
            ];
        }
        if ($diff >= 6 && 30 - $allReferredUsers >= 0) {
            return  [
                'availableReferral' => 30 - $allReferredUsers,
                'success' => TRUE
            ];
        } else {
            return  [
                'error' => "Reached Referral Code user limit",
                'success' => false
            ];
        }
    }
}
