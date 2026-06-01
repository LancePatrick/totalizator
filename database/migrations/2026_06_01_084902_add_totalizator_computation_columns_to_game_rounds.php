<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('game_rounds', function (Blueprint $table) {
            if (!Schema::hasColumn('game_rounds', 'meron_total')) {
                $table->decimal('meron_total', 15, 2)->default(0)->after('status');
            }

            if (!Schema::hasColumn('game_rounds', 'wala_total')) {
                $table->decimal('wala_total', 15, 2)->default(0)->after('meron_total');
            }

            if (!Schema::hasColumn('game_rounds', 'draw_total')) {
                $table->decimal('draw_total', 15, 2)->default(0)->after('wala_total');
            }

            if (!Schema::hasColumn('game_rounds', 'total_pool')) {
                $table->decimal('total_pool', 15, 2)->default(0)->after('draw_total');
            }

            if (!Schema::hasColumn('game_rounds', 'commission_rate')) {
                $table->decimal('commission_rate', 8, 4)->default(5)->after('total_pool');
            }

            if (!Schema::hasColumn('game_rounds', 'commission_amount')) {
                $table->decimal('commission_amount', 15, 2)->default(0)->after('commission_rate');
            }

            if (!Schema::hasColumn('game_rounds', 'net_pool')) {
                $table->decimal('net_pool', 15, 2)->default(0)->after('commission_amount');
            }

            if (!Schema::hasColumn('game_rounds', 'meron_odds')) {
                $table->decimal('meron_odds', 12, 4)->default(0)->after('net_pool');
            }

            if (!Schema::hasColumn('game_rounds', 'wala_odds')) {
                $table->decimal('wala_odds', 12, 4)->default(0)->after('meron_odds');
            }

            if (!Schema::hasColumn('game_rounds', 'draw_odds')) {
                $table->decimal('draw_odds', 12, 4)->default(0)->after('wala_odds');
            }

            if (!Schema::hasColumn('game_rounds', 'payout_total')) {
                $table->decimal('payout_total', 15, 2)->default(0)->after('draw_odds');
            }

            if (!Schema::hasColumn('game_rounds', 'admin_income')) {
                $table->decimal('admin_income', 15, 2)->default(0)->after('payout_total');
            }
        });
    }

    public function down(): void
    {
        // Safe rollback for SQLite.
    }
};