<?php

namespace Packages\Payments\Http\Controllers\Admin;

use App\Http\Controllers\Admin\DashboardController as ParentDashboardController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Packages\Payments\Models\Deposit;
use Packages\Payments\Models\Withdrawal;

class DashboardController extends ParentDashboardController
{
    protected function getAccountingProfit(Request $request): Collection
    {
        return collect([
            'accounts_total' => (float) User::regular()
                ->join('accounts', 'users.id', '=', 'accounts.user_id')
                ->sum('balance'),

            'deposits_total' => (float) User::regular()
                ->join('accounts', 'users.id', '=', 'accounts.user_id')
                ->join('deposits', function ($query) {
                    $query->on('accounts.id', '=', 'deposits.account_id');
                    $query->where('deposits.status', '=', Deposit::STATUS_COMPLETED);
                })
                ->sum('deposits.amount'),

            'withdrawals_total' => (float) User::regular()
                ->join('accounts', 'users.id', '=', 'accounts.user_id')
                ->join('withdrawals', function ($query) {
                    $query->on('accounts.id', '=', 'withdrawals.account_id');
                    $query->where('withdrawals.status', '!=', Withdrawal::STATUS_CANCELLED);
                })
                ->sum('withdrawals.amount')
        ]);
    }

    protected function getAccountingBalance(Request $request): Collection
    {
        $getData = function () use ($request) {
            return collect([
                (float) Deposit::completed()->period($request->query('period'))->sum('amount'),
                (float) Withdrawal::completed()->period($request->query('period'))->sum('amount')
            ]);
        };

        return empty($request->query()) // cache requests without filters
            ? Cache::remember('admin.dashboard.accounting-balance', $this->cacheTtl, $getData)
            : $getData();
    }

    protected function getAccountingDepositsComparison(Request $request): Collection
    {
        $getData = function ($period) {
            return (float) Deposit::completed()
                ->period($period)
                ->sum('amount');
        };

        return $this->getComparison('accounting-deposits', $getData);
    }

    protected function getAccountingDepositsHistory(Request $request): Collection
    {
        return Cache::remember('admin.dashboard.accounting-deposits-history', $this->cacheTtl, function () {
            $data = Deposit::select(
                    DB::raw('SUM(amount) AS amount'),
                    DB::raw('WEEK(created_at, 1) AS week_number')
                )
                ->completed()
                ->where('created_at', '>=', Carbon::now()->subWeeks(7)->startOfWeek())
                ->groupBy('week_number')
                ->orderBy('week_number', 'asc')
                ->get()
                ->keyBy('week_number')
                ->map
                ->amount;

            $dataByWeek = collect();
            for ($i = 7; $i >= 0; $i--) {
                $weekNumber = Carbon::now()->subWeeks($i)->weekOfYear;
                $dataByWeek->put($weekNumber, $data->has($weekNumber) ? $data->get($weekNumber) : 0);
            }

            return $dataByWeek->values();
        });
    }

    protected function getAccountingWithdrawalsHistory(Request $request): Collection
    {
        return Cache::remember('admin.dashboard.accounting-withdrawals-history', $this->cacheTtl, function () {
            $data = Withdrawal::select(
                    DB::raw('SUM(amount) AS amount'),
                    DB::raw('WEEK(created_at, 1) AS week_number')
                )
                ->completed()
                ->where('created_at', '>=', Carbon::now()->subWeeks(7)->startOfWeek())
                ->groupBy('week_number')
                ->orderBy('week_number', 'asc')
                ->get()
                ->keyBy('week_number')
                ->map
                ->amount;

            $dataByWeek = collect();
            for ($i = 7; $i >= 0; $i--) {
                $weekNumber = Carbon::now()->subWeeks($i)->weekOfYear;
                $dataByWeek->put($weekNumber, $data->has($weekNumber) ? $data->get($weekNumber) : 0);
            }

            return $dataByWeek->values();
        });
    }

    protected function getAccountingDepositsByStatus(Request $request): Collection
    {
        $getData = function () use ($request) {
            return Deposit::select(
                    'status',
                    DB::raw('COUNT(id) AS count'),
                    DB::raw('MIN(amount) AS amount_min'),
                    DB::raw('MAX(amount) AS amount_max'),
                    DB::raw('AVG(amount) AS amount_avg'),
                    DB::raw('SUM(amount) AS amount')
                )
                ->period($request->query('period'))
                ->groupBy('status')
                ->orderBy('status')
                ->get();
        };

        return empty($request->query()) // cache requests without filters
            ? Cache::remember('admin.dashboard.accounting-deposits-by-status', $this->cacheTtl, $getData)
            : $getData();
    }

    protected function getAccountingWithdrawalsByStatus(Request $request): Collection
    {
        $getData = function () use ($request) {
            return Withdrawal::select(
                    'status',
                    DB::raw('COUNT(id) AS count'),
                    DB::raw('MIN(amount) AS amount_min'),
                    DB::raw('MAX(amount) AS amount_max'),
                    DB::raw('AVG(amount) AS amount_avg'),
                    DB::raw('SUM(amount) AS amount')
                )
                ->period($request->query('period'))
                ->groupBy('status')
                ->orderBy('status')
                ->get();
        };

        return empty($request->query()) // cache requests without filters
            ? Cache::remember('admin.dashboard.accounting-withdrawals-by-status', $this->cacheTtl, $getData)
            : $getData();
    }
}
