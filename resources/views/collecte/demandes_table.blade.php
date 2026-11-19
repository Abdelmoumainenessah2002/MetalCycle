<div style="overflow-x: auto;">
    <table id="demandeTable" style="width: 100%; border-collapse: collapse; text-align: left; font-size: 14px;">
        <thead style="background-color: #f8fafc; border-bottom: 2px solid #e2e8f0;">
            <tr>
                <th style="padding: 12px 15px; color: #ffffff; font-weight: 600;">N° Demande</th>
                <th style="padding: 12px 15px; color: #ffffff; font-weight: 600;">Date</th>
                <th style="padding: 12px 15px; color: #ffffff; font-weight: 600;">Fournisseur</th>
                <th style="padding: 12px 15px; color: #ffffff; font-weight: 600;">Matière</th>
                <th style="padding: 12px 15px; color: #ffffff; font-weight: 600;">Qté Estimée</th>
                <th style="padding: 12px 15px; color: #ffffff; font-weight: 600;">Statut</th>
                <th style="padding: 12px 15px; color: #ffffff; font-weight: 600; text-align: center;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($demandes as $demande)
            <tr id="demande-row-{{ $demande->id }}" style="border-bottom: 1px solid #f1f5f9; transition: background 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                <td style="padding: 12px 15px;">
                    @if($demande->numero)
                        <span style="background: #f1f5f9; color: #334155; padding: 4px 8px; border-radius: 6px; font-weight: 600; font-size: 13px;">
                            {{ $demande->numero }}
                        </span>
                    @else
                        <span style="color: #000000; font-style: italic; font-size: 13px;">---</span>
                    @endif
                </td>
                <td style="padding: 12px 15px; color: #334155;">
                    {{ \Carbon\Carbon::parse($demande->date_demande)->format('d/m/Y') }}
                </td>
                <td style="padding: 12px 15px; color: #1e293b;">
                    <strong>{{ $demande->fournisseur->nom_fournisseur ?? '---' }}</strong>
                </td>
                <td style="padding: 12px 15px; color: #0284c7; font-weight: 600;">
                    {{ $demande->materiau->nom ?? '---' }}
                </td>
                <td style="padding: 12px 15px; color: #334155;">
                    {{ $demande->quantite_estimee }} {{ $demande->materiau->unite ?? 'Kg' }}
                </td>
                <td style="padding: 12px 15px;" class="status-cell">
                    @if($demande->statut == 'En attente')
                        <span style="padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; background: #fef08a; color: #854d0e;">En attente</span>
                    @elseif($demande->statut == 'Validée')
                        <span style="padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; background: #dcfce7; color: #15803d;">Validée</span>
                    @elseif($demande->statut == 'Traitée')
                        <span style="padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; background: #e0f2fe; color: #0369a1;">Traitée</span>
                    @elseif($demande->statut == 'Annulée')
                        <span style="padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; background: #f1f5f9; color: #475569;">Annulée</span>
                    @elseif($demande->statut == 'Refusée')
                        <span style="padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; background: #fee2e2; color: #991b1b;">Refusée</span>
                    @endif
                </td>
                <td style="padding: 12px 15px;">
                    <div style="display: flex; gap: 6px; justify-content: center; align-items: center;">
                        <button type="button" style="border:none; cursor:pointer; font-size:12px; padding: 6px 10px; background: #3b82f6; color: white; border-radius: 6px;" title="Consulter" onclick="openViewModal({{ json_encode($demande) }})">
                            <i class="fa-solid fa-eye"></i>
                        </button>

                        @if($demande->statut == 'En attente')
                            @if(auth()->user()->canAccess($screen, 'edit'))
                                <button type="button" style="border:none; cursor:pointer; font-size:12px; padding: 6px 10px; background: #f59e0b; color: white; border-radius: 6px;" title="Modifier" onclick="openEditModal({{ json_encode($demande) }})">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                            @endif

                            @if(auth()->user()->canAccess($screen, 'validate'))
                                <button type="button" style="border:none; cursor:pointer; font-size:12px; padding: 6px 10px; background: #10b981; color: white; border-radius: 6px;" title="Valider" onclick="openValiderModal({{ $demande->id }})">
                                    <i class="fa-solid fa-check"></i>
                                </button>
                            @endif

                            @if(auth()->user()->canAccess($screen, 'delete'))
                                <form action="{{ route('collecte.demandes.destroy', $demande->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette demande ?')" style="margin:0;">
                                    @csrf @method('DELETE')
                                    <button type="submit" style="border:none; cursor:pointer; font-size:12px; padding: 6px 10px; background: #ef4444; color: white; border-radius: 6px;" title="Supprimer">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            @endif
                        @endif

                        @if($demande->statut == 'Validée')
                            @if(auth()->user()->canAccess($screen, 'cancel'))
                                <button type="button" class="btn-annuler" style="border:none; cursor:pointer; font-size:12px; padding: 6px 10px; background: #64748b; color: white; border-radius: 6px;" title="Annuler" onclick="openAnnulerDemandeModal({{ $demande->id }})">
                                    <i class="fa-solid fa-ban"></i>
                                </button>
                            @endif
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align:center; color: #94a3b8; padding: 30px; font-weight: 500;">
                    <i class="fa-solid fa-inbox" style="font-size: 24px; display: block; margin-bottom: 8px; color: #cbd5e1;"></i>
                    Aucune demande de collecte trouvée.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- رابط الترقيم (Pagination) -->
<div class="pagination-wrapper" style="padding: 15px 20px; border-top: 1px solid #f1f5f9; background: #ffffff;">
    {{ $demandes->links() }}
</div>