@extends('layouts.app')

@section('content')
<div class="page-title" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
    <div>
        <h2>Gestion du Personnel</h2>
        <p style="color: #777;">Liste des employés enregistrés dans l'établissement</p>
    </div>
    
    {{-- فحص صلاحية الإنشاء قبل إظهار زر الإضافة --}}
    @if(auth()->user()->canAccess($screen, 'create'))
        <button class="red" style="border:none; padding:10px 20px; color: white; background-color: #C1121F; border-radius:8px; cursor:pointer; font-weight: 600;" onclick="openAddModal()">
            <i class="fa-solid fa-users"></i> Ajouter un Employé
        </button>
    @endif
</div>

<div style="margin-bottom: 20px; display: flex; max-width: 400px; position: relative;">
    <span style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #64748b;">
        <i class="fa-solid fa-magnifying-glass"></i>
    </span>
    <input type="text" id="personnelSearchBar" onkeyup="filterPersonnelTable()" placeholder="Rechercher par nom, matricule..." 
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
    <table id="personnelTable">
        <thead>
            <tr>
                <th>Matricule</th>
                <th>Nom & Prénom / Infos</th>
                <th>Sexe</th>
                <th>Contact & Adresse</th>
                <th>Fonction / Rôle</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($people as $p)
            <tr>
                <td><span style="background: #F1F5F9; color: #475569; padding: 5px 10px; border-radius:6px; font-weight:600;">{{ $p->matricule }}</span></td>
                <td>
                    <strong>{{ $p->nom }} {{ $p->prenom }}</strong>
                    <div style="font-size: 11px; color: #64748b; margin-top: 4px;">
                        @if($p->date_naissance) 
                            <i class="fa-solid fa-cake-candles"></i> Né(e) le: {{ date('d/m/Y', strtotime($p->date_naissance)) }} <br>
                        @endif
                        @if($p->date_embauche) 
                            <i class="fa-solid fa-briefcase"></i> Recruté(e) le: {{ date('d/m/Y', strtotime($p->date_embauche)) }}
                        @endif
                    </div>
                </td>
                <td>{{ $p->sexe == 'M' ? 'Homme' : 'Femme' }}</td>
                <td>
                    <div style="font-size: 13px;">
                        <i class="fa-solid fa-phone" style="color:#64748b;"></i> {{ $p->telephone ?? '---' }} <br>
                        <i class="fa-solid fa-envelope" style="color:#64748b;"></i> {{ $p->email ?? '---' }} <br>
                        <i class="fa-solid fa-location-dot" style="color:#64748b;"></i> <span style="font-size:12px; color:#475569;">{{ $p->adresse ?? '---' }}</span>
                    </div>
                </td>
                <td>
                    <span style="padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; background: #E0F2FE; color: #0369A1;">
                        {{ $p->role->nom ?? 'Aucun' }}
                    </span>
                </td>
                <td>
                    <div style="display: flex; gap: 10px;">
                        {{-- فحص صلاحية التعديل --}}
                        @if(auth()->user()->canAccess($screen, 'edit'))
                            <button type="button" class="warning" style="border:none; cursor:pointer; font-size:14px; padding: 6px 12px; background: #f59e0b; color: white; border-radius: 6px;" onclick="openEditModal({{ json_encode($p) }})">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                        @endif

                        {{-- فحص صلاحية الحذف --}}
                        @if(auth()->user()->canAccess($screen, 'delete'))
                            <form action="{{ route('referentiels.people.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Supprimer cet employé ?')" style="margin:0;">
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
                <td colspan="6" style="text-align:center; color: #888; padding: 30px;">Aucun employé enregistré.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div id="addModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index:999; align-items:center; justify-content:center; padding: 20px; box-sizing: border-box;">
    
    <div style="background:white; width:100%; max-width:720px; padding: 25px 30px; border-radius: 20px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); overflow: hidden; box-sizing: border-box;">
        <h3 style="margin-top:0; margin-bottom: 18px; font-weight: 700; color: #1e293b; font-size: 20px;">Ajouter un Employé</h3>
        
        <form action="{{ route('referentiels.people.store') }}" method="POST" style="display:flex; flex-direction:column; gap:12px; margin: 0;">
            @csrf
            
            <div style="display: flex; gap: 15px;">
                <div style="flex: 2; display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 13px; color: #334155; font-weight: 600;">Matricule</label>
                    <input type="text" name="matricule" required style="padding:10px 14px; border:1px solid #e2e8f0; border-radius:10px; width:100%; box-sizing:border-box; font-size: 13px; outline: none;">
                </div>
                <div style="flex: 1; display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 13px; color: #334155; font-weight: 600;">Sexe</label>
                    <select name="sexe" required style="padding:10px 14px; border:1px solid #e2e8f0; border-radius:10px; background:white; width:100%; box-sizing:border-box; font-size: 13px; outline: none;">
                        <option value="M">M</option>
                        <option value="F">F</option>
                    </select>
                </div>
            </div>

            <div style="display: flex; gap: 15px;">
                <div style="flex: 1; display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 13px; color: #334155; font-weight: 600;">Nom</label>
                    <input type="text" name="nom" required style="padding:10px 14px; border:1px solid #e2e8f0; border-radius:10px; width:100%; box-sizing:border-box; font-size: 13px; outline: none;">
                </div>
                <div style="flex: 1; display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 13px; color: #334155; font-weight: 600;">Prénom</label>
                    <input type="text" name="prenom" required style="padding:10px 14px; border:1px solid #e2e8f0; border-radius:10px; width:100%; box-sizing:border-box; font-size: 13px; outline: none;">
                </div>
            </div>
            
            <div style="display: flex; gap: 15px;">
                <div style="flex: 1; display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 13px; color: #334155; font-weight: 600;">Date de Naissance</label>
                    <input type="date" name="date_naissance" style="padding:10px 14px; border:1px solid #e2e8f0; border-radius:10px; width:100%; box-sizing:border-box; font-size: 13px; outline: none;">
                </div>
                <div style="flex: 1; display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 13px; color: #334155; font-weight: 600;">Date d'Embauche</label>
                    <input type="date" name="date_embauche" style="padding:10px 14px; border:1px solid #e2e8f0; border-radius:10px; width:100%; box-sizing:border-box; font-size: 13px; outline: none;">
                </div>
            </div>

            <div style="display: flex; gap: 15px;">
                <div style="flex: 1; display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 13px; color: #334155; font-weight: 600;">Téléphone</label>
                    <input type="text" name="telephone" style="padding:10px 14px; border:1px solid #e2e8f0; border-radius:10px; width:100%; box-sizing:border-box; font-size: 13px; outline: none;">
                </div>
                <div style="flex: 1; display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 13px; color: #334155; font-weight: 600;">Email</label>
                    <input type="email" name="email" style="padding:10px 14px; border:1px solid #e2e8f0; border-radius:10px; width:100%; box-sizing:border-box; font-size: 13px; outline: none;">
                </div>
            </div>

            <div style="display: flex; gap: 15px;">
                <div style="flex: 1; display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 13px; color: #334155; font-weight: 600;">Adresse</label>
                    <input type="text" name="adresse" style="padding:10px 14px; border:1px solid #e2e8f0; border-radius:10px; width:100%; box-sizing:border-box; font-size: 13px; outline: none;">
                </div>
                <div style="flex: 1; display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 13px; color: #334155; font-weight: 600;">Fonction / Rôle</label>
                    <select name="role_id" required style="padding:10px 14px; border:1px solid #e2e8f0; border-radius:10px; background:white; width:100%; box-sizing:border-box; font-size: 13px; outline: none;">
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->nom }}</option>
                        @endforeach
                    </select>
                </div>
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
        <h3 style="margin-top:0; margin-bottom: 18px; font-weight: 700; color: #1e293b; font-size: 20px;">Modifier l'Employé</h3>
        
        <form id="editForm" method="POST" style="display:flex; flex-direction:column; gap:12px; margin: 0;">
            @csrf
            @method('PUT')
            
            <div style="display: flex; gap: 15px;">
                <div style="flex: 2; display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 13px; color: #334155; font-weight: 600;">Matricule</label>
                    <input type="text" name="matricule" id="edit_matricule" required style="padding:10px 14px; border:1px solid #e2e8f0; border-radius:10px; width:100%; box-sizing:border-box; font-size: 13px; outline: none;">
                </div>
                <div style="flex: 1; display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 13px; color: #334155; font-weight: 600;">Sexe</label>
                    <select name="sexe" id="edit_sexe" required style="padding:10px 14px; border:1px solid #e2e8f0; border-radius:10px; background:white; width:100%; box-sizing:border-box; font-size: 13px; outline: none;">
                        <option value="M">M</option>
                        <option value="F">F</option>
                    </select>
                </div>
            </div>

            <div style="display: flex; gap: 15px;">
                <div style="flex: 1; display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 13px; color: #334155; font-weight: 600;">Nom</label>
                    <input type="text" name="nom" id="edit_nom" required style="padding:10px 14px; border:1px solid #e2e8f0; border-radius:10px; width:100%; box-sizing:border-box; font-size: 13px; outline: none;">
                </div>
                <div style="flex: 1; display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 13px; color: #334155; font-weight: 600;">Prénom</label>
                    <input type="text" name="prenom" id="edit_prenom" required style="padding:10px 14px; border:1px solid #e2e8f0; border-radius:10px; width:100%; box-sizing:border-box; font-size: 13px; outline: none;">
                </div>
            </div>
            
            <div style="display: flex; gap: 15px;">
                <div style="flex: 1; display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 13px; color: #334155; font-weight: 600;">Date de Naissance</label>
                    <input type="date" name="date_naissance" id="edit_date_naissance" style="padding:10px 14px; border:1px solid #e2e8f0; border-radius:10px; width:100%; box-sizing:border-box; font-size: 13px; outline: none;">
                </div>
                <div style="flex: 1; display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 13px; color: #334155; font-weight: 600;">Date d'Embauche</label>
                    <input type="date" name="date_embauche" id="edit_date_embauche" style="padding:10px 14px; border:1px solid #e2e8f0; border-radius:10px; width:100%; box-sizing:border-box; font-size: 13px; outline: none;">
                </div>
            </div>

            <div style="display: flex; gap: 15px;">
                <div style="flex: 1; display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 13px; color: #334155; font-weight: 600;">Téléphone</label>
                    <input type="text" name="telephone" id="edit_telephone" style="padding:10px 14px; border:1px solid #e2e8f0; border-radius:10px; width:100%; box-sizing:border-box; font-size: 13px; outline: none;">
                </div>
                <div style="flex: 1; display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 13px; color: #334155; font-weight: 600;">Email</label>
                    <input type="email" name="email" id="edit_email" style="padding:10px 14px; border:1px solid #e2e8f0; border-radius:10px; width:100%; box-sizing:border-box; font-size: 13px; outline: none;">
                </div>
            </div>

            <div style="display: flex; gap: 15px;">
                <div style="flex: 1; display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 13px; color: #334155; font-weight: 600;">Adresse</label>
                    <input type="text" name="adresse" id="edit_adresse" style="padding:10px 14px; border:1px solid #e2e8f0; border-radius:10px; width:100%; box-sizing:border-box; font-size: 13px; outline: none;">
                </div>
                <div style="flex: 1; display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 13px; color: #334155; font-weight: 600;">Fonction / Rôle</label>
                    <select name="role_id" id="edit_role_id" required style="padding:10px 14px; border:1px solid #e2e8f0; border-radius:10px; background:white; width:100%; box-sizing:border-box; font-size: 13px; outline: none;">
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->nom }}</option>
                        @endforeach
                    </select>
                </div>
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

function openEditModal(p) {
    document.getElementById('editForm').action = '/referentiels/personnel/' + p.id;
    document.getElementById('edit_matricule').value = p.matricule;
    document.getElementById('edit_sexe').value = p.sexe;
    document.getElementById('edit_nom').value = p.nom;
    document.getElementById('edit_prenom').value = p.prenom;
    document.getElementById('edit_telephone').value = p.telephone ? p.telephone : '';
    document.getElementById('edit_email').value = p.email ? p.email : '';
    document.getElementById('edit_adresse').value = p.adresse ? p.adresse : '';
    document.getElementById('edit_role_id').value = p.role_id;
    
    // 📅 استخراج التواريخ بشكل نظيف بصيغة YYYY-MM-DD لتوافق حقل الدخل من نوع date
    document.getElementById('edit_date_naissance').value = p.date_naissance ? p.date_naissance.substring(0, 10) : '';
    document.getElementById('edit_date_embauche').value = p.date_embauche ? p.date_embauche.substring(0, 10) : '';
    
    document.getElementById('editModal').style.display = 'flex';
}
function closeEditModal() { document.getElementById('editModal').style.display = 'none'; }

// 🔍 دالة جافاسكريبت للبحث الفوري في جدول الموظفين
function filterPersonnelTable() {
    let input = document.getElementById("personnelSearchBar");
    let filter = input.value.toLowerCase().trim();
    let table = document.getElementById("personnelTable");
    let tr = table.getElementsByTagName("tr");

    for (let i = 1; i < tr.length; i++) {
        let tdMatricule = tr[i].getElementsByTagName("td")[0]; // عمود رقم التسجيل
        let tdNomPrenom = tr[i].getElementsByTagName("td")[1]; // عمود الاسم واللقب والتواريخ

        if (tdMatricule || tdNomPrenom) {
            let matriculeText = tdMatricule.textContent || tdMatricule.innerText;
            let nomPrenomText = tdNomPrenom.textContent || tdNomPrenom.innerText;

            if (
                matriculeText.toLowerCase().indexOf(filter) > -1 || 
                nomPrenomText.toLowerCase().indexOf(filter) > -1
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