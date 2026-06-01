<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    protected $fillable = [
        'user_id',
        'admin_id',
        'type',
        'direction',
        'amount',
        'balance_before',
        'balance_after',
        'reference_type',
        'reference_id',
        'description',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function reference()
    {
        return $this->morphTo(null, 'reference_type', 'reference_id');
    }

    public function isCredit(): bool
    {
        return $this->direction === 'credit';
    }

    public function isDebit(): bool
    {
        return $this->direction === 'debit';
    }

    public function getFormattedTypeAttribute(): string
    {
        return strtoupper(str_replace('_', ' ', $this->type ?? ''));
    }

    public function getFormattedDirectionAttribute(): string
    {
        return strtoupper($this->direction ?? '');
    }

    public function getSignedAmountAttribute(): string
    {
        $sign = $this->direction === 'credit' ? '+' : '-';

        return $sign . ' ₱' . number_format((float) $this->amount, 2);
    }
}