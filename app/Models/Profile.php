<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class Profile extends Model
{
    use LogsActivity;
    // تحديد اسم الجدول الفعلي في SQL Server
    protected $table = 'Profile';

    protected $fillable = [
        'code',
        'nom',
        'description',
        'actif'
    ];

    /**
     * علاقة البروفايل مع الحسابات (كل بروفايل يمكن أن يمتلكه العديد من المستخدمين)
     */
    public function utilisateurs()
    {
        return $this->hasMany(Utilisateur::class, 'profile_id');
    }
}