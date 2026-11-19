<?php

namespace App\Http\Controllers;

use App\Models\Reception;
use App\Models\ReceptionDetail;
use App\Models\MissionCollecte;
use App\Models\Fournisseur;
use App\Models\Person;
use App\Models\Materiau;
use App\Models\ZoneStockage;
use App\Models\StockMP;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Services\NumberingService;
class ReceptionController extends Controller
{
    public const SCREEN_CODE = 'reception_receptions';

    public function index(Request $request)
    {
        $screen = self::SCREEN_CODE;

        $query = Reception::with([
            'mission.chauffeur', 
            'mission.equipement', 
            'mission.demandes.fournisseur', 
            'fournisseur', 
            'controleur', 
            'details.materiau',
            'zoneStockage'
        ]);

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('numero', 'like', "%{$search}%")
                  ->orWhereHas('mission', function ($m) use ($search) {
                      $m->where('numero', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date); 
        }

        $receptions = $query->orderBy('created_at', 'desc')
                            ->paginate(15)
                            ->appends($request->all());

        $fournisseurs = Fournisseur::where('fournisseur_bloque', 0)->orderBy('nom_fournisseur', 'asc')->get();

        $existingMissionIds = Reception::whereNotNull('mission_collecte_id')
            ->pluck('mission_collecte_id')
            ->toArray();

        $allMissionsRaw = MissionCollecte::with(['chauffeur', 'equipement', 'demandes.fournisseur'])->get();

        $missions = $allMissionsRaw->map(function($mission) {
            $fournisseursNames = $mission->demandes->map(function($d) {
                return $d->fournisseur ? $d->fournisseur->nom_fournisseur : null;
            })->filter()->unique()->values()->toArray();

            return [
                'id' => $mission->id,
                'numero' => $mission->numero,
                'fournisseurs_text' => count($fournisseursNames) > 0 ? implode(' , ', $fournisseursNames) : '---',
                'chauffeur' => $mission->chauffeur ? ['nom' => $mission->chauffeur->nom, 'prenom' => $mission->chauffeur->prenom] : null,
                'equipement' => $mission->equipement ? [
                    'matricule' => $mission->equipement->matricule ?? $mission->equipement->code ?? '---',
                    'libelle' => $mission->equipement->libelle ?? $mission->equipement->nom ?? '---'
                ] : null
            ];
        });

        $availableMissions = MissionCollecte::where('statut', 'Terminée')
            ->whereNotIn('id', $existingMissionIds)
            ->orderBy('numero', 'asc')
            ->get();

        $controleurs = Person::where('actif', 1)->get(); 
        $materiaux = Materiau::where('actif', 1)->get();
        $zones = ZoneStockage::where('actif', 1)->get();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'html' => view('reception._table', compact('receptions', 'screen', 'zones'))->render(),
                'pagination' => (string) $receptions->links()
            ]);
        }

        $countEnAttenteTare = Reception::where('statut', 'En attente Tare')->count();
        $countEnAttenteValidation = Reception::where('statut', 'En attente Validation')->count();
        $countAcceptee = Reception::where('statut', 'Acceptée')->count();

        return view('reception.index', compact(
            'receptions', 
            'missions', 
            'availableMissions', 
            'fournisseurs', 
            'controleurs', 
            'materiaux', 
            'zones', 
            'screen',
            'countEnAttenteTare',
            'countEnAttenteValidation',
            'countAcceptee'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type_reception' => 'required|in:Collecte Interne,Réception Directe',
            'mission_collecte_id' => 'nullable|exists:MissionCollecte,id',
            'fournisseur_id' => 'nullable|exists:Fournisseur,id',
            'date_reception' => 'required|date',
            'poids_fournisseur' => 'nullable|numeric|min:0',
            'poids_entree' => 'required|numeric|min:0',
            'materiau_id' => 'required|exists:Materiau,id',
            'prix_unitaire' => 'nullable|numeric|min:0',
            'observation' => 'nullable|string',
        ]);
    
        DB::transaction(function () use ($request) {
            $finalFournisseurId = $request->fournisseur_id;
            if ($request->type_reception === 'Collecte Interne' && $request->mission_collecte_id) {
                $mission = MissionCollecte::with('demandes')->find($request->mission_collecte_id);
                if ($mission) {
                    $finalFournisseurId = $mission->demandes->map(fn($d) => $d->fournisseur_id)->filter()->first();
                }
            }
    
            $reception = Reception::create([
                'numero' => null,
                'mission_collecte_id' => $request->type_reception === 'Collecte Interne' ? $request->mission_collecte_id : null,
                'fournisseur_id' => $finalFournisseurId,
                'type_reception' => $request->type_reception,
                'date_reception' => Carbon::parse($request->date_reception)->format('Y-m-d H:i:s'),
                'poids_fournisseur' => $request->poids_fournisseur,
                'poids_entree' => $request->poids_entree,
                'poids_sortie' => null,
                'poids_net' => null,
                'statut' => 'En attente Tare',
                'observation' => $request->observation
            ]);
    
            ReceptionDetail::create([
                'reception_id' => $reception->id,
                'materiau_id' => $request->materiau_id,
                'quantite_declaree' => $request->poids_fournisseur ?? 0,
                'quantite_reelle' => 0,
                'prix_unitaire' => $request->prix_unitaire ?? 0,
                'observation' => null
            ]);
        });
    
        return redirect()->route('reception.index')->with('success', 'Étape 1 enregistrée : Camion en attente de déchargement.');
    }

    public function storeTare(Request $request, $id)
    {
        $request->validate([
            'poids_sortie' => 'required|numeric|min:0',
            'observation'  => 'nullable|string',
        ]);

        $reception = Reception::findOrFail($id);
        $poids_net = abs($reception->poids_entree - $request->poids_sortie);

        $newObservation = $request->filled('observation') ? $request->observation : $reception->observation;

        $reception->update([
            'poids_sortie' => $request->poids_sortie,
            'poids_net'    => $poids_net,
            'statut'       => 'En attente Validation',
            'observation'  => $newObservation,
        ]);

        return redirect()->route('reception.index')->with('success', 'Étape 2 enregistrée : Poids Tare ajouté avec succès.');
    }

    /**
     * المصادقة على عملية الاستلام مع تسجيل تاريخ Validation
     */
   public function validateReception(Request $request, $id)
    {
        $reception = Reception::with('details')->findOrFail($id);

        if ($request->statut === 'Acceptée') {
            $request->validate([
                'numero'           => 'nullable|string|max:30|unique:Reception,numero,'.$id,
                'zone_stockage_id' => 'required|exists:ZoneStockage,id',
                'date_validation'  => 'required|date',
                'observation'      => 'nullable|string',
            ]);

            DB::transaction(function () use ($reception, $request) {
                $newObservation = $request->filled('observation') ? $request->observation : $reception->observation;

                // 🟢 استخدام الرقم المدخل، ثم المسجل سابقاً، أو استدعاء NumberingService للإنشاء
              $numero = $reception->numero ?? NumberingService::generate('compteur_reception');

                $reception->update([
                    'numero'           => $numero,
                    'zone_stockage_id' => $request->zone_stockage_id,
                    'date_validation'  => Carbon::parse($request->date_validation)->format('Y-m-d H:i:s'),
                    'statut'           => 'Acceptée',
                    'observation'      => $newObservation,
                ]);

                foreach ($reception->details as $detail) {
                    $detail->update([
                        'quantite_reelle' => $reception->poids_net
                    ]);

                    $qteNouvelle = $reception->poids_net;
                    $prixNouveau = $detail->prix_unitaire ?? 0;
                    $zoneId = $request->zone_stockage_id;
                    $materiauId = $detail->materiau_id;

                    $stockExisting = StockMP::where('zone_stockage_id', $zoneId)
                        ->where('materiau_id', $materiauId)
                        ->first();

                    if ($stockExisting) {
                        $qteAncienne = $stockExisting->quantite;
                        $prixAncien = $stockExisting->prix_moyen;
                        $qteTotale = $qteAncienne + $qteNouvelle;

                        $nouveauPrixMoyen = $qteTotale > 0 
                            ? (($qteNouvelle * $prixNouveau) + ($qteAncienne * $prixAncien)) / $qteTotale 
                            : $prixNouveau;

                        $stockExisting->update([
                            'quantite'    => $qteTotale,
                            'prix_moyen'  => $nouveauPrixMoyen,
                            'date_entree' => now(),
                        ]);
                    } else {
                        StockMP::create([
                            'zone_stockage_id' => $zoneId,
                            'materiau_id'      => $materiauId,
                            'quantite'         => $qteNouvelle,
                            'prix_moyen'       => $prixNouveau,
                            'date_entree'      => now(),
                            'statut'           => 'Disponible',
                        ]);
                    }
                }
            });
        } else {
            $request->validate([
                'date_validation' => 'nullable|date',
                'observation'     => 'nullable|string',
            ]);

            $newObservation = $request->filled('observation') ? $request->observation : $reception->observation;

            $reception->update([
                'statut'          => 'Refusée',
                'date_validation' => $request->date_validation ? Carbon::parse($request->date_validation)->format('Y-m-d H:i:s') : now(),
                'observation'     => $newObservation,
            ]);
        }

        return redirect()->route('reception.index')->with('success', 'Étape 3 terminée : Réception clôturée et stock mis à jour.');
    }

    public function update(Request $request, $id)
    {
        $reception = Reception::findOrFail($id);

        if (in_array($reception->statut, ['Acceptée', 'Refusée'])) {
            return redirect()->back()->with('error', 'Impossible de modifier une réception déjà clôturée.');
        }

        $request->validate([
            'type_reception'      => 'required|in:Collecte Interne,Réception Directe',
            'mission_collecte_id' => 'nullable|exists:MissionCollecte,id',
            'fournisseur_id'      => 'nullable|exists:Fournisseur,id',
            'poids_fournisseur'   => 'nullable|numeric|min:0',
            'poids_entree'        => 'required|numeric|min:0',
            'poids_sortie'        => 'nullable|numeric|min:0',
            'materiau_id'         => 'required|exists:Materiau,id',
            'prix_unitaire'       => 'nullable|numeric|min:0',
            'observation'         => 'nullable|string',
        ]);

        DB::transaction(function () use ($reception, $request) {
            $finalFournisseurId = $request->fournisseur_id;
            if ($request->type_reception === 'Collecte Interne' && $request->mission_collecte_id) {
                $mission = MissionCollecte::with('demandes')->find($request->mission_collecte_id);
                if ($mission) {
                    $finalFournisseurId = $mission->demandes->map(fn($d) => $d->fournisseur_id)->filter()->first();
                }
            }

            if ($reception->statut === 'En attente Tare') {
                $poidsSortie = null;
                $poidsNet = null;
            } else {
                $poidsSortie = $request->poids_sortie;
                $poidsNet = ($poidsSortie !== null && $poidsSortie !== '') ? abs($request->poids_entree - $poidsSortie) : null;
            }

            $reception->update([
                'mission_collecte_id' => $request->type_reception === 'Collecte Interne' ? $request->mission_collecte_id : null,
                'fournisseur_id'      => $finalFournisseurId,
                'type_reception'      => $request->type_reception,
                'poids_fournisseur'   => $request->poids_fournisseur,
                'poids_entree'        => $request->poids_entree,
                'poids_sortie'        => $poidsSortie,
                'poids_net'           => $poidsNet,
                'observation'         => $request->observation,
            ]);

            $detail = $reception->details()->first();
            if ($detail) {
                $detail->update([
                    'materiau_id'       => $request->materiau_id,
                    'quantite_declaree' => $request->poids_fournisseur ?? 0,
                    'prix_unitaire'     => $request->prix_unitaire ?? 0,
                ]);
            }
        });

        return redirect()->route('reception.index')->with('success', 'Réception modifiée avec succès.');
    }

    public function destroy($id)
    {
        $reception = Reception::findOrFail($id);

        if (in_array($reception->statut, ['Acceptée', 'Refusée'])) {
            return redirect()->back()->with('error', 'Impossible de supprimer une réception déjà clôturée.');
        }

        DB::transaction(function () use ($reception) {
            $reception->details()->delete();
            $reception->delete();
        });

        return redirect()->route('reception.index')->with('success', 'Réception supprimée avec succès.');
    }
}