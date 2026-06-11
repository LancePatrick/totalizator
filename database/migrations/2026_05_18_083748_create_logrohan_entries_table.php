<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('logrohan_entries')) {
            Schema::create('logrohan_entries', function (Blueprint $table) {
                $table->id();

                $table->foreignId('game_round_id')
                    ->constrained('game_rounds')
                    ->cascadeOnDelete();

                $table->string('round_number')->nullable();
                $table->enum('result', ['meron', 'wala', 'draw']);

                $table->timestamps();

                $table->index('game_round_id');
                $table->index('result');
            });

            return;
        }

        Schema::table('logrohan_entries', function (Blueprint $table) {
            if (!Schema::hasColumn('logrohan_entries', 'game_round_id')) {
                $table->foreignId('game_round_id')
                    ->after('id')
                    ->constrained('game_rounds')
                    ->cascadeOnDelete();
            }

            if (!Schema::hasColumn('logrohan_entries', 'round_number')) {
                $table->string('round_number')->nullable()->after('game_round_id');
            }

            if (!Schema::hasColumn('logrohan_entries', 'result')) {
                $table->enum('result', ['meron', 'wala', 'draw'])->after('round_number');
            }

            if (!Schema::hasColumn('logrohan_entries', 'created_at')) {
                $table->timestamps();
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logrohan_entries');
    }
};