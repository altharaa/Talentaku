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
            $table->id();
            $table->string('username');
            $table->string('name');
            $table->string('email')->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('nomor_induk')->unique()->nullable();
            $table->string('address')->nullable();
            $table->string('place_of_birth')->nullable();
            $table->date('birth_date')->nullable();
            $table->date('joining_year')->nullable();
            $table->string('password');
            $table->string('photo')->nullable();
            $table->enum('status', ['aktif', 'non-aktif'])->default('aktif');
            $table->string('fcm_token')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
