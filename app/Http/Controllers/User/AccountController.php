<?php

namespace App\Http\Controllers\User;

use App\Helpers\Queries\AccountTransactionQuery;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AccountDebit;
use App\Models\Account;
use App\Models\GenericAccountTransaction;
use App\Services\AccountService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function transactions(Request $request, AccountTransactionQuery $query)
    {
        $account = $request->user()->account;

        $items = $query
            ->addWhere(['account_id', '=', $account->id])
            ->get();

        return [
            'count' => $query->getRowsCount(),
            'items' => $items
        ];
    }

    public function debit(AccountDebit $request)
    {
        $account = Account::where('user_id', Auth::id())->first();
        $accountService = new AccountService($account);
        $accountService->createGenericTransaction(GenericAccountTransaction::TYPE_DEBIT, -$request->amount);

        return TRUE;
    }
}
