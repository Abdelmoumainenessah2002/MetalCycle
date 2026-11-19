<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>État des Réceptions</title>
    <style>
        @page { size: A4 portrait; margin: 15mm 12mm; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11px; color: #000; line-height: 1.4; }
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; border-bottom: 3px solid #C1121F; padding-bottom: 10px; }
        .header-table td { border: none; padding: 0; vertical-align: middle; }
        .title { font-size: 20px; font-weight: bold; color: #000; margin: 0; text-transform: uppercase; }
        .subtitle { font-size: 11px; color: #C1121F; font-weight: bold; margin-top: 4px; }
        .meta-info { text-align: right; font-size: 10.5px; color: #333; }
        table.data-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.data-table th { background-color: #111827; color: #fff; font-weight: bold; font-size: 9.5px; text-transform: uppercase; border: 1px solid #111827; padding: 7px 6px; text-align: left; }
        table.data-table td { border: 1px solid #e5e7eb; padding: 6px; font-size: 10px; vertical-align: middle; }
        table.data-table tr:nth-child(even) { background-color: #f9fafb; }
        table.data-table tr { page-break-inside: avoid; }
        tfoot tr td { background-color: #fef2f2; border-top: 2px solid #C1121F !important; font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .text-primary { color: #C1121F; }
        
        /* شارات الحالة المخصصة لملفات PDF */
        .badge { padding: 3px 8px; border-radius: 10px; font-size: 8.5px; font-weight: bold; display: inline-block; }
        .badge-success { background-color: #DCFCE7; color: #15803D; }
        .badge-warning { background-color: #FEF3C7; color: #92400E; }
        .badge-danger { background-color: #FEE2E2; color: #991B1B; }
        .badge-secondary { background-color: #F1F5F9; color: #475569; }
        
        .footer { position: fixed; bottom: -10mm; left: 0; right: 0; font-size: 9px; color: #6b7280; text-align: center; border-top: 1px solid #e5e7eb; padding-top: 5px; }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td>
                <h1 class="title">État des Réceptions</h1>
                <div class="subtitle">Récapitulatif des réceptions de matières</div>
            </td>
            <td class="meta-info">
                <strong>Période:</strong> {{ \Carbon\Carbon::parse($dateDebut)->format('d/m/Y') }} au {{ \Carbon\Carbon::parse($dateFin)->format('d/m/Y') }}<br>
                <strong>Édité le:</strong> {{ date('d/m/Y H:i') }}
            </td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 15%;">N° Réception</th>
                <th style="width: 23%;">Fournisseur</th>
                <th style="width: 20%;">Matériau</th>
                <th style="width: 16%; text-align: right;">Poids Net Total</th>
                <th style="width: 12%; text-align: center;">Statut</th>
                <th style="width: 14%; text-align: center;">Date Réception</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $row)
                <tr>
                    <td class="font-bold text-primary">{{ $row->numero ?? $row->code ?? '---' }}</td>
                    <td><strong>{{ $row->fournisseur->nom_fournisseur ?? $row->provenance ?? '-' }}</strong></td>
                    
                    {{-- عرض اسم المادة --}}
                    <td>
                        @php
                            $materiaux = $row->receptionDetails->map(function($detail) {
                                return $detail->materiau->nom_materiau 
                                    ?? $detail->materiau->designation 
                                    ?? $detail->materiau->nom 
                                    ?? null;
                            })->filter()->unique()->implode(', ');
                        @endphp
                        {{ $materiaux ?: '---' }}
                    </td>

                    {{-- تصحيح حساب الوزن --}}
                    <td class="text-right font-bold">
                        {{ number_format($row->poids_net ?? $row->receptionDetails->sum('quantite_reelle'), 2, '.', ' ') }} Kg
                    </td>

                    {{-- عرض شارة الحالة --}}
                    <td class="text-center">
                        @if($row->statut == 'Validée')
                            <span class="badge badge-success">Validée</span>
                        @elseif($row->statut == 'En attente' || $row->statut == 'En cours')
                            <span class="badge badge-warning">En attente</span>
                        @elseif($row->statut == 'Annulée' || $row->statut == 'Refusée')
                            <span class="badge badge-danger">Annulée</span>
                        @else
                            <span class="badge badge-secondary">{{ $row->statut ?? '-' }}</span>
                        @endif
                    </td>

                    <td class="text-center">{{ \Carbon\Carbon::parse($row->created_at)->format('d/m/Y H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 20px; color: #9ca3af;">Aucune réception enregistrée.</td>
                </tr>
            @endforelse
        </tbody>
        @if($data->count() > 0)
        <tfoot>
            <tr>
                <td colspan="3" class="text-right font-bold">Total Poids Réceptionné :</td>
                <td class="text-right font-bold text-primary">
                    {{ number_format($data->sum(fn($r) => $r->poids_net ?? $r->receptionDetails->sum('quantite_reelle')), 2, '.', ' ') }} Kg
                </td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
        @endif
    </table>

    <div class="footer">Système de Gestion des Collectes — Généré automatiquement</div>
</body>
</html>