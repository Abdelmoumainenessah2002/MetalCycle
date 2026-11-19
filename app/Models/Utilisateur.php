<?php

namespace App\Models;

// تم استبدال Model بـ Authenticatable لأن هذا الجدول يُستخدم لتسجيل الدخول (Auth)
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;
use App\Traits\LogsActivity;

class Utilisateur extends Authenticatable
{
    use LogsActivity;
  
    protected $table = 'Utilisateur';

    protected $fillable = [
        'person_id',
        'profile_id', // 1. أضفنا الحقل الجديد هنا لتجنب خطأ MassAssignment
        'username',
        'password_hash',
        'actif',
        'dernier_login'
    ];

    protected $hidden = [
        'password_hash'
    ];

    /**
     * تحديد العلاقة العكسية مع جدول الشخص بشكل صريح لـ SQL Server
     */
    public function person()
    {
        return $this->belongsTo(Person::class, 'person_id');
    }

    /**
     * 2. علاقة المستخدم مع البروفايل التقني (Profile)
     */
    public function profile()
    {
        return $this->belongsTo(Profile::class, 'profile_id');
    }

    /**
     * 3. دالة فحص الصلاحية المعتمدة على الصفحة والعملية معاً لقراءة الجدول الوسيط الثلاثي
     */
    public function canAccess($pageCode, $actionCode)
    {
        // إذا لم يكن للمستخدم بروفايل أو كان البروفايل معطلاً، ارفض الدخول فوراً
        if (!$this->profile || !$this->profile->actif) {
            return false;
        }

        // دعم أسماء العمليات القياسية والسابقة في قاعدة البيانات (Smart Aliases)
        $actionAliases = [
            'validate'  => ['validate', 'validatee'],
            'validatee' => ['validate', 'validatee'],
            'cancel'    => ['cancel', 'annuler'],
            'annuler'   => ['cancel', 'annuler'],
        ];

        $actions = $actionAliases[$actionCode] ?? [$actionCode];

        // استعلام مباشر وسريع داخل الجدول الوسيط الثلاثي (profile_page_action)
        return DB::table('profile_page_action')
            ->join('SystemPage', 'profile_page_action.page_id', '=', 'SystemPage.id')
            ->join('SystemAction', 'profile_page_action.action_id', '=', 'SystemAction.id')
            ->where('profile_page_action.profile_id', $this->profile_id)
            ->where('SystemPage.code', $pageCode)
            ->whereIn('SystemAction.code', $actions)
            ->exists();
    }

    public function getAuthPassword()
{
    return $this->password_hash;
}


public function getNameAttribute()
    {
        if ($this->person) {
            $fullName = trim(($this->person->nom ?? '') . ' ' . ($this->person->prenom ?? ''));
            return $fullName !== '' ? $fullName : $this->username;
        }
        return $this->username;
    }

    /**
     * 4. Accessor لجلب اسم الدور/البروفايل مباشرة عبر $user->role_name
     */
    public function getRoleNameAttribute()
    {
        // 1. إذا كانت العلاقة مباشرة مع Role
        if ($this->relationLoaded('role') && $this->role) {
            return $this->role->nom;
        }
    
        // 2. إذا كان الدور مرتبطاً بجدول Person
        if ($this->person && $this->person->role) {
            return $this->person->role->nom;
        }
    
        // 3. إذا كان مرتبطاً بـ Profile
        if ($this->profile) {
            return $this->profile->nom ?? $this->profile->nom_profile ?? $this->profile->libelle;
        }
    
        return 'Utilisateur';
    }
}