<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::create('roles', function (Blueprint $table) {
        $table->id('role_id');
        $table->string('role_name');
        $table->string('role_description')->nullable();
        $table->boolean('IsActive')->default(true);
        $table->timestamps(); // Creates created_at and updated_at automatically
    });
}

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};