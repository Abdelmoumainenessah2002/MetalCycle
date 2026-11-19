@forelse($missions as $mission)
<tr style="border-bottom: 1px solid #f1f5f9;">
    <td style="padding: 10px;">
        <span style="background: #E2E8F0; padding: 4px 8px; border-radius:6px; font-weight:600; font-size: 13px;">
           {{ (empty($mission->numero) || str_contains($mission->numero, '---')) ? '---' : $mission->numero }}
        </span>
    </td>
    
    <td style="padding: 10px; color: #334155; font-size: 12px; white-space: nowrap;">
        <div><b>Départ:</b> {{ \Carbon\Carbon::parse($mission->date_depart_prevue)->format('d/m/Y H:i') }}</div>
        <div><b>Retour:</b> {{ $mission->date_retour_prevue ? \Carbon\Carbon::parse($mission->date_retour_prevue)->format('d/m/Y H:i') : '---' }}</div>
    </td>

    <td style="padding: 10px; color: #334155; font-size: 12px; white-space: nowrap;">
        <div><b>Départ:</b> {{ $mission->date_depart ? \Carbon\Carbon::parse($mission->date_depart)->format('d/m/Y H:i') : '---' }}</div>
        <div><b>Retour:</b> {{ $mission->date_retour ? \Carbon\Carbon::parse($mission->date_retour)->format('d/m/Y H:i') : '---' }}</div>
    </td>

    <td style="padding: 10px; font-size: 13px;"><strong>{{ $mission->chauffeur->nom ?? '---' }} {{ $mission->chauffeur->prenom ?? '' }}</strong></td>
    <td style="padding: 10px; font-size: 13px;">{{ $mission->equipement->nom ?? '---' }} <small style="color:#64748b;">({{ $mission->equipement->immatriculation ?? 'N/A' }})</small></td>
    <td style="padding: 10px; text-align: center;"><span style="background: #e0f2fe; color: #0369a1; padding: 2px 8px; border-radius: 12px; font-weight: 600; font-size: 12px;">{{ $mission->demandes->count() }}</span></td>
    <td style="padding: 10px;" class="status-cell">
        @if($mission->statut == 'Planifiée')
            <span style="padding: 4px 8px; border-radius: 20px; font-size: 11px; font-weight: 600; background: #E0F2FE; color: #0369A1; white-space: nowrap;">Planifiée</span>
        @elseif($mission->statut == 'En cours')
            <span style="padding: 4px 8px; border-radius: 20px; font-size: 11px; font-weight: 600; background: #FEF08A; color: #854D0E; white-space: nowrap;">En cours</span>
        @elseif($mission->statut == 'Terminée')
            <span style="padding: 4px 8px; border-radius: 20px; font-size: 11px; font-weight: 600; background: #DCFCE7; color: #15803D; white-space: nowrap;">Terminée</span>
        @elseif($mission->statut == 'Annulée')
            <span style="padding: 4px 8px; border-radius: 20px; font-size: 11px; font-weight: 600; background: #FEE2E2; color: #991B1B; white-space: nowrap;">Annulée</span>
        @endif
    </td>
    
    <!-- 🎯 خانة الأزرار المحدثة -->
    <td style="padding: 10px; text-align: center; white-space: nowrap;">
        <div style="display: inline-flex; gap: 4px; align-items: center; justify-content: center;">
            <button type="button" style="border:none; background: #3b82f6; color: white; padding: 5px 8px; border-radius: 6px; cursor: pointer; font-size: 12px;" title="Consulter" onclick="openViewModal({{ json_encode($mission->load('demandes')) }})">
                <i class="fa-solid fa-eye"></i>
            </button>
            
            @if(auth()->user()->canAccess($screen, 'edit') && $mission->statut == 'Planifiée')
                <button type="button" style="border:none; background: #f59e0b; color: white; padding: 5px 8px; border-radius: 6px; cursor: pointer; font-size: 12px;" title="Modifier" onclick="openEditModal({{ json_encode($mission) }}, {{ $mission->demandes->pluck('id') }})">
                    <i class="fa-solid fa-pen"></i>
                </button>
            @endif

           @if(auth()->user()->canAccess($screen, 'start') && $mission->statut == 'Planifiée')
    <button type="button" 
            style="border:none; background: #854D0E; color: white; padding: 5px 8px; border-radius: 6px; cursor: pointer; font-size: 12px;" 
            title="Départ" 
            onclick="openDepartModal({{ $mission->id }})">
        <i class="fa-solid fa-truck-fast"></i>
    </button>
@endif

            @if(auth()->user()->canAccess($screen, 'close') && $mission->statut == 'En cours')
                <button type="button" style="border:none; background: #10b981; color: white; padding: 5px 8px; border-radius: 6px; cursor: pointer; font-size: 12px;" title="Clôturer" onclick="openClotureModal({{ $mission->id }}, '{{ $mission->date_depart }}')">
                    <i class="fa-solid fa-lock"></i>
                </button>
            @endif

            @if(auth()->user()->canAccess($screen, 'cancel') && $mission->statut == 'En cours')
                <button type="button" style="border:none; background: #ef4444; color: white; padding: 5px 8px; border-radius: 6px; cursor: pointer; font-size: 12px;" title="Annuler" onclick="openAnnulerModal({{ $mission->id }})">
                    <i class="fa-solid fa-ban"></i>
                </button>
            @endif

            @if(auth()->user()->canAccess($screen, 'delete') && $mission->statut == 'Planifiée')
                <form action="{{ route('collecte.missions.destroy', $mission->id) }}" method="POST" style="margin:0; display:inline;"> @csrf @method('DELETE')
                    <button type="submit" style="border:none; background: #991b1b; color: white; padding: 5px 8px; border-radius: 6px; cursor: pointer; font-size: 12px;" title="Supprimer" onclick="return confirm('Supprimer ?')">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </form>
            @endif
        </div>
    </td>
</tr>
@empty
<tr><td colspan="8" style="text-align:center; padding: 30px; color: #888;">Aucune mission trouvée.</td></tr>
@endforelse