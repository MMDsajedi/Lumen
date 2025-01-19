<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    { 
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key
            $table->string('first_name', 50); // First name
            $table->string('last_name', 50); // Last name
            $table->string('username', 50)->unique(); // Unique username
            $table->string('email', 50)->unique(); // Unique email
            $table->string('token', 64)->unique()->nullable();
            $table->string('phone_number', 11); // Phone number
            $table->string('password'); // Hashed password
            $table->timestamps(); // Created_at and updated_at columns
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
