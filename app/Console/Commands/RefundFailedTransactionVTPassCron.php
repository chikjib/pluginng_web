<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Subcategory;
use App\Models\User;
use App\Traits\IntegrationsTrait;
use App\Traits\ReferenceTrait;
use App\Traits\WalletTrait;
use Illuminate\Console\Command;


class RefundFailedTransactionVTPassCron extends Command
{
    use IntegrationsTrait;
    use WalletTrait;
    use ReferenceTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'refundfailedtransactionvtpass:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to refund all failed vtpass transaction';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $pendingOrders = Order::where('status', 0)->where('ref','LIKE',"%VPA%")->take(10)->get();
        \Log::info("VtPass Order Presentation");
        \Log::info($pendingOrders);
       
        
        if(!is_null($pendingOrders)){
            foreach ($pendingOrders as $orders) {
                $refund = $this->VtPassRequery($orders->ref);
                \Log::info('VtPass Order Checked if it was VtPass');
                \Log::info($refund);
            
            $subcategory = Subcategory::where('title', 'Reversal')->first();
            if (!is_null($refund)){
                if($orders->subcategory_id == 11 || $orders->subcategory_id == 12 || $orders->subcategory_id == 13){
                    if ($refund['code'] == "016") {
                
                        $updateOrder = Order::where('ref', $orders->ref)->first();
                        $updateOrder->status = 4;
                        $updateOrder->description = $updateOrder->description . " for Ref: " . $orders->ref;
                        $updateOrder->save();
                        
                        //016 transaction failed;
                        //$this->isCredited($orders->subtotal);
                        $userWallet = User::lockForUpdate()->find($orders->user_id);
                        $prev = $userWallet->wallet;
                        $bal = $userWallet->wallet + $orders->subtotal;
        
        
                        $userWallet->wallet  = $userWallet->wallet + $orders->subtotal;
                        $userWallet->save();
        
                        $order = new Order();
                        $order->ref = $this->referenceCode();
                        $order->user_id =  $orders->user_id;
                        $order->subcategory_id =  $subcategory->id;
                        $order->plan =  $subcategory->title . " " . $orders->plan;
                        $order->amount =  $orders->subtotal;
                        $order->quantity =  1;
                        $order->subtotal =  $orders->subtotal;
                        $order->total = $orders->subtotal;
                        $order->bal = $bal;
                        $order->prev_bal = $prev;
                        $order->description = $subcategory->title . " " . $orders->plan . " Reversal";
                        $order->status = 2;
                        $order->save();
                
                    }
                }
            
            }
        }
    }
    }
}
