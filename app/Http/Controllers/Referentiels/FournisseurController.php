<?php

namespace App\Http\Controllers\Referentiels;

use App\Http\Controllers\Controller;
use App\Models\Fournisseur;
use App\Models\Filiale;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FournisseurController extends Controller
{
    const SCREEN_CODE = 'fournisseurs';

    public function index()
    {
        $fournisseurs = Fournisseur::with('filiale')->get();
        $filiales = Filiale::all(); 
        
        return view('referentiels.fournisseurs', [
            'fournisseurs' => $fournisseurs,
            'filiales' => $filiales,
            'screen' => self::SCREEN_CODE
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // استخدام Rule لتحديد اسم الجدول الفعلي في SQL Server
            'code_fournisseur'  => ['required', 'string', 'max:50', Rule::unique('Fournisseur', 'code_fournisseur')],
            'nom_fournisseur'   => 'required|string|max:255',
            'filiale_id'        => ['required', Rule::exists('Filiale', 'id')],
            'telephone'         => 'nullable|string|max:50',
            'fax'               => 'nullable|string|max:50',
            'email'             => 'nullable|email|max:255',
            'contact'           => 'nullable|string|max:255',
            'adresse'           => 'nullable|string|max:255',
            'remarque'          => 'nullable|string',
        ]);

        Fournisseur::create([
            'code_fournisseur'  => $validated['code_fournisseur'],
            'nom_fournisseur'   => $validated['nom_fournisseur'],
            'filiale_id'        => $validated['filiale_id'],
            'telephone'         => $validated['telephone'],
            'fax'               => $validated['fax'],
            'email'             => $validated['email'],
            'contact'           => $validated['contact'],
            'adresse'           => $validated['adresse'],
            'remarque'          => $validated['remarque'],
            'fournisseur_bloque' => false,
        ]);

        return redirect()->back()->with('success', 'Fournisseur ajouté avec succès.');
    }

    public function update(Request $request, $id)
    {
        $fournisseur = Fournisseur::findOrFail($id);

        $validated = $request->validate([
            // استخدام Rule مع تجاهل الـ ID الحالي لتفادي خطأ الـ Unique لنفس السجل
            'code_fournisseur'   => ['required', 'string', 'max:50', Rule::unique('Fournisseur', 'code_fournisseur')->ignore($id)],
            'nom_fournisseur'    => 'required|string|max:255',
            'filiale_id'         => ['required', Rule::exists('Filiale', 'id')],
            'telephone'          => 'nullable|string|max:50',
            'fax'                => 'nullable|string|max:50',
            'email'              => 'nullable|email|max:255',
            'contact'            => 'nullable|string|max:255',
            'adresse'            => 'nullable|string|max:255',
            'remarque'           => 'nullable|string',
            'fournisseur_bloque' => 'required|boolean',
        ]);

        $fournisseur->update([
            'code_fournisseur'   => $validated['code_fournisseur'],
            'nom_fournisseur'    => $validated['nom_fournisseur'],
            'filiale_id'         => $validated['filiale_id'],
            'telephone'          => $validated['telephone'],
            'fax'                => $validated['fax'],
            'email'              => $validated['email'],
            'contact'            => $validated['contact'],
            'adresse'            => $validated['adresse'],
            'remarque'           => $validated['remarque'],
            'fournisseur_bloque' => $validated['fournisseur_bloque'],
        ]);

        return redirect()->back()->with('success', 'Fournisseur modifié avec succès.');
    }

    public function destroy($id)
    {
        $fournisseur = Fournisseur::findOrFail($id);
        $fournisseur->delete();

        return redirect()->back()->with('success', 'Fournisseur supprimé avec succès.');
    }
}