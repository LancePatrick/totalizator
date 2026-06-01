<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commission_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')->constrained('users')->cascadeOnDelete();
            $table->string('type');
            $table->string('direction');
            $table->decimal('amount', 12, 2);
            $table->decimal('balance_before', 12, 2)->default(0);
            $table->decimal('balance_after', 12, 2)->default(0);
            $table->nullableMorphs('reference');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['agent_id', 'direction']);
            $table->index(['agent_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commission_transactions');
    }
};