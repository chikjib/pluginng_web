<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Order;
use App\Models\Setting;
use App\Models\Subcategory;
use App\Traits\WalletTrait;
use App\Traits\ReferenceTrait;
use App\Traits\IntegrationsTrait;
use Spatie\WebhookClient\Jobs\ProcessWebhookJob as SpatieProcessWebhookJob;


class WisperProcessWebhookJob extends SpatieProcessWebhookJob
{
    use IntegrationsTrait;
    use WalletTrait;
    use ReferenceTrait;

    public function handle()
    {
        // $this->webhookCall // contains an instance of `WebhookCall`

        // perform the work here

        $payload = $this->webhookCall->payload;

        $response = $payload;
        \Log::info("WISPER WEBHOOK RESPONSE");
        \Log::info($response);
        //if($response['transaction']['status'])

        $ref = $response['transaction']['customer_reference'];

        $oldOrder = Order::where('ref', $ref)->where('status', 0)->first();


        if (!is_null($oldOrder)) {
            $subcategory = Subcategory::findOrFail($oldOrder->subcategory_id);

            if ($response['transaction']['status'] == "success") {

                // $updateOrder = Order::where('ref', $ref)->first();
                $oldOrder->status = 1;
                $resp = $response['transaction']['response'];
                $oldOrder->response = $resp;
                $oldOrder->save();
                $msg = $resp . " SMEPLUG Ref: " . $ref;
                //$subcategory =  Subcategory::find($oldOrder->subcategory_id);

               // $this->sendTelegramMessage($oldOrder->amount, $oldOrder->phone, $subcategory->pins, "", $ref, $subcategory->telegram, $msg);
            }elseif ($response['transaction']['status'] == "failed") {
                $oldOrder->status = 4;
                $oldOrder->save();

                //refund
                $this->isCredited($oldOrder->subtotal, $oldOrder->user_id);

                $order = new Order();
                $order->ref = $ref;
                $order->custom_reference = $oldOrder->custom_reference;
                $order->user_id =  $oldOrder->user_id;
                $order->subcategory_id =  $subcategory->id;
                $order->plan =  $subcategory->subcategory_title . " " . $oldOrder->plan . " at N" . $oldOrder->amount;
                $order->amount =  $oldOrder->subtotal;
                $order->quantity =  1;
                $order->channel = is_null($oldOrder->channel) ? 'Web' : 'App';

                $order->subtotal =  $oldOrder->subtotal;
                $order->total = $oldOrder->subtotal;
                $order->phone = $oldOrder->phone;
                $order->bal = $oldOrder->prev;
                $order->prev_bal = $oldOrder->bal;
                $order->description = $subcategory->title . " " . $oldOrder->plan .
                    " at N" . $oldOrder->subtotal;
                $order->status = 2;
                $resp = $response['transaction']['response'];
                $order->response = $resp;
                $order->save();

            }
        }

    }
}
