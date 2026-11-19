@forelse($receptions as $rep)
<tr data-statut="{{ $rep->statut }}" style="border-bottom: 1px solid #f1f5f9; transition: background 0.15s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='white'">
    <td style="padding: 12px 16px; font-weight: 700; color: #0f172a;">
        {{ $rep->numero ?? '---' }}
    </td>
    <td style="padding: 12px 16px;">
        <div style="font-weight: 600; color: #334155;">{{ $rep->type_reception }}</div>
        @if($rep->mission)
            <small style="color: #64748b; font-weight: 600;"><i class="fa-solid fa-truck-ramp-box" style="font-size: 10px;"></i> {{ $rep->mission->numero }}</small>
        @endif
    </td>
    <td style="padding: 12px 16px; color: #64748b;">{{ $rep->poids_fournisseur ? number_format($rep->poids_fournisseur, 2).' Kg' : '---' }}</td>
    <td style="padding: 12px 16px; font-weight: 600; color: #334155;">{{ number_format($rep->poids_entree, 2) }} Kg</td>
    <td style="padding: 12px 16px; color: #64748b;">{{ $rep->poids_sortie ? number_format($rep->poids_sortie, 2).' Kg' : '---' }}</td>
    <td style="padding: 12px 16px; font-weight: 800; color: #0f172a;">{{ $rep->poids_net ? number_format($rep->poids_net, 2).' Kg' : '---' }}</td>
    <td style="padding: 12px 16px;">
        @if($rep->statut == 'En attente Tare')
            <span class="badge-status" style="background: #FEF3C7; color: #92400E;"><i class="fa-solid fa-clock"></i> Attente Tare</span>
        @elseif($rep->statut == 'En attente Validation')
            <span class="badge-status" style="background: #E0F2FE; color: #0369A1;"><i class="fa-solid fa-spinner"></i> Attente Validation</span>
        @elseif($rep->statut == 'Acceptée')
            <span class="badge-status" style="background: #DCFCE7; color: #15803D;"><i class="fa-solid fa-check"></i> Acceptée</span>
        @elseif($rep->statut == 'Refusée')
            <span class="badge-status" style="background: #FEE2E2; color: #991B1B;"><i class="fa-solid fa-xmark"></i> Refusée</span>
        @endif
    </td>
    <td style="padding: 12px 16px; text-align: center;">
        <div style="display: flex; gap: 6px; justify-content: center;">

            @php
                $repData = $rep->loadMissing(['details.materiau', 'zoneStockage']);
                $repData->zone_nom_calculated = $rep->zoneStockage ? $rep->zoneStockage->nom : '---';
                $repData->date_val_formatted = $rep->date_validation ? \Carbon\Carbon::parse($rep->date_validation)->format('Y-m-d H:i') : null;
                $repData->numero_official = $rep->numero ?? \App\Services\NumberingService::preview('compteur_reception');
            @endphp
            
            {{-- زر إدخال Tare --}}
            @if($rep->statut == 'En attente Tare' && auth()->user()->canAccess($screen, 'tare'))
                <button class="btn-action btn-warning-custom" title="Saisir Tare" onclick='openStage2Modal(@json($repData))'>
                    <i class="fa-solid fa-weight-hanging"></i>
                </button>
            @endif

            {{-- زر التأكيد والقبول --}}
            @if($rep->statut == 'En attente Validation' && auth()->user()->canAccess($screen, 'validate'))
    <button class="btn-action btn-success-custom" 
            title="Valider la réception" 
            data-reception="{{ json_encode($repData) }}"
            onclick="openStage3Modal(JSON.parse(this.dataset.reception))">
        <i class="fa-solid fa-clipboard-check"></i>
    </button>
@endif

            {{-- زر عرض التفاصيل --}}
            <button class="btn-action btn-info-custom" title="Consulter les détails" onclick='openViewModal(@json($repData))'>
                <i class="fa-solid fa-eye"></i>
            </button>

            {{-- زر التعديل --}}
            @if(auth()->user()->canAccess($screen, 'edit') && !in_array($rep->statut, ['Acceptée', 'Refusée']))
                <button class="btn-action" style="background-color: #e0e7ff; color: #3730a3;" title="Modifier" onclick='openEditModal(@json($repData))'>
                    <i class="fa-solid fa-pen-to-square"></i>
                </button>
            @endif

            {{-- زر الحذف --}}
            @if(auth()->user()->canAccess($screen, 'delete') && !in_array($rep->statut, ['Acceptée', 'Refusée']))
                <form action="{{ route('reception.destroy', $rep->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette réception ?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-action" style="background-color: #fee2e2; color: #991b1b;" title="Supprimer">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </form>
            @endif

        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="8" style="text-align:center; padding: 40px; color: #94a3b8; font-weight: 500;">
        Aucune réception trouvée.
    </td>
</tr>
@endforelse