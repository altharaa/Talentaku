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
        Schema::create('announcement_media', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('announce_id');
            $table->string('file_name');
            $table->timestamps();

            $table->foreign('announce_id')->references('id')->on('announcements')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcement_media');
    }
};
