<?php

namespace App\Http\Controllers\User;

use App\Helpers\Queries\AffiliateCommissionQuery;
use App\Http\Controllers\Controller;
use App\Models\AffiliateCommission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\GenericAccountTransaction;
use App\Services\AccountService;

class AffiliateController extends Controller
{
    public function commissions(Request $request, AffiliateCommissionQuery $query)
    {
        $account = $request->user()->account;

        $items = $query->addWhere(['account_id', '=', $account->id])
            ->get()
            ->makeHidden(['referral_account_id', 'commissionable_id', 'commissionable_type', 'ip']);

        return [
            'count' => $query->getRowsCount(),
            'items' => $items
        ];
    }

    public function stats(Request $request)
    {
        $registrations = User::selectRaw('COUNT(DISTINCT tier1.id) AS tier1_count, 
            COUNT(DISTINCT tier2.id) AS tier2_count, 
            COUNT(DISTINCT tier3.id) AS tier3_count')
            ->where('users.id', $request->user()->id)
            ->leftJoin('users AS tier1', 'tier1.referrer_id', '=', 'users.id')
            ->leftJoin('users AS tier2', 'tier2.referrer_id', '=', 'tier1.id')
            ->leftJoin('users AS tier3', 'tier3.referrer_id', '=', 'tier2.id')
            ->groupBy('users.id')
            ->get()
            ->map
            ->setAppends([])
            ->first();

        $commissionsByType = AffiliateCommission::select('type', DB::raw('SUM(amount) AS commissions_total'))
            ->where('account_id', $request->user()->account->id)
            ->groupBy('type')
            ->orderBy('type')
            ->get()
            ->map
            ->setAppends(['title']);

        $commissionsByTier = AffiliateCommission::select('tier', DB::raw('SUM(amount) AS commissions_total'))
            ->where('account_id', $request->user()->account->id)
            ->groupBy('tier')
            ->orderBy('tier')
            ->get()
            ->map
            ->setAppends([])
            ->keyBy('tier');

        return response()->json([
            'registrations' => $registrations,
            'commissions_by_type' => $commissionsByType,
            'commissions_by_tier' => $commissionsByTier
        ]);
    }
    public function redeem(AffiliateCommission $commission)
    {
        if ($commission->is_redeemed) {
            return response()->json([
                'success' => FALSE,
                'message' => __('This affiliate commission is already redeemed.')
            ]);
        }

        $commission->update(['status' => AffiliateCommission::STATUS_REDEEMED]);
        $accountService = new AccountService($commission->account);
        $accountService->createGenericTransaction(GenericAccountTransaction::TYPE_AFFILIATE_COMMISSION, $commission->amount);

        return response()->json([
            'success' => TRUE,
            'message' => __('Commission is successfully approved.')
        ]);
    }
    public function tree(Request $request)
    {
        //  (SQL: select `users`.`id` as `id`, `users`.`name` as `name`, `tier1`.`id` as `tier1_id`, `tier1`.`name` as `tier1_name`, `tier2`.`id` as `tier2_id`, `tier2`.`name` as `tier2_name`, `tier3`.`id` as `tier3_id`, `tier3`.`name` as `tier3_name` from `users` inner join `users` as `tier1` on `tier1`.`referrfrer_id` = `users`.`id` left join `users` as `tier2` on `tier2`.`referrer_id` = `tier1`.`id` left join `users` as `tier3` on `tier3`.`referrer_id` = `tier2`.`id` where `users`.`referrer_id` is null order by `users`.`id` asc);
        $userId = $request->userId;
        $registrations = User::select(
            'users.id AS id',
            'users.name AS name',
            'tier1.id AS tier1_id',
            'tier1.name AS tier1_name',
            'tier2.id AS tier2_id',
            'tier2.name AS tier2_name',
            'tier3.id AS tier3_id',
            'tier3.name AS tier3_name'
            )
            ->where('users.id',$userId)
            ->join('users AS tier1', 'tier1.referrer_id', '=', 'users.id')
            ->leftJoin('users AS tier2', 'tier2.referrer_id', '=', 'tier1.id')
            ->leftJoin('users AS tier3', 'tier3.referrer_id', '=', 'tier2.id')
            ->orderBy('users.id')
            ->get()
            ->map
            ->setAppends([])
            ->reduce(function ($carry, $item) {
                if (!array_key_exists($item['id'], $carry)) {
                    $carry[$item['id']] = ['id' => $item['id'], 'name' => $item['name'], 'children' => []];
                }

                if ($item['tier1_id'] && !array_key_exists($item['tier1_id'], $carry[$item['id']]['children'])) {
                    $carry[$item['id']]['children'][$item['tier1_id']] = ['id' => $item['tier1_id'], 'name' => $item['tier1_name'], 'children' => []];
                }

                if ($item['tier2_id'] && !array_key_exists($item['tier2_id'], $carry[$item['id']]['children'][$item['tier1_id']]['children'])) {
                    $carry[$item['id']]['children'][$item['tier1_id']]['children'][$item['tier2_id']] = ['id' => $item['tier2_id'], 'name' => $item['tier2_name'], 'children' => []];
                }

                if ($item['tier3_id'] && !array_key_exists($item['tier3_id'], $carry[$item['id']]['children'][$item['tier1_id']]['children'][$item['tier2_id']]['children'])) {
                    $carry[$item['id']]['children'][$item['tier1_id']]['children'][$item['tier2_id']]['children'][$item['tier3_id']] = ['id' => $item['tier3_id'], 'name' => $item['tier3_name']];
                }

                return $carry;
            }, []);

        $removeKeys = function (&$array) use (&$removeKeys) {
            array_walk($array, function (&$item) use ($removeKeys) {
                if (isset($item['children'])) {
                    $removeKeys($item['children']);
                    $item['children'] = array_values($item['children']);
                }
            });
        };

        $removeKeys($registrations);

        return array_values($registrations);
    }
}
