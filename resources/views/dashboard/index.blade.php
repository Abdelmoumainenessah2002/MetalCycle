@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<!-- مكتبة Chart.js للرسوم البيانية -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="dashboard" style="padding-bottom: 30px;">

    <!-- 🚀 الهيدر وأزرار الإجراءات السريعة -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; margin-bottom: 25px;">
        <div>
            <h2 style="margin: 0; font-size: 24px; color: #0f172a; font-weight: 700;">Tableau de Bord</h2>
            <p style="margin: 4px 0 0 0; color: #64748b; font-size: 14px;">Bienvenue, <strong>{{ session('user_name') ?? auth()->user()->name }}</strong> ! Voici un aperçu de vos activités.</p>
        </div>
        
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            @if(auth()->user() && auth()->user()->canAccess('collecte_demandes', 'create'))
            <a href="{{ route('collecte.demandes.index') }}" style="background: #C1121F; color: white; padding: 9px 16px; border-radius: 8px; text-decoration: none; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 8px; transition: all 0.2s;">
                <i class="fa-solid fa-plus"></i> Nouvelle Demande
            </a>
            @endif

            @if(auth()->user() && auth()->user()->canAccess('reception_receptions', 'view'))
            <a href="{{ route('reception.index') }}" style="background: #0284c7; color: white; padding: 9px 16px; border-radius: 8px; text-decoration: none; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 8px; transition: all 0.2s;">
                <i class="fa-solid fa-scale-balanced"></i> Réception
            </a>
            @endif

            @if(auth()->user() && auth()->user()->canAccess('stock_mp', 'view'))
            <a href="{{ route('stock.index') }}" style="background: #15803D; color: white; padding: 9px 16px; border-radius: 8px; text-decoration: none; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 8px; transition: all 0.2s;">
                <i class="fa-solid fa-cubes"></i> Stock MP
            </a>
            @endif
        </div>
    </div>

    <!-- 📊 بطاقات الإحصائيات الرئيسية (KPIs) -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)); gap: 18px; margin-bottom: 25px;">
        
        <!-- بطاقة طلبات الجمع -->
        <div style="background: white; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.04); display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
                <span style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase;">Demandes de Collecte</span>
                <h3 style="margin: 8px 0 6px 0; color: #0f172a; font-size: 26px; font-weight: 700;">{{ $demandesCount }}</h3>
                <span style="font-size: 12px; padding: 3px 8px; border-radius: 12px; background: #FEF3C7; color: #D97706; font-weight: 600;">
                    <i class="fa-solid fa-clock"></i> {{ $demandesPending }} En attente
                </span>
            </div>
            <div style="background: #eff6ff; color: #2563eb; width: 48px; height: 48px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-recycle"></i>
            </div>
        </div>

        <!-- بطاقة المهام -->
        <div style="background: white; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.04); display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
                <span style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase;">Missions de Collecte</span>
                <h3 style="margin: 8px 0 6px 0; color: #0f172a; font-size: 26px; font-weight: 700;">{{ $missionsCount }}</h3>
                <span style="font-size: 12px; padding: 3px 8px; border-radius: 12px; background: #DCFCE7; color: #15803D; font-weight: 600;">
                    <i class="fa-solid fa-truck-fast"></i> {{ $missionsActive }} En cours
                </span>
            </div>
            <div style="background: #f0fdf4; color: #16a34a; width: 48px; height: 48px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-truck"></i>
            </div>
        </div>

        <!-- بطاقة إجمالي المخزون -->
        <div style="background: white; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.04); display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
                <span style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase;">Stock Total MP</span>
                <h3 style="margin: 8px 0 6px 0; color: #0f172a; font-size: 24px; font-weight: 700;">{{ number_format($totalStockKg, 2, '.', ' ') }} <span style="font-size: 14px;">Kg</span></h3>
                <span style="font-size: 12px; color: #15803D; font-weight: 600;">
                    Valeur: {{ number_format($totalStockValeur, 2, '.', ' ') }} DA
                </span>
            </div>
            <div style="background: #fef2f2; color: #C1121F; width: 48px; height: 48px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-cubes"></i>
            </div>
        </div>

        <!-- بطاقة المستخدمين والفروع -->
        <div style="background: white; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.04); display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
                <span style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase;">Structure</span>
                <h3 style="margin: 8px 0 6px 0; color: #0f172a; font-size: 26px; font-weight: 700;">{{ $centresCount }} <span style="font-size: 14px; font-weight: 500; color: #64748b;">Filiales</span></h3>
                <span style="font-size: 12px; color: #475569; font-weight: 600;">
                    <i class="fa-solid fa-users"></i> {{ $usersCount }} Utilisateurs
                </span>
            </div>
            <div style="background: #fff7ed; color: #ea580c; width: 48px; height: 48px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-sitemap"></i>
            </div>
        </div>

    </div>

    <!-- 📈 الرسم البياني + توزيع المخزون -->
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 25px; align-items: stretch;">
        
        <!-- الرسم البياني للعمليات -->
        <div style="background: white; padding: 22px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h3 style="margin: 0; font-size: 16px; color: #1e293b; font-weight: 700;">Aperçu des Collectes & Missions</h3>
                <span style="font-size: 12px; color: #94a3b8;"><i class="fa-solid fa-chart-column"></i> Statuts globaux</span>
            </div>
            <div style="height: 230px; position: relative;">
                <canvas id="dashboardChart"></canvas>
            </div>
        </div>

        <!-- أهم المواد المخزنة -->
        <div style="background: white; padding: 22px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.04); display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <h3 style="margin: 0 0 15px 0; font-size: 16px; color: #1e293b; font-weight: 700;">Top Matériaux en Stock</h3>
                
                <div style="display: flex; flex-direction: column; gap: 14px;">
                    @forelse($topMateriaux as $item)
                        @php
                            $percentage = $totalStockKg > 0 ? round(($item->total_qty / $totalStockKg) * 100) : 0;
                        @endphp
                        <div>
                            <div style="display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 5px;">
                                <span style="font-weight: 600; color: #334155;">{{ $item->materiau->nom ?? $item->materiau->designation ?? 'N/A' }}</span>
                                <span style="color: #64748b; font-weight: 700;">{{ number_format($item->total_qty, 0, '.', ' ') }} Kg</span>
                            </div>
                            <div style="width: 100%; background: #f1f5f9; height: 7px; border-radius: 10px; overflow: hidden;">
                                <div style="width: {{ $percentage }}%; background: #0284c7; height: 100%; border-radius: 10px;"></div>
                            </div>
                        </div>
                    @empty
                        <p style="text-align: center; color: #94a3b8; font-size: 13px; margin-top: 30px;">Aucune donnée de stock disponible.</p>
                    @endforelse
                </div>
            </div>

            <div style="margin-top: 15px; text-align: center;">
                @if(auth()->user() && auth()->user()->canAccess('stock_mp', 'view'))
                <a href="{{ route('stock.index') }}" style="color: #0284c7; font-size: 12.5px; font-weight: 600; text-decoration: none;">
                    Voir tout le stock <i class="fa-solid fa-arrow-right" style="font-size: 10px; margin-left: 3px;"></i>
                </a>
                @endif
            </div>
        </div>

    </div>

    <!-- 📋 جدول أحدث طلبات الجمع -->
    <div style="background: white; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.04); overflow: hidden;">
        <div style="padding: 18px 22px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 16px; color: #1e293b; font-weight: 700;">Dernières Demandes de Collecte</h3>
            @if(auth()->user() && auth()->user()->canAccess('collecte_demandes', 'view'))
                <a href="{{ route('collecte.demandes.index') }}" style="color: #C1121F; font-size: 13px; font-weight: 600; text-decoration: none;">
                    Voir tout <i class="fa-solid fa-chevron-right" style="font-size: 11px;"></i>
                </a>
            @endif
        </div>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13.5px;">
                <thead>
                    <tr style="background: #f8fafc; color: #64748b; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">
                        <th style="padding: 12px 20px;">Fournisseur / Client</th>
                        <th style="padding: 12px 20px;">Matériau</th>
                        <th style="padding: 12px 20px;">Date de demande</th>
                        <th style="padding: 12px 20px; text-align: center;">Statut</th>
                    </tr>
                </thead>
                <tbody style="color: #334155;">
                    @forelse($latestDemandes as $demande)
                        <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.15s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                            <td style="padding: 14px 20px; font-weight: 600; color: #0f172a;">
                                <i class="fa-solid fa-building" style="color: #94a3b8; margin-right: 8px;"></i>
                                {{ $demande->fournisseur->nom_fournisseur ?? 'Non spécifié' }}
                            </td>
                            <td style="padding: 14px 20px;">
                                <span style="background: #f1f5f9; color: #475569; padding: 4px 10px; border-radius: 6px; font-weight: 600; font-size: 12px;">
                                    {{ $demande->materiau->nom ?? $demande->materiau->designation ?? $demande->dechet_nom ?? 'N/A' }}
                                </span>
                            </td>
                            <td style="padding: 14px 20px; color: #64748b; font-size: 13px;">
                                {{ \Carbon\Carbon::parse($demande->created_at)->format('d/m/Y à H:i') }}
                            </td>
                            <td style="padding: 14px 20px; text-align: center; white-space: nowrap;">
                                @php
                                    $st = strtolower($demande->statut);
                                @endphp
                                @if(in_array($st, ['traitée','terminée', 'terminee', 'validée', 'validee', 'reçue']))
                                    <span style="padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; background: #DCFCE7; color: #15803D; display: inline-block;">
                                        <i class="fa-solid fa-check" style="font-size: 10px; margin-right: 4px;"></i> {{ $demande->statut }}
                                    </span>
                                @elseif(in_array($st, ['en attente', 'en_attente', 'en cours', 'en_cours', 'nouvelle']))
                                    <span style="padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; background: #FEF3C7; color: #D97706; display: inline-block;">
                                        <i class="fa-solid fa-spinner" style="font-size: 10px; margin-right: 4px;"></i> {{ $demande->statut }}
                                    </span>
                                @else
                                    <span style="padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; background: #FEE2E2; color: #991B1B; display: inline-block;">
                                        <i class="fa-solid fa-xmark" style="font-size: 10px; margin-right: 4px;"></i> {{ $demande->statut }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; color: #94a3b8; padding: 30px;">
                                Aucune demande de collecte récente enregistrée.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Script إعداد الرسم البياني -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    const ctx = document.getElementById('dashboardChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Demandes Totales', 'Demandes En Attente', 'Missions Totales', 'Missions En Cours'],
            datasets: [{
                label: 'Quantité / Nombre',
                data: [{{ $demandesCount }}, {{ $demandesPending }}, {{ $missionsCount }}, {{ $missionsActive }}],
                backgroundColor: [
                    'rgba(37, 99, 235, 0.85)',
                    'rgba(217, 119, 6, 0.85)',
                    'rgba(22, 163, 74, 0.85)',
                    'rgba(193, 18, 31, 0.85)'
                ],
                borderRadius: 6,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#f1f5f9' },
                    ticks: { precision: 0 }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
});
</script>
@endsection