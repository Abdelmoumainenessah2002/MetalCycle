@extends('layouts.app')

@section('content')
<div class="page-title" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
    <div>
        <h2>Gestion du Stock MP</h2>
        <p style="color: #777;">État actuel des stocks de matières premières</p>
    </div>
</div>

<!-- 📊 بطاقات الإحصائيات (KPIs) -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 25px;">
    <div style="background: white; padding: 18px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <span style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase;">Quantité Totale</span>
        <h3 style="margin: 8px 0 0 0; color: #0284c7; font-size: 22px;">{{ number_format($totalQuantite, 2, '.', ' ') }} <span style="font-size: 14px;">Kg</span></h3>
    </div>

    <div style="background: white; padding: 18px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <span style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase;">Valeur du Stock</span>
        <h3 style="margin: 8px 0 0 0; color: #15803D; font-size: 22px;">{{ number_format($valeurTotale, 2, '.', ' ') }} <span style="font-size: 14px;">DA</span></h3>
    </div>

    <div style="background: white; padding: 18px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <span style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase;">Matériaux en Stock</span>
        <h3 style="margin: 8px 0 0 0; color: #4f46e5; font-size: 22px;">{{ $totalMateriauxCount }}</h3>
    </div>

    <div style="background: white; padding: 18px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <span style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase;">Zones Utilisées</span>
        <h3 style="margin: 8px 0 0 0; color: #d97706; font-size: 22px;">{{ $totalZonesCount }}</h3>
    </div>
</div>

<!-- 🔍 قسم الفلترة -->
<div style="background: white; padding: 18px 20px; border-radius: 12px; border: 1px solid #e2e8f0; margin-bottom: 25px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
    <div style="display: flex; gap: 20px; align-items: flex-end; flex-wrap: wrap;">
        
        <!-- فلترة حسب المادة -->
        <div style="flex: 1; min-width: 220px;">
            <label style="font-size: 13px; font-weight: 700; color: #334155; display: block; margin-bottom: 6px;">
                <i class="fa-solid fa-filter" style="margin-right: 5px; color: #64748b;"></i> Matériau
            </label>
            <select id="selectMateriau" onchange="filterStockTable()" 
                    style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13.5px; outline: none; background: white; cursor: pointer; color: #1e293b; transition: border-color 0.2s;"
                    onfocus="this.style.borderColor='#C1121F'" onblur="this.style.borderColor='#cbd5e1'">
                <option value="">Tous les matériaux</option>
                @foreach($materiaux as $mat)
                    <option value="{{ $mat->id }}">{{ $mat->nom ?? $mat->designation }}</option>
                @endforeach
            </select>
        </div>

        <!-- فلترة حسب المنطقة -->
        <div style="flex: 1; min-width: 220px;">
            <label style="font-size: 13px; font-weight: 700; color: #334155; display: block; margin-bottom: 6px;">
                <i class="fa-solid fa-warehouse" style="margin-right: 5px; color: #64748b;"></i> Zone de Stockage
            </label>
            <select id="selectZone" onchange="filterStockTable()" 
                    style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13.5px; outline: none; background: white; cursor: pointer; color: #1e293b; transition: border-color 0.2s;"
                    onfocus="this.style.borderColor='#C1121F'" onblur="this.style.borderColor='#cbd5e1'">
                <option value="">Toutes les zones</option>
                @foreach($zones as $z)
                    <option value="{{ $z->id }}">{{ $z->nom }}</option>
                @endforeach
            </select>
        </div>

        <!-- زر إعادة الضبط -->
        <div>
            <button type="button" onclick="resetStockFilters()" 
                    style="padding: 10.5px 20px; background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: background 0.2s;"
                    onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f1f5f9'">
                <i class="fa-solid fa-rotate-left"></i> Réinitialiser
            </button>
        </div>

    </div>
</div>

@if(session('success'))
    <div style="background-color: #DCFCE7; color: #15803D; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: 600;">
        {{ session('success') }}
    </div>
@endif

<!-- 📋 جدول عرض البيانات -->
<div class="table-card">
    <table id="stockTable">
        <thead>
            <tr>
                <th>Matériau</th>
                <th>Zone de Stockage</th>
                <th>Quantité en Stock</th>
                <th>Prix Moyen (PMP)</th>
                <th>Valeur Totale</th>
                <th>Dernière Entrée</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            @forelse($stocks as $stock)
                @php
                    $valeurLigne = $stock->quantite * $stock->prix_moyen;
                @endphp
                <tr data-materiau="{{ $stock->materiau_id }}" data-zone="{{ $stock->zone_stockage_id }}">
                    <td><strong>{{ $stock->materiau->nom ?? $stock->materiau->designation ?? 'N/A' }}</strong></td>
                    <td>
                        <span style="background: #e0f2fe; color: #0369a1; padding: 4px 10px; border-radius: 6px; font-weight: 600; font-size: 12px; white-space: nowrap; display: inline-block;">
                            {{ $stock->zoneStockage->nom ?? 'Non assignée' }}
                        </span>
                    </td>
                    <td style="white-space: nowrap;"><strong style="color: #0f172a;">{{ number_format($stock->quantite, 2, '.', ' ') }} Kg</strong></td>
                    <td style="color: #475569; white-space: nowrap;">{{ number_format($stock->prix_moyen, 2, '.', ' ') }} DA</td>
                    <td style="white-space: nowrap;"><strong style="color: #15803D;">{{ number_format($valeurLigne, 2, '.', ' ') }} DA</strong></td>
                    <td style="color: #64748b; font-size: 13px; white-space: nowrap;">
                        {{ $stock->date_entree ? \Carbon\Carbon::parse($stock->date_entree)->format('d/m/Y H:i') : '---' }}
                    </td>
                    <!-- 👈 تعديل خانة الحالة لمنع الـ Word Wrap -->
                    <td style="white-space: nowrap;">
                        @if($stock->quantite > 0)
                            <span style="padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; background: #DCFCE7; color: #15803D; white-space: nowrap; display: inline-block;">En Stock</span>
                        @else
                            <span style="padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; background: #FEE2E2; color: #991B1B; white-space: nowrap; display: inline-block;">Épuisé</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align:center; color: #888; padding: 30px;">Aucune donnée de stock disponible.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<script>
function filterStockTable() {
    let selectedMat = document.getElementById("selectMateriau").value;
    let selectedZone = document.getElementById("selectZone").value;

    let table = document.getElementById("stockTable");
    let tr = table.getElementsByTagName("tr");

    for (let i = 1; i < tr.length; i++) {
        let row = tr[i];
        let rowMat = row.getAttribute("data-materiau");
        let rowZone = row.getAttribute("data-zone");

        let matchMat = (selectedMat === "" || rowMat === selectedMat);
        let matchZone = (selectedZone === "" || rowZone === selectedZone);

        if (matchMat && matchZone) {
            row.style.display = "";
        } else {
            row.style.display = "none";
        }
    }
}

function resetStockFilters() {
    document.getElementById("selectMateriau").value = "";
    document.getElementById("selectZone").value = "";
    filterStockTable();
}
</script>
@endsection