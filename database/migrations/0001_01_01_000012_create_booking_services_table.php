<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_services', function (Blueprint $table) {
            $table->id('booking_service_id');
            $table->foreignId('booking_id')->constrained('bookings', 'booking_id')->onDelete('cascade');
            $table->foreignId('service_id')->constrained('services', 'service_id')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_services');
    }
};