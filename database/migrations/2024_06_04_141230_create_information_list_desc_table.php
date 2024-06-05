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
        Schema::create('information_list_desc', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('information_id');
            $table->unsignedBigInteger('list_desc_id');
            $table->timestamps();

            $table->foreign('information_id')->references('id')->on('information')->onDelete('cascade');
            $table->foreign('list_desc_id')->references('id')->on('list_desc')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('information_list_desc');
    }
};
