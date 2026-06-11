<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true);
            }

            if (!Schema::hasColumn('users', 'deactivation_reason')) {
                $table->text('deactivation_reason')->nullable();
            }

            if (!Schema::hasColumn('users', 'deactivated_at')) {
                $table->timestamp('deactivated_at')->nullable();
            }

            if (!Schema::hasColumn('users', 'deactivated_by')) {
                $table->foreignId('deactivated_by')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('users', 'appeal_reason')) {
                $table->text('appeal_reason')->nullable();
            }

            if (!Schema::hasColumn('users', 'appeal_image')) {
                $table->string('appeal_image')->nullable();
            }

            if (!Schema::hasColumn('users', 'appeal_status')) {
                $table->string('appeal_status')->nullable();
            }

            if (!Schema::hasColumn('users', 'appeal_submitted_at')) {
                $table->timestamp('appeal_submitted_at')->nullable();
            }

            if (!Schema::hasColumn('users', 'appeal_reviewed_at')) {
                $table->timestamp('appeal_reviewed_at')->nullable();
            }

            if (!Schema::hasColumn('users', 'appeal_reviewed_by')) {
                $table->foreignId('appeal_reviewed_by')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'appeal_reviewed_by')) {
                $table->dropConstrainedForeignId('appeal_reviewed_by');
            }

            if (Schema::hasColumn('users', 'deactivated_by')) {
                $table->dropConstrainedForeignId('deactivated_by');
            }

            $columns = [
                'appeal_reviewed_at',
                'appeal_submitted_at',
                'appeal_status',
                'appeal_image',
                'appeal_reason',
                'deactivated_at',
                'deactivation_reason',
                'is_active',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};