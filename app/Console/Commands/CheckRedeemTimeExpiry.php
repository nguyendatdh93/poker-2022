<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AffiliateCommission;
use App\Models\GenericAccountTransaction;
use App\Services\AccountService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class CheckRedeemTimeExpiry extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'commission:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expire pending affiliate commissions';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $format = 'Y-m-d H:i:s';
        // $timeZone = "Asia/Kolkata";
        $today = Carbon::now();
        $today = Carbon::createFromFormat($format, $today);
        // get pending commissions accounts and total
        $items = AffiliateCommission::select('account_id', 'amount', 'created_at', 'id')
            ->pending()
            ->with('account')
            ->where('amount','>',0)
            ->get()
            ->map
            ->setAppends([]);
        Log::info("Found pending commisions count" . count($items));
        // expire commissions and balance for each account
        $items->each(function ($item) use ($format, $today) {

            
            $createdDate = Carbon::createFromFormat($format, $item->created_at);
            $diff = $createdDate->diffInHours($today);
            if ($diff >= 48) {
                Log::info("Making this commision expire ", [$item]);
                $item->pending()->update(['status' => AffiliateCommission::STATUS_EXPIRED]);

                $accountService = new AccountService($item->account);
                $accountService->createGenericTransaction(
                    GenericAccountTransaction::TYPE_AFFILIATE_COMMISSION,
                    $item->amount
                );
            }
        });
    }
}
