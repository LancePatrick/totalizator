<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'commission_balance')) {
            Schema::table('users', function (Blueprint $table) {
                $table->decimal('commission_balance', 15, 2)->default(0)->after('wallet_balance');
            });
        }

        if (Schema::hasTable('commission_transactions')) {
            Schema::table('commission_transactions', function (Blueprint $table) {
                if (!Schema::hasColumn('commission_transactions', 'agent_id')) {
                    $table->unsignedBigInteger('agent_id')->nullable()->index();
                }

                if (!Schema::hasColumn('commission_transactions', 'player_id')) {
                    $table->unsignedBigInteger('player_id')->nullable()->index();
                }

                if (!Schema::hasColumn('commission_transactions', 'game_round_id')) {
                    $table->unsignedBigInteger('game_round_id')->nullable()->index();
                }

                if (!Schema::hasColumn('commission_transactions', 'game_bet_id')) {
                    $table->unsignedBigInteger('game_bet_id')->nullable()->index();
                }

                if (!Schema::hasColumn('commission_transactions', 'reference_type')) {
                    $table->string('reference_type')->nullable()->index();
                }

                if (!Schema::hasColumn('commission_transactions', 'reference_id')) {
                    $table->unsignedBigInteger('reference_id')->nullable()->index();
                }

                if (!Schema::hasColumn('commission_transactions', 'direction')) {
                    $table->string('direction')->default('credit')->index();
                }

                if (!Schema::hasColumn('commission_transactions', 'balance_before')) {
                    $table->decimal('balance_before', 15, 2)->default(0);
                }

                if (!Schema::hasColumn('commission_transactions', 'balance_after')) {
                    $table->decimal('balance_after', 15, 2)->default(0);
                }

                if (!Schema::hasColumn('commission_transactions', 'description')) {
                    $table->text('description')->nullable();
                }
            });
        }

        if (!Schema::hasTable('commission_transactions')) {
            return;
        }

        $bets = DB::table('game_bets as gb')
            ->join('users as player', 'player.id', '=', 'gb.user_id')
            ->leftJoin('game_rounds as gr', 'gr.id', '=', 'gb.game_round_id')
            ->whereNotNull('player.agent_id')
            ->whereNotIn('gb.status', ['refunded', 'cancelled'])
            ->where(function ($query) {
                $query->whereNull('gr.winning_side')
                    ->orWhere('gr.winning_side', '!=', 'cancelled');
            })
            ->select([
                'gb.id as bet_id',
                'gb.game_round_id',
                'gb.user_id as player_id',
                'gb.amount',
                'gb.side',
                'player.agent_id',
            ])
            ->orderBy('gb.id')
            ->get();

        foreach ($bets as $bet) {
            $exists = DB::table('commission_transactions')
                ->where('agent_id', $bet->agent_id)
                ->where('type', 'player_bet_commission')
                ->where(function ($query) use ($bet) {
                    $query->where('game_bet_id', $bet->bet_id)
                        ->orWhere(function ($q) use ($bet) {
                            $q->where('reference_type', 'App\\Models\\GameBet')
                                ->where('reference_id', $bet->bet_id);
                        });
                })
                ->exists();

            if ($exists) {
                continue;
            }

            $commissionAmount = round((float) $bet->amount * 0.02, 2);

            if ($commissionAmount <= 0) {
                continue;
            }

            $balanceBefore = (float) DB::table('users')
                ->where('id', $bet->agent_id)
                ->value('commission_balance');

            $balanceAfter = round($balanceBefore + $commissionAmount, 2);

            DB::table('users')
                ->where('id', $bet->agent_id)
                ->update([
                    'commission_balance' => $balanceAfter,
                    'updated_at' => now(),
                ]);

            $payload = [
                'user_id' => $bet->agent_id,
                'agent_id' => $bet->agent_id,
                'player_id' => $bet->player_id,
                'game_round_id' => $bet->game_round_id,
                'game_bet_id' => $bet->bet_id,
                'type' => 'player_bet_commission',
                'direction' => 'credit',
                'amount' => $commissionAmount,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'reference_type' => 'App\\Models\\GameBet',
                'reference_id' => $bet->bet_id,
                'description' => 'Agent 2% commission from player bet.',
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $payload = collect($payload)
                ->filter(function ($value, $column) {
                    return Schema::hasColumn('commission_transactions', $column);
                })
                ->all();

            DB::table('commission_transactions')->insert($payload);
        }
    }

    public function down(): void
    {
        //
    }
};