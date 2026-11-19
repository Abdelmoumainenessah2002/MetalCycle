<?php

namespace App\Http\Controllers\Referentiels;

use App\Http\Controllers\Controller;
use App\Models\Utilisateur; 
use App\Models\Person;
use App\Models\Role;
use App\Models\Profile; // استدعاء موديل البروفايل
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    // تثبيت نوع الصفحة كمتغير ثابت ليقرأه الـ Middleware والـ Blade تلقائياً
    const SCREEN_CODE = 'utilisateurs'; 

    /**
     * عرض قائمة المستخدمين والأدوار والموظفين والبروفايلات
     */
    public function index()
    {
        // جلب المستخدمين مع علاقة الشخص والـ Role وعلاقة الـ Profile الجديدة
        $utilisateurs = Utilisateur::with(['person.role', 'profile'])->get();
        $roles = Role::all();
        $profiles = Profile::where('actif', 1)->get(); // جلب البروفايلات النشطة فقط
        
        // جلب الموظفين الذين ليس لديهم حساب مستخدم حتى الآن لعرضهم في القائمة المنسدلة
        $personnesSansCompte = Person::doesntHave('utilisateur')->get();

        return view('referentiels.users', [
            'utilisateurs' => $utilisateurs,
            'roles' => $roles,
            'profiles' => $profiles,
            'personnesSansCompte' => $personnesSansCompte,
            'screen' => self::SCREEN_CODE 
        ]);
    }

    /**
     * حفظ مستخدم جديد وربطه بالموظف والبروفايل المختار
     */
    public function store(Request $request)
    {
        $request->validate([
            'person_id' => 'required|exists:Person,id|unique:Utilisateur,person_id',
            'profile_id' => 'required|exists:Profile,id', // التحقق من البروفايل
            'username' => 'required|unique:Utilisateur,username|max:100',
            'password' => 'required|min:6',
        ]);

        Utilisateur::create([
            'person_id' => $request->person_id,
            'profile_id' => $request->profile_id, // حفظ البروفايل المختار
            'username' => $request->username,
            'password_hash' => Hash::make($request->password),
            'actif' => 1
        ]);

        return redirect()->back()->with('success', 'Compte utilisateur créé avec succès !');
    }

    /**
     * تحديث بيانات مستخدم موجود مع بروفايل الصلاحيات الخاص به
     */
    public function update(Request $request, $id)
    {
        $utilisateur = Utilisateur::findOrFail($id);

        $request->validate([
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'role_id' => 'required|exists:Role,id',
            'profile_id' => 'required|exists:Profile,id', // التحقق من البروفايل عند التعديل
            'username' => 'required|max:100|unique:Utilisateur,username,' . $id,
        ]);

        DB::transaction(function() use ($request, $utilisateur) {
            // تحديث بيانات الشخص الأساسية (الاسم واللقب والوظيفة)
            $utilisateur->person->update([
                'nom' => $request->nom,
                'prenom' => $request->prenom,
                'role_id' => $request->role_id
            ]);

            $userData = [
                'username' => $request->username,
                'profile_id' => $request->profile_id // تحديث البروفايل التقني
            ];

            if ($request->filled('password')) {
                $request->validate(['password' => 'min:6']);
                $userData['password_hash'] = Hash::make($request->password);
            }

            $utilisateur->update($userData);
        });

        return redirect()->back()->with('success', 'Utilisateur modifié avec succès !');
    }

    /**
     * حذف الحساب فقط
     */
    public function destroy($id)
    {
        $utilisateur = Utilisateur::findOrFail($id);
        $utilisateur->delete(); 

        return redirect()->back()->with('success', 'Compte utilisateur supprimé avec succès !');
    }
}