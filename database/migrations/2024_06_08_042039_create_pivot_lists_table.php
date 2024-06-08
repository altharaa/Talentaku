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
        Schema::create('pivot_lists', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('information_id');
            $table->unsignedBigInteger('list_desc_id');
            $table->timestamps();

            $table->foreign('information_id')->references('id')->on('info_lists')->onDelete('cascade');
            $table->foreign('list_desc_id')->references('id')->on('pivot_lists')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pivot_lists');
    }
};
