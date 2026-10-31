<?php

namespace App\Traits;

use App\AppConstants;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Category;
use App\Models\Dispatch;
use App\Models\AgentStock;
use App\Models\Subcategory;
use App\Traits\WalletTrait;
use App\Traits\TelegramTrait;
use App\Traits\ReferenceTrait;
use App\Traits\IntegrationsTrait;
use Illuminate\Support\Facades\DB;
use App\Providers\UploadImageClass;
use App\Http\Resources\OrderResource;
use App\Http\Resources\PaymentResource;
use Illuminate\Support\Facades\Validator;
use App\Notifications\TransactionNotification;

trait FunctionsTrait
{
    public function recordTransaction($ref,$user_id,$subcategory_id,$title,$amount,$amountActual,$phone,$channel=null,$bal,$prev,$status,$msg=null){
        try{
            DB::beginTransaction();
                $order = new Order();
                $order->ref = $ref;
                $order->user_id =  $user_id;
                $order->subcategory_id =  $subcategory_id;
                $order->plan =  $title . " N" . $amount;
                $order->amount =  $amount;
                $order->quantity =  1;
                $order->subtotal =  $amountActual;
                $order->phone = $phone;
                $order->channel = is_null($channel) ? 'Web' : 'App';
                $order->bal = $bal;
                $order->prev_bal = $prev;
                $order->response = $msg;
                $order->total = $amountActual;
                $order->description =  $title . " N" . $amount;
                $order->status = $status;
                $order->save();
            DB::commit();
        }catch(\Exception $e){
            DB::rollBack();
            throw $e;
        }
    }

    public function recordTransactionData($ref,$user_id,$subcategory_id,$title,$plan,$amount,$amountActual,$phone,$channel=null,$bal,$prev,$status,$msg=null){
        try{
            DB::beginTransaction();
                $order = new Order();
                $order->ref = $ref;
                $order->user_id =  $user_id;
                $order->subcategory_id =  $subcategory_id;
                $order->plan =  $title . " " . $plan . " at N" . $amount;
                $order->amount =  $amountActual;
                $order->quantity =  1;
                $order->subtotal =  $amountActual;
                $order->total = $amountActual;
                $order->phone = $phone;
                $order->channel = is_null($channel) ? 'Web' : 'App';
                $order->bal = $bal;
                $order->prev_bal = $prev;
                $order->response = $msg;
                $order->description = $title . " " . $plan .
                    " at N" . $amountActual;
                $order->status = $status;
                $order->save();
            DB::commit();
        }catch(\Exception $e){
            DB::rollBack();
            throw $e;
        }
    }

    public function recordTransactionCable($ref,$user_id,$subcategory_id,$title,$plan,$amount,$amountActual,$phone,$channel=null,$cardno,$bal,$prev,$status,$msg=null){
        try{
            DB::beginTransaction();
                $order = new Order();
                $order->ref = $ref;
                $order->user_id =  $user_id;
                $order->subcategory_id =  $subcategory_id;
                $order->plan =  $title . " " . $plan . " at N" . $amount;
                $order->amount =  $amountActual;
                $order->quantity =  1;
                $order->subtotal =  $amountActual;
                $order->total = $amountActual;
                $order->iuc = $cardno;
                $order->phone = $phone;
                $order->channel = is_null($channel) ? 'Web' : 'App';
                $order->bal = $bal;
                $order->prev_bal = $prev;
                $order->response = $msg;
                $order->description = $title . " " . $plan .
                    " at N" . $amount;
                $order->status = $status;
                $order->save();
            DB::commit();
        }catch(\Exception $e){
            DB::rollBack();
            throw $e;
        }
    }
    public function recordTransactionElectricity($ref,$user_id,$subcategory_id,$title,$plan,$amount,$amountActual,$phone,$channel=null,$cardno,$bal,$prev,$status,$description="",$msg=null){
        try{
            DB::beginTransaction();
                $order = new Order();
                $order->ref = $ref;
                $order->user_id =  $user_id;
                $order->subcategory_id =  $subcategory_id;
                $order->plan =  $title . " " . $plan . " at N" . $amount;
                $order->amount =  $amountActual;
                $order->quantity =  1;
                $order->subtotal =  $amountActual;
                $order->total = $amountActual;
                $order->meter = $cardno;
                $order->phone = $phone;
                $order->channel = is_null($channel) ? 'Web' : 'App';
                $order->bal = $bal;
                $order->prev_bal = $prev;
                $order->response = $msg;
                $order->description = $title . " " . $plan .
                    " at N" . $amount. " ".$description;
                $order->status = $status;
                $order->save();
            DB::commit();
        }catch(\Exception $e){
            DB::rollBack();
            throw $e;
        }
    }


    public function recordBonus($title,$bonus,$amountadwarded,$wallet_balance,$channel=null,$lastname){
        try{
            DB::beginTransaction();
            $order = new Order();
            $order->ref = $this->referenceCode();
            $order->user_id =  $this->user->referrer()->id;
            $order->subcategory_id =  23;
            $order->plan =  $title . " " . $bonus . "%";
            $order->amount = $amountadwarded;
            $order->quantity =  1;
            $order->subtotal =  $amountadwarded;
            $order->total = $amountadwarded;
            $order->bal = $wallet_balance;
            $order->channel = is_null($channel) ? 'Web' : 'App';
            $order->prev_bal = $wallet_balance - $bonus;
            $order->description = $bonus . "% " .  $title . "   for Referring " . $this->user->lastname;
            $order->status = 1;
            $order->save();
            DB::commit();
        }catch(\Exception $e){
            DB::rollBack();
            throw $e;
        }
    }
}