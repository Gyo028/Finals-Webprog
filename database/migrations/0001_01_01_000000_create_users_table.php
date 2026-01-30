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
        Schema::create('users', function (Blueprint $table) {
            // Your custom primary key
            $table->id('user_id'); 
            
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('mobile_number')->nullable();
            
            // Foreign Key for Roles
            $table->foreignId('role_id')->constrained(
                table: 'roles', 
                column: 'role_id', // specifically pointing to your custom PK in roles
                indexName: 'users_role_id_foreign'
            );

            $table->boolean('IsActive')->default(true);
            $table->rememberToken();
            $table->timestamps(); 
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            // Pointing to your custom user_id primary key
            $table->foreignId('user_id')->nullable()->index()->constrained(
                table: 'users', 
                column: 'user_id'
            );
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};