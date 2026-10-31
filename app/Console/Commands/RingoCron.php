<?php

namespace App\Console\Commands;
use App\Models\Order;
use App\Models\Subcategory;
use App\Models\User;
use Illuminate\Console\Command;
use App\Traits\IntegrationsTrait;
use App\Traits\ReferenceTrait;
use App\Traits\WalletTrait;

class RingoCron extends Command
{
    use IntegrationsTrait;
    use WalletTrait;
    use ReferenceTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ringo:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refund Ringo Failed Transaction';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $pendingOrders = Order::where('status', 0)->where('ref','LIKE',"%RNG%")->get();

        if(!is_null($pendingOrders)){
           foreach ($pendingOrders as $orders) {
            
            $refund = $this->ringoReQuery($orders->ref);
            \Log::info('Ringo Order Checked if it was Ringo');
            \Log::info($refund);
            $subcategory = Subcategory::where('title', 'Reversal')->first();
            if (!is_null($refund)){
                if($orders->subcategory_id == 11 || $orders->subcategory_id == 12 || $orders->subcategory_id == 13){
                    if($refund['status'] == "300") {
                        //016 transaction failed;
                        
                        $updateOrder = Order::where('ref', $orders->ref)->first();
                        $updateOrder->status = 4;
                        $updateOrder->description = $updateOrder->description . " for Ref: " . $orders->ref;
                        $updateOrder->save();
                        
                        //$this->isCredited($orders->subtotal);
                        $userWallet = User::lockForUpdate()->find($orders->user_id);
                        $prev = $userWallet->wallet;
                        $bal = $userWallet->wallet + $orders->subtotal;
        
        
                        $userWallet->wallet  = $userWallet->wallet + $orders->subtotal;
                        $userWallet->save();
        
                        $order = new Order();
                        $order->ref = $orders->ref;
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
            }elseif($orders->subcategory_id != 11 || $orders->subcategory_id != 12 || $orders->subcategory_id != 13){
            
                if(!is_null($refund) && $refund['status'] == "200"){
                    $updateOrder = Order::where('ref', $ref)->first();
                    $updateOrder->status = 1;
                    $updateOrder->description = $updateOrder->description . ' Token :' . $refund['token'];
                    $updateOrder->save();
                }
            }
        }
        \Log::info("Ringo Requery Done");
        }
       
        return Command::SUCCESS;
    }
}
