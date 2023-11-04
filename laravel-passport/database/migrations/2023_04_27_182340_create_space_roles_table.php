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
        Schema::create('space_roles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('space_id')->references('id')->on('spaces')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->json('abilities')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('space_roles');
    }
};
