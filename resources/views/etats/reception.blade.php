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
        <h2 style="margin: 0; font-size: 24px; font-weight: 700; color: #1e293b;">État des Réceptions de Matières</h2>
        <p style="color: #777; margin: 4px 0 0 0; font-size: 14px;">Synthèse et suivi des réceptions enregistrées en usine</p>
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
            {{-- 🟢 تحديث الرابط لنقل قيم الفلترة المحددة إلى الـ PDF --}}
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

        <span style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase;">Total Réceptions</span>

        <h3 style="margin: 8px 0 0 0; color: #0284c7; font-size: 22px;">{{ $data->count() }}</h3>

    </div>



    <div style="background: white; padding: 18px; border-radius: 12px; border: 1px solid #e2e8f0;">

        <span style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase;">Poids Net Total</span>

        <h3 style="margin: 8px 0 0 0; color: #15803D; font-size: 22px;">

            {{ number_format($data->sum(fn($r) => $r->poids_net ?? $r->receptionDetails->sum('quantite_reelle')), 2, '.', ' ') }}<span style="font-size: 14px;">Kg</span>

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
        <div style="flex: 1; min-width: 180px;">
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

        <!-- 🟢 فلتر الحالة (Statut) -->
        <div style="flex: 1; min-width: 150px;">
            <label style="font-size: 13px; font-weight: 700; color: #334155; display: block; margin-bottom: 6px;">
                <i class="fa-solid fa-list-check" style="margin-right: 5px; color: #64748b;"></i> Statut
            </label>
            <select name="statut" style="width: 100%; padding: 9px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13.5px; background: white;">
                <option value="">Tous les statuts</option>
                <option value="Acceptée" {{ request('statut') == 'Acceptée' ? 'selected' : '' }}>Acceptée</option>
                <option value="En attente Tare" {{ request('statut') == 'En attente Tare' ? 'selected' : '' }}>En attente Tare</option>
                <option value="En attente Validation" {{ request('statut') == 'En attente Validation' ? 'selected' : '' }}>En attente Validation</option>
                <option value="Refusée" {{ request('statut') == 'Refusée' ? 'selected' : '' }}>Refusée</option>
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
<!-- 📋 جدول عرض البيانات -->
<div class="table-card">
    <table id="receptionsTable">
        <thead>
            <tr>
                <th>N° Réception</th>
                <th>Fournisseur</th>
                <th>Matériau</th> {{-- 🟢 تم تغيير العنوان --}}
                <th>Poids Net Total</th>
                <th style="text-align: center;">Statut</th>
                <th>Date Réception</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $row)
                <tr>
                    <td style="white-space: nowrap;">
                        <strong style="color: #0284c7;">{{ $row->numero ?? $row->code ?? '---' }}</strong>
                    </td>
                    <td>
                        <strong style="color: #0f172a; display: block;">
                           {{ $row->fournisseur->nom_fournisseur ?? $row->provenance ?? '---' }}
                        </strong>
                    </td>

                    {{-- 🟢 عرض اسم/نوع المادة بدلاً من العدد --}}
                    <td>
                        @php
                            $materiaux = $row->receptionDetails->map(function($detail) {
                                return $detail->materiau->nom_materiau 
                                    ?? $detail->materiau->designation 
                                    ?? $detail->materiau->nom 
                                    ?? null;
                            })->filter()->unique()->implode(', ');
                        @endphp
                        <span style="color: #334155; font-weight: 600; font-size: 13px;">
                            {{ $materiaux ?: '---' }}
                        </span>
                    </td>

                    <td style="white-space: nowrap;">
                        <strong style="color: #0f172a;">
                            {{ number_format($row->poids_net ?? $row->receptionDetails->sum('quantite_reelle'), 2, '.', ' ') }} Kg
                        </strong>
                    </td>

                    <td style="text-align: center; white-space: nowrap;">
                        @if($row->statut == 'Validée')
                            <span style="padding: 4px 12px; border-radius: 20px; font-size: 11.5px; font-weight: 600; background: #DCFCE7; color: #15803D;">Validée</span>
                        @elseif($row->statut == 'En attente' || $row->statut == 'En cours')
                            <span style="padding: 4px 12px; border-radius: 20px; font-size: 11.5px; font-weight: 600; background: #FEF3C7; color: #92400E;">En attente</span>
                        @elseif($row->statut == 'Annulée' || $row->statut == 'Refusée')
                            <span style="padding: 4px 12px; border-radius: 20px; font-size: 11.5px; font-weight: 600; background: #FEE2E2; color: #991B1B;">Annulée</span>
                        @else
                            <span style="padding: 4px 12px; border-radius: 20px; font-size: 11.5px; font-weight: 600; background: #F1F5F9; color: #475569;">{{ $row->statut ?? '-' }}</span>
                        @endif
                    </td>

                    <td style="color: #64748b; font-size: 13px; white-space: nowrap;">
                        {{ \Carbon\Carbon::parse($row->created_at)->format('d/m/Y H:i') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align:center; color: #888; padding: 30px;">
                        Aucune réception enregistrée.
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if($data->count() > 0)
        <tfoot style="background: #f8fafc; font-weight: 700;">
            <tr>
                <td colspan="3" style="text-align: right; color: #64748b; text-transform: uppercase; font-size: 12px; padding: 12px 16px;">Total Réceptionné :</td>
                <td style="color: #15803D; font-size: 14px; padding: 12px 16px;">
                    {{ number_format($data->sum(fn($r) => $r->poids_net ?? $r->receptionDetails->sum('quantite_reelle')), 2, '.', ' ') }} Kg
                </td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
        @endif
    </table>
</div>
@endsection