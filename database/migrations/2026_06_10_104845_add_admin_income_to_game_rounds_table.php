<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('game_rounds')) {
            return;
        }

        Schema::table('game_rounds', function (Blueprint $table) {
            if (!Schema::hasColumn('game_rounds', 'admin_income')) {
                $table->decimal('admin_income', 15, 2)->default(0);
            }

            if (!Schema::hasColumn('game_rounds', 'payout_total')) {
                $table->decimal('payout_total', 15, 2)->default(0);
            }

            if (!Schema::hasColumn('game_rounds', 'company_commission_amount')) {
                $table->decimal('company_commission_amount', 15, 2)->default(0);
            }

            if (!Schema::hasColumn('game_rounds', 'agent_commission_amount')) {
                $table->decimal('agent_commission_amount', 15, 2)->default(0);
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('game_rounds')) {
            return;
        }

        Schema::table('game_rounds', function (Blueprint $table) {
            foreach ([
                'agent_commission_amount',
                'company_commission_amount',
                'payout_total',
                'admin_income',
            ] as $column) {
                if (Schema::hasColumn('game_rounds', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};