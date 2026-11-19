<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DemandeCollecte;
use App\Models\MissionCollecte;
use App\Models\Reception;
use App\Models\StockMP;
use App\Models\Materiau;      // 🟢 موديل المادة
use App\Models\ZoneStockage;  // 🟢 موديل منطقة التخزين
use Barryvdh\DomPDF\Facade\Pdf;

class EtatController extends Controller
{
    protected array $screenCodes = [
        'demande'   => 'etats_demande',
        'mission'   => 'etats_mission',
        'reception' => 'etats_reception',
        'stock'     => 'etats_stock',
    ];

    public function getScreenCode(Request $request)
    {
        $type = $request->route('type') ?? $request->input('type', 'demande');
        return $this->screenCodes[$type] ?? 'etats_demande';
    }

    public function index(Request $request, $type)
    {
        $screen = $this->getScreenCode($request);

        $dateDebut  = $request->input('date_debut', date('Y-m-01'));
        $dateFin    = $request->input('date_fin', date('Y-m-d'));
        $statut     = $request->input('statut');
        $materiauId = $request->input('materiau_id');
        $zoneId     = $request->input('zone_stockage_id'); // 🟢 جلب معرف المنطقة

        $data      = $this->getDataQuery($type, $request)->get();
        $materiaux = Materiau::all();     // 🟢 جلب جميع المواد
        $zones     = ZoneStockage::all(); // 🟢 جلب جميع مناطق التخزين

        return view("etats.{$type}", compact(
            'data', 'dateDebut', 'dateFin', 'type', 'screen', 
            'statut', 'materiauId', 'zoneId', 'materiaux', 'zones'
        ));
    }

    public function exportPdf(Request $request, $type)
    {
        $dateDebut  = $request->input('date_debut', date('Y-m-01'));
        $dateFin    = $request->input('date_fin', date('Y-m-d'));
        $statut     = $request->input('statut');
        $materiauId = $request->input('materiau_id');
        $zoneId     = $request->input('zone_stockage_id');

        $data = $this->getDataQuery($type, $request)->get();

        $pdf = Pdf::loadView("etats.pdf.{$type}", compact(
            'data', 'dateDebut', 'dateFin', 'statut', 'materiauId', 'zoneId'
        ));

        return $pdf->download("etat_{$type}_{$dateDebut}_au_{$dateFin}.pdf");
    }

    private function getDataQuery($type, Request $request)
    {
        $dateDebut  = $request->input('date_debut', date('Y-m-01'));
        $dateFin    = $request->input('date_fin', date('Y-m-d'));
        $statut     = $request->input('statut');
        $materiauId = $request->input('materiau_id');
        $zoneId     = $request->input('zone_stockage_id'); // 🟢

        return match ($type) {
            'demande'   => DemandeCollecte::with('fournisseur', 'materiau')
                                ->whereBetween('created_at', ["{$dateDebut} 00:00:00", "{$dateFin} 23:59:59"])
                                ->when($statut, fn($query) => $query->where('statut', $statut)),

            'mission'   => MissionCollecte::with(['demandes.fournisseur', 'chauffeur'])
                                ->whereBetween('created_at', ["{$dateDebut} 00:00:00", "{$dateFin} 23:59:59"])
                                ->when($statut, fn($query) => $query->where('statut', $statut)),

            'reception' => Reception::with(['fournisseur', 'receptionDetails.materiau'])
                                ->whereBetween('created_at', ["{$dateDebut} 00:00:00", "{$dateFin} 23:59:59"])
                                ->when($statut, fn($q) => $q->where('statut', $statut))
                                ->when($materiauId, function($q) use ($materiauId) {
                                    $q->whereHas('receptionDetails', fn($subQ) => $subQ->where('materiau_id', $materiauId));
                                }),

            // 🟢 التحديث هنا: إضافة الفلترة بالمادة ومنطقة التخزين للمخزون
            'stock'     => StockMP::with('materiau', 'zoneStockage')
                                ->whereBetween('updated_at', ["{$dateDebut} 00:00:00", "{$dateFin} 23:59:59"])
                                ->when($materiauId, fn($q) => $q->where('materiau_id', $materiauId))
                                ->when($zoneId, fn($q) => $q->where('zone_stockage_id', $zoneId)),

            default     => abort(404),
        };
    }
}