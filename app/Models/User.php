<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;

#[Fillable([
    'name',
    'email',
    'password',
    'role',
    'agent_id',
    'agent_code',
    'wallet_balance',
    'is_active',
    'kyc_status',
    'phone',
    'location',
    'last_login_at',
])]
#[Hidden([
    'password',
    'two_factor_secret',
    'two_factor_recovery_codes',
    'remember_token',
])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'wallet_balance' => 'decimal:2',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Role Helpers
    |--------------------------------------------------------------------------
    */

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isAgent(): bool
    {
        return $this->role === 'agent';
    }

    public function isPlayer(): bool
    {
        return $this->role === 'player';
    }

    /*
    |--------------------------------------------------------------------------
    | Management Rules
    |--------------------------------------------------------------------------
    */

    public function canManageUser(User $user): bool
    {
        if ($this->isAdmin()) {
            return $user->id !== $this->id;
        }

        if ($this->isAgent()) {
            return $user->isPlayer() && (int) $user->agent_id === (int) $this->id;
        }

        return false;
    }

    public function canActivateUser(User $user): bool
    {
        return $this->canManageUser($user);
    }

    public function canDeactivateUser(User $user): bool
    {
        return $this->canManageUser($user);
    }

    public function canApprovePlayerMoneyRequest(User $player): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        if ($this->isAgent()) {
            return $player->isPlayer() && (int) $player->agent_id === (int) $this->id;
        }

        return false;
    }

    public function canApproveAgentRequest(User $agent): bool
    {
        return $this->isAdmin() && $agent->isAgent();
    }

    /*
    |--------------------------------------------------------------------------
    | User Relationships
    |--------------------------------------------------------------------------
    */

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function players()
    {
        return $this->hasMany(User::class, 'agent_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Wallet / Request Relationships
    |--------------------------------------------------------------------------
    */

    public function walletTransactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public function moneyRequests()
    {
        return $this->hasMany(MoneyRequest::class, 'user_id');
    }

    public function reviewedMoneyRequests()
    {
        return $this->hasMany(MoneyRequest::class, 'reviewed_by');
    }

    public function withdrawalRequests()
    {
        return $this->hasMany(WithdrawalRequest::class, 'user_id');
    }

    public function reviewedWithdrawalRequests()
    {
        return $this->hasMany(WithdrawalRequest::class, 'reviewed_by');
    }

    /*
    |--------------------------------------------------------------------------
    | KYC Relationships
    |--------------------------------------------------------------------------
    */

    public function kycSubmissions()
    {
        return $this->hasMany(KycSubmission::class);
    }

    public function latestKycSubmission()
    {
        return $this->hasOne(KycSubmission::class)->latestOfMany();
    }

    /*
    |--------------------------------------------------------------------------
    | Game Relationships
    |--------------------------------------------------------------------------
    */

    public function gameBets()
    {
        return $this->hasMany(GameBet::class);
    }

    public function createdGameRounds()
    {
        return $this->hasMany(GameRound::class, 'created_by');
    }

    /*
    |--------------------------------------------------------------------------
    | Display Helpers
    |--------------------------------------------------------------------------
    */

    public function roleLabel(): string
    {
        return match ($this->role) {
            'admin' => 'Admin',
            'agent' => 'Agent',
            default => 'Player',
        };
    }

    public function kycLabel(): string
    {
        return match ($this->kyc_status) {
            'approved' => 'Approved',
            'pending' => 'Pending',
            'rejected' => 'Rejected',
            default => 'Not Submitted',
        };
    }

    public function statusLabel(): string
    {
        return $this->is_active ? 'Active' : 'Inactive';
    }

    public function agentCodeLabel(): string
    {
        return $this->agent_code ?: 'No Agent Code';
    }

    public function assignedAgentName(): string
    {
        return $this->agent?->name ?? 'No Agent Assigned';
    }

    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }
}