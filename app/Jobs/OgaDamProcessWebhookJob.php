<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Order;
use App\Models\Setting;
use App\Models\Subcategory;
use App\Traits\WalletTrait;
use App\Traits\ReferenceTrait;
use App\Traits\IntegrationsTrait;
use App\Traits\TelegramTrait;
use Spatie\WebhookClient\Jobs\ProcessWebhookJob as SpatieProcessWebhookJob;


class OgaDamProcessWebhookJob extends SpatieProcessWebhookJob
{
    use IntegrationsTrait;
    use WalletTrait;
    use ReferenceTrait;
    use TelegramTrait;

    public function handle()
    {
        // $this->webhookCall // contains an instance of `WebhookCall`

        // perform the work here

        $payload = $this->webhookCall->payload;

        $response = $payload;
        $ref = $response['event']['data']['reference'];




        $oldOrder = Order::where('ref', $ref)->where('status', 0)->first();

        if (!is_null($oldOrder)) {
            if ($response['code'] != 200) {
                //reverse
                $this->isCredited($oldOrder->subtotal, $oldOrder->user_id);

                $order = new Order();
                $order->ref = $ref;
                $order->user_id =  $oldOrder->user_id;
                $order->subcategory_id =  $oldOrder->subcategory_id;
                $order->plan =  $oldOrder->plan;
                $order->amount =  $oldOrder->amount;
                $order->quantity =  1;
                $order->channel = $oldOrder->channel;

                $order->subtotal =  $oldOrder->subtotal;
                $order->total = $oldOrder->total;
                $order->phone = $oldOrder->phone;
                $order->bal = User::find($oldOrder->user_id)->wallet + $oldOrder->amount;
                $order->prev_bal = User::find($oldOrder->user_id)->wallet;
                $order->description = $oldOrder->description;
                $order->status = 2;
                $order->save();
                $subcategory =  Subcategory::find($oldOrder->subcategory_id);

                $this->sendTelegramMessage($oldOrder->amount, $oldOrder->phone, $subcategory->pins, "", $ref, $subcategory->telegram);
            } else {

                // $updateOrder = Order::where('ref', $ref)->first();
                $oldOrder->status = 1;
                $oldOrder->response = $response['event']['data']['msg'];
                $oldOrder->save();
                $msg = $response['event']['data']['msg'] . " OGADAM Ref: " . $response['event']['data']['msg'];
                $subcategory =  Subcategory::find($oldOrder->subcategory_id);
                $this->sendTelegramMessage($oldOrder->amount, $oldOrder->phone, $subcategory->pins, "", $ref, $subcategory->telegram, $msg);
            }
        }
    }
}
