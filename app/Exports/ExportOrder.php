<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\User;
use App\Models\Subcategory;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;


use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;

ini_set('memory_limit','1024M');

class ExportOrder implements FromQuery,WithMapping,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
   use Exportable;
    
        protected $start_date;
        protected $end_date;
    
    public function __construct($start_date,$end_date) {
                $this->start_date = $start_date;
                $this->end_date = $end_date;
        }
    
    public function query()
    {
        \Log::info("START DATE FROM EXPORT MAP");
        \Log::info($this->start_date);
        $startDate = Carbon::createFromFormat('Y-m-d', $this->start_date)->startOfDay();
        $endDate = Carbon::createFromFormat('Y-m-d', $this->end_date)->endOfDay();

        return Order::with(['subcategory:id,category_id,title'])->where('user_id',Auth::user()->id)->whereBetween('created_at',[$startDate,$endDate])->orderBy('created_at', 'desc');

        
    }
    
    
    private function getStatus($stat)
    {
        switch($stat)
        { 
            case "0":
                return "Pending"; 
                break;
            case "1":
                return "Confirmed";
                break;
            case "2":
                return "Reversed";
                break;
            case "3":
                return "Ignored";
                break;
            case "4":
                return "Failed";
                break;
            default:
                return "Failed";
                break;
        }
        
    }
    
    private function getType($id)
    {
        $category = Category::find($id);
        $title = $category->title;
        return $title;
    }
    
    public function map($order): array
    {
        \Log::info("FROM EXPORT ORDER MAP");
        \Log::info($order);
        
        return [
            $order->ref,
            $this->getType($order->subcategory->category_id),
            $order->description,
            $order->subtotal,
            $order->phone,
            $order->iuc,
            $order->meter,
            $order->bal,
            $order->prev_bal,
            $order->response,
            $this->getStatus($order->status),
            $order->created_at
        ];
    }
    
    public function headings(): array
    {
        return [
            'Ref',
            'Type',
            'Description',
            'Amount',
            'Phone',
            'Iuc',
            'Meter',
            'Bal',
            'Prev Bal',
            'Response',
            'Status',
            'Created At'
        ];
    }
}
