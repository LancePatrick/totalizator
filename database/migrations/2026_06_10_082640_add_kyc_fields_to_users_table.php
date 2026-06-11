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
            if (!Schema::hasColumn('users', 'kyc_status')) {
                $table->string('kyc_status')->nullable()->default('pending');
            }

            if (!Schema::hasColumn('users', 'kyc_rejection_reason')) {
                $table->text('kyc_rejection_reason')->nullable();
            }

            if (!Schema::hasColumn('users', 'kyc_full_name')) {
                $table->string('kyc_full_name')->nullable();
            }

            if (!Schema::hasColumn('users', 'kyc_birthdate')) {
                $table->date('kyc_birthdate')->nullable();
            }

            if (!Schema::hasColumn('users', 'kyc_address')) {
                $table->text('kyc_address')->nullable();
            }

            if (!Schema::hasColumn('users', 'kyc_valid_id_type')) {
                $table->string('kyc_valid_id_type')->nullable();
            }

            if (!Schema::hasColumn('users', 'kyc_valid_id_number')) {
                $table->string('kyc_valid_id_number')->nullable();
            }

            if (!Schema::hasColumn('users', 'kyc_valid_id_image')) {
                $table->string('kyc_valid_id_image')->nullable();
            }

            if (!Schema::hasColumn('users', 'kyc_selfie_image')) {
                $table->string('kyc_selfie_image')->nullable();
            }

            if (!Schema::hasColumn('users', 'kyc_submitted_at')) {
                $table->timestamp('kyc_submitted_at')->nullable();
            }

            if (!Schema::hasColumn('users', 'kyc_reviewed_at')) {
                $table->timestamp('kyc_reviewed_at')->nullable();
            }

            if (!Schema::hasColumn('users', 'kyc_reviewed_by')) {
                $table->foreignId('kyc_reviewed_by')
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
            if (Schema::hasColumn('users', 'kyc_reviewed_by')) {
                $table->dropConstrainedForeignId('kyc_reviewed_by');
            }

            $columns = [
                'kyc_reviewed_at',
                'kyc_submitted_at',
                'kyc_selfie_image',
                'kyc_valid_id_image',
                'kyc_valid_id_number',
                'kyc_valid_id_type',
                'kyc_address',
                'kyc_birthdate',
                'kyc_full_name',
                'kyc_rejection_reason',
                'kyc_status',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};