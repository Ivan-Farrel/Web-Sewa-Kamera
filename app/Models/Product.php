<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'category',
        'price_8h',
        'price_12h',
        'price_24h',
        'price_2d',
        'overtime_per_hour',
        'status',
        'image',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}