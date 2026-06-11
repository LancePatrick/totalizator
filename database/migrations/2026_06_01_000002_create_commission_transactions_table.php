<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('commission_transactions')) {
            Schema::create('commission_transactions', function (Blueprint $table) {
                $table->id();

                $table->foreignId('user_id')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();

                $table->foreignId('agent_id')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();

                $table->foreignId('player_id')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();

                $table->foreignId('game_round_id')
                    ->nullable()
                    ->constrained('game_rounds')
                    ->nullOnDelete();

                $table->foreignId('game_bet_id')
                    ->nullable()
                    ->constrained('game_bets')
                    ->nullOnDelete();

                $table->string('type')->index();
                $table->string('direction')->default('credit')->index();

                $table->decimal('amount', 15, 2)->default(0);
                $table->decimal('balance_before', 15, 2)->default(0);
                $table->decimal('balance_after', 15, 2)->default(0);

                $table->text('description')->nullable();
                $table->json('meta')->nullable();

                $table->timestamps();

                $table->index(['type', 'direction']);
                $table->index(['agent_id', 'type']);
                $table->index(['created_at']);
            });

            return;
        }

        Schema::table('commission_transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('commission_transactions', 'user_id')) {
                $table->foreignId('user_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('users')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('commission_transactions', 'agent_id')) {
                $table->foreignId('agent_id')
                    ->nullable()
                    ->after('user_id')
                    ->constrained('users')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('commission_transactions', 'player_id')) {
                $table->foreignId('player_id')
                    ->nullable()
                    ->after('agent_id')
                    ->constrained('users')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('commission_transactions', 'game_round_id')) {
                $table->foreignId('game_round_id')
                    ->nullable()
                    ->after('player_id')
                    ->constrained('game_rounds')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('commission_transactions', 'game_bet_id')) {
                $table->foreignId('game_bet_id')
                    ->nullable()
                    ->after('game_round_id')
                    ->constrained('game_bets')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('commission_transactions', 'type')) {
                $table->string('type')->index()->after('game_bet_id');
            }

            if (!Schema::hasColumn('commission_transactions', 'direction')) {
                $table->string('direction')->default('credit')->index()->after('type');
            }

            if (!Schema::hasColumn('commission_transactions', 'amount')) {
                $table->decimal('amount', 15, 2)->default(0)->after('direction');
            }

            if (!Schema::hasColumn('commission_transactions', 'balance_before')) {
                $table->decimal('balance_before', 15, 2)->default(0)->after('amount');
            }

            if (!Schema::hasColumn('commission_transactions', 'balance_after')) {
                $table->decimal('balance_after', 15, 2)->default(0)->after('balance_before');
            }

            if (!Schema::hasColumn('commission_transactions', 'description')) {
                $table->text('description')->nullable()->after('balance_after');
            }

            if (!Schema::hasColumn('commission_transactions', 'meta')) {
                $table->json('meta')->nullable()->after('description');
            }

            if (!Schema::hasColumn('commission_transactions', 'created_at')) {
                $table->timestamps();
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commission_transactions');
    }
};