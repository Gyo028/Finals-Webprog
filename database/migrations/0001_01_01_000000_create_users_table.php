<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id('user_id'); 
            $table->string('username')->unique();
            $table->string('password');
            $table->string('email')->unique();
            $table->string('mobile_number')->nullable();
            
            // We must explicitly reference 'role_id' on the 'roles' table
            $table->foreignId('role_id')->constrained(
                table: 'roles', indexName: 'users_role_id_foreign'
            )->references('role_id')->on('roles');

            $table->boolean('IsActive')->default(true);
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
                table: 'users', column: 'user_id'
            );
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};