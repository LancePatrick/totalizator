<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('game_bets', function (Blueprint $table) {
            $table->id();

            $table->foreignId('game_round_id')
                ->constrained('game_rounds')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->enum('side', ['meron', 'wala', 'draw']);
            $table->decimal('amount', 15, 2);

            // Odds when player placed bet
            $table->decimal('odds_at_bet', 10, 2)->default(0);

            // pending, won, lost, refunded
            $table->string('status')->default('pending');

            $table->decimal('payout_amount', 15, 2)->default(0);

            $table->timestamps();

            $table->index('side');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_bets');
    }
};