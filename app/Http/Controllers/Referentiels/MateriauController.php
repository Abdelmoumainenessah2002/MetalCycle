<?php

namespace App\Http\Controllers\Referentiels;

use App\Http\Controllers\Controller;
use App\Models\Materiau;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; // استدعاء مكتبة الفاليديشن الآمنة

class MateriauController extends Controller
{
    const SCREEN_CODE = 'materiaux';

    public function index()
    {
        $materiaux = Materiau::all();
        
        return view('referentiels.materiaux', [
            'materiaux' => $materiaux,
            'screen' => self::SCREEN_CODE
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            // استخدام التحقق المتوافق مع SQL Server لمنع التكرار
            'code' => ['required', Rule::unique('Materiau', 'code')],
            'nom' => 'required|string|max:100',
            'categorie' => 'required|in:Ferreux,Non Ferreux',
            'unite' => 'required|string|max:20',
            'description' => 'nullable|string|max:255'
        ]);

        Materiau::create([
            'code' => $request->code,
            'nom' => $request->nom,
            'categorie' => $request->categorie,
            'unite' => $request->unite,
            'description' => $request->description,
            'actif' => 1 // افتراضي نشط
        ]);

        return redirect()->back()->with('success', 'Matériau ajouté avec succès!');
    }

    public function update(Request $request, $id)
    {
        $materiau = Materiau::findOrFail($id);

        $request->validate([
            // تحديث الكود مع استثناء السجل الحالي ($id) وبطريقة آمنة لـ SQL Server
            'code' => ['required', Rule::unique('Materiau', 'code')->ignore($id)],
            'nom' => 'required|string|max:100',
            'categorie' => 'required|in:Ferreux,Non Ferreux',
            'unite' => 'required|string|max:20',
            'description' => 'nullable|string|max:255'
        ]);
        
        $materiau->update([
            'code' => $request->code, // حفظ التحديث على الكود أيضاً
            'nom' => $request->nom,
            'categorie' => $request->categorie,
            'unite' => $request->unite,
            'description' => $request->description
        ]);

        return redirect()->back()->with('success', 'Matériau modifié avec succès!');
    }

    public function destroy($id)
    {
        $materiau = Materiau::findOrFail($id);
        $materiau->delete();

        return redirect()->back()->with('success', 'Matériau supprimé avec succès!');
    }
}