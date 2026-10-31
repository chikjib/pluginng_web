<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\Subcategory;
use App\Models\User;
use App\Traits\IntegrationsTrait;
use App\Traits\ReferenceTrait;
use App\Traits\WalletTrait;

class JonetCron extends Command
{
    use IntegrationsTrait;
    use WalletTrait;
    use ReferenceTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jonet:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Requery Jonet Server';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        // $pendingOrders = Order::where('status', 0)->take(10);

        // foreach ($pendingOrders as $order) {
        //     $subcategory_id = $order->subcategory_id;
        //     $subcategory = Subcategory::find($subcategory_id);
        //     if($subcategory->description == "JONET"){
        //         $response = $this->jonetRequery($order->ref); 
        //         \Log::info("JONET REQUERY");
        //         \Log::info($response);
        //         if($response['responseCode'] == "200" && $response['status'] == "Successful"){
        //             $updateOrder = Order::where('ref', $order->ref)->first();
        //             $updateOrder->status = 1;
        //             $updateOrder->response = $response['server_response'];
        //             $updateOrder->save();
        //         }
        //     }
        // }


        return Command::SUCCESS;
    }
}
