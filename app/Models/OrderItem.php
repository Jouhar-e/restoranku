<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderItem extends Model
{
    use SoftDeletes;

    protected $fillable = ['order_id', 'item_id', 'quantity', 'price', 'tax', 'total_price', 'created_at', 'updated_at'];
    protected $dates = ['deleted_at'];

    public function orders(){
        return $this->belongsTo(Order::class);
    }

    public function items(){
        return $this->belongsTo(Item::class);
    }

}
