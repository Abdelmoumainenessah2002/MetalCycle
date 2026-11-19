@extends('layouts.app')

@section('content')
<style>
@media print {
    /* إخفاء عناصر التحكم والواجهة */
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
        <h2 style="margin: 0; font-size: 24px; font-weight: 700; color: #1e293b;">État des Demandes de Collecte</h2>
        <p style="color: #777; margin: 4px 0 0 0; font-size: 14px;">Vue d'ensemble et suivi des demandes enregistrées</p>
    </div>

    <!-- أزرار التحكم محمية بناءً على الصلاحيات -->
    <div class="no-print" style="display: flex; gap: 10px;">
        
        {{-- 🔴 التحقق من صلاحية الطباعة --}}
        @if(auth()->user()->canAccess($screen, 'imprimer'))
            <button type="button" onclick="window.print()" 
                    style="padding: 10px 18px; background: white; color: #475569; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: all 0.2s;"
                    onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='white'">
                <i class="fa-solid fa-print"></i> Imprimer
            </button>
        @endif
        
        {{-- 🔴 التحقق من صلاحية تصدير PDF (تم تحديث الرابط ليمرر الحالة المختارة) --}}
        @if(auth()->user()->canAccess($screen, 'exporter'))
            <a href="{{ route('etats.pdf', ['type' => $type, 'date_debut' => $dateDebut, 'date_fin' => $dateFin, 'statut' => request('statut')]) }}" target="_blank"
               style="padding: 10px 18px; background: #C1121F; color: white; border: none; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px; text-decoration: none; transition: background 0.2s;"
               onmouseover="this.style.background='#9e0e19'" onmouseout="this.style.background='#C1121F'">
                <i class="fa-solid fa-file-pdf"></i> Exporter PDF
            </a>
        @endif

    </div>
</div>

<!-- 📊 بطاقات الإحصائيات (KPIs) -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 25px;">
    <div style="background: white; padding: 18px; border-radius: 12px; border: 1px solid #e2e8f0;">
        <span style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase;">Total Demandes</span>
        <h3 style="margin: 8px 0 0 0; color: #0284c7; font-size: 22px;">{{ $data->count() }}</h3>
    </div>

    <div style="background: white; padding: 18px; border-radius: 12px; border: 1px solid #e2e8f0;">
        <span style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase;">Qté Totale Estimée</span>
        <h3 style="margin: 8px 0 0 0; color: #15803D; font-size: 22px;">{{ number_format($data->sum('quantite_estimee'), 2, '.', ' ') }} <span style="font-size: 14px;">Kg</span></h3>
    </div>

    <div style="background: white; padding: 18px; border-radius: 12px; border: 1px solid #e2e8f0;">
        <span style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase;">En Attente</span>
        <h3 style="margin: 8px 0 0 0; color: #d97706; font-size: 22px;">{{ $data->where('statut', 'En attente')->count() }}</h3>
    </div>

    <div style="background: white; padding: 18px; border-radius: 12px; border: 1px solid #e2e8f0;">
        <span style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase;">Traitées / Validées</span>
        <h3 style="margin: 8px 0 0 0; color: #4f46e5; font-size: 22px;">{{ $data->whereIn('statut', ['Validée', 'Traitée'])->count() }}</h3>
    </div>
</div>

<!-- 🔍 قسم الفلترة (مخفي أثناء الطباعة) -->
<div class="no-print" style="background: white; padding: 18px 20px; border-radius: 12px; border: 1px solid #e2e8f0; margin-bottom: 25px;">
    <form method="GET" action="{{ route('etats.index', $type) }}" style="display: flex; gap: 20px; align-items: flex-end; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 180px;">
            <label style="font-size: 13px; font-weight: 700; color: #334155; display: block; margin-bottom: 6px;">
                <i class="fa-solid fa-calendar-day" style="margin-right: 5px; color: #64748b;"></i> Date Début
            </label>
            <input type="date" name="date_debut" value="{{ $dateDebut }}" required
                   style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13.5px;">
        </div>

        <div style="flex: 1; min-width: 180px;">
            <label style="font-size: 13px; font-weight: 700; color: #334155; display: block; margin-bottom: 6px;">
                <i class="fa-solid fa-calendar-check" style="margin-right: 5px; color: #64748b;"></i> Date Fin
            </label>
            <input type="date" name="date_fin" value="{{ $dateFin }}" required
                   style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13.5px;">
        </div>

        <!-- 🆕 حقل الفلترة حسب الحالة -->
        <div style="flex: 1; min-width: 180px;">
            <label style="font-size: 13px; font-weight: 700; color: #334155; display: block; margin-bottom: 6px;">
                <i class="fa-solid fa-list-check" style="margin-right: 5px; color: #64748b;"></i> Statut
            </label>
            <select name="statut" style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13.5px; background-color: white; color: #334155;">
                <option value="">Tous les statuts</option>
                <option value="En attente" {{ request('statut') == 'En attente' ? 'selected' : '' }}>En attente</option>
                <option value="Validée" {{ request('statut') == 'Validée' ? 'selected' : '' }}>Validée</option>
                <option value="Traitée" {{ request('statut') == 'Traitée' ? 'selected' : '' }}>Traitée</option>
                <option value="Annulée" {{ request('statut') == 'Annulée' ? 'selected' : '' }}>Annulée</option>
            </select>
        </div>

        <div style="display: flex; gap: 10px;">
            <button type="submit" style="padding: 10.5px 22px; background: #0284c7; color: white; border: none; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer;">
                <i class="fa-solid fa-filter"></i> Appliquer
            </button>
            <a href="{{ route('etats.index', $type) }}" style="padding: 10.5px 18px; background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; font-weight: 600; text-decoration: none;">
                <i class="fa-solid fa-rotate-left"></i> Réinitialiser
            </a>
        </div>
    </form>
</div>

<!-- 📋 جدول عرض البيانات -->
<div class="table-card">
    <table id="demandesTable">
        <thead>
            <tr>
                <th>N° Demande</th>
                <th>Fournisseur</th>
                <th>Matériau</th>
                <th>Quantité Estimée</th>
                <th>Statut</th>
                <th>Date Demande</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $row)
                <tr>
                    <td style="white-space: nowrap;">
                        <strong style="color: #0284c7;">{{ $row->numero ?? 'En attente' }}</strong>
                    </td>
                    <td>
                        <strong style="color: #0f172a; display: block;">
                           {{ $row->fournisseur->nom_fournisseur ?? 'N/A' }}
                        </strong>
                        @if($row->contact)
                            <small style="color: #64748b; font-size: 12px;">{{ $row->contact }}</small>
                        @endif
                    </td>
                    <td>
                        <span style="background: #f1f5f9; color: #334155; padding: 4px 10px; border-radius: 6px; font-weight: 600; font-size: 12px; display: inline-block;">
                            {{ $row->materiau->nom ?? $row->materiau->designation ?? 'N/A' }}
                        </span>
                    </td>
                    <td style="white-space: nowrap;">
                        <strong style="color: #0f172a;">{{ number_format($row->quantite_estimee, 2, '.', ' ') }} Kg</strong>
                    </td>
                    <td style="white-space: nowrap;">
                        @if($row->statut == 'Validée')
                            <span style="padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; background: #DCFCE7; color: #15803D;">Validée</span>
                        @elseif($row->statut == 'En attente')
                            <span style="padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; background: #FEF3C7; color: #92400E;">En attente</span>
                        @elseif($row->statut == 'Traitée')
                            <span style="padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; background: #E0F2FE; color: #0369A1;">Traitée</span>
                        @elseif($row->statut == 'Annulée')
                            <span style="padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; background: #FEE2E2; color: #991B1B;">Annulée</span>
                        @else
                            <span style="padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; background: #F1F5F9; color: #475569;">{{ $row->statut }}</span>
                        @endif
                    </td>
                    <td style="color: #64748b; font-size: 13px; white-space: nowrap;">
                        {{ \Carbon\Carbon::parse($row->created_at)->format('d/m/Y H:i') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align:center; color: #888; padding: 30px;">
                        Aucune demande trouvée.
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if($data->count() > 0)
        <tfoot style="background: #f8fafc; font-weight: 700;">
            <tr>
                <td colspan="3" style="text-align: right; color: #64748b; text-transform: uppercase; font-size: 12px; padding: 12px 16px;">Total :</td>
                <td style="color: #15803D; font-size: 14px; padding: 12px 16px;">{{ number_format($data->sum('quantite_estimee'), 2, '.', ' ') }} Kg</td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
        @endif
    </table>
</div>
@endsection