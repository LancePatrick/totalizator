<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('withdrawal_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('withdrawal_requests', 'user_id')) {
                $table->foreignId('user_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('users')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('withdrawal_requests', 'agent_id')) {
                $table->foreignId('agent_id')
                    ->nullable()
                    ->after('user_id')
                    ->constrained('users')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('withdrawal_requests', 'admin_id')) {
                $table->foreignId('admin_id')
                    ->nullable()
                    ->after('agent_id')
                    ->constrained('users')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('withdrawal_requests', 'account_name')) {
                $table->string('account_name')->nullable()->after('payment_method');
            }

            if (!Schema::hasColumn('withdrawal_requests', 'account_number')) {
                $table->string('account_number')->nullable()->after('account_name');
            }

            if (!Schema::hasColumn('withdrawal_requests', 'admin_notes')) {
                $table->text('admin_notes')->nullable()->after('notes');
            }

            if (!Schema::hasColumn('withdrawal_requests', 'reviewed_by')) {
                $table->foreignId('reviewed_by')
                    ->nullable()
                    ->after('status')
                    ->constrained('users')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('withdrawal_requests', 'reviewed_at')) {
                $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
            }
        });

        /*
        |--------------------------------------------------------------------------
        | Copy old player_id to user_id if your old table used player_id
        |--------------------------------------------------------------------------
        */
        if (
            Schema::hasColumn('withdrawal_requests', 'player_id') &&
            Schema::hasColumn('withdrawal_requests', 'user_id')
        ) {
            DB::table('withdrawal_requests')
                ->whereNull('user_id')
                ->update([
                    'user_id' => DB::raw('player_id'),
                ]);
        }
    }

    public function down(): void
    {
        // Safe rollback: do not drop columns because SQLite may fail on constrained columns.
    }
};