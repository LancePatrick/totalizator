<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('withdrawal_requests')) {
            Schema::table('withdrawal_requests', function (Blueprint $table) {
                if (Schema::hasColumn('withdrawal_requests', 'player_id')) {
                    $table->unsignedBigInteger('player_id')->nullable()->change();
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('withdrawal_requests')) {
            Schema::table('withdrawal_requests', function (Blueprint $table) {
                if (Schema::hasColumn('withdrawal_requests', 'player_id')) {
                    $table->unsignedBigInteger('player_id')->nullable(false)->change();
                }
            });
        }
    }
};