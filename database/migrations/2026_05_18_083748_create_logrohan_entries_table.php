<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logrohan_entries', function (Blueprint $table) {
            $table->id();

            $table->foreignId('game_round_id')
                ->constrained('game_rounds')
                ->cascadeOnDelete();

            $table->string('round_number')->nullable();
            $table->enum('result', ['meron', 'wala', 'draw']);

            $table->timestamps();

            $table->index('result');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logrohan_entries');
    }
};