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
        Schema::create('space_invites', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('space_id')->references('id')->on('spaces')->onDelete('cascade');
            $table->foreignUuid('role_id')->references('id')->on('space_roles')->onDelete('cascade');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('email');
            $table->tinyInteger('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('space_invites');
    }
};
