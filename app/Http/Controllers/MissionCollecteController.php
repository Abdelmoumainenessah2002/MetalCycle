<?php

namespace App\Http\Controllers;

use App\Models\MissionCollecte;
use App\Models\DemandeCollecte;
use App\Models\Person;
use App\Models\Equipement;
use App\Services\NumberingService; // 👈 استدعاء خدمة الترقيم
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MissionCollecteController extends Controller
{
    public const SCREEN_CODE = 'collecte_missions';

    public function index(Request $request)
    {
        $screen = self::SCREEN_CODE;
        
        $query = MissionCollecte::with(['chauffeur', 'equipement', 'demandes']);
    
        // 1. فلترة حسب نص البحث
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('numero', 'like', "%{$search}%")
                  ->orWhereHas('chauffeur', function($ch) use ($search) {
                      $ch->where('nom', 'like', "%{$search}%")
                         ->orWhere('prenom', 'like', "%{$search}%");
                  });
            });
        }
    
        // 2. فلترة حسب الحالة
        if ($request->filled('status') && $request->status !== 'ALL') {
            $query->where('statut', $request->status);
        }
    
        // 3. الترقيم مع الحفاظ على مدخلات البحث
        $missions = $query->latest()->paginate(15)->appends($request->all());

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'html' => view('collecte._missions_table', compact('missions', 'screen'))->render(),
                'pagination' => (string) $missions->links()
            ]);
        }
    
        $chauffeurs = Person::where('actif', 1)
            ->whereHas('role', function($q) { 
                $q->where('code', 'CHAUFFEUR'); 
            })
            ->get();
            
        $equipements = Equipement::where('actif', 1)
            ->where('type', 'Camion')
            ->get(); 
    
        $allDemandes = DemandeCollecte::whereIn('statut', ['Validée', 'Planifiée'])
            ->with(['fournisseur', 'materiau'])
            ->get();
    
        // 🟢 معاينة الرقم التلقائي القادم للمهمة
        $nextNumero = NumberingService::preview('compteur_mission');

        return view('collecte.missions', compact('missions', 'chauffeurs', 'equipements', 'allDemandes', 'screen', 'nextNumero'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date_depart_prevue' => 'required|date',
            'date_retour_prevue' => 'required|date|after_or_equal:date_depart_prevue',
            'chauffeur_id'       => 'required|exists:Person,id',
            'equipement_id'      => 'required|exists:Equipement,id',
            'demandes'           => 'required|array|min:1',
            'observation'        => 'nullable|string|max:255',
        ], [
            'date_retour_prevue.after_or_equal' => 'La date de retour prévue doit être égale ou supérieure à la date de départ prévue.',
        ]);

        DB::transaction(function () use ($request) {
            $tempNumero = 'TEMP-' . time();

            $mission = MissionCollecte::create([
                'numero'           => null,
                'date_depart_prevue' => Carbon::parse($request->date_depart_prevue)->format('Y-m-d H:i:s'), 
                'date_retour_prevue' => Carbon::parse($request->date_retour_prevue)->format('Y-m-d H:i:s'), 
                'chauffeur_id'       => $request->chauffeur_id,
                'equipement_id'      => $request->equipement_id,
                'statut'             => 'Planifiée', 
                'observation'        => $request->observation,
            ]);

            $mission->demandes()->attach($request->demandes);
            DemandeCollecte::whereIn('id', $request->demandes)->update(['statut' => 'Planifiée']);
        });

        return redirect()->route('collecte.missions.index')->with('success', 'Mission créée et planifiée avec succès.');
    }

    public function update(Request $request, $id)
    {
        $mission = MissionCollecte::findOrFail($id);

        if ($mission->statut !== 'Planifiée') {
            return back()->with('error', 'Impossible de modifier une mission en cours ou terminée.');
        }

        $request->validate([
            'date_depart_prevue' => 'required|date',
            'date_retour_prevue' => 'required|date|after_or_equal:date_depart_prevue',
            'chauffeur_id'       => 'required|exists:Person,id',
            'equipement_id'      => 'required|exists:Equipement,id',
            'demandes'           => 'required|array|min:1',
            'observation'        => 'nullable|string|max:255',
        ], [
            'date_retour_prevue.after_or_equal' => 'La date de retour prévue doit être égale ou supérieure à la date de départ prévue.',
        ]);

        DB::transaction(function () use ($request, $mission) {
            $oldDemandes = $mission->demandes()->pluck('demande_collecte_id')->toArray();
            DemandeCollecte::whereIn('id', $oldDemandes)->update(['statut' => 'Validée']);

            $mission->update([
                'date_depart_prevue' => Carbon::parse($request->date_depart_prevue)->format('Y-m-d H:i:s'),
                'date_retour_prevue' => Carbon::parse($request->date_retour_prevue)->format('Y-m-d H:i:s'),
                'chauffeur_id'       => $request->chauffeur_id,
                'equipement_id'      => $request->equipement_id,
                'observation'        => $request->observation,
            ]);

            $mission->demandes()->sync($request->demandes);
            DemandeCollecte::whereIn('id', $request->demandes)->update(['statut' => 'Planifiée']);
        });

        return redirect()->route('collecte.missions.index')->with('success', 'Mission modifiée avec succès.');
    }

    // 🟢 الانطلاق وانشاء الرقم الرسمي تلقائياً
    public function demarrer(Request $request, $id)
    {
        $mission = MissionCollecte::findOrFail($id);

        $request->validate([
            'date_depart' => 'required|date',
        ]);

        if ($mission->statut === 'Planifiée') {
            // توليد الرقم الرسمي للمهمة وزيادة العداد في الـ DB
            $officialNumero = NumberingService::generate('compteur_mission');

            $mission->update([
                'numero'      => $officialNumero,
                'statut'      => 'En cours',
                'date_depart' => Carbon::parse($request->date_depart)->format('Y-m-d H:i:s')
            ]);

            return back()->with('success', 'La mission est démarrée avec le N° ' . $officialNumero);
        }

        return back()->with('error', 'Statut invalide.');
    }

    public function cloturer(Request $request, $id)
    {
        $mission = MissionCollecte::findOrFail($id);

        $request->validate([
            'date_retour' => 'required|date|after_or_equal:' . $mission->date_depart,
        ], [
            'date_retour.after_or_equal' => 'La date de retour réelle ne peut pas être antérieure à la date de départ réelle.',
        ]);

        if ($mission->statut === 'En cours') {
            $mission->update([
                'statut'      => 'Terminée',
                'date_retour' => Carbon::parse($request->date_retour)->format('Y-m-d H:i:s')
            ]);
            return back()->with('success', 'La mission est terminée.');
        }
        return back()->with('error', 'Statut invalide.');
    }

    public function annuler(Request $request, $id)
    {
        $mission = MissionCollecte::findOrFail($id);

        if ($mission->statut !== 'En cours') {
            return back()->with('error', 'Seules les missions en cours peuvent être annulées.');
        }

        $request->validate([
            'motif_annulation' => 'required|string|max:255',
        ], [
            'motif_annulation.required' => 'Le motif d\'annulation est obligatoire.',
        ]);

        DB::transaction(function () use ($mission, $request) {
            $demandesIds = $mission->demandes()->pluck('demande_collecte_id')->toArray();
            DemandeCollecte::whereIn('id', $demandesIds)->update(['statut' => 'Validée']);

            $observation = $mission->observation 
                ? $mission->observation . " | Annulée: " . $request->motif_annulation 
                : "Annulée: " . $request->motif_annulation;

            $mission->update([
                'statut'      => 'Annulée',
                'observation' => $observation,
            ]);
        });

        return back()->with('success', 'La mission a été annulée et ses demandes sont réouvertes.');
    }

    public function destroy($id)
    {
        $mission = MissionCollecte::findOrFail($id);

        if ($mission->statut !== 'Planifiée') {
            return back()->with('error', 'Impossible de supprimer cette mission.');
        }

        DB::transaction(function () use ($mission) {
            $oldDemandes = $mission->demandes()->pluck('demande_collecte_id')->toArray();
            DemandeCollecte::whereIn('id', $oldDemandes)->update(['statut' => 'Validée']);
            
            $mission->demandes()->detach();
            $mission->delete();
        });

        return back()->with('success', 'Mission supprimée avec succès.');
    }
}