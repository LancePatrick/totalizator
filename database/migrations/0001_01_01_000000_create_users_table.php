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

            // Basic account info
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            // Account role system
            $table->enum('role', ['admin', 'agent', 'player'])->default('player');

            // If user is a player, this stores which agent owns/handles the player.
            $table->foreignId('agent_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // If user is an agent, this is their unique code for player registration.
            $table->string('agent_code')
                ->nullable()
                ->unique();

            // Wallet
            $table->decimal('wallet_balance', 15, 2)->default(0);

            // Account status
            $table->boolean('is_active')->default(true);

            // KYC status
            $table->enum('kyc_status', [
                'not_submitted',
                'pending',
                'approved',
                'rejected',
            ])->default('not_submitted');

            // Optional user profile
            $table->string('phone')->nullable();
            $table->string('location')->nullable();

            // Login tracking
            $table->timestamp('last_login_at')->nullable();

            $table->rememberToken();
            $table->timestamps();

            $table->index('role');
            $table->index('agent_id');
            $table->index('is_active');
            $table->index('kyc_status');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();

            $table->foreignId('user_id')
                ->nullable()
                ->index();

            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};