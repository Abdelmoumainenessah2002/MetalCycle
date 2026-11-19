<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class Person extends Model
{
    use LogsActivity;
    // 🌟 تحديد اسم الجدول بدقة كما هو في SQL Server
    protected $table = 'Person'; 

    protected $primaryKey = 'id';

    protected $fillable = [
        'matricule', 'nom', 'prenom', 'sexe', 'date_naissance', 
        'date_embauche', 'role_id', 'telephone', 'email', 'adresse', 'actif'
    ];

    // 🌟 السطر السحري لمنع انهيار السيرفر بسبب حقول الوقت datetime2 في SQL Server
    protected $dateFormat = 'Y-m-d H:i:s'; 

    protected $casts = [
        'actif' => 'boolean',
        'date_naissance' => 'date',
        'date_embauche' => 'date',
    ];

    // 🌟 العلاقة العكسية التي يستدعيها الـ PersonController بنجاح
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    // علاقة المستخدم (إذا كانت موجودة)
    public function utilisateur()
    {
        return $this->hasOne(Utilisateur::class, 'person_id'); // تأكد من اسم الحقل الرابط هنا
    }
}