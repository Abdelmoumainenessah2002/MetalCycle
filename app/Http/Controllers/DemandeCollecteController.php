<?php

namespace App\Http\Controllers;

use App\Models\DemandeCollecte;
use App\Models\Fournisseur;
use App\Models\Materiau;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\NumberingService;
class DemandeCollecteController extends Controller
{
    public const SCREEN_CODE = 'collecte_demandes';

    public function index(Request $request)
{
    $screen = self::SCREEN_CODE;
    
    $query = DemandeCollecte::with(['fournisseur', 'materiau'])->orderBy('created_at', 'desc');

    // الفلترة حسب الحالة
    if ($request->filled('status') && $request->status !== 'ALL') {
        $query->where('statut', $request->status);
    }

    // البحث بالرقم، اسم المورد، أو اسم المادة
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('numero', 'like', "%{$search}%")
              ->orWhereHas('fournisseur', function($q) use ($search) {
                  $q->where('nom_fournisseur', 'like', "%{$search}%");
              })
              ->orWhereHas('materiau', function($q) use ($search) {
                  $q->where('nom', 'like', "%{$search}%");
              });
        });
    }

    // الحفاظ على معاملات URL في روابط الـ Pagination
    $demandes = $query->paginate(10)->withQueryString();

    // إذا كان الطلب عبر AJAX نرجع Partial View
    if ($request->ajax()) {
        return view('collecte.demandes_table', compact('demandes', 'screen'))->render();
    }

    $fournisseurs = Fournisseur::where('fournisseur_bloque', 0)->get();
    $materiaux = Materiau::where('actif', 1)->get();


    $nextNumero = NumberingService::preview('compteur_demande');

    return view('collecte.demandes', compact('demandes', 'fournisseurs', 'materiaux', 'screen'));
}

    public function store(Request $request)
    {
        $request->validate([
            'fournisseur_id'   => 'required|exists:Fournisseur,id',
            'materiau_id'      => 'required|exists:Materiau,id',
            'quantite_estimee' => 'required|numeric|min:0.01',
            'date_demande'     => 'required|date',
            'adresse_collecte' => 'nullable|string|max:255',
            'contact'          => 'nullable|string|max:100',
            'telephone'        => 'nullable|string|max:30',
            'observation'      => 'nullable|string|max:255',
        ]);

        DemandeCollecte::create([
            'numero'           => null, // لا يوجد رقم عند الإنشاء
            'fournisseur_id'   => $request->fournisseur_id,
            'materiau_id'      => $request->materiau_id,
            'quantite_estimee' => $request->quantite_estimee,
            'date_demande'     => $request->date_demande,
            'adresse_collecte' => $request->adresse_collecte,
            'contact'          => $request->contact,
            'telephone'        => $request->telephone,
            'statut'           => 'En attente',
            'observation'      => $request->observation,
        ]);

        return redirect()->route('collecte.demandes.index')->with('success', 'Demande créée avec succès.');
    }

    public function update(Request $request, $id)
    {
        $demande = DemandeCollecte::findOrFail($id);

        // حماية: السماح بالتعديل فقط إذا كانت الحالة En attente
        if ($demande->statut !== 'En attente') {
            return back()->with('error', 'Seules les demandes "En attente" peuvent être modifiées.');
        }

        $request->validate([
            'fournisseur_id' => 'required|exists:Fournisseur,id',
            'materiau_id' => 'required|exists:Materiau,id',
            'quantite_estimee' => 'required|numeric|min:0',
            'date_demande' => 'required|date',
            'adresse_collecte' => 'nullable|string|max:255',
            'contact' => 'nullable|string|max:100',
            'telephone' => 'nullable|string|max:30',
            'observation' => 'nullable|string|max:255',
        ]);

        $demande->update([
            'fournisseur_id' => $request->fournisseur_id,
            'materiau_id' => $request->materiau_id,
            'quantite_estimee' => $request->quantite_estimee,
            'date_demande' => $request->date_demande,
            'adresse_collecte' => $request->adresse_collecte,
            'contact' => $request->contact,
            'telephone' => $request->telephone,
            'observation' => $request->observation,
        ]);

        return redirect()->route('collecte.demandes.index')->with('success', 'Demande modifiée avec succès.');
    }

    public function valider(Request $request, $id)
{
    $demande = DemandeCollecte::findOrFail($id);

    // التأكد من أن الطلب في حالة انتظار قبل اعتماده
    if ($demande->statut !== 'En attente') {
        return back()->with('error', 'Seules les demandes "En attente" peuvent être validées.');
    }

    // 🟢 توليد الرقم التلقائي من جدول البرامتر إذا لم يكن للطلب رقم سابق
    if (empty($demande->numero)) {
        $demande->numero = NumberingService::generate('compteur_demande');
    }

    // تحديث حالة الطلب
    $demande->statut = 'Validée';
    $demande->save();

    return back()->with('success', 'La demande a été validée avec le N° ' . $demande->numero);
}

    public function refuser($id)
    {
        $demande = DemandeCollecte::findOrFail($id);

        if ($demande->statut !== 'En attente') {
            return back()->with('error', 'Seules les demandes "En attente" peuvent être refusées.');
        }

        $demande->update(['statut' => 'Refusée']);
        return back()->with('success', 'La demande a été refusée.');
    }

    public function annuler(Request $request, $id)
    {
        $demande = DemandeCollecte::findOrFail($id);
    
        // حماية: الإلغاء متاح فقط للحالة Validée
        if ($demande->statut !== 'Validée') {
            $errorMsg = 'Seules les demandes validées peuvent être annulées.';
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMsg
                ], 422);
            }
            
            return back()->with('error', $errorMsg);
        }
    
        // اشتراط إدخال سبب الإلغاء
        $request->validate([
            'motif_annulation' => 'required|string|max:255',
        ]);
    
        $demande->update([
            'statut' => 'Annulée',
            'motif_annulation' => $request->motif_annulation,
        ]);
    
        $successMsg = 'La demande a été annulée avec succès.';
    
        // إذا كان الطلب قادماً عبر AJAX نرجع استجابة JSON
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $successMsg
            ]);
        }
    
        return back()->with('success', $successMsg);
    }

    public function destroy($id)
    {
        $demande = DemandeCollecte::findOrFail($id);

        // حماية: الحذف متاح فقط في حالة En attente
        if ($demande->statut !== 'En attente') {
            return back()->with('error', 'Seules les demandes "En attente" peuvent être supprimées.');
        }

        // التحقق مما إذا كان الطلب مرتبطاً بأي مهمة في الجدول الوسيط
        $estLieAMission = DB::table('MissionCollecteDemande')
            ->where('demande_collecte_id', $id)
            ->exists();

        if ($estLieAMission) {
            return back()->with('error', 'Impossible de supprimer cette demande car elle est déjà associée à une mission.');
        }

        $demande->delete();

        return back()->with('success', 'Demande supprimée avec succès.');
    }
}

