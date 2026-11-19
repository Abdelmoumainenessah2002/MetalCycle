@extends('layouts.app')

@section('content')
<style>
@media print {
    .sidebar, nav, header, .navbar, .topbar, .no-print, button, form {
        display: none !important;
    }

    body, #app, main, .main-content {
        margin: 0 !important;
        padding: 0 !important;
        width: 100% !important;
        background: white !important;
    }

    @page {
        size: A4 portrait;
        margin: 10mm 12mm;
    }

    div[style*="box-shadow"], .table-card {
        box-shadow: none !important;
        border: 1px solid #e2e8f0 !important;
    }

    table {
        width: 100% !important;
        border-collapse: collapse !important;
    }

    thead {
        display: table-header-group;
    }

    tr {
        page-break-inside: avoid;
    }

    th, td {
        padding: 6px 8px !important;
        font-size: 11px !important;
    }
}
</style>

<!-- 📌 العنوان وأزرار التصدير -->
<div class="page-title" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
    <div>
        <h2 style="margin: 0; font-size: 24px; font-weight: 700; color: #1e293b;">État des Stocks MP</h2>
        <p style="color: #777; margin: 4px 0 0 0; font-size: 14px;">Quantités en stock par matière première et zone</p>
    </div>

    <div class="no-print" style="display: flex; gap: 10px;">
        @if(auth()->user()->canAccess($screen, 'imprimer'))
            <button type="button" onclick="window.print()" 
                    style="padding: 10px 18px; background: white; color: #475569; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: all 0.2s;"
                    onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='white'">
                <i class="fa-solid fa-print"></i> Imprimer
            </button>
        @endif
        
        @if(auth()->user()->canAccess($screen, 'exporter'))
            {{-- 🟢 تحديث الرابط لنقل جميع معاملات الفلترة إلى الـ PDF --}}
            <a href="{{ route('etats.pdf', array_merge(['type' => $type], request()->all())) }}" target="_blank"
               style="padding: 10px 18px; background: #C1121F; color: white; border: none; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px; text-decoration: none; transition: background 0.2s;"
               onmouseover="this.style.background='#9e0e19'" onmouseout="this.style.background='#C1121F'">
                <i class="fa-solid fa-file-pdf"></i> Exporter PDF
            </a>
        @endif
    </div>
</div>

<!-- 📊 بطاقات الإحصائيات (KPIs) -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 25px;">
    <div style="background: white; padding: 18px; border-radius: 12px; border: 1px solid #e2e8f0;">
        <span style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase;">Matières Référencées</span>
        <h3 style="margin: 8px 0 0 0; color: #0284c7; font-size: 22px;">{{ $data->count() }}</h3>
    </div>

    <div style="background: white; padding: 18px; border-radius: 12px; border: 1px solid #e2e8f0;">
        <span style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase;">Stock Total Global</span>
        <h3 style="margin: 8px 0 0 0; color: #15803D; font-size: 22px;">
            {{ number_format($data->sum('quantite'), 2, '.', ' ') }} <span style="font-size: 14px;">Kg</span>
        </h3>
    </div>
</div>

<!-- 🔍 قسم الفلترة المحدث -->
<div class="no-print" style="background: white; padding: 18px 20px; border-radius: 12px; border: 1px solid #e2e8f0; margin-bottom: 25px;">
    <form method="GET" action="{{ route('etats.index', $type) }}" style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap;">
        
        <!-- تاريخ البداية -->
        <div style="flex: 1; min-width: 150px;">
            <label style="font-size: 13px; font-weight: 700; color: #334155; display: block; margin-bottom: 6px;">
                <i class="fa-solid fa-calendar-day" style="margin-right: 5px; color: #64748b;"></i> Date Début
            </label>
            <input type="date" name="date_debut" value="{{ $dateDebut }}" required
                   style="width: 100%; padding: 9px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13.5px;">
        </div>

        <!-- تاريخ النهاية -->
        <div style="flex: 1; min-width: 150px;">
            <label style="font-size: 13px; font-weight: 700; color: #334155; display: block; margin-bottom: 6px;">
                <i class="fa-solid fa-calendar-check" style="margin-right: 5px; color: #64748b;"></i> Date Fin
            </label>
            <input type="date" name="date_fin" value="{{ $dateFin }}" required
                   style="width: 100%; padding: 9px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13.5px;">
        </div>

        <!-- 🟢 فلتر المادة (Matériau) -->
        <div style="flex: 1; min-width: 170px;">
            <label style="font-size: 13px; font-weight: 700; color: #334155; display: block; margin-bottom: 6px;">
                <i class="fa-solid fa-boxes-stacked" style="margin-right: 5px; color: #64748b;"></i> Matériau
            </label>
            <select name="materiau_id" style="width: 100%; padding: 9px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13.5px; background: white;">
                <option value="">Tous les matériaux</option>
                @foreach($materiaux as $mat)
                    <option value="{{ $mat->id }}" {{ request('materiau_id') == $mat->id ? 'selected' : '' }}>
                        {{ $mat->nom_materiau ?? $mat->designation ?? $mat->nom }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- 🟢 فلتر منطقة التخزين (Zone de Stockage) -->
        <div style="flex: 1; min-width: 170px;">
            <label style="font-size: 13px; font-weight: 700; color: #334155; display: block; margin-bottom: 6px;">
                <i class="fa-solid fa-warehouse" style="margin-right: 5px; color: #64748b;"></i> Zone Stockage
            </label>
            <select name="zone_stockage_id" style="width: 100%; padding: 9px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13.5px; background: white;">
                <option value="">Toutes les zones</option>
                @foreach($zones as $zone)
                    <option value="{{ $zone->id }}" {{ request('zone_stockage_id') == $zone->id ? 'selected' : '' }}>
                        {{ $zone->nom ?? $zone->designation ?? $zone->libelle }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- أزرار التطبيق وإعادة الضبط -->
        <div style="display: flex; gap: 8px;">
            <button type="submit" style="padding: 9.5px 18px; background: #0284c7; color: white; border: none; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer;">
                <i class="fa-solid fa-filter"></i> Filtrer
            </button>
            <a href="{{ route('etats.index', $type) }}" style="padding: 9.5px 14px; background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; font-weight: 600; text-decoration: none;">
                <i class="fa-solid fa-rotate-left"></i>
            </a>
        </div>
    </form>
</div>

<!-- 📋 جدول عرض البيانات -->
<div class="table-card">
    <table id="stockTable">
        <thead>
            <tr>
                <th>Matériau</th>
                <th>Zone de Stockage</th>
                <th>Quantité En Stock</th>
                <th>Dernière Maj</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $row)
                <tr>
                    <td>
                        <strong style="color: #0284c7;">{{ $row->materiau->nom_materiau ?? $row->materiau->nom ?? $row->materiau->designation ?? '---' }}</strong>
                    </td>
                    <td>
                        <span style="background: #f1f5f9; color: #334155; padding: 4px 10px; border-radius: 6px; font-weight: 600; font-size: 12px; display: inline-block;">
                            {{ $row->zoneStockage->nom ?? $row->zoneStockage->designation ?? 'Zone Générale' }}
                        </span>
                    </td>
                    <td style="white-space: nowrap;">
                        <strong style="color: #0f172a;">{{ number_format($row->quantite, 2, '.', ' ') }} Kg</strong>
                    </td>
                    <td style="color: #64748b; font-size: 13px; white-space: nowrap;">
                        {{ \Carbon\Carbon::parse($row->updated_at)->format('d/m/Y H:i') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align:center; color: #888; padding: 30px;">
                        Aucune donnée de stock disponible.
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if($data->count() > 0)
        <tfoot style="background: #f8fafc; font-weight: 700;">
            <tr>
                <td colspan="2" style="text-align: right; color: #64748b; text-transform: uppercase; font-size: 12px; padding: 12px 16px;">Total Stock Général :</td>
                <td style="color: #15803D; font-size: 14px; padding: 12px 16px;">{{ number_format($data->sum('quantite'), 2, '.', ' ') }} Kg</td>
                <td></td>
            </tr>
        </tfoot>
        @endif
    </table>
</div>
@endsection