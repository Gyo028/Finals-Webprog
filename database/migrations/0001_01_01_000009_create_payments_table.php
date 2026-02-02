<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id('payment_id');

            // 1 Booking = 1 Payment
            $table->unsignedBigInteger('booking_id')->unique();
            $table->foreign('booking_id')
                ->references('booking_id')
                ->on('bookings')
                ->onDelete('cascade');

            $table->decimal('amount', 10, 2);

            // Pending / Paid / Rejected (depends on your flow)
            $table->string('payment_status')->default('Pending');

            // Receipt image path stored in local storage
            $table->string('receipt_path')->nullable();

            $table->timestamp('payment_date')->nullable();


            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
