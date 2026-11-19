<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\StockMP;
use App\Models\Materiau;
use App\Models\ZoneStockage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockMPController extends Controller
{
    const SCREEN_CODE = 'stock_mp';

    public function index(Request $request)
    {
        $screen = self::SCREEN_CODE;

        $stocks = StockMP::with(['materiau', 'zoneStockage'])->latest('updated_at')->get();

        // 📊 الإحصائيات (KPIs)
        $totalQuantite = StockMP::sum('quantite');
        $valeurTotale = StockMP::select(DB::raw('SUM(quantite * prix_moyen) as total'))->value('total') ?? 0;
        $totalMateriauxCount = StockMP::distinct('materiau_id')->count('materiau_id');
        $totalZonesCount = StockMP::distinct('zone_stockage_id')->count('zone_stockage_id');

        // 📋 جلب المواد والمناطق لتغذية قوائم الـ Select
        $materiaux = Materiau::all();
        $zones = ZoneStockage::all();

        return view('stock.index', compact(
            'stocks', 
            'screen',
            'totalQuantite', 
            'valeurTotale', 
            'totalMateriauxCount', 
            'totalZonesCount',
            'materiaux',
            'zones'
        ));
    }
}