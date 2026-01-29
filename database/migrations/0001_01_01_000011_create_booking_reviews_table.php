<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('booking_reviews', function (Blueprint $table) {
            $table->id('booking_review_id');
            $table->foreignId('booking_id')->constrained('bookings', 'booking_id')->onDelete('cascade');
            $table->foreignId('manager_id')->constrained('managers', 'manager_id');
            
            // Manager's internal notes about the verification
            $table->text('remarks')->nullable(); 
            $table->boolean('payment_verified')->default(false);
            $table->boolean('details_verified')->default(false);
            
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('booking_reviews'); }
};