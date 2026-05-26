<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('game_rounds', function (Blueprint $table) {
            $table->id();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('round_name');
            $table->string('round_number')->nullable();

            // Admin can paste video URL here
            $table->text('video_url')->nullable();

            // waiting, open, closed, ended, settled, cancelled
            $table->string('status')->default('waiting');

            // Totals
            $table->decimal('meron_total', 15, 2)->default(0);
            $table->decimal('wala_total', 15, 2)->default(0);
            $table->decimal('draw_total', 15, 2)->default(0);
            $table->decimal('total_pool', 15, 2)->default(0);

            // Commission and net pool
            $table->decimal('commission_rate', 5, 2)->default(10);
            $table->decimal('commission_amount', 15, 2)->default(0);
            $table->decimal('net_pool', 15, 2)->default(0);

            // Auto odds
            $table->decimal('meron_odds', 10, 2)->default(0);
            $table->decimal('wala_odds', 10, 2)->default(0);
            $table->decimal('draw_odds', 10, 2)->default(0);

            // Result
            $table->enum('winning_side', ['meron', 'wala', 'draw'])->nullable();

            $table->timestamp('started_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->timestamp('settled_at')->nullable();

            $table->timestamps();

            $table->index('status');
            $table->index('winning_side');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_rounds');
    }
};