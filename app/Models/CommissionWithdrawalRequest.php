<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommissionWithdrawalRequest extends Model
{
    protected $fillable = [
        'agent_id',
        'admin_id',
        'amount',
        'payment_method',
        'account_name',
        'account_number',
        'notes',
        'status',
        'reviewed_at',
        'reviewed_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'reviewed_at' => 'datetime',
    ];

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}