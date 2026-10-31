<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\WebhookServer\WebhookCall;

class Order extends Model
{
    use HasFactory;
    protected $fillable = [
        'ref',
        'user_id',
        'category_id',
        'subcategory_id',
        'price',
        'quantity',
        'total',
        'subtotal',
        'description',
        'status',
        'phone',
        'iuc',
        'meter',
        'bal',
        'prev_bal',
        'channel'

    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function payment_method()
    {
        return $this->belongsTo(Payment_method::class);
    }

    public function payment()
    { 
        return $this->belongsTo(Payment::class);
    }
    
    public static function boot()
    {
        parent::boot();
        static::updated(function (Order $order){ 
            $user = User::find($order->user_id);
            $url = $user->webhook_url;

            if(!is_null($url)){
                WebhookCall::create()
                    ->url($url)
                    ->payload($order->toArray())
                    ->throwExceptionOnFailure()
                    ->useSecret('sign-using-this-secret')
                    ->dispatch();
            }


        });
    }
    
    
}
