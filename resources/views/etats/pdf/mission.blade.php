<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>État des Missions de Collecte</title>
    <style>
        @page { size: A4 portrait; margin: 15mm 12mm; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11px; color: #000; line-height: 1.4; }
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; border-bottom: 3px solid #C1121F; padding-bottom: 10px; }
        .header-table td { border: none; padding: 0; vertical-align: middle; }
        .title { font-size: 20px; font-weight: bold; color: #000; margin: 0; text-transform: uppercase; }
        .subtitle { font-size: 11px; color: #C1121F; font-weight: bold; margin-top: 4px; }
        .meta-info { text-align: right; font-size: 10.5px; color: #333; }
        table.data-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.data-table th { background-color: #111827; color: #fff; font-weight: bold; font-size: 10px; text-transform: uppercase; border: 1px solid #111827; padding: 8px; text-align: left; }
        table.data-table td { border: 1px solid #e5e7eb; padding: 7px 8px; font-size: 10.5px; vertical-align: middle; }
        table.data-table tr:nth-child(even) { background-color: #f9fafb; }
        table.data-table tr { page-break-inside: avoid; }
        .badge { display: inline-block; padding: 3px 7px; border-radius: 10px; font-size: 9.5px; font-weight: bold; text-align: center; }
        .badge-validee { background-color: #dcfce7; color: #15803d; }
        .badge-attente { background-color: #fef3c7; color: #92400e; }
        .badge-annulee { background-color: #fee2e2; color: #991b1b; }
        .badge-default { background-color: #f3f4f6; color: #374151; }
        tfoot tr td { background-color: #fef2f2; border-top: 2px solid #C1121F !important; font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .text-primary { color: #C1121F; }
        .footer { position: fixed; bottom: -10mm; left: 0; right: 0; font-size: 9px; color: #6b7280; text-align: center; border-top: 1px solid #e5e7eb; padding-top: 5px; }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td>
                <h1 class="title">État des Missions</h1>
                <div class="subtitle">Rapport des missions de collecte</div>
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
                <th style="width: 15%;">N° Mission</th>
                <th style="width: 20%;">N° Demande</th>
                <th style="width: 25%;">Fournisseur</th>
                <th style="width: 15%; text-align: center;">Chauffeur</th>
                <th style="width: 13%; text-align: center;">Statut</th>
                <th style="width: 12%; text-align: center;">Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $row)
                @php
                    $demande = $row->demandes->first(); // 🟢 جلب طلب الجمع المرتبط بالمهمة
                @endphp
                <tr>
                    <td class="font-bold text-primary">{{ $row->numero ?? $row->code ?? 'N/A' }}</td>
                    <td>{{ $demande->numero ?? '-' }}</td>
                    <td><strong>{{ $demande->fournisseur->nom_fournisseur ?? '-' }}</strong></td>
                    <td class="text-center">{{ $row->chauffeur->nom ?? $row->chauffeur ?? '-' }}</td>
                    <td class="text-center">
                        <span class="badge {{ $row->statut == 'Terminée' ? 'badge-validee' : ($row->statut == 'En cours' ? 'badge-attente' : 'badge-default') }}">
                            {{ $row->statut }}
                        </span>
                    </td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($row->created_at)->format('d/m/Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 20px; color: #9ca3af;">Aucune mission trouvée pour cette période.</td>
                </tr>
            @endforelse
        </tbody>
        @if($data->count() > 0)
        <tfoot>
            <tr>
                <td colspan="4" class="text-right font-bold">Total Missions :</td>
                <td class="text-center font-bold text-primary">{{ $data->count() }}</td>
                <td></td>
            </tr>
        </tfoot>
        @endif
    </table>

    <div class="footer">Système de Gestion des Collectes — Généré automatiquement</div>
</body>
</html>