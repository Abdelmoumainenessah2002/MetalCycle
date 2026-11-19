<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $table = 'activity_logs'; // ربط الموديل بالجدول اليدوي
protected $dateFormat = 'Y-m-d\TH:i:s';
    protected $fillable = [
        'user_id', 'action', 'model_type', 'model_id',
        'description', 'old_values', 'new_values', 'ip_address', 'user_agent'
    ];

    public function user()
    {
        return $this->belongsTo(Utilisateur::class, 'user_id');
    }
}