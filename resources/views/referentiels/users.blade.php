@extends('layouts.app')

@section('content')
<div class="page-title" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
    <div>
        <h2>Gestion des Comptes Utilisateurs</h2>
        <p style="color: #777;">Attribuer des comptes d'accès pour le personnel du système MetalCycle</p>
    </div>
    
    @if(auth()->user()->canAccess($screen, 'create'))
        <button class="red" style="border:none; padding:10px 20px; color: white; background-color: #C1121F; border-radius:8px; cursor:pointer; font-weight: 600;" onclick="openAddModal()">
            <i class="fa-solid fa-user-plus"></i> Créer un Compte
        </button>
    @endif
</div>

<div style="margin-bottom: 20px; display: flex; max-width: 400px; position: relative;">
    <span style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #64748b;">
        <i class="fa-solid fa-magnifying-glass"></i>
    </span>
    <input type="text" id="userSearchBar" onkeyup="filterUsersTable()" placeholder="Rechercher par nom, prénom, identifiant..." 
           style="width: 100%; padding: 12px 12px 12px 40px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; outline: none; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); transition: border 0.2s;"
           onfocus="this.style.borderColor='#C1121F'" onblur="this.style.borderColor='#e2e8f0'">
</div>

@if(session('success'))
    <div style="background-color: #DCFCE7; color: #15803D; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: 600;">
        {{ session('success') }}
    </div>
@endif

<div class="table-card">
    <table id="usersTable"> 
        <thead>
            <tr>
                <th>Matricule</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Nom d'utilisateur</th>
                <th>Rôle / Fonction</th>
                <th>Profil Système</th> 
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($utilisateurs as $u)
            <tr>
                <td><span style="background: #F1F5F9; color: #475569; padding: 5px 10px; border-radius:6px; font-weight:600;">{{ $u->person->matricule ?? '---' }}</span></td>
                <td><strong>{{ $u->person->nom ?? '---' }}</strong></td>
                <td><strong>{{ $u->person->prenom ?? '---' }}</strong></td>
                <td style="color: #0284c7; font-weight:600;">{{ $u->username }}</td>
                <td>
                    <span style="padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; background: #FEF3C7; color: #92400E;">
                        {{ $u->person->role->nom ?? 'Aucun' }}
                    </span>
                </td>
                <td>
                    <span style="padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; background: #E0F2FE; color: #0369A1;">
                        {{ $u->profile->nom ?? 'Non assigné' }}
                    </span>
                </td>
                <td>
                    <div style="display: flex; gap: 10px;">
                        @if(auth()->user()->canAccess($screen, 'edit'))
                            <button type="button" class="warning" style="border:none; cursor:pointer; font-size:14px; padding: 6px 12px; background: #f59e0b; color: white; border-radius: 6px;" onclick="openEditModal({{ json_encode($u) }})">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                        @endif

                        @if(auth()->user()->canAccess($screen, 'delete'))
                            <form action="{{ route('referentiels.users.destroy', $u->id) }}" method="POST" onsubmit="return confirm('Supprimer ce compte utilisateur ? (L\'employé restera inscrit)')" style="margin:0;">
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
                <td colspan="7" style="text-align:center; color: #888; padding: 30px;">Aucun compte utilisateur créé.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div id="addModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index:999; align-items:center; justify-content:center;">
    <div style="background:white; width:480px; padding:35px; border-radius:20px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);">
        <h3 style="margin-top:0; margin-bottom: 20px; font-weight: 700; color: #1e293b; font-size: 22px;">Créer un Compte</h3>
        <form action="{{ route('referentiels.users.store') }}" method="POST" style="display:flex; flex-direction:column; gap:15px;">
            @csrf
            
            <div style="display: flex; flex-direction: column; gap: 4px;">
                <label style="font-size: 13px; color: #334155; font-weight: 600;">Sélectionner l'Employé</label>
                <select name="person_id" required style="padding:12px; border:1px solid #e2e8f0; border-radius:8px; width:100%; box-sizing:border-box; background:white; font-size:14px;">
                    <option value="" disabled selected>-- Choisir un employé disponible --</option>
                    @foreach($personnesSansCompte as $p)
                        <option value="{{ $p->id }}">{{ $p->matricule }} - {{ $p->nom }} {{ $p->prenom }}</option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; flex-direction: column; gap: 4px;">
                <label style="font-size: 13px; color: #334155; font-weight: 600;">Profil Droits & Permissions</label>
                <select name="profile_id" required style="padding:12px; border:1px solid #e2e8f0; border-radius:8px; width:100%; box-sizing:border-box; background:white; font-size:14px;">
                    <option value="" disabled selected>-- Choisir un profil de droits --</option>
                    @foreach($profiles as $profile)
                        <option value="{{ $profile->id }}">{{ $profile->nom }} ({{ $profile->code }})</option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; flex-direction: column; gap: 4px;">
                <label style="font-size: 13px; color: #334155; font-weight: 600;">Identifiant (Username)</label>
                <input type="text" name="username" placeholder="Nom de connexion" required style="padding:10px; border:1px solid #e2e8f0; border-radius:8px; width:100%; box-sizing:border-box;">
            </div>

            <div style="display: flex; flex-direction: column; gap: 4px;">
                <label style="font-size: 13px; color: #334155; font-weight: 600;">Mot de passe</label>
                <input type="password" name="password" placeholder="••••••••" required style="padding:10px; border:1px solid #e2e8f0; border-radius:8px; width:100%; box-sizing:border-box;">
            </div>

            <div style="display:flex; justify-content: flex-end; gap:12px; margin-top:15px;">
                <button type="button" onclick="closeAddModal()" style="background:#f1f5f9; color:#475569; border:none; padding:10px 20px; border-radius:8px; cursor:pointer; font-weight:600;">Annuler</button>
                <button type="submit" style="background:#C1121F; color:white; border:none; padding:10px 20px; border-radius:8px; cursor:pointer; font-weight:600;">Créer le Compte</button>
            </div>
        </form>
    </div>
</div>

<div id="editModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index:999; align-items:center; justify-content:center;">
    <div style="background:white; width:500px; padding:35px; border-radius:20px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);">
        <h3 style="margin-top:0; margin-bottom: 20px; font-weight: 700; color: #1e293b; font-size: 22px;">Modifier l'Utilisateur</h3>
        <form id="editForm" method="POST" style="display:flex; flex-direction:column; gap:12px;">
            @csrf
            @method('PUT')
            <div style="display: flex; flex-direction: column; gap: 4px;">
                <label style="font-size: 13px; color: #64748b; font-weight: 600;">Matricule (Non modifiable)</label>
                <input type="text" id="edit_matricule" disabled style="padding:10px; border:1px solid #e2e8f0; border-radius:8px; background: #f8fafc; color:#64748b; width:100%; box-sizing:border-box;">
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                <div style="display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 13px; color: #334155; font-weight: 600;">Nom</label>
                    <input type="text" name="nom" id="edit_nom" required style="padding:10px; border:1px solid #e2e8f0; border-radius:8px; width:100%; box-sizing:border-box;">
                </div>
                <div style="display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 13px; color: #334155; font-weight: 600;">Prénom</label>
                    <input type="text" name="prenom" id="edit_prenom" required style="padding:10px; border:1px solid #e2e8f0; border-radius:8px; width:100%; box-sizing:border-box;">
                </div>
            </div>
            <div style="display: flex; flex-direction: column; gap: 4px;">
                <label style="font-size: 13px; color: #334155; font-weight: 600;">Rôle / Fonction</label>
                <select name="role_id" id="edit_role" required style="padding:10px; border:1px solid #e2e8f0; border-radius:8px; width:100%; box-sizing:border-box; background:white;">
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}">{{ $role->nom }}</option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; flex-direction: column; gap: 4px;">
                <label style="font-size: 13px; color: #334155; font-weight: 600;">Profil Système (الصلاحيات)</label>
                <select name="profile_id" id="edit_profile" required style="padding:10px; border:1px solid #e2e8f0; border-radius:8px; width:100%; box-sizing:border-box; background:white;">
                    @foreach($profiles as $profile)
                        <option value="{{ $profile->id }}">{{ $profile->nom }}</option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; flex-direction: column; gap: 4px;">
                <label style="font-size: 13px; color: #334155; font-weight: 600;">Identifiant (Username)</label>
                <input type="text" name="username" id="edit_username" required style="padding:10px; border:1px solid #e2e8f0; border-radius:8px; width:100%; box-sizing:border-box;">
            </div>
            <div style="display: flex; flex-direction: column; gap: 4px;">
                <label style="font-size: 13px; color: #334155; font-weight: 600;">Mot de passe (Laisser vide pour ne pas changer)</label>
                <input type="password" name="password" placeholder="••••••••" style="padding:10px; border:1px solid #e2e8f0; border-radius:8px; width:100%; box-sizing:border-box;">
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

function openEditModal(u) {
    document.getElementById('editForm').action = '/referentiels/utilisateurs/' + u.id;
    
    document.getElementById('edit_matricule').value = u.person ? u.person.matricule : '';
    document.getElementById('edit_nom').value = u.person ? u.person.nom : '';
    document.getElementById('edit_prenom').value = u.person ? u.person.prenom : '';
    document.getElementById('edit_role').value = u.person ? u.person.role_id : '';
    document.getElementById('edit_profile').value = u.profile_id; // تمرير قيمة البروفايل للـ Modal تلقائياً
    document.getElementById('edit_username').value = u.username;
    
    document.getElementById('editModal').style.display = 'flex';
}
function closeEditModal() { document.getElementById('editModal').style.display = 'none'; }



// 🔍 دالة تصفية وجدولة البحث الفوري
function filterUsersTable() {
    let input = document.getElementById("userSearchBar");
    let filter = input.value.toLowerCase().trim();
    let table = document.getElementById("usersTable");
    let tr = table.getElementsByTagName("tr");

    // نمر على كل الأسطر في الجدول (ما عدا الـ Header رقم 0)
    for (let i = 1; i < tr.length; i++) {
        let tdNom = tr[i].getElementsByTagName("td")[1];      // عمود اللقب
        let tdPrenom = tr[i].getElementsByTagName("td")[2];   // عمود الاسم
        let tdUser = tr[i].getElementsByTagName("td")[3];     // عمود الـ Username

        if (tdNom || tdPrenom || tdUser) {
            let nomText = tdNom.textContent || tdNom.innerText;
            let prenomText = tdPrenom.textContent || tdPrenom.innerText;
            let userText = tdUser.textContent || tdUser.innerText;

            // إذا تطابق نص البحث مع أي عمود من الأعمدة الثلاثة نظهر السطر، وإلا نخفيه
            if (
                nomText.toLowerCase().indexOf(filter) > -1 || 
                prenomText.toLowerCase().indexOf(filter) > -1 || 
                userText.toLowerCase().indexOf(filter) > -1
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