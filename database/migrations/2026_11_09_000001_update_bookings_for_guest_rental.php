<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->change();
            $table->string('booking_code')->nullable()->after('id');
            $table->string('customer_name')->nullable()->after('product_name');
            $table->string('customer_phone')->nullable()->after('customer_name');
            $table->text('customer_address')->nullable()->after('customer_phone');
            $table->string('delivery_method')->nullable()->after('customer_address');
            $table->string('payment_method')->nullable()->after('delivery_method');
            $table->string('ktp_photo')->nullable()->after('payment_method');
            $table->string('guarantee_photo')->nullable()->after('ktp_photo');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'booking_code',
                'customer_name',
                'customer_phone',
                'customer_address',
                'delivery_method',
                'payment_method',
                'ktp_photo',
                'guarantee_photo',
            ]);
        });
    }
};
