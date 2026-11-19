<?php

namespace App\Http\Controllers\Referentiels;

use App\Http\Controllers\Controller;
use App\Models\Filiale;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; // مهم جداً للتحقق المتوافق مع SQL Server

class FilialeController extends Controller
{
    const SCREEN_CODE = 'filiales'; 

    public function index()
    {
        // جلب البيانات من الجدول المربوط بالموديل
        $filiales = Filiale::all();

        return view('referentiels.filiales', [
            'filiales' => $filiales,
            'screen' => self::SCREEN_CODE
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            // التحقق من عدم تكرار الكود مع تحديد اسم الجدول 'Filiale' كما هو في قاعدة البيانات
            'code_filiale' => ['required', Rule::unique('Filiale', 'code_filiale')],
            'nom_filiale'  => 'required',
            'type_filiale' => 'required',
        ]);

        Filiale::create([
            'code_filiale'   => $request->code_filiale,
            'nom_filiale'    => $request->nom_filiale,
            'type_filiale'   => $request->type_filiale,
            'filiale_bloque' => 0 
        ]);

        return redirect()->back()->with('success', 'Filiale ajoutée avec succès.');
    }

    public function update(Request $request, $id)
    {
        $filiale = Filiale::findOrFail($id);

        $request->validate([
            // استثناء السجل الحالي ($id) عند التحقق من التكرار لحقن الاستعلام بشكل آمن في SQL Server
            'code_filiale' => ['required', Rule::unique('Filiale', 'code_filiale')->ignore($id)],
            'nom_filiale'  => 'required',
            'type_filiale' => 'required',
            'filiale_bloque' => 'required'
        ]);

        $filiale->update([
            'code_filiale'   => $request->code_filiale,
            'nom_filiale'    => $request->nom_filiale,
            'type_filiale'   => $request->type_filiale,
            'filiale_bloque' => $request->filiale_bloque,
        ]);

        return redirect()->back()->with('success', 'Filiale modifiée avec succès.');
    }

    public function destroy($id)
    {
        $filiale = Filiale::findOrFail($id);
        $filiale->delete();

        return redirect()->back()->with('success', 'Filiale supprimée avec succès.');
    }
}