<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\Subcategory;
use App\Models\User;
use App\Traits\IntegrationsTrait;
use App\Traits\ReferenceTrait;
use App\Traits\WalletTrait;
use Spatie\WebhookClient\Jobs\ProcessWebhookJob as SpatieProcessWebhookJob;


class PalmpayProcessWebhookJob extends SpatieProcessWebhookJob
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

        \Log::info("Payload from Palmpay");

        \Log::info($payload);
        $payload = $payload['payload'];

        $response = $payload;



        if ($this->verifyPalmPayTrans($payload) == 1 && $payload['orderStatus'] == 1) {
            $amount = $response['orderAmount']/100;
            $charge = 0.004 * $amount;
            if($charge > 300){
                $amount = $amount - 300;
            }else{
                $amount = $amount - $charge;
            }

            $findOrder = Order::where('ref', $response['orderNo'])->first();
            if (is_null($findOrder)) {
                $findUser = User::where('palmpay_account', $response['virtualAccountNo'])->first();

                if (!is_null($findUser)) {
                    if ($this->isCredited($amount, $findUser->id)) {

                        $subcategory = Subcategory::where('title', 'Palmpay')->first();

                        $ref = $response['orderNo'];
                        $order = new Order();
                        $order->ref = $ref;
                        // $order->palmpay_reference = $response['reference'];
                        $order->user_id = $findUser->id;
                        $order->subcategory_id = $subcategory->id;
                        $order->plan = $subcategory->title;
                        $order->amount = $amount;
                        $order->quantity = 1;
                        $order->bal = $findUser->wallet + $amount;
                        $order->prev_bal = $findUser->wallet;
                        $order->subtotal = $amount;
                        $order->total = $amount;
                        $order->description = $subcategory->title . " Auto Credited";
                        $order->status = 1;
                        $order->save();
                    }
                }
            }
            
        }
        
        return response('success', 200)->header('Content-Type','text/plain');
    }

}
