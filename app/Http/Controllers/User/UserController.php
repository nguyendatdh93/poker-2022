<?php

namespace App\Http\Controllers\User;

use App\Http\Requests\GetUser;
use App\Http\Requests\UpdateUser;
use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;

class UserController extends Controller
{
    public function show(Request $request)
    {
        return $request
            ->user()
            ->append('two_factor_auth_enabled', 'two_factor_auth_passed', 'is_admin')
            ->loadMissing('account', 'profiles');
    }

    /**
     * Update the user's profile information.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUser $request)
    {
        $user = $request->user();
        $variables = collect($request->only('name', 'hide_profit'));

        // user can change the email only if it's not verified
        if (!$user->hasVerifiedEmail()) {
            $variables->put('email', $request->email);
        }

        if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
            // delete previous avatar if it's set
            if ($user->avatar) {
                Storage::disk('public')->delete('avatars/' . $user->avatar);
            }

            $fileName = $user->id . '_' . time() . '.' . $request->avatar->extension();
            // save uploaded logo in storage
            $request->avatar->storeAs('avatars', $fileName, 'public');
            $variables->put('avatar', $fileName);
        }

        return tap($user)->update($variables->toArray())->loadMissing('account');
    }

    /**
     * Public user profile
     *
     * @param GetUser $request
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function profile(GetUser $request, User $user)
    {
        $isAdminOrCurrentUser = $request->user()->is_admin || $request->user()->id == $user->id;

        $getUserStats = function () use ($user, $isAdminOrCurrentUser) {
            return $user
                ->account
                ->games()
                ->selectRaw('COUNT(*) AS bet_count')
                ->selectRaw('IFNULL(SUM(IF(win > bet,1,0)),0) AS win_count')
                ->selectRaw('IFNULL(SUM(bet),0) AS bet_total')
                ->when(!$user->hide_profit || $isAdminOrCurrentUser, function ($query) {
                    $query->selectRaw('IFNULL(SUM(win-bet),0) AS profit_total')
                        ->selectRaw('IFNULL(MAX(win-bet),0) AS profit_max');
                })
                ->get()
                ->map
                ->makeHidden(['title', 'profit', 'is_completed', 'created'])
                ->first();
        };

        $stats = $isAdminOrCurrentUser
            ? $getUserStats()
            : Cache::remember('user.' . $user->id . '.profile', 15 * 60, $getUserStats);

        return response()->json([
            'user' => $user->only('id', 'name', 'avatar_url', 'created_ago'),
            'stats' => $stats
        ]);
    }
    public function newUserJoiningAvailability(Request $request)
    {
        $format = 'Y-m-d H:i:s';

        // $timeZone = "Asia/Kolkata";
        $now = Carbon::now();
        $user = User::find($request->userId)->toArray();
        // dd($user['created_at']);
        $createdAt = Carbon::createFromFormat($format, $user['created_at']);
        $afterTwoMonth = Carbon::createFromFormat($format, $user['created_at'])->addMonths(2);
        $afterSixMonth = Carbon::createFromFormat($format, $user['created_at'])->addMonths(6);
        return  response()->json([
            'twoMonth' => $afterTwoMonth,
            'sixMonth' => $afterSixMonth,
            'success' => TRUE
        ]);
    }

    public function remainingUserForCurrentCycle(User $user)
    {
        $format = 'Y-m-d H:i:s';
        // $timeZone = "Asia/Kolkata";
        $allReferredUsers = User::where('referrer_id', $user->id)->count();
        $now = Carbon::now();
        $createdAt = Carbon::createFromFormat($format, $this->created_at);
        $diff = $createdAt->diffInMonths($now);
        if ($diff < 2) {
            return  response()->json([
                'availableReferral' => 10 - $allReferredUsers,
                'success' => TRUE
            ]);
        }
        if ($diff > 2 && $diff < 6) {
            return  response()->json([
                'availableReferral' => 20 - $allReferredUsers,
                'success' => TRUE
            ]);
        }
        if ($diff >= 6 && 30 - $allReferredUsers <= 0) {
            return  response()->json([
                'availableReferral' => 30 - $allReferredUsers,
                'success' => TRUE
            ]);
        } else {
            return  response()->json([
                'error' => "Reached Referral Code user limit",
                'success' => false
            ]);
        }
    }
}
