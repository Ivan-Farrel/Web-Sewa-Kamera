<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    protected $fillable = [
        'user_id',
        'booking_code',
        'product_name',
        'customer_name',
        'customer_phone',
        'customer_address',
        'delivery_method',
        'payment_method',
        'ktp_photo',
        'guarantee_photo',
        'start_date',
        'end_date',
        'days',
        'daily_rate',
        'total',
        'status',
    ];

    protected function casts(): array
    {
        return ['start_date' => 'date', 'end_date' => 'date'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
