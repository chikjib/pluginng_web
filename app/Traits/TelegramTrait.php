<?php

namespace App\Traits;

use App\Models\Subcategory;
use App\Notifications\TransactionNotification;
use Illuminate\Http\Request;

trait TelegramTrait
{

    /**
     * @param Request $request
     * @return $this|false|string
     */


    public function sendTelegramMessage($amount, $phone, $pin, $channelCode, $ref, $channelID, $channelText = '')
    {


        $findcode = Subcategory::where('pins', $channelCode)->first();


        if (!is_null($findcode) && $findcode->category_id == 2) {
            $channelText = $channelText == '' ?
                str_replace(
                    ['PHONE', 'PIN', 'AMOUNT', 'REF'],
                    [$phone, $pin, $amount, $ref],
                    $channelCode
                )
                : $channelText;
        } else {
            $channelText = $channelText == '' ?
                str_replace(
                    ['PHONE', 'PIN', 'AMOUNT', 'REF'],
                    [$phone, $pin, $amount, $ref],
                    $channelCode
                )
                . ", " . $ref : $channelText;
        }






        //check for smec
        $finalCh =  substr($channelText, 0, 4);

        $channelText =    str_replace('*', '\*', $channelText);

        $transactionData = [
            //'name' => 'BOGO',
            'body' => 'You received an offer.',
            'thanks' => 'Thank you',
            'transactionText' => 'Check out the offer',
            'transactionUrl' => url('/'),
            'transaction_id' => $ref,
            'channelID' => $finalCh == 'SMEC' ? '-381394887' : $channelID,
            'telegramText' => $channelText,
        ];

        return $this->user->notify(new TransactionNotification($transactionData));
    }
}
