@extends('layouts.app')

@section('content')
<div class="page-title" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
    <div>
        <h2>Gestion des Équipements & Camions</h2>
        <p style="color: #777;">Liste des véhicules, machines et engins enregistrés</p>
    </div>
    
    {{-- فحص صلاحية الإنشاء قبل إظهار زر الإضافة --}}
    @if(auth()->user()->canAccess($screen, 'create'))
        <button class="red" style="border:none; padding:10px 20px; color: white; background-color: #C1121F; border-radius:8px; cursor:pointer; font-weight: 600;" onclick="openAddModal()">
            <i class="fa-solid fa-plus"></i> Ajouter Équipement
        </button>
    @endif
</div>

<div style="margin-bottom: 20px; display: flex; max-width: 400px; position: relative;">
    <span style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #64748b;">
        <i class="fa-solid fa-magnifying-glass"></i>
    </span>
    <input type="text" id="equipementSearchBar" onkeyup="filterEquipementTable()" placeholder="Rechercher par nom, code, immatriculation..." 
           style="width: 100%; padding: 12px 12px 12px 40px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; outline: none; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); transition: border 0.2s;"
           onfocus="this.style.borderColor='#C1121F'" onblur="this.style.borderColor='#e2e8f0'">
</div>

@if(session('success'))
    <div style="background-color: #DCFCE7; color: #15803D; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: 600;">
        {{ session('success') }}
    </div>
@endif

<div class="table-card">
    <table id="equipementTable">
        <thead>
            <tr>
                <th>Code</th>
                <th>Nom</th>
                <th>Type</th>
                <th>Marque / Modèle</th>
                <th>Immatriculation</th>
                <th>N° de série</th> <th>État</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($equipements as $equipement)
            <tr>
                <td>
                    <span style="background: #F1F5F9; color: #475569; padding: 5px 10px; border-radius:6px; font-weight:600;">
                        {{ $equipement->code }}
                    </span>
                </td>
                <td><strong>{{ $equipement->nom }}</strong></td>
                <td>{{ $equipement->type }}</td>
                <td>{{ $equipement->marque ?? '---' }} {{ $equipement->modele ?? '' }}</td>
                <td>{{ $equipement->immatriculation ?? '---' }}</td>
                
                <td style="color: #475569; font-weight: 500;">
                    {{ $equipement->numero_serie ?? '---' }}
                </td>

                <td>
                    @if($equipement->etat == 'Disponible')
                        <span style="padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; background: #DCFCE7; color: #15803D;">Disponible</span>
                    @elseif($equipement->etat == 'En panne')
                        <span style="padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; background: #FEE2E2; color: #991B1B;">En panne</span>
                    @else
                        <span style="padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; background: #FEF3C7; color: #92400E;">{{ $equipement->etat }}</span>
                    @endif
                </td>
                <td>
                    <div style="display: flex; gap: 10px;">
                        {{-- فحص صلاحية التعديل --}}
                        @if(auth()->user()->canAccess($screen, 'edit'))
                            <button type="button" class="warning" style="border:none; cursor:pointer; font-size:14px; padding: 6px 12px; background: #f59e0b; color: white; border-radius: 6px;" onclick="openEditModal({{ json_encode($equipement) }})">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                        @endif
                        
                        {{-- فحص صلاحية الحذف --}}
                        @if(auth()->user()->canAccess($screen, 'delete'))
                            <form action="{{ route('referentiels.equipements.destroy', $equipement->id) }}" method="POST" onsubmit="return confirm('Voulez-vous vraiment supprimer cet équipement ?')" style="margin:0;">
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
                <td colspan="8" style="text-align:center; color: #888; padding: 30px;">Aucun équipement trouvé dans la base de données.</td> </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div id="addModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index:999; align-items:center; justify-content:center;">
    <div style="background:white; width:580px; padding:35px; border-radius:20px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); position:relative;">
        <h3 style="margin-top:0; margin-bottom: 25px; font-size: 22px; font-weight: 700; color: #1e293b;">Ajouter un Équipement</h3>
        
        <form action="{{ route('referentiels.equipements.store') }}" method="POST" style="display:flex; flex-direction:column; gap:18px;">
            @csrf
            
            <div style="display: flex; gap: 15px;">
                <div style="flex: 1; display: flex; flex-direction: column; gap: 6px;">
                    <label style="font-size: 13px; color: #334155; font-weight: 600;">Code Équipement</label>
                    <input type="text" name="code" placeholder="Ex: CAM-01" required style="padding:12px; border:1px solid #e2e8f0; border-radius:10px; width:100%; box-sizing:border-box;">
                </div>
                <div style="flex: 2; display: flex; flex-direction: column; gap: 6px;">
                    <label style="font-size: 13px; color: #334155; font-weight: 600;">Nom de l'engin</label>
                    <input type="text" name="nom" placeholder="Nom de l'engin ou véhicule" required style="padding:12px; border:1px solid #e2e8f0; border-radius:10px; width:100%; box-sizing:border-box;">
                </div>
            </div>

            <div style="display: flex; flex-direction: column; gap: 6px;">
                <label style="font-size: 13px; color: #334155; font-weight: 600;">Type</label>
                <select name="type" required style="padding:12px; border:1px solid #e2e8f0; border-radius:10px; width:100%; box-sizing:border-box; color:#0f172a; background-color: white;">
                    <option value="">Sélectionnez le Type</option>
                    <option value="Camion">Camion</option>
                    <option value="Chariot">Chariot élévateur</option>
                    <option value="Broyeur">Broyeur</option>
                    <option value="Balance">Balance Industrielle</option>
                </select>
            </div>
            
            <div style="display: flex; gap: 15px;">
                <div style="flex: 1; display: flex; flex-direction: column; gap: 6px;">
                    <label style="font-size: 13px; color: #334155; font-weight: 600;">Marque</label>
                    <input type="text" name="marque" placeholder="Ex: Volvo" style="padding:12px; border:1px solid #e2e8f0; border-radius:10px; width:100%; box-sizing:border-box;">
                </div>
                <div style="flex: 1; display: flex; flex-direction: column; gap: 6px;">
                    <label style="font-size: 13px; color: #334155; font-weight: 600;">Modèle</label>
                    <input type="text" name="modele" placeholder="Ex: FH16" style="padding:12px; border:1px solid #e2e8f0; border-radius:10px; width:100%; box-sizing:border-box;">
                </div>
            </div>
            
            <div style="display: flex; gap: 15px;">
                <div style="flex: 1; display: flex; flex-direction: column; gap: 6px;">
                    <label style="font-size: 13px; color: #334155; font-weight: 600;">Immatriculation</label>
                    <input type="text" name="immatriculation" placeholder="Ex: 123456-116" style="padding:12px; border:1px solid #e2e8f0; border-radius:10px; width:100%; box-sizing:border-box;">
                </div>
                <div style="flex: 1; display: flex; flex-direction: column; gap: 6px;">
                    <label style="font-size: 13px; color: #334155; font-weight: 600;">Numéro de série</label>
                    <input type="text" name="numero_serie" placeholder="Numéro de série" style="padding:12px; border:1px solid #e2e8f0; border-radius:10px; width:100%; box-sizing:border-box;">
                </div>
            </div>

            <div style="display:flex; justify-content: flex-end; gap:12px; margin-top:15px; width:100%;">
                <button type="button" onclick="closeAddModal()" style="background:#f1f5f9; color:#475569; border:none; padding:12px 26px; border-radius:10px; cursor:pointer; font-weight:600;">Annuler</button>
                <button type="submit" style="background:#C1121F; color:white; border:none; padding:12px 26px; border-radius:10px; cursor:pointer; font-weight:600;">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<div id="editModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index:999; align-items:center; justify-content:center;">
    <div style="background:white; width:580px; padding:35px; border-radius:20px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); position:relative;">
        <h3 style="margin-top:0; margin-bottom: 25px; font-size: 22px; font-weight: 700; color: #1e293b;">Modifier l'Équipement</h3>
        
        <form id="editForm" method="POST" style="display:flex; flex-direction:column; gap:18px;">
            @csrf
            @method('PUT')
            
            <div style="display: flex; gap: 15px;">
                <div style="flex: 1; display: flex; flex-direction: column; gap: 6px;">
                    <label style="font-size: 13px; color: #334155; font-weight: 600;">Code Équipement</label>
                    <input type="text" name="code" id="edit_code" required style="padding:12px; border:1px solid #e2e8f0; border-radius:10px; width:100%; box-sizing:border-box; font-weight: 500;">
                </div>
                <div style="flex: 2; display: flex; flex-direction: column; gap: 6px;">
                    <label style="font-size: 13px; color: #334155; font-weight: 600;">Nom de l'engin</label>
                    <input type="text" name="nom" id="edit_nom" required style="padding:12px; border:1px solid #e2e8f0; border-radius:10px; width:100%; box-sizing:border-box; color:#0f172a;">
                </div>
            </div>

            <div style="display: flex; gap: 15px;">
                <div style="flex: 1; display: flex; flex-direction: column; gap: 6px;">
                    <label style="font-size: 13px; color: #334155; font-weight: 600;">Type</label>
                    <select name="type" id="edit_type" required style="padding:12px; border:1px solid #e2e8f0; border-radius:10px; width:100%; box-sizing:border-box; color:#0f172a; background-color: white;">
                        <option value="Camion">Camion</option>
                        <option value="Chariot">Chariot élévateur</option>
                        <option value="Broyeur">Broyeur</option>
                        <option value="Balance">Balance Industrielle</option>
                    </select>
                </div>
                <div style="flex: 1; display: flex; flex-direction: column; gap: 6px;">
                    <label style="font-size: 13px; color: #334155; font-weight: 600;">État</label>
                    <select name="etat" id="edit_etat" style="padding:12px; border:1px solid #e2e8f0; border-radius:10px; width:100%; box-sizing:border-box; color:#0f172a; background-color: white;">
                        <option value="Disponible">Disponible</option>
                        <option value="En cours d'utilisation">En cours d'utilisation</option>
                        <option value="En panne">En panne</option>
                    </select>
                </div>
            </div>
            
            <div style="display: flex; gap: 15px;">
                <div style="flex: 1; display: flex; flex-direction: column; gap: 6px;">
                    <label style="font-size: 13px; color: #334155; font-weight: 600;">Marque</label>
                    <input type="text" name="marque" id="edit_marque" placeholder="Ex: Volvo" style="padding:12px; border:1px solid #e2e8f0; border-radius:10px; width:100%; box-sizing:border-box;">
                </div>
                <div style="flex: 1; display: flex; flex-direction: column; gap: 6px;">
                    <label style="font-size: 13px; color: #334155; font-weight: 600;">Modèle</label>
                    <input type="text" name="modele" id="edit_modele" placeholder="Ex: FH16" style="padding:12px; border:1px solid #e2e8f0; border-radius:10px; width:100%; box-sizing:border-box;">
                </div>
            </div>
            
            <div style="display: flex; gap: 15px;">
                <div style="flex: 1; display: flex; flex-direction: column; gap: 6px;">
                    <label style="font-size: 13px; color: #334155; font-weight: 600;">Immatriculation</label>
                    <input type="text" name="immatriculation" id="edit_immatriculation" placeholder="Ex: 123456-116" style="padding:12px; border:1px solid #e2e8f0; border-radius:10px; width:100%; box-sizing:border-box;">
                </div>
                <div style="flex: 1; display: flex; flex-direction: column; gap: 6px;">
                    <label style="font-size: 13px; color: #334155; font-weight: 600;">Numéro de série</label>
                    <input type="text" name="numero_serie" id="edit_numero_serie" placeholder="Numéro de série" style="padding:12px; border:1px solid #e2e8f0; border-radius:10px; width:100%; box-sizing:border-box;">
                </div>
            </div>

            <div style="display:flex; justify-content: flex-end; gap:12px; margin-top:15px; width:100%;">
                <button type="button" onclick="closeEditModal()" style="background:#f1f5f9; color:#475569; border:none; padding:12px 26px; border-radius:10px; cursor:pointer; font-weight:600;">Annuler</button>
                <button type="submit" style="background:#f59e0b; color:white; border:none; padding:12px 26px; border-radius:10px; cursor:pointer; font-weight:600;">Modifier</button>
            </div>
        </form>
    </div>
</div>

<script>
function openAddModal() {
    document.getElementById('addModal').style.display = 'flex';
}
function closeAddModal() {
    document.getElementById('addModal').style.display = 'none';
}

function openEditModal(equipement) {
    document.getElementById('editForm').action = '/referentiels/equipements/' + equipement.id;
    
    document.getElementById('edit_code').value = equipement.code;
    document.getElementById('edit_nom').value = equipement.nom;
    document.getElementById('edit_type').value = equipement.type;
    document.getElementById('edit_marque').value = equipement.marque || '';
    document.getElementById('edit_modele').value = equipement.modele || '';
    document.getElementById('edit_immatriculation').value = equipement.immatriculation || '';
    document.getElementById('edit_numero_serie').value = equipement.numero_serie || '';
    document.getElementById('edit_etat').value = equipement.etat;
    
    document.getElementById('editModal').style.display = 'flex';
}

function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}

// 🔍 دالة جافاسكريبت للبحث الفوري في جدول الأجهزة والشاحنات
function filterEquipementTable() {
    let input = document.getElementById("equipementSearchBar");
    let filter = input.value.toLowerCase().trim();
    let table = document.getElementById("equipementTable");
    let tr = table.getElementsByTagName("tr");

    for (let i = 1; i < tr.length; i++) {
        let tdCode = tr[i].getElementsByTagName("td")[0]; // الكود
        let tdNom = tr[i].getElementsByTagName("td")[1];  // الاسم
        let tdImmatriculation = tr[i].getElementsByTagName("td")[4]; // اللوحة (العمود الخامس الآن)
        let tdSerie = tr[i].getElementsByTagName("td")[5]; // رقم التسلسل (العمود السادس الآن)

        if (tdCode || tdNom || tdImmatriculation || tdSerie) {
            let codeText = tdCode.textContent || tdCode.innerText;
            let nomText = tdNom.textContent || tdNom.innerText;
            let immText = tdImmatriculation.textContent || tdImmatriculation.innerText;
            let serieText = tdSerie ? (tdSerie.textContent || tdSerie.innerText) : '';

            if (
                codeText.toLowerCase().indexOf(filter) > -1 || 
                nomText.toLowerCase().indexOf(filter) > -1 ||
                immText.toLowerCase().indexOf(filter) > -1 ||
                serieText.toLowerCase().indexOf(filter) > -1
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