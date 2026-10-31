<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\Subcategory;
use App\Models\User;
use App\Traits\IntegrationsTrait;
use App\Traits\ReferenceTrait;
use App\Traits\WalletTrait;
use Spatie\WebhookClient\Jobs\ProcessWebhookJob as SpatieProcessWebhookJob;


class ProcessRehobothWebhookJob extends SpatieProcessWebhookJob
{
    use IntegrationsTrait;
    use WalletTrait;
    use ReferenceTrait;

    public function handle()
    {
        
         //$this->webhookCall // contains an instance of `WebhookCall`
        
        //echo "yes";
        
        // perform the work here

        $payload = $this->webhookCall;

        \Log::info("Payload from rehoboth");
        
        \Log::info($payload);

       $response = $payload['payload'];
        
        \Log::info("Response Payload from rehoboth payload");
        
       \Log::info($response);
       
      // $trans_ref = $response['TransactionReference'];
       
       //$my_transaction = $this->rehobothWebHookTransaction($trans_ref);
       
      // \Log::info($my_transaction);

        // if ($response['status'] == 'successful') {

        //     // $fundLimitsubCategory =  Subcategory::where('title', 'Upgrade')->first();
        //     // $fundproductCollection = collect(json_decode($fundLimitsubCategory->products));
        //     // $getSelectedLevel = $fundproductCollection->where('title', $this->translateLevel($this->user->userlevel))->first();



        //     // if ($request->amount < $getSelectedLevel->fundmin) {
        //     //     return $this->sendError('You cannot fund below ' . $getSelectedLevel->fundmin, 'You cannot fund below ' . $getSelectedLevel->fundmin);
        //     // }
        //     // if ($request->amount > $getSelectedLevel->fundmax) {
        //     //     return $this->sendError('You cannot fund above ' . $getSelectedLevel->fundmax, 'You cannot fund above ' . $getSelectedLevel->fundmax);
        //     // }
        
        if($response['Amount'] >= 5000){
            $amount = $response['Amount'] - 35;
        }else{
            $amount = $response['Amount'];
        }

             $findOrder = Order::where('ref', $response['TransactionReference'])->first();
             if (is_null($findOrder)) {
               $findUser = User::where('rehoboth_account', $response['AccountNumber'])->first();

                //  if (!is_null($findUser)) {
                //     if ($this->isCredited($amount, $findUser->id)) {

                //          $subcategory =  Subcategory::where('title', 'Rehoboth')->first();

                //       $ref = $response['TransactionReference'];
                //         $order = new Order();
                //         $order->ref = $ref;
                //          $order->user_id =  $findUser->id;
                //         $order->subcategory_id =  $subcategory->id;
                //         $order->plan =  $subcategory->title;
                //         $order->amount = $amount;
                //         $order->quantity =  1;
                //         $order->bal = $findUser->wallet + $amount;
                //         $order->prev_bal = $findUser->wallet;
                //         $order->subtotal =  $amount;
                //         $order->total = $amount;
                //       $order->description = $subcategory->title . " Auto Credited";
                //         $order->status = 1;
                //       $order->save();
                //      }
                // }
            }
        // }
    }
}
