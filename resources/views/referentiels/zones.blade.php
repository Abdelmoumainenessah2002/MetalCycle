@extends('layouts.app')

@section('content')
<div class="page-title" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
    <div>
        <h2>Gestion des Zones de Stockage</h2>
        <p style="color: #777;">Liste des zones de stockage configurées dans le système</p>
    </div>
    
    @if(auth()->user()->canAccess($screen, 'create'))
        <button class="red" style="border:none; padding:10px 20px; color: white; background-color: #C1121F; border-radius:8px; cursor:pointer; font-weight: 600;" onclick="openAddModal()">
            <i class="fa-solid fa-plus"></i> Ajouter Zone
        </button>
    @endif
</div>

<div style="margin-bottom: 20px; display: flex; max-width: 400px; position: relative;">
    <span style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #64748b;">
        <i class="fa-solid fa-magnifying-glass"></i>
    </span>
    <input type="text" id="zoneSearchBar" onkeyup="filterZoneTable()" placeholder="Rechercher par nom, code, description..." 
           style="width: 100%; padding: 12px 12px 12px 40px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; outline: none; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); transition: border 0.2s;"
           onfocus="this.style.borderColor='#C1121F'" onblur="this.style.borderColor='#e2e8f0'">
</div>

@if(session('success'))
    <div style="background-color: #DCFCE7; color: #15803D; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: 600;">
        {{ session('success') }}
    </div>
@endif

<div class="table-card">
    <table id="zoneTable">
        <thead>
            <tr>
                <th>Code</th>
                <th>Nom de la Zone</th>
                <th>Capacité Max</th>
                <th>Unité</th>
                <th>Description</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($zones as $zone)
            <tr>
                <td><span style="background: #F1F5F9; color: #475569; padding: 5px 10px; border-radius:6px; font-weight:600;">{{ $zone->code }}</span></td>
                <td><strong>{{ $zone->nom }}</strong></td>
                <td>{{ $zone->capacite_max ?? '---' }}</td>
                <td><span style="color: #0284c7; font-weight: 600;">{{ $zone->unite ?? 'Kg' }}</span></td>
                <td style="max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: #64748b;" title="{{ $zone->description }}">
                    {{ $zone->description ?? '---' }}
                </td>
                <td>
                    @if($zone->actif)
                        <span style="padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; background: #DCFCE7; color: #15803D;">Actif</span>
                    @else
                        <span style="padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; background: #FEE2E2; color: #991B1B;">Inactif</span>
                    @endif
                </td>
                <td>
                    <div style="display: flex; gap: 8px;">
                        @if(auth()->user()->canAccess($screen, 'edit'))
                            <button type="button" class="warning" style="border:none; cursor:pointer; font-size:14px; padding: 6px 12px; background: #f59e0b; color: white; border-radius: 6px;" onclick="openEditModal({{ json_encode($zone) }})">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                        @endif

                        @if(auth()->user()->canAccess($screen, 'delete'))
                            <form action="{{ route('referentiels.zones.destroy', $zone->id) }}" method="POST" onsubmit="return confirm('Supprimer cette zone ?')" style="margin:0;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="danger" style="border:none; cursor:pointer; font-size:14px; padding: 6px 12px; background: #dc2626; color: white; border-radius: 6px;">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align:center; color: #888; padding: 30px;">Aucune zone de stockage enregistrée.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div id="addModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index:999; align-items:center; justify-content:center; padding: 20px; box-sizing: border-box;">
    <div style="background:white; width:100%; max-width:720px; padding: 25px 30px; border-radius: 20px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); overflow: hidden; box-sizing: border-box;">
        <h3 style="margin-top:0; margin-bottom: 18px; font-weight: 700; color: #1e293b; font-size: 20px;">Ajouter une Zone de Stockage</h3>
        
        <form action="{{ route('referentiels.zones.store') }}" method="POST" style="display:flex; flex-direction:column; gap:12px; margin: 0;">
            @csrf
            
            <div style="display: flex; gap: 15px;">
                <div style="flex: 1; display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 13px; color: #334155; font-weight: 600;">Code Zone</label>
                    <input type="text" name="code" placeholder="Ex: Z-001" required style="padding:10px 14px; border:1px solid #e2e8f0; border-radius:10px; width:100%; box-sizing:border-box; font-size: 13px; outline: none;">
                </div>
                <div style="flex: 1; display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 13px; color: #334155; font-weight: 600;">Nom de la Zone</label>
                    <input type="text" name="nom" required style="padding:10px 14px; border:1px solid #e2e8f0; border-radius:10px; width:100%; box-sizing:border-box; font-size: 13px; outline: none;">
                </div>
            </div>

            <div style="display: flex; gap: 15px;">
                <div style="flex: 1; display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 13px; color: #334155; font-weight: 600;">Capacité Max</label>
                    <input type="number" step="0.01" name="capacite_max" placeholder="Ex: 1500.00" style="padding:10px 14px; border:1px solid #e2e8f0; border-radius:10px; width:100%; box-sizing:border-box; font-size: 13px; outline: none;">
                </div>
                <div style="flex: 1; display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 13px; color: #334155; font-weight: 600;">Unité de mesure</label>
                    <input type="text" name="unite" value="Kg" placeholder="Ex: Kg, L, Palette" style="padding:10px 14px; border:1px solid #e2e8f0; border-radius:10px; width:100%; box-sizing:border-box; font-size: 13px; outline: none;">
                </div>
            </div>

            <div style="display: flex; flex-direction: column; gap: 4px;">
                <label style="font-size: 13px; color: #334155; font-weight: 600;">Description</label>
                <textarea name="description" rows="2" style="padding:10px 14px; border:1px solid #e2e8f0; border-radius:10px; resize: none; font-size: 13px; outline: none; width:100%; box-sizing:border-box;"></textarea>
            </div>

            <div style="display:flex; justify-content: flex-end; gap:12px; margin-top:10px;">
                <button type="button" onclick="closeAddModal()" style="background:#f1f5f9; color:#475569; border:none; padding:11px 20px; border-radius:10px; cursor:pointer; font-weight:600; font-size: 13px;">Annuler</button>
                <button type="submit" style="background:#C1121F; color:white; border:none; padding:11px 20px; border-radius:10px; cursor:pointer; font-weight:600; font-size: 13px;">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<div id="editModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index:999; align-items:center; justify-content:center; padding: 20px; box-sizing: border-box;">
    <div style="background:white; width:100%; max-width:720px; padding: 25px 30px; border-radius: 20px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); overflow: hidden; box-sizing: border-box;">
        <h3 style="margin-top:0; margin-bottom: 18px; font-weight: 700; color: #1e293b; font-size: 20px;">Modifier la Zone de Stockage</h3>
        
        <form id="editForm" method="POST" style="display:flex; flex-direction:column; gap:12px; margin: 0;">
            @csrf
            @method('PUT')
            
            <div style="display: flex; gap: 15px;">
                <div style="flex: 1; display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 13px; color: #334155; font-weight: 600;">Code Zone</label>
                    <input type="text" name="code" id="edit_code" required style="padding:10px 14px; border:1px solid #e2e8f0; border-radius:10px; width:100%; box-sizing:border-box; font-size: 13px; outline: none;">
                </div>
                <div style="flex: 1; display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 13px; color: #334155; font-weight: 600;">Nom de la Zone</label>
                    <input type="text" name="nom" id="edit_nom" required style="padding:10px 14px; border:1px solid #e2e8f0; border-radius:10px; width:100%; box-sizing:border-box; font-size: 13px; outline: none;">
                </div>
            </div>

            <div style="display: flex; gap: 15px;">
                <div style="flex: 1; display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 13px; color: #334155; font-weight: 600;">Capacité Max</label>
                    <input type="number" step="0.01" name="capacite_max" id="edit_capacite" style="padding:10px 14px; border:1px solid #e2e8f0; border-radius:10px; width:100%; box-sizing:border-box; font-size: 13px; outline: none;">
                </div>
                <div style="flex: 1; display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 13px; color: #334155; font-weight: 600;">Unité</label>
                    <input type="text" name="unite" id="edit_unite" style="padding:10px 14px; border:1px solid #e2e8f0; border-radius:10px; width:100%; box-sizing:border-box; font-size: 13px; outline: none;">
                </div>
            </div>

            <div style="display: flex; flex-direction: column; gap: 4px;">
                <label style="font-size: 13px; color: #334155; font-weight: 600;">Description</label>
                <textarea name="description" id="edit_description" rows="2" style="padding:10px 14px; border:1px solid #e2e8f0; border-radius:10px; resize: none; font-size: 13px; outline: none; width:100%; box-sizing:border-box;"></textarea>
            </div>

            <div style="display: flex; flex-direction: column; gap: 4px;">
                <label style="font-size: 13px; color: #334155; font-weight: 600;">Statut</label>
                <select name="actif" id="edit_actif" required style="padding:10px 14px; border:1px solid #e2e8f0; border-radius:10px; background:white; font-size: 13px; outline: none; width:100%; box-sizing:border-box;">
                    <option value="1">Actif</option>
                    <option value="0">Inactif</option>
                </select>
            </div>

            <div style="display:flex; justify-content: flex-end; gap:12px; margin-top:10px;">
                <button type="button" onclick="closeEditModal()" style="background:#f1f5f9; color:#475569; border:none; padding:11px 20px; border-radius:10px; cursor:pointer; font-weight:600; font-size: 13px;">Annuler</button>
                <button type="submit" style="background:#f59e0b; color:white; border:none; padding:11px 20px; border-radius:10px; cursor:pointer; font-weight:600; font-size: 13px;">Modifier</button>
            </div>
        </form>
    </div>
</div>

<script>
function openAddModal() { document.getElementById('addModal').style.display = 'flex'; }
function closeAddModal() { document.getElementById('addModal').style.display = 'none'; }

function openEditModal(z) {
    document.getElementById('editForm').action = '/referentiels/zones/' + z.id;
    document.getElementById('edit_code').value = z.code;
    document.getElementById('edit_nom').value = z.nom;
    document.getElementById('edit_capacite').value = z.capacite_max ? z.capacite_max : '';
    document.getElementById('edit_unite').value = z.unite ? z.unite : 'Kg';
    document.getElementById('edit_description').value = z.description ? z.description : '';
    document.getElementById('edit_actif').value = z.actif;
    document.getElementById('editModal').style.display = 'flex';
}
function closeEditModal() { document.getElementById('editModal').style.display = 'none'; }

// نظام الفلترة والبحث الفوري المحدّث (يبحث في الكود والاسم والوصف)
function filterZoneTable() {
    let input = document.getElementById("zoneSearchBar");
    let filter = input.value.toLowerCase().trim();
    let table = document.getElementById("zoneTable");
    let tr = table.getElementsByTagName("tr");

    for (let i = 1; i < tr.length; i++) {
        let tdCode = tr[i].getElementsByTagName("td")[0]; 
        let tdNom = tr[i].getElementsByTagName("td")[1];  
        let tdDesc = tr[i].getElementsByTagName("td")[4];  

        if (tdCode || tdNom || tdDesc) {
            let codeText = tdCode.textContent || tdCode.innerText;
            let nomText = tdNom.textContent || tdNom.innerText;
            let descText = tdDesc.textContent || tdDesc.innerText;

            if (
                codeText.toLowerCase().indexOf(filter) > -1 || 
                nomText.toLowerCase().indexOf(filter) > -1 ||
                descText.toLowerCase().indexOf(filter) > -1
            ) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }
    }
}
</script>
@endsection