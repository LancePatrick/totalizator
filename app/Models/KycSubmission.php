<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KycSubmission extends Model
{
    protected $fillable = [
        'user_id',
        'reviewed_by',
        'full_name',
        'birthdate',
        'id_type',
        'id_number',
        'id_image_path',
        'selfie_image_path',
        'status',
        'admin_notes',
        'reviewed_at',
    ];

    protected $casts = [
        'birthdate' => 'date',
        'reviewed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}