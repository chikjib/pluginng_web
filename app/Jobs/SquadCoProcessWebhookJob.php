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


class SquadCoProcessWebhookJob extends SpatieProcessWebhookJob
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
        \Log::info("PayLoad Response");
        \Log::info($response);
        
        
        
        if (!is_null($response)) {
        
        $email_check = explode("-",$response['customer_identifier']);
        
        if(count($email_check) > 1){
        	$first_name = $email_check[0];
        	$new_email = $email_check[1];
        	$check_user = User::where(['firstname' => $first_name, 'email' => $new_email])->first();
        	if(!is_null($check_user)){
        		$email = $new_email;
        	}
        }else{
        	$email = $email_check[0];
        }
        

            $check = Order::where('ref', $response['transaction_reference'])->first();

            if (!is_null($check)) {
                $data = array(
                    "response_code" => 400,
                    "transaction_reference" => $response['transaction_reference'],
                    "response_description" => "Validation failure",
                );
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode($data);
                exit;
            } else {

                $findOrder = Order::where('ref', $response['transaction_reference'])->where('status', 0)->first();

                if (is_null($findOrder)) {
                    $findUser = User::where('email', $email)->first();

                    if (!is_null($findUser)) {

                        //$com = 0.0085 * $response['settlementAmount'];
                        // $amount = $response['amountPaid'];
                        //$amt = $response['settlementAmount'] - $com;
                        $amount = $response['principal_amount'];
                        
                        if($amount >= 10000){
                            $amount = $amount - (0.005*$amount); 
                        }else{
                            $amount = $amount - (0.002*$amount); 
                        }
                        // $amt = $amount - 50;
                        // if ($amount < 10000) {
                        //     $amount = $amount - 0;
                        // } else {
                        //     $amount =  $amount - 50;
                        // }
                        $amt = $amount;
                        if ($this->isCredited($amt, $findUser->id)) {

                            $subcategory =  Subcategory::where('title', 'GTbank')->first();

                            $ref = $response['transaction_reference'];
                            $order = new Order();
                            $order->ref = $ref;
                            $order->user_id =  $findUser->id;
                            $order->subcategory_id =  $subcategory->id;
                            $order->plan =  $subcategory->title;
                            $order->amount = $amt;
                            $order->quantity =  1;
                            $order->bal = $findUser->wallet + $amt;
                            $order->prev_bal = $findUser->wallet;
                            $order->subtotal =  $response['settled_amount'];
                            $order->total = $response['settled_amount'];
                            $order->description = $subcategory->title . " RA: Auto Credited";
                            $order->status = 1;
                            $order->save();
                        }
                        $data = array(
                            "response_code" => 200,
                            "transaction_reference" => $response['transaction_reference'],
                            "response_description" => "Success",
                        );
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode($data);
                        exit;
                    } else {

                        $data = array(
                            "response_code" => 500,
                            "transaction_reference" => $response['transaction_reference'],
                            "response_description" => "System malfunction",
                        );
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode($data);
                        exit;
                    }
                } else {
                    $data = array(
                        "response_code" => 400,
                        "transaction_reference" => $response['transaction_reference'],
                        "response_description" => "Validation failure",
                    );
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode($data);
                    exit;
                }
            }
        } else {
            $data = array(
                "response_code" => 500,
                "transaction_reference" => $response['transaction_reference'],
                "response_description" => "System malfunction",
            );
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($data);
            exit;
        }
    }
}
