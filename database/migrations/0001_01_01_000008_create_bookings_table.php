<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id('booking_id');

            // Relationship: The client who is making the booking
            $table->foreignId('client_id')
                ->constrained('clients', 'client_id')
                ->onDelete('cascade');

            // Relationships for booking details
            $table->foreignId('pax_id')
                ->constrained('paxes', 'pax_id')
                ->onDelete('restrict');

            $table->foreignId('venue_id')
                ->constrained('venues', 'venue_id')
                ->onDelete('restrict');

            $table->foreignId('event_id')
                ->constrained('events', 'event_id')
                ->onDelete('restrict');

            $table->decimal('total_price', 15, 2);

            // Booking Schedule
            $table->date('booking_date');
            $table->time('booking_start_time');
            $table->time('booking_end_time');

            /**
             * Integrated Verification Fields
             * These replace the old booking_verifications table
             */
            $table->foreignId('verified_by_manager_id')
                ->nullable()
                ->constrained('managers', 'manager_id')
                ->onDelete('set null');

            $table->text('verification_remarks')->nullable();
            $table->boolean('is_payment_verified')->default(false);
            $table->boolean('is_details_verified')->default(false);
            $table->timestamp('verified_at')->nullable();

            /**
             * Booking Status
             * draft: Client is still editing/selecting services
             * pending: Submitted by client, awaiting manager review
             * approved: Manager has verified and confirmed the booking
             * denied: Manager rejected the booking (requires re-booking)
             */
            $table->enum('status', ['draft', 'pending', 'approved', 'denied'])
                  ->default('draft');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};