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
        Schema::create('oauth_access_tokens', function (Blueprint $table) {
            $table->string('id', 100)->primary();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->uuid('client_id');
            $table->string('name')->nullable();
            $table->text('scopes')->nullable();
            $table->boolean('revoked');
            /* Custom columns */
            $table->string('two_factor_code', 10)->nullable();
            $table->string('two_factor_type', 50)->nullable();
            $table->timestamp('two_factor_expires_at')->nullable();
            $table->timestamp('two_factor_verified_at')->nullable();
            $table->text('device_info')->nullable();
            $table->string('ip', 100)->nullable();
            /* Custom columns */
            $table->timestamps();
            $table->dateTime('expires_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('oauth_access_tokens');
    }
};
