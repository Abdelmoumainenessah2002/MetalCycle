<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>État du Stock MP</title>
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
                <h1 class="title">État du Stock MP</h1>
                <div class="subtitle">Inventaire des Matières Premières</div>
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
                <th style="width: 32%;">Matériau</th>
                <th style="width: 28%;">Zone de Stockage</th>
                <th style="width: 22%; text-align: right;">Quantité En Stock</th>
                <th style="width: 18%; text-align: center;">Dernière Maj</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $row)
                <tr>
                    <td class="font-bold text-primary">
                        {{ $row->materiau->nom_materiau ?? $row->materiau->nom ?? $row->materiau->designation ?? '---' }}
                    </td>
                    <td>
                        <strong>{{ $row->zoneStockage->nom ?? $row->zoneStockage->designation ?? 'Zone Générale' }}</strong>
                    </td>
                    <td class="text-right font-bold">
                        {{ number_format($row->quantite, 2, '.', ' ') }} <span style="font-size: 9px; color: #6b7280;">Kg</span>
                    </td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($row->updated_at)->format('d/m/Y H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center" style="padding: 20px; color: #9ca3af;">Aucune donnée de stock disponible.</td>
                </tr>
            @endforelse
        </tbody>
        @if($data->count() > 0)
        <tfoot>
            <tr>
                <td colspan="2" class="text-right font-bold">Total Stock Général :</td>
                <td class="text-right font-bold text-primary">
                    {{ number_format($data->sum('quantite'), 2, '.', ' ') }} Kg
                </td>
                <td></td>
            </tr>
        </tfoot>
        @endif
    </table>

    <div class="footer">Système de Gestion des Collectes — Généré automatiquement</div>
</body>
</html>