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
            // Links to the booking_id in your bookings table
            $table->foreignId('booking_id')->constrained('bookings', 'booking_id');
            $table->decimal('amount', 10, 2);
            $table->string('payment_status'); // e.g., 'Pending', 'Paid'
            $table->timestamp('payment_date')->nullable();
            $table->timestamps(); // Automatically handles created_at and updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};