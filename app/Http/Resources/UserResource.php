<?php

namespace App\Http\Resources;

use App\Models\Commissions;
use App\Models\Withdrawal;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'email' => $this->email,
            'phone' => $this->phone,

            'role' => $this->role,
            'wallet' => $this->wallet,

            'userlevel' => $this->userlevel,
            'hear_about_us' => $this->hear_about_us,

            'commission' => $this->commission,
            'withdrawal' => $this->withdrawal,

            'reserved_acct' => $this->reserved_acct,
            
            'wema_reserved_acct' => $this->wema_reserved_acct,
            'moniepoint_reserved_acct'  => $this->moniepoint_reserved_acct,
            'gtbank_reserved_acct'  => $this->gtbank_reserved_acct,
            'rehoboth_reserved_acct'  => $this->rehoboth_reserved_acct,
            'palmpay_reserved_acct'  => $this->palmpay_reserved_acct,
            
            'mtn_sme_wallet' => $this->mtn_sme_wallet,
            'mtn_sme_wallet_status' => $this->mtn_sme_wallet_status,
            'airtel_eds_wallet' => $this->airtel_eds_wallet,
            'airtel_eds_wallet_status' => $this->airtel_eds_wallet_status,
            'glo_cg_wallet' => $this->glo_cg_wallet,
            'glo_cg_wallet_status' => $this->glo_cg_wallet_status,
            'nmobile_cg_wallet' => $this->nmobile_cg_wallet,
            'nmobile_cg_wallet_status' => $this->nmobile_cg_wallet_status,
            'mtn_smart_wallet' => $this->mtn_smart_wallet,
            'mtn_smart_wallet_status' => $this->mtn_smart_wallet_status,
            'airtel_awoof_wallet' => $this->airtel_awoof_wallet,
            'airtel_awoof_wallet_status' => $this->airtel_awoof_wallet_status,
            'glo_awoof_wallet' => $this->glo_awoof_wallet,
            'glo_awoof_wallet_status' => $this->glo_awoof_wallet_status,


            
            'userToken' => $this->userToken,
            'deviceToken' => $this->deviceToken,

            'bank_name' => $this->bank_name,
            'account_number' => $this->account_number,
            
            'bvn' => $this->bvn,
            'nin' => $this->nin,
            'webhook_url' => $this->webhook_url,

            'referral_id' => $this->referral_id,
            'bank_name' => $this->bank_name,
            'account_number' => $this->account_number,
            'status' => $this->status,
            'created_at' => $this->created_at->format('d/m/Y'),
            'updated_at' => $this->updated_at->format('d/m/Y'),
        ];
    }
}
