<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Utilisateur;
use App\Models\DemandeCollecte;
use App\Models\MissionCollecte;
use App\Models\Filiale;
use App\Models\StockMP; // 👈 استخدام الموديل الصحيح لديك
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. الإحصائيات العامة (KPIs)
        $usersCount       = Utilisateur::count();
        $demandesCount    = DemandeCollecte::count();
        $demandesPending  = DemandeCollecte::whereIn(DB::raw('LOWER(statut)'), ['en attente', 'en_attente', 'nouvelle'])->count();
        
        $missionsCount    = MissionCollecte::count();
        $missionsActive   = MissionCollecte::whereIn(DB::raw('LOWER(statut)'), ['en cours', 'en_cours', 'planifiée'])->count();
        
        $centresCount     = Filiale::count();

        // 2. إحصائيات المخزون الحقيقية باستخدام StockMP
        $totalStockKg     = StockMP::sum('quantite') ?? 0;
        $totalStockValeur = StockMP::sum(DB::raw('quantite * prix_moyen')) ?? 0;

        // 3. أحدث 6 طلبات جمع
        $latestDemandes = DemandeCollecte::with(['fournisseur', 'materiau'])
            ->latest()
            ->take(6)
            ->get();

        // 4. أعلى 4 مواد من حيث الكمية المخزنة
        $topMateriaux = StockMP::with('materiau')
            ->select('materiau_id', DB::raw('SUM(quantite) as total_qty'))
            ->groupBy('materiau_id')
            ->orderByDesc('total_qty')
            ->take(4)
            ->get();

        return view('dashboard.index', compact(
            'usersCount',
            'demandesCount',
            'demandesPending',
            'missionsCount',
            'missionsActive',
            'centresCount',
            'totalStockKg',
            'totalStockValeur',
            'latestDemandes',
            'topMateriaux'
        ));
    }
}