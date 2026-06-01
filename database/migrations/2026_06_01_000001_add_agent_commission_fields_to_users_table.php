<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'commission_balance')) {
                $table->decimal('commission_balance', 12, 2)->default(0)->after('wallet_balance');
            }

            if (!Schema::hasColumn('users', 'agent_code')) {
                $table->string('agent_code')->nullable()->unique()->after('commission_balance');
            }
        });

        User::where('role', 'agent')
            ->whereNull('agent_code')
            ->chunkById(100, function ($agents) {
                foreach ($agents as $agent) {
                    do {
                        $code = 'AGT-' . Str::upper(Str::random(8));
                    } while (User::where('agent_code', $code)->exists());

                    $agent->forceFill([
                        'agent_code' => $code,
                    ])->save();
                }
            });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'commission_balance')) {
                $table->dropColumn('commission_balance');
            }

            if (Schema::hasColumn('users', 'agent_code')) {
                $table->dropUnique(['agent_code']);
                $table->dropColumn('agent_code');
            }
        });
    }
};