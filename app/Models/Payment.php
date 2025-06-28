<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'code',
        'message',
        'pay_method',
        'amount',
        'currency',
        'order_number',
        'authorization_code',
        'transaction_id',
        'state_message',
        'transaction_date',
        'transaction_time',
        'unique_id',
        'reference_number',
        'merchant_code',
        'buyer_id',
        'card_brand',
        'card_pan_masked',
        'customer_first_name',
        'customer_last_name',
        'customer_email',
        'customer_document_type',
        'customer_document',
        'customer_address',
        'customer_city',
        'customer_country',
        'full_response',
    ];

    protected $casts = [
        'full_response' => 'array',
    ];
public function cotizacion()
{
    return $this->hasOne(Cotizacion::class);
}
}
