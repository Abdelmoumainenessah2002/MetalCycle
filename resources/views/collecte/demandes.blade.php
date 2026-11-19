@extends('layouts.app')

@section('content')
<div class="page-title" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
    <div>
        <h2>Demandes de Collecte</h2>
        <p style="color: #777;">Gestion et suivi des demandes provenant des fournisseurs</p>
    </div>
    
    @if(auth()->user()->canAccess($screen, 'create'))
        <button style="border:none; padding:10px 20px; color: white; background-color: #C1121F; border-radius:8px; cursor:pointer; font-weight: 600; box-shadow: 0 4px 6px -1px rgba(193, 18, 31, 0.2);" onclick="openAddModal()">
            <i class="fa-solid fa-plus"></i> Nouvelle Demande
        </button>
    @endif
</div>

<!-- شريط البحث والفلترة -->
<div style="margin-bottom: 20px; display: flex; gap: 15px; align-items: center;">
    <div style="flex: 1; max-width: 400px; position: relative;">
        <span style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #64748b;">
            <i class="fa-solid fa-magnifying-glass"></i>
        </span>
        <input type="text" id="demandeSearchBar" placeholder="Rechercher (N°, Fournisseur, Matière)..." 
               style="width: 100%; padding: 12px 12px 12px 40px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; outline: none; box-sizing: border-box;">
    </div>
    <div style="width: 200px;">
        <select id="statusFilter" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; outline: none; background: white; cursor: pointer;">
            <option value="ALL">Tous les statuts</option>
            <option value="En attente">En attente</option>
            <option value="Validée">Validée</option>
            <option value="Traitée">Traitée</option>
            <option value="Annulée">Annulée</option>
            <option value="Refusée">Refusée</option>
        </select>
    </div>
</div>

<div id="alertMessageContainer"></div>

@if(session('success'))
    <div class="alert-box" style="background-color: #DCFCE7; color: #15803D; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: 600;">
        <i class="fa-solid fa-circle-check" style="margin-right: 5px;"></i> {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="alert-box" style="background-color: #FEE2E2; color: #991B1B; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: 600;">
        {{ session('error') }}
    </div>
@endif

<!-- حاوية الجدول والترقيم للتحديث التفاعلي -->
<div id="tableContainer" class="table-card" style="background: white; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow: hidden;">
    @include('collecte.demandes_table')
</div>

<!-- 1. نافذة الإضافة -->
<div id="addModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index:999; align-items:center; justify-content:center; padding: 20px; box-sizing: border-box;">
    <div style="background:white; width:100%; max-width:720px; padding: 25px 30px; border-radius: 20px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); overflow: hidden; box-sizing: border-box;">
        <h3 style="margin-top:0; margin-bottom: 18px; font-weight: 700; color: #1e293b; font-size: 20px;">Nouvelle Demande de Collecte</h3>
        
        <form action="{{ route('collecte.demandes.store') }}" method="POST" style="display:flex; flex-direction:column; gap:12px; margin: 0;">
            @csrf
            <div style="display: flex; gap: 15px;">
                <div style="flex: 1; display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 13px; color: #334155; font-weight: 600;">Date de demande <span style="color:red">*</span></label>
                    <input type="date" name="date_demande" value="{{ date('Y-m-d') }}" required style="padding:10px 14px; border:1px solid #e2e8f0; border-radius:10px; width:100%; box-sizing:border-box; font-size: 13px; outline: none;">
                </div>
                <div style="flex: 1; display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 13px; color: #334155; font-weight: 600;">Fournisseur / Filiale <span style="color:red">*</span></label>
                    <select name="fournisseur_id" required style="padding:10px 14px; border:1px solid #e2e8f0; border-radius:10px; width:100%; box-sizing:border-box; font-size: 13px; outline: none; background:white;">
                        <option value="">Sélectionnez un fournisseur...</option>
                        @foreach($fournisseurs as $fr)
                            <option value="{{ $fr->id }}">{{ $fr->code_fournisseur }} - {{ $fr->nom_fournisseur }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="display: flex; gap: 15px;">
                <div style="flex: 1; display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 13px; color: #334155; font-weight: 600;">Matière <span style="color:red">*</span></label>
                    <select name="materiau_id" required style="padding:10px 14px; border:1px solid #e2e8f0; border-radius:10px; width:100%; box-sizing:border-box; font-size: 13px; outline: none; background:white;">
                        <option value="">Sélectionnez une matière...</option>
                        @foreach($materiaux as $mat)
                            <option value="{{ $mat->id }}">{{ $mat->nom }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="flex: 1; display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 13px; color: #334155; font-weight: 600;">Quantité estimée <span style="color:red">*</span></label>
                    <input type="number" step="0.01" name="quantite_estimee" placeholder="Ex: 500" required style="padding:10px 14px; border:1px solid #e2e8f0; border-radius:10px; width:100%; box-sizing:border-box; font-size: 13px; outline: none;">
                </div>
            </div>

            <div style="display: flex; gap: 15px;">
                <div style="flex: 1; display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 13px; color: #334155; font-weight: 600;">Contact (Nom)</label>
                    <input type="text" name="contact" placeholder="Nom du contact" style="padding:10px 14px; border:1px solid #e2e8f0; border-radius:10px; width:100%; box-sizing:border-box; font-size: 13px; outline: none;">
                </div>
                <div style="flex: 1; display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 13px; color: #334155; font-weight: 600;">Téléphone</label>
                    <input type="text" name="telephone" placeholder="N° de téléphone" style="padding:10px 14px; border:1px solid #e2e8f0; border-radius:10px; width:100%; box-sizing:border-box; font-size: 13px; outline: none;">
                </div>
            </div>

            <div style="display: flex; flex-direction: column; gap: 4px;">
                <label style="font-size: 13px; color: #334155; font-weight: 600;">Adresse de collecte</label>
                <input type="text" name="adresse_collecte" placeholder="Lieu exact de la collecte" style="padding:10px 14px; border:1px solid #e2e8f0; border-radius:10px; width:100%; box-sizing:border-box; font-size: 13px; outline: none;">
            </div>

            <div style="display: flex; flex-direction: column; gap: 4px;">
                <label style="font-size: 13px; color: #334155; font-weight: 600;">Observation</label>
                <textarea name="observation" rows="2" style="padding:10px 14px; border:1px solid #e2e8f0; border-radius:10px; resize: none; font-size: 13px; outline: none; width:100%; box-sizing:border-box;"></textarea>
            </div>

            <div style="display:flex; justify-content: flex-end; gap:12px; margin-top:10px;">
                <button type="button" onclick="closeAddModal()" style="background:#f1f5f9; color:#475569; border:none; padding:11px 20px; border-radius:10px; cursor:pointer; font-weight:600; font-size: 13px;">Annuler</button>
                <button type="submit" style="background:#C1121F; color:white; border:none; padding:11px 20px; border-radius:10px; cursor:pointer; font-weight:600; font-size: 13px;">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<!-- 2. نافذة القبول -->
<div id="validerModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index:999; align-items:center; justify-content:center; padding: 20px; box-sizing: border-box;">
    <div style="background:white; width:100%; max-width:450px; padding: 25px; border-radius: 20px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);">
        <h3 style="margin-top:0; color: #10b981;">Valider la Demande</h3>
        <p style="font-size: 13px; color: #64748b; margin-bottom: 15px;">
            Voulez-vous vraiment valider cette demande ? Un numéro officiel lui sera attribué automatiquement par le système.
        </p>
        <form id="validerForm" method="POST" style="display:flex; flex-direction:column; gap:15px; margin: 0;">
            @csrf
            @method('PATCH')
            
            <!-- حقل للعرض فقط يُوضح عملية التوليد التلقائي -->
           <div style="display: flex; flex-direction: column; gap: 6px;">
    <label style="font-size: 13px; font-weight: 600;">N° Demande Officiel</label>
    <input type="text" 
           value="{{ $nextNumero ?? \App\Services\NumberingService::preview('compteur_demande') }}" 
           readonly 
           style="padding:10px; border:1px solid #cbd5e1; border-radius:10px; width:100%; box-sizing:border-box; font-size:13px; outline:none; background-color:#f1f5f9; color:#0f172a; font-weight:bold; cursor: not-allowed;">
</div>

            <div style="display:flex; justify-content: flex-end; gap:12px; margin-top:10px;">
                <button type="button" onclick="closeValiderModal()" style="background:#f1f5f9; color:#475569; border:none; padding:10px 18px; border-radius:10px; cursor:pointer; font-weight:600;">Annuler</button>
                <button type="submit" style="background:#10b981; color:white; border:none; padding:10px 18px; border-radius:10px; cursor:pointer; font-weight:600;">Valider et Confirmer</button>
            </div>
        </form>
    </div>
</div>

<!-- 3. نافذة التعديل / المشاهدة -->
<div id="editModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index:999; align-items:center; justify-content:center; padding: 20px; box-sizing: border-box;">
    <div style="background:white; width:100%; max-width:720px; padding: 25px 30px; border-radius: 20px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); overflow: hidden; box-sizing: border-box;">
        <h3 id="modalTitle" style="margin-top:0; margin-bottom: 18px; font-weight: 700; color: #1e293b; font-size: 20px;">Modifier la Demande</h3>
        
        <form id="editForm" method="POST" style="display:flex; flex-direction:column; gap:12px; margin: 0;">
            @csrf
            @method('PUT')
            
            <div style="display: flex; gap: 15px;">
                <div style="flex: 1; display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 13px; color: #334155; font-weight: 600;">N° Demande</label>
                    <input type="text" id="edit_numero" readonly style="padding:10px 14px; border:1px solid #e2e8f0; border-radius:10px; width:100%; box-sizing:border-box; font-size: 13px; outline: none; background: #f8fafc; color: #64748b;">
                </div>
                <div style="flex: 1; display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 13px; color: #334155; font-weight: 600;">Date de demande</label>
                    <input type="date" name="date_demande" id="edit_date" required class="modal-input" style="padding:10px 14px; border:1px solid #e2e8f0; border-radius:10px; width:100%; box-sizing:border-box; font-size: 13px; outline: none;">
                </div>
            </div>

            <div style="display: flex; gap: 15px;">
                <div style="flex: 1; display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 13px; color: #334155; font-weight: 600;">Fournisseur / Filiale</label>
                    <select name="fournisseur_id" id="edit_fournisseur" required class="modal-input" style="padding:10px 14px; border:1px solid #e2e8f0; border-radius:10px; width:100%; box-sizing:border-box; font-size: 13px; outline: none; background:white;">
                        @foreach($fournisseurs as $fr)
                            <option value="{{ $fr->id }}">{{ $fr->code_fournisseur }} - {{ $fr->nom_fournisseur }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="flex: 1; display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 13px; color: #334155; font-weight: 600;">Matière</label>
                    <select name="materiau_id" id="edit_materiau" required class="modal-input" style="padding:10px 14px; border:1px solid #e2e8f0; border-radius:10px; width:100%; box-sizing:border-box; font-size: 13px; outline: none; background:white;">
                        @foreach($materiaux as $mat)
                            <option value="{{ $mat->id }}">{{ $mat->nom }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="display: flex; gap: 15px;">
                <div style="flex: 1; display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 13px; color: #334155; font-weight: 600;">Quantité estimée</label>
                    <input type="number" step="0.01" name="quantite_estimee" id="edit_quantite" required class="modal-input" style="padding:10px 14px; border:1px solid #e2e8f0; border-radius:10px; width:100%; box-sizing:border-box; font-size: 13px; outline: none;">
                </div>
                <div style="flex: 1; display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 13px; color: #334155; font-weight: 600;">Téléphone</label>
                    <input type="text" name="telephone" id="edit_telephone" class="modal-input" style="padding:10px 14px; border:1px solid #e2e8f0; border-radius:10px; width:100%; box-sizing:border-box; font-size: 13px; outline: none;">
                </div>
            </div>

            <div style="display: flex; gap: 15px;">
                <div style="flex: 1; display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 13px; color: #334155; font-weight: 600;">Contact</label>
                    <input type="text" name="contact" id="edit_contact" class="modal-input" style="padding:10px 14px; border:1px solid #e2e8f0; border-radius:10px; width:100%; box-sizing:border-box; font-size: 13px; outline: none;">
                </div>
                <div style="flex: 1; display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 13px; color: #334155; font-weight: 600;">Adresse de collecte</label>
                    <input type="text" name="adresse_collecte" id="edit_adresse" class="modal-input" style="padding:10px 14px; border:1px solid #e2e8f0; border-radius:10px; width:100%; box-sizing:border-box; font-size: 13px; outline: none;">
                </div>
            </div>

            <div style="display: flex; flex-direction: column; gap: 4px;">
                <label style="font-size: 13px; color: #334155; font-weight: 600;">Observation</label>
                <textarea name="observation" id="edit_observation" rows="2" class="modal-input" style="padding:10px 14px; border:1px solid #e2e8f0; border-radius:10px; resize: none; font-size: 13px; outline: none; width:100%; box-sizing:border-box;"></textarea>
            </div>

            <div style="display:flex; justify-content: flex-end; gap:12px; margin-top:10px;">
                <button type="button" onclick="closeEditModal()" style="background:#f1f5f9; color:#475569; border:none; padding:11px 20px; border-radius:10px; cursor:pointer; font-weight:600; font-size: 13px;">Fermer</button>
                <button type="submit" id="btnSubmitEdit" style="background:#f59e0b; color:white; border:none; padding:11px 20px; border-radius:10px; cursor:pointer; font-weight:600; font-size: 13px;">Modifier</button>
            </div>
        </form>
    </div>
</div>

<!-- 4. نافذة الإلغاء (Annuler Modal) -->
<div id="annulerDemandeModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index:999; align-items:center; justify-content:center; padding: 20px; box-sizing: border-box;">
    <div style="background:white; width:100%; max-width:450px; padding: 25px; border-radius: 20px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);">
        <h3 style="margin-top:0; color: #991B1B;">Annuler la Demande</h3>
        <p style="font-size: 13px; color: #64748b; margin-bottom: 15px;">
            Veuillez indiquer le motif de l'annulation de cette demande.
        </p>
        <form id="annulerDemandeForm" style="display:flex; flex-direction:column; gap:15px; margin: 0;">
            @csrf
            <div style="display: flex; flex-direction: column; gap: 6px;">
                <label style="font-size: 13px; font-weight: 600;">Motif d'annulation *</label>
                <textarea name="motif_annulation" id="input_motif_annulation" required rows="3" placeholder="Raison de l'annulation..." style="padding:10px; border:1px solid #e2e8f0; border-radius:10px; width:100%; box-sizing:border-box; resize:none; font-size:13px; outline:none;"></textarea>
            </div>
            <div style="display:flex; justify-content: flex-end; gap:12px; margin-top:10px;">
                <button type="button" onclick="closeAnnulerDemandeModal()" style="background:#f1f5f9; color:#475569; border:none; padding:10px 18px; border-radius:10px; cursor:pointer; font-weight:600;">Retour</button>
                <button type="submit" id="btnConfirmAnnuler" style="background:#ef4444; color:white; border:none; padding:10px 18px; border-radius:10px; cursor:pointer; font-weight:600;">Confirmer l'annulation</button>
            </div>
        </form>
    </div>
</div>

<script>
    // --- المتغيرات العامة ---
    let currentAnnulerId = null;
    let searchTimeout = null;
    let searchController = null;
    let lastSearchQuery = null; // لمنع تكرار طلبات البحث المتشابهة
    
    // --- إدارة النوافذ المنبثقة (Modals) ---
    function openAddModal() { document.getElementById('addModal').style.display = 'flex'; }
    function closeAddModal() { document.getElementById('addModal').style.display = 'none'; }
    
    function openValiderModal(id) {
        document.getElementById('validerForm').action = '/collecte/demandes/' + id + '/valider';
        document.getElementById('validerModal').style.display = 'flex';
    }
    function closeValiderModal() { document.getElementById('validerModal').style.display = 'none'; }
    
    function populateModal(d, isReadonly) {
        document.getElementById('edit_numero').value = d.numero || 'Non attribué';
        document.getElementById('edit_date').value = d.date_demande;
        document.getElementById('edit_fournisseur').value = d.fournisseur_id;
        document.getElementById('edit_materiau').value = d.materiau_id;
        document.getElementById('edit_quantite').value = d.quantite_estimee;
        document.getElementById('edit_telephone').value = d.telephone || '';
        document.getElementById('edit_contact').value = d.contact || '';
        document.getElementById('edit_adresse').value = d.adresse_collecte || '';
        document.getElementById('edit_observation').value = d.observation || '';
    
        let inputs = document.getElementsByClassName('modal-input');
        for (let i = 0; i < inputs.length; i++) {
            inputs[i].disabled = isReadonly;
            inputs[i].style.background = isReadonly ? '#f8fafc' : 'white';
        }
    }
    
    function openEditModal(d) {
        document.getElementById('modalTitle').innerText = "Modifier la Demande";
        document.getElementById('editForm').action = '/collecte/demandes/' + d.id;
        document.getElementById('btnSubmitEdit').style.display = 'block';
        populateModal(d, false);
        document.getElementById('editModal').style.display = 'flex';
    }
    
    function openViewModal(d) {
        document.getElementById('modalTitle').innerText = "Consulter la Demande";
        document.getElementById('btnSubmitEdit').style.display = 'none';
        populateModal(d, true);
        document.getElementById('editModal').style.display = 'flex';
    }
    
    function closeEditModal() { document.getElementById('editModal').style.display = 'none'; }
    
    function openAnnulerDemandeModal(id) {
        currentAnnulerId = id;
        document.getElementById('input_motif_annulation').value = '';
        document.getElementById('annulerDemandeModal').style.display = 'flex';
    }
    
    function closeAnnulerDemandeModal() {
        document.getElementById('annulerDemandeModal').style.display = 'none';
        currentAnnulerId = null;
    }
    
    // --- الفلترة والبحث والترقيم التفاعلي عبر AJAX ---
    function fetchDemandes(page = 1) {
        let search = document.getElementById('demandeSearchBar').value.trim();
        let status = document.getElementById('statusFilter').value;
    
        // تجنب إرسال طلب مكرر إذا لم يتغير نص البحث في الصفحة الأولى
        if (page === 1 && search === lastSearchQuery) {
            return;
        }
        lastSearchQuery = search;
    
        // إلغاء أي طلب بحث سابق لا يزال قيد التنفيذ في الخلفية
        if (searchController) {
            searchController.abort();
        }
        searchController = new AbortController();
    
        let url = `{{ route('collecte.demandes.index') }}?page=${page}&search=${encodeURIComponent(search)}&status=${encodeURIComponent(status)}`;
    
        fetch(url, {
            signal: searchController.signal,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            // إعادة تحميل الصفحة في حالة انتهاء الجلسة (401 أو 419) أو إعادة التوجيه
            if (response.redirected || response.status === 401 || response.status === 419) {
                window.location.reload();
                return null;
            }
            return response.text();
        })
        .then(html => {
            if (html) {
                document.getElementById('tableContainer').innerHTML = html;
            }
        })
        .catch(error => {
            if (error.name === 'AbortError') return; // إخفاء خطأ الإلغاء الطبيعي
            console.error('Erreur lors du chargement des données:', error);
        });
    }
    
    // البحث عند الكتابة بمهلة انتظر 450ms لتخفيف الطلبات السريعة
    document.getElementById('demandeSearchBar').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            fetchDemandes(1);
        }, 450);
    });
    
    // الفلترة الفورية عند تغيير القائمة المنسدلة
    document.getElementById('statusFilter').addEventListener('change', function() {
        lastSearchQuery = null; // إعادة ضبط للتمكن من البحث بنفس الكلمة عند تغيير الفلتر
        fetchDemandes(1);
    });
    
    // الترقيم (Pagination)
    document.getElementById('tableContainer').addEventListener('click', function(e) {
        let link = e.target.closest('.pagination-wrapper a');
        if (link) {
            e.preventDefault();
            let url = new URL(link.href);
            let page = url.searchParams.get('page') || 1;
            fetchDemandes(page);
        }
    });
    
    // --- معالجة إلغاء الطلب بواسطة AJAX ---
    document.getElementById('annulerDemandeForm').addEventListener('submit', function(e) {
        e.preventDefault();
        if (!currentAnnulerId) {
            alert('ID de demande introuvable.');
            return;
        }
    
        let btnSubmit = document.getElementById('btnConfirmAnnuler');
        let motif = document.getElementById('input_motif_annulation').value;
        let token = '{{ csrf_token() }}';
    
        btnSubmit.disabled = true;
        btnSubmit.innerText = "Traitement...";
    
        fetch('/collecte/demandes/' + currentAnnulerId + '/annuler', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                _method: 'PATCH',
                motif_annulation: motif
            })
        })
        .then(response => {
            if (response.status === 419 || response.status === 401) {
                alert('Session expirée. La page va se recharger.');
                location.reload();
                return;
            }
            return response.json().then(data => ({ status: response.status, body: data }));
        })
        .then(res => {
            if (!res) return;
    
            btnSubmit.disabled = false;
            btnSubmit.innerText = "Confirmer l'annulation";
    
            if (res.status === 200 || res.body.success) {
                let row = document.getElementById('demande-row-' + currentAnnulerId);
                if (row) {
                    let statusCell = row.querySelector('.status-cell');
                    if (statusCell) {
                        statusCell.innerHTML = '<span style="padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; background: #F1F5F9; color: #475569;">Annulée</span>';
                    }
                    let btnCancel = row.querySelector('.btn-annuler');
                    if (btnCancel) btnCancel.remove();
                }
    
                let alertContainer = document.getElementById('alertMessageContainer');
                if (alertContainer) {
                    alertContainer.innerHTML = `
                        <div style="background-color: #DCFCE7; color: #15803D; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: 600;">
                            <i class="fa-solid fa-circle-check" style="margin-right: 5px;"></i> ${res.body.message || 'La demande a été annulée avec succès.'}
                        </div>
                    `;
                }
    
                closeAnnulerDemandeModal();
            } else {
                alert(res.body.message || 'Une erreur est survenue lors de l\'annulation.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            btnSubmit.disabled = false;
            btnSubmit.innerText = "Confirmer l'annulation";
            alert('Une erreur réseau est survenue.');
        });
    });
    </script>
@endsection


{{-- <!-- 3. الرفض -->
    @if(auth()->user()->canAccess($screen, 'reject'))
        <form action="{{ route('collecte.demandes.refuser', $demande->id) }}" method="POST" onsubmit="return confirm('Refuser cette demande ?')" style="margin:0;">
            @csrf @method('PATCH')
            <button type="submit" style="border:none; cursor:pointer; font-size:13px; padding: 6px 10px; background: #ef4444; color: white; border-radius: 6px;" title="Refuser">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </form>
    @endif --}}