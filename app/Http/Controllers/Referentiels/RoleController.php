<?php

namespace App\Http\Controllers\Referentiels;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    // شاشة الأدوار المعرفة بالـ Middleware لحماية الشاشة كاملة
    const SCREEN_CODE = 'roles'; 

    public function index()
    {
        $roles = Role::all(); 
        $screen = self::SCREEN_CODE; 
        
        return view('referentiels.roles', compact('roles', 'screen'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:Role,code|max:30',
            'nom' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
        ]);

        Role::create([
            'code' => strtoupper(trim($request->code)), 
            'nom' => $request->nom,
            'description' => $request->description,
            'actif' => 1
        ]);

        return redirect()->back()->with('success', 'Rôle créé avec succès!');
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $request->validate([
            'code' => 'required|max:30|unique:Role,code,' . $id,
            'nom' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
        ]);

        $role->update([
            'code' => strtoupper(trim($request->code)),
            'nom' => $request->nom,
            'description' => $request->description,
        ]);

        return redirect()->back()->with('success', 'Rôle modifié avec succès!');
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        
        // التحقق الصحيح عبر علاقة الموديل المستقرة المتصلة بـ Person
        if($role->persons()->exists()) { 
             return redirect()->back()->with('error', 'Impossible de supprimer ce rôle car il est lié à des employés!');
        }

        $role->delete();
        return redirect()->back()->with('success', 'Rôle supprimé avec succès!');
    }
}