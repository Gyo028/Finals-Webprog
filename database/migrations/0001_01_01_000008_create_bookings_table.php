<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id('booking_id');
            $table->foreignId('client_id')->constrained('clients', 'client_id');
            $table->foreignId('pax_id')->constrained('paxes', 'pax_id');
            $table->foreignId('venue_id')->constrained('venues', 'venue_id');
            $table->foreignId('event_id')->constrained('events', 'event_id');
            $table->decimal('total_price', 15, 2);
            $table->date('booking_date');
            $table->time('booking_startTime');
            $table->time('booking_endTime');
            
            // THE STATUS COLUMN:
            // This allows the manager to change the state from 'pending' to 'approved'
            $table->string('status')->default('pending'); 
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};