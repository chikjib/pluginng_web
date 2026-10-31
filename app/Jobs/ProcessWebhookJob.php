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


class ProcessWebhookJob extends SpatieProcessWebhookJob
{
    use IntegrationsTrait;
    use WalletTrait;
    use ReferenceTrait;

    public function handle()
    {
        // $this->webhookCall // contains an instance of `WebhookCall`

        // perform the work here

        $payload = $this->webhookCall->payload;

        $response = $payload['eventData'];
        
        $transactionReference = $response['transactionReference'];
        
        $new_response = $this->VerifyMonnify($transactionReference);
        
        \Log::info("Monnify Verification Response");
        \Log::info($new_response);
        
        $response = $new_response['responseBody'];
        
        
        if($new_response['requestSuccessful'] == true && $new_response['responseCode'] == '0' && $response['paymentStatus'] == 'PAID') {
            

            switch ($response['product']['type']) {
                case "RESERVED_ACCOUNT":
                    $findUser = User::where('email', $response['customer']['email'])->first();

                    if (!is_null($findUser)) {

                        $findOrder = Order::where('ref', $response['transactionReference'])->first();

                        if (is_null($findOrder)) {
                            //$com = 0.0085 * $response['settlementAmount'];
                            // $amount = $response['amountPaid'];
                            //$amt = $response['settlementAmount'];
                            //- $com;
                            // $amount = $response['amountPaid'];
                            //$amt = $amount - 50;
                            // $amt = $amount;
                            $amount = $response['amountPaid'];
                            $amt = $amount - 50;
                            if ($this->isCredited($amt, $findUser->id)) {

                                $subcategory =  Subcategory::where('title', 'ReservedAcct')->first();

                                $ref = $response['transactionReference'];
                                $order = new Order();
                                $order->ref = $ref;
                                $order->user_id =  $findUser->id;
                                $order->subcategory_id =  $subcategory->id;
                                $order->plan =  $subcategory->title;
                                $order->amount = $amt;
                                $order->quantity =  1;
                                $order->bal = $findUser->wallet + $amt;
                                $order->prev_bal = $findUser->wallet;
                                $order->subtotal =  $response['settlementAmount'];
                                $order->total = $response['settlementAmount'];
                                $order->description = $subcategory->title . " RA: Auto Credited";
                                $order->status = 1;
                                $order->save();
                            }
                        }
                    }
                    break;
                case "WEB_SDK":

                    // $findOrder = Order::where('ref', $response['paymentReference'])->exists();

                    if (Order::where('ref', $response['transactionReference'])->exists() == false) {


                        $findUser = User::where('email', $response['customer']['email'])->first();


                        $amount = $response['amountPaid'];
                        $settings = Setting::where('key', 'MONNIFY_CARD_BANK_CHARGE');
                        $charge =  $settings->first()->value;
                        $amt = $amount - 50;

                        
                        if ($this->isCredited($amt, $findUser->id)) {

                                $subcategory =  Subcategory::where('title', 'Monnify')->first();

                                $ref = $response['transactionReference'];
                                $order = new Order();
                                $order->ref = $ref;
                                $order->user_id =  $findUser->id;
                                $order->subcategory_id =  $subcategory->id;
                                $order->plan =  $subcategory->title;
                                $order->amount = $amt;
                                $order->quantity =  1;
                                $order->bal = $findUser->wallet + $amt;
                                $order->prev_bal = $findUser->wallet;
                                $order->subtotal =  $amt;
                                $order->total = $amt;
                                $order->description = $subcategory->title . " Card Auto Credited";
                                $order->status = 1;
                                $order->save();
                            }
                    }

                    break;

                case "MOBILE_SDK":
                    // $findOrder = Order::where('ref', $response['paymentReference'])->first();
                    
                    if (Order::where('ref', $response['transactionReference'])->exists() == false) {



                        $findUser = User::where('email', $response['customer']['email'])->first();

                        //$com = 0.0085 * $response['settlementAmount'];
                        // $amount = $response['amountPaid'];
                        //$amt = $response['settlementAmount'];

                        $amount = $response['amountPaid'];
                        $settings = Setting::where('key', 'MONNIFY_CARD_BANK_CHARGE');
                        $charge =  $settings->first()->value;
                        $amt = $amount - 50;
                        
                        \Log::info("Monnify Crediting");
                        
                        if ($this->isCredited($amt, $findUser->id)) {

                                $subcategory =  Subcategory::where('title', 'Monnify')->first();

                                $ref = $response['transactionReference'];
                                
                                \Log::info($ref);
                                
                                $order = new Order();
                                $order->ref = $ref;
                                $order->user_id =  $findUser->id;
                                $order->subcategory_id =  $subcategory->id;
                                $order->plan =  $subcategory->title;
                                $order->amount = $amt;
                                $order->quantity =  1;
                                $order->bal = $findUser->wallet + $amt;
                                $order->prev_bal = $findUser->wallet;
                                $order->subtotal =  $amt;
                                $order->total = $amt;
                                $order->description = $subcategory->title . " Card Auto Credited";
                                $order->status = 1;
                                $order->save();
                                
                                \Log::info("Monnify Crediting done");
                        }
                    }
                    break;

                default:
                    break;
            }
        }
    }
}
