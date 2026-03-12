<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Client;
use App\Models\Product;
use App\Models\Gateway;

class Transaction extends Model
{
    protected $fillable = [
        'client_id',
        'gateway_id',
        'external_id',
        'status',
        'amount',
        'card_last_numbers'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function gateway()
    {
        return $this->belongsTo(Gateway::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class)
            ->withPivot('quantity');
    }
}