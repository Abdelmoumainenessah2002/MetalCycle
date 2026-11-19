<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class Role extends Model
{
    use LogsActivity;
    // اسم الجدول في SQL Server
    protected $table = 'Role'; 

    protected $primaryKey = 'id';

    protected $fillable = ['code', 'nom', 'description', 'actif'];

    protected $casts = [
        'actif' => 'boolean',
    ];

    // 🌟 الحل السحري لـ SQL Server لمنع حدوث خطأ تحويل الوقت (datetime2)
    protected $dateFormat = 'Y-m-d H:i:s'; 

    // العلاقة الصحيحة مع الموظفين التي أصلحناها سابقاً
    public function persons()
    {
        return $this->hasMany(Person::class, 'role_id'); 
    }
}