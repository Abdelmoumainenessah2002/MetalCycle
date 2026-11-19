<?php

namespace App\Http\Controllers\Referentiels;

use App\Http\Controllers\Controller;
use App\Models\Equipement;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; // استدعاء فئة التحقق الآمن لـ SQL Server

class EquipementController extends Controller
{
    const SCREEN_CODE = 'equipements';

    // 1. عرض قائمة المعدات والشاحنات (Read)
    public function index()
    {
        // جلب جميع البيانات مباشرة من قاعدة البيانات
        $equipements = Equipement::all();
        
        return view('referentiels.equipements', [
            'equipements' => $equipements,
            'screen' => self::SCREEN_CODE
        ]);
    }

    // 2. إضافة معدة جديدة (Create)
    public function store(Request $request)
    {
        $request->validate([
            'code' => ['required', Rule::unique('Equipement', 'code')],
            'nom' => 'required',
            'type' => 'required',
        ]);

        Equipement::create([
            'code' => $request->code,
            'nom' => $request->nom,
            'type' => $request->type,
            'marque' => $request->marque,
            'modele' => $request->modele,
            'immatriculation' => $request->immatriculation,
            'numero_serie' => $request->numero_serie,
            'etat' => 'Disponible', 
            'actif' => 1
        ]);

        return redirect()->back()->with('success', 'Équipement ajouté avec succès!');
    }

    // 3. تعديل بيانات المعدة (Update)
    public function update(Request $request, $id)
    {
        $equipement = Equipement::findOrFail($id);

        $request->validate([
            'code' => ['required', Rule::unique('Equipement', 'code')->ignore($id)],
            'nom' => 'required',
            'type' => 'required',
            'etat' => 'required',
        ]);

        $equipement->update([
            'code' => $request->code, // حفظ التحديث الجديد للكود
            'nom' => $request->nom,
            'type' => $request->type,
            'marque' => $request->marque,
            'modele' => $request->modele,
            'immatriculation' => $request->immatriculation,
            'numero_serie' => $request->numero_serie,
            'etat' => $request->etat,
        ]);

        return redirect()->back()->with('success', 'Équipement modifié avec succès!');
    }

    // 4. حذف المعدة (Delete)
    public function destroy($id)
    {
        $equipement = Equipement::findOrFail($id);
        $equipement->delete();

        return redirect()->back()->with('success', 'Équipement supprimé avec succès!');
    }
}