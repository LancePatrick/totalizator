<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'agent_id',
        'agent_code',
        'wallet_balance',
        'commission_balance',
        'is_active',
        'phone',
        'location',
        'last_login_at',

        /*
        |--------------------------------------------------------------------------
        | Account Deactivation / Appeal Fields
        |--------------------------------------------------------------------------
        */
        'deactivation_reason',
        'deactivated_at',
        'deactivated_by',
        'appeal_reason',
        'appeal_status',
        'appeal_submitted_at',
        'appeal_reviewed_at',
        'appeal_reviewed_by',

        /*
        |--------------------------------------------------------------------------
        | KYC Fields
        |--------------------------------------------------------------------------
        */
        'kyc_status',
        'kyc_rejection_reason',
        'kyc_full_name',
        'kyc_birthdate',
        'kyc_address',
        'kyc_valid_id_type',
        'kyc_valid_id_number',
        'kyc_valid_id_image',
        'kyc_selfie_image',
        'kyc_submitted_at',
        'kyc_reviewed_at',
        'kyc_reviewed_by',
    ];

    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'wallet_balance' => 'decimal:2',
            'commission_balance' => 'decimal:2',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',

            'deactivated_at' => 'datetime',
            'appeal_submitted_at' => 'datetime',
            'appeal_reviewed_at' => 'datetime',

            'kyc_birthdate' => 'date',
            'kyc_submitted_at' => 'datetime',
            'kyc_reviewed_at' => 'datetime',
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
    | KYC Helpers
    |--------------------------------------------------------------------------
    */

    public function isKycApproved(): bool
    {
        return $this->kyc_status === 'approved';
    }

    public function isKycPending(): bool
    {
        return $this->kyc_status === 'pending';
    }

    public function isKycRejected(): bool
    {
        return $this->kyc_status === 'rejected';
    }

    public function needsKyc(): bool
    {
        return $this->isPlayer() && $this->kyc_status !== 'approved';
    }

    /*
    |--------------------------------------------------------------------------
    | Account Status Helpers
    |--------------------------------------------------------------------------
    */

    public function isAccountActive(): bool
    {
        return (bool) $this->is_active;
    }

    public function isAccountInactive(): bool
    {
        return !$this->isAccountActive();
    }

    public function hasPendingAppeal(): bool
    {
        return $this->appeal_status === 'pending';
    }

    public function appealLabel(): string
    {
        return match ($this->appeal_status) {
            'pending' => 'Pending',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            default => 'No Appeal',
        };
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

    public function deactivatedBy()
    {
        return $this->belongsTo(User::class, 'deactivated_by');
    }

    public function appealReviewedBy()
    {
        return $this->belongsTo(User::class, 'appeal_reviewed_by');
    }

    /*
    |--------------------------------------------------------------------------
    | Wallet / Request Relationships
    |--------------------------------------------------------------------------
    */

    public function walletTransactions()
    {
        return $this->hasMany(WalletTransaction::class, 'user_id');
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
    | Commission Relationships
    |--------------------------------------------------------------------------
    */

    public function commissionTransactions()
    {
        return $this->hasMany(CommissionTransaction::class, 'agent_id');
    }

    public function commissionWithdrawals()
    {
        return $this->hasMany(CommissionWithdrawalRequest::class, 'agent_id');
    }

    /*
    |--------------------------------------------------------------------------
    | KYC Relationships
    |--------------------------------------------------------------------------
    */

    public function kycSubmissions()
    {
        return $this->hasMany(KycSubmission::class, 'user_id');
    }

    public function latestKycSubmission()
    {
        return $this->hasOne(KycSubmission::class, 'user_id')->latestOfMany();
    }

    /*
    |--------------------------------------------------------------------------
    | Game Relationships
    |--------------------------------------------------------------------------
    */

    public function gameBets()
    {
        return $this->hasMany(GameBet::class, 'user_id');
    }

    public function createdGameRounds()
    {
        return $this->hasMany(GameRound::class, 'created_by');
    }

    /*
    |--------------------------------------------------------------------------
    | Agent Registration Link
    |--------------------------------------------------------------------------
    */

    public function getRegistrationLinkAttribute(): ?string
    {
        if (!$this->agent_code) {
            return null;
        }

        return url('/register?agent=' . $this->agent_code);
    }

    public function ensureAgentCode(): string
    {
        if ($this->agent_code) {
            return $this->agent_code;
        }

        do {
            $code = 'AGT-' . Str::upper(Str::random(8));
        } while (self::where('agent_code', $code)->exists());

        $this->forceFill([
            'agent_code' => $code,
        ])->save();

        return $code;
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
        $name = trim((string) $this->name);

        if ($name === '') {
            return 'U';
        }

        $parts = preg_split('/\s+/', $name);

        if (count($parts) >= 2) {
            return Str::upper(
                Str::substr($parts[0], 0, 1) .
                Str::substr($parts[1], 0, 1)
            );
        }

        return Str::upper(Str::substr($name, 0, 2));
    }
}