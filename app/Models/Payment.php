<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'full_name',
        'address',
        'city',
        'state',
        'phone',
        'additional_info',
        'payment_method',
        'pdf_invoice',
        'email_invoice',
        'confirmation_code',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }
}
