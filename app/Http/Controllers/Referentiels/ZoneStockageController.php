<?php

namespace App\Http\Controllers\Referentiels;

use App\Http\Controllers\Controller;
use App\Models\ZoneStockage;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ZoneStockageController extends Controller
{
    const SCREEN_CODE = 'zones';

    public function index()
    {
        $zones = ZoneStockage::all();
        $screen = self::SCREEN_CODE;

        return view('referentiels.zones', compact('zones', 'screen'));
    }

    public function store(Request $request)
    {
        // التحقق من البيانات بنفس أسلوب FournisseurController
        $validated = $request->validate([
            'code'         => ['required', 'string', 'max:30', Rule::unique('ZoneStockage', 'code')],
            'nom'          => 'required|string|max:100',
            'capacite_max' => 'nullable|numeric',
            'unite'        => 'nullable|string|max:20',
            'description'  => 'nullable|string|max:255',
            'actif'        => 'nullable',
        ]);

        // معالجة القيمة المنطقية (Checkbox)
        $validated['actif'] = $request->has('actif') ? 1 : 0;
        $validated['created_at'] = now();

        ZoneStockage::create($validated);

        return redirect()->back()->with('success', 'Zone ajoutée avec succès.');
    }

    public function update(Request $request, $id)
    {
        $zone = ZoneStockage::findOrFail($id);

        $validated = $request->validate([
            'code'         => ['required', 'string', 'max:30', Rule::unique('ZoneStockage', 'code')->ignore($id)],
            'nom'          => 'required|string|max:100',
            'capacite_max' => 'nullable|numeric',
            'unite'        => 'nullable|string|max:20',
            'description'  => 'nullable|string|max:255',
            'actif'        => 'nullable',
        ]);

        $validated['actif'] = $request->has('actif') ? 1 : 0;
        $validated['updated_at'] = now();

        $zone->update($validated);

        return redirect()->back()->with('success', 'Zone modifiée avec succès.');
    }

    public function destroy($id)
    {
        $zone = ZoneStockage::findOrFail($id);
        $zone->delete();

        return redirect()->back()->with('success', 'Zone supprimée avec succès.');
    }
}