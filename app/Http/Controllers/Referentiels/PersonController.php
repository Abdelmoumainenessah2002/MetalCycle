<?php

namespace App\Http\Controllers\Referentiels;

use App\Http\Controllers\Controller;
use App\Models\Person;
use App\Models\Role;
use Illuminate\Http\Request;

class PersonController extends Controller
{
    // الثابت السحري لربطه بنظام الصلاحيات الثلاثي
    const SCREEN_CODE = 'personnel';

    public function index()
    {
        $people = Person::with('role')->get();
        $roles = Role::all();
        
        // تمرير متغير الـ screen للواجهة لكي تستخدمه أزرار الـ Blade
        $screen = self::SCREEN_CODE;
        return view('referentiels.people', compact('people', 'roles', 'screen'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'matricule' => 'required|unique:Person,matricule|max:30',
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'sexe' => 'required|in:M,F',
            'date_naissance' => 'nullable|date', // أضف هذا
            'date_embauche' => 'nullable|date',
            'role_id' => 'required|exists:Role,id',
            'telephone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:150',
            'adresse' => 'nullable|string|max:255',
        ]);

        Person::create([
            'matricule' => $request->matricule,
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'sexe' => $request->sexe,
            'date_naissance' => $request->date_naissance, // أضف هذا
            'date_embauche' => $request->date_embauche,
            'role_id' => $request->role_id,
            'telephone' => $request->telephone,
            'email' => $request->email,
            'adresse' => $request->adresse,
            'actif' => 1
        ]);

        return redirect()->back()->with('success', 'Employé enregistré avec succès!');
    }

    public function update(Request $request, $id)
    {
        $person = Person::findOrFail($id);

        $request->validate([
            'matricule' => 'required|max:30|unique:Person,matricule,' . $id,
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'sexe' => 'required|in:M,F',
            'date_naissance' => 'nullable|date', // أضف هذا
            'date_embauche' => 'nullable|date',  // أضف هذا
            'role_id' => 'required|exists:Role,id',
            'telephone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:150',
            'adresse' => 'nullable|string|max:255',
        ]);

        $person->update($request->all());

        return redirect()->back()->with('success', 'Infos de l\'employé modifiées!');
    }

    public function destroy($id)
    {
        $person = Person::findOrFail($id);
        
        if($person->utilisateur()->exists()) { 
            return redirect()->back()->with('error', 'Impossible de supprimer car cet employé possède un compte utilisateur !');
        }

        $person->delete();
        return redirect()->back()->with('success', 'Employé supprimé avec succès!');
    }
}