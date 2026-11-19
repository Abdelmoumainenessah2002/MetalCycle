<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>État des Demandes de Collecte</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm 12mm 15mm 12mm;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #000000;
            line-height: 1.4;
        }

        /* 🏛️ الهيدر والمعلومات العامة (أحمر وأسود) */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border-bottom: 3px solid #C1121F; /* خط أحمر رئيسي */
            padding-bottom: 10px;
        }

        .header-table td {
            border: none;
            padding: 0;
            vertical-align: middle;
        }

        .title {
            font-size: 20px;
            font-weight: bold;
            color: #000000; /* أسود */
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .subtitle {
            font-size: 11px;
            color: #C1121F; /* أحمر */
            font-weight: bold;
            margin-top: 4px;
        }

        .meta-info {
            text-align: right;
            font-size: 10.5px;
            color: #333333;
        }

        /* 📋 جدول البيانات (رأس أسود، تفاصيل باللون الأحمر) */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table.data-table th {
            background-color: #111827; /* خلفية سوداء */
            color: #ffffff; /* نص أبيض */
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
            border: 1px solid #111827;
            padding: 8px;
            text-align: left;
        }

        table.data-table td {
            border: 1px solid #e5e7eb;
            padding: 7px 8px;
            font-size: 10.5px;
            vertical-align: middle;
            color: #111827;
        }

        table.data-table tr:nth-child(even) {
            background-color: #f9fafb;
        }

        table.data-table tr {
            page-break-inside: avoid;
        }

        /* 🏷️ شارات الحالة (Statuts) */
        .badge {
            display: inline-block;
            padding: 3px 7px;
            border-radius: 10px;
            font-size: 9.5px;
            font-weight: bold;
            text-align: center;
        }
        .badge-validee  { background-color: #dcfce7; color: #15803d; }
        .badge-attente  { background-color: #fef3c7; color: #92400e; }
        .badge-traitee  { background-color: #e0f2fe; color: #0369a1; }
        .badge-annulee  { background-color: #fee2e2; color: #991b1b; }
        .badge-default  { background-color: #f3f4f6; color: #374151; }

        /* 📊 التذييل والإجماليات */
        tfoot tr td {
            background-color: #fef2f2; /* خلفية حمراء فاتحة جداً */
            border-top: 2px solid #C1121F !important;
            font-weight: bold;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .text-primary { color: #C1121F; } /* رقم الطلب والنصوص المميزة بالأحمر */

        /* 📄 رقم الصفحة للتذييل */
        .footer {
            position: fixed;
            bottom: -10mm;
            left: 0;
            right: 0;
            font-size: 9px;
            color: #6b7280;
            text-align: center;
            border-top: 1px solid #e5e7eb;
            padding-top: 5px;
        }
    </style>
</head>
<body>

    <!-- 📌 الهيدر رأس الصفحة -->
    <table class="header-table">
        <tr>
            <td>
                <h1 class="title">État des Demandes</h1>
                <div class="subtitle">Rapport détaillé des demandes de collecte</div>
            </td>
            <td class="meta-info">
                <strong>Période:</strong> {{ \Carbon\Carbon::parse($dateDebut)->format('d/m/Y') }} au {{ \Carbon\Carbon::parse($dateFin)->format('d/m/Y') }}<br>
                <strong>Édité le:</strong> {{ date('d/m/Y H:i') }}
            </td>
        </tr>
    </table>

    <!-- 📋 جدول البيانات الرئيسي -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 15%;">N° Demande</th>
                <th style="width: 25%;">Fournisseur</th>
                <th style="width: 20%;">Matériau</th>
                <th style="width: 15%; text-align: right;">Qté Estimée</th>
                <th style="width: 12%; text-align: center;">Statut</th>
                <th style="width: 13%; text-align: center;">Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $row)
                <tr>
                    <td class="font-bold text-primary">{{ $row->numero ?? 'En attente' }}</td>
                    <td>
                        <strong style="color: #000000;">{{ $row->fournisseur->nom_fournisseur ?? '-' }}</strong>
                        @if($row->contact)
                            <br><span style="font-size: 9px; color: #6b7280;">({{ $row->contact }})</span>
                        @endif
                    </td>
                    <td>{{ $row->materiau->nom ?? $row->materiau->designation ?? '-' }}</td>
                    <td class="text-right font-bold">
                        {{ number_format($row->quantite_estimee, 2, '.', ' ') }} <span style="font-size: 9px; color: #6b7280;">Kg</span>
                    </td>
                    <td class="text-center">
                        @switch($row->statut)
                            @case('Validée')
                                <span class="badge badge-validee">Validée</span>
                                @break
                            @case('En attente')
                                <span class="badge badge-attente">En attente</span>
                                @break
                            @case('Traitée')
                                <span class="badge badge-traitee">Traitée</span>
                                @break
                            @case('Annulée')
                                <span class="badge badge-annulee">Annulée</span>
                                @break
                            @default
                                <span class="badge badge-default">{{ $row->statut }}</span>
                        @endswitch
                    </td>
                    <td class="text-center" style="color: #374151;">
                        {{ \Carbon\Carbon::parse($row->created_at)->format('d/m/Y') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 20px; color: #9ca3af;">
                        Aucune demande trouvée pour cette période.
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if($data->count() > 0)
        <tfoot>
            <tr>
                <td colspan="3" class="text-right font-bold" style="color: #000000; text-transform: uppercase;">Total Général :</td>
                <td class="text-right font-bold text-primary" style="font-size: 11px;">
                    {{ number_format($data->sum('quantite_estimee'), 2, '.', ' ') }} Kg
                </td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
        @endif
    </table>

    <!-- 🦶 تذييل الصفحة -->
    <div class="footer">
        Système de Gestion des Collectes — Généré automatiquement
    </div>

</body>
</html>