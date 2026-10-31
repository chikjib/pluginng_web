<?php

namespace App\Traits;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

use DB;

trait WalletTrait
{

    /**
     * @param Request $request
     * @return $this|false|string
     */


    public function isDebited($amount, $userid = null)
    {
    
        

        try {
            DB::beginTransaction();
                $userWallet = User::query()->lockForUpdate()->findOrFail($userid == null ? $this->user->id : $userid);

                // \Log::info(print_r($userWallet, true));

                if (!is_numeric($amount)) {
                    return false;
                } else if ($amount <= 0) {
                    return false;
                } else if ($userWallet->wallet < $amount) {
                    return false;
                } else {
                    $userWallet->wallet  = $userWallet->wallet - $amount;
                    $userWallet->save();
                    DB::commit();
                    return true;
                }
            
                

           } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
          }
    }


    public function isCredited($amount, $userid = null)
    {
        

        try {
            DB::beginTransaction();
        $userWallet = User::query()->lockForUpdate()->findOrFail($userid == null ? $this->user->id : $userid);
        // $userWallet->wallet  = $userWallet->wallet + $amount;
        // return $userWallet->save();


        if (!is_numeric($amount)) {
            return false;
        } else if ($amount <= 0) {
            return false;
        } else {
            $userWallet->wallet  = $userWallet->wallet + $amount;
            $userWallet->save();
            DB::commit();
            return true;
        }
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function adwardBonus($id, $bonus, $transactionAmount)
    {
        //SETTINGS KEY
        $amount = ($bonus / 100) * $transactionAmount;
        $userWallet = User::lockForUpdate()->find($id);
        $userWallet->commission  = $userWallet->commission + $amount;
        $userWallet->save();
        return $amount;
    }
    
    public function DecrementBucket($wallet_name,$user_id, $data_cost)
    {
        try {
            DB::beginTransaction();
            $userWallet = User::query()->lockForUpdate()->findOrFail($user_id == null ? $this->user->id : $user_id);


            if (!is_numeric($data_cost)) {
                return false;
            } else if ($data_cost <= 0) {
                return false;
            } else if ($userWallet->$wallet_name < $data_cost) {
                return false;
            } else {
                $userWallet->$wallet_name -= $data_cost;
                $userWallet->save();
                DB::commit();
                return true;


            }
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function IncrementBucket($wallet_name,$user_id, $data_cost)
    {
        try {
            DB::beginTransaction();
            $userWallet = User::query()->lockForUpdate()->findOrFail($user_id == null ? $this->user->id : $user_id);


            if (!is_numeric($data_cost)) {
                return false;
            } else if ($data_cost <= 0) {
                return false;
            } else {
                $userWallet->$wallet_name += $data_cost;
                $userWallet->save();
                DB::commit();
                return true;


            }
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
