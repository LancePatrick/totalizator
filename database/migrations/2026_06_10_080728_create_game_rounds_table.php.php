<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('game_rounds')) {
            Schema::create('game_rounds', function (Blueprint $table) {
                $table->id();

                $table->foreignId('created_by')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();

                $table->string('round_name');
                $table->string('round_number')->nullable();
                $table->text('video_url')->nullable();

                $table->string('status')->default('waiting')->index();
                $table->string('winning_side')->nullable()->index();

                $table->decimal('meron_total', 15, 2)->default(0);
                $table->decimal('wala_total', 15, 2)->default(0);
                $table->decimal('draw_total', 15, 2)->default(0);

                $table->decimal('total_pool', 15, 2)->default(0);
                $table->decimal('commission_rate', 8, 4)->default(0.05);
                $table->decimal('commission_amount', 15, 2)->default(0);
                $table->decimal('company_commission_amount', 15, 2)->default(0);
                $table->decimal('agent_commission_amount', 15, 2)->default(0);
                $table->decimal('net_pool', 15, 2)->default(0);

                $table->decimal('meron_odds', 12, 4)->default(0);
                $table->decimal('wala_odds', 12, 4)->default(0);
                $table->decimal('draw_odds', 12, 4)->default(0);

                $table->timestamp('opened_at')->nullable();
                $table->timestamp('closed_at')->nullable();
                $table->timestamp('declared_at')->nullable();
                $table->timestamp('ended_at')->nullable();

                $table->timestamps();

                $table->index(['status', 'winning_side']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('game_rounds');
    }
};