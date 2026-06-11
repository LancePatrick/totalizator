<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('game_bets')) {
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

                $table->decimal('odds_at_bet', 10, 2)->default(0);

                // pending, won, lost, refunded, paid
                $table->string('status')->default('pending');

                $table->decimal('payout_amount', 15, 2)->default(0);

                $table->timestamps();

                $table->index('side');
                $table->index('status');
                $table->index('game_round_id');
                $table->index('user_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('game_bets');
    }
};