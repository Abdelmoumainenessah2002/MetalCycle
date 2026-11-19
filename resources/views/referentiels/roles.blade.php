@extends('layouts.app')

@section('content')
<div class="page-title" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
    <div>
        <h2>Gestion des Rôles</h2>
        <p style="color: #777;">Gestion des fonctions et permissions du système</p>
    </div>
    
    {{-- 🔒 فحص صلاحية الإنشاء قبل إظهار زر الإضافة --}}
    @if(auth()->user()->canAccess($screen, 'create'))
        <button class="red" style="border:none; padding:10px 20px; color: white; background-color: #C1121F; border-radius:8px; cursor:pointer; font-weight: 600;" onclick="openAddModal()">
            <i class="fa-solid fa-shield-halved"></i> Créer Rôle
        </button>
    @endif
</div>

<div style="margin-bottom: 20px; display: flex; max-width: 400px; position: relative;">
    <span style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #64748b;">
        <i class="fa-solid fa-magnifying-glass"></i>
    </span>
    <input type="text" id="roleSearchBar" onkeyup="filterRoleTable()" placeholder="Rechercher par nom ou code..." 
           style="width: 100%; padding: 12px 12px 12px 40px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; outline: none; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); transition: border 0.2s;"
           onfocus="this.style.borderColor='#C1121F'" onblur="this.style.borderColor='#e2e8f0'">
</div>

@if(session('success'))
    <div style="background-color: #DCFCE7; color: #15803D; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: 600;">
        {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div style="background-color: #FEE2E2; color: #991B1B; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: 600;">
        {{ session('error') }}
    </div>
@endif

<div class="table-card">
    <table id="roleTable">
        <thead>
            <tr>
                <th>Code</th>
                <th>Nom du Rôle</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($roles as $r)
            <tr>
                <td><span style="background: #F1F5F9; color: #475569; padding: 5px 10px; border-radius:6px; font-weight:600;">{{ $r->code }}</span></td>
                <td><strong>{{ $r->nom }}</strong></td>
                <td style="color: #64748b;">{{ $r->description ?? '---' }}</td>
                <td>
                    <div style="display: flex; gap: 10px;">
                        {{-- 🔒 فحص صلاحية التعديل قبل إظهار الزر --}}
                        @if(auth()->user()->canAccess($screen, 'edit'))
                            <button type="button" class="warning" style="border:none; cursor:pointer; font-size:14px; padding: 6px 12px; background: #f59e0b; color: white; border-radius: 6px;" onclick="openEditModal({{ json_encode($r) }})">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                        @endif

                        {{-- 🔒 فحص صلاحية الحذف قبل إظهار الفورم --}}
                        @if(auth()->user()->canAccess($screen, 'delete'))
                            <form action="{{ route('referentiels.roles.destroy', $r->id) }}" method="POST" onsubmit="return confirm('Supprimer ce rôle définitivement ?')" style="margin:0;">
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
                <td colspan="4" style="text-align:center; color: #888; padding: 30px;">Aucun rôle enregistré.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div id="addModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index:999; align-items:center; justify-content:center;">
    <div style="background:white; width:450px; padding:35px; border-radius:20px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);">
        <h3 style="margin-top:0; margin-bottom: 20px; font-weight: 700; color: #1e293b; font-size: 22px;">Créer un Rôle</h3>
        <form action="{{ route('referentiels.roles.store') }}" method="POST" style="display:flex; flex-direction:column; gap:12px;">
            @csrf
            <div style="display: flex; flex-direction: column; gap: 4px;">
                <label style="font-size: 13px; color: #334155; font-weight: 600;">Code du Rôle</label>
                <input type="text" name="code" placeholder="Ex: ADMIN, OPERATOR" required style="padding:10px; border:1px solid #e2e8f0; border-radius:8px; width:100%; box-sizing:border-box;">
            </div>
            <div style="display: flex; flex-direction: column; gap: 4px;">
                <label style="font-size: 13px; color: #334155; font-weight: 600;">Nom</label>
                <input type="text" name="nom" placeholder="Ex: Administrateur" required style="padding:10px; border:1px solid #e2e8f0; border-radius:8px; width:100%; box-sizing:border-box;">
            </div>
            <div style="display: flex; flex-direction: column; gap: 4px;">
                <label style="font-size: 13px; color: #334155; font-weight: 600;">Description</label>
                <textarea name="description" rows="3" style="padding:10px; border:1px solid #e2e8f0; border-radius:8px; width:100%; box-sizing:border-box; resize: none; font-family:inherit;"></textarea>
            </div>
            <div style="display:flex; justify-content: flex-end; gap:12px; margin-top:15px;">
                <button type="button" onclick="closeAddModal()" style="background:#f1f5f9; color:#475569; border:none; padding:10px 20px; border-radius:8px; cursor:pointer; font-weight:600;">Annuler</button>
                <button type="submit" style="background:#C1121F; color:white; border:none; padding:10px 20px; border-radius:8px; cursor:pointer; font-weight:600;">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<div id="editModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index:999; align-items:center; justify-content:center;">
    <div style="background:white; width:450px; padding:35px; border-radius:20px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);">
        <h3 style="margin-top:0; margin-bottom: 20px; font-weight: 700; color: #1e293b; font-size: 22px;">Modifier le Rôle</h3>
        <form id="editForm" method="POST" style="display:flex; flex-direction:column; gap:12px;">
            @csrf
            @method('PUT')
            <div style="display: flex; flex-direction: column; gap: 4px;">
                <label style="font-size: 13px; color: #334155; font-weight: 600;">Code du Rôle</label>
                <input type="text" name="code" id="edit_code" required style="padding:10px; border:1px solid #e2e8f0; border-radius:8px; width:100%; box-sizing:border-box;">
            </div>
            <div style="display: flex; flex-direction: column; gap: 4px;">
                <label style="font-size: 13px; color: #334155; font-weight: 600;">Nom</label>
                <input type="text" name="nom" id="edit_nom" required style="padding:10px; border:1px solid #e2e8f0; border-radius:8px; width:100%; box-sizing:border-box;">
            </div>
            <div style="display: flex; flex-direction: column; gap: 4px;">
                <label style="font-size: 13px; color: #334155; font-weight: 600;">Description</label>
                <textarea name="description" id="edit_description" rows="3" style="padding:10px; border:1px solid #e2e8f0; border-radius:8px; width:100%; box-sizing:border-box; resize: none; font-family:inherit;"></textarea>
            </div>
            <div style="display:flex; justify-content: flex-end; gap:12px; margin-top:15px;">
                <button type="button" onclick="closeEditModal()" style="background:#f1f5f9; color:#475569; border:none; padding:10px 20px; border-radius:8px; cursor:pointer; font-weight:600;">Annuler</button>
                <button type="submit" style="background:#f59e0b; color:white; border:none; padding:10px 20px; border-radius:8px; cursor:pointer; font-weight:600;">Modifier</button>
            </div>
        </form>
    </div>
</div>

<script>
function openAddModal() { document.getElementById('addModal').style.display = 'flex'; }
function closeAddModal() { document.getElementById('addModal').style.display = 'none'; }
function openEditModal(r) {
    document.getElementById('editForm').action = '/referentiels/roles/' + r.id;
    document.getElementById('edit_code').value = r.code;
    document.getElementById('edit_nom').value = r.nom;
    document.getElementById('edit_description').value = r.description ? r.description : '';
    document.getElementById('editModal').style.display = 'flex';
}
function closeEditModal() { document.getElementById('editModal').style.display = 'none'; }

function filterRoleTable() {
    let input = document.getElementById("roleSearchBar");
    let filter = input.value.toLowerCase().trim();
    let table = document.getElementById("roleTable");
    let tr = table.getElementsByTagName("tr");

    for (let i = 1; i < tr.length; i++) {
        let tdCode = tr[i].getElementsByTagName("td")[0];
        let tdNom = tr[i].getElementsByTagName("td")[1];

        if (tdCode || tdNom) {
            let codeText = tdCode.textContent || tdCode.innerText;
            let nomText = tdNom.textContent || tdNom.innerText;

            if (codeText.toLowerCase().indexOf(filter) > -1 || nomText.toLowerCase().indexOf(filter) > -1) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }
    }
}
</script>
@endsection