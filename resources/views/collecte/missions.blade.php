@extends('layouts.app')

@section('content')
<div class="page-title" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
    <div>
        <h2>Missions de Collecte</h2>
        <p style="color: #777;">Organisation et suivi des tournées de collecte</p>
    </div>
    
    @if(auth()->user()->canAccess($screen, 'create'))
        <button style="border:none; padding:10px 20px; color: white; background-color: #C1121F; border-radius:8px; cursor:pointer; font-weight: 600;" onclick="openAddModal()">
            <i class="fa-solid fa-plus"></i> Nouvelle Mission
        </button>
    @endif
</div>

<!-- شريط البحث والفلترة المباشرة عبر AJAX (بدون زر إعادة الضبط) -->
<div style="margin-bottom: 20px; display: flex; gap: 15px; align-items: center;">
    <div style="flex: 1; max-width: 400px; position: relative;">
        <span style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #64748b;">
            <i class="fa-solid fa-magnifying-glass"></i>
        </span>
        <input type="text" id="searchInput" value="{{ request('search') }}" placeholder="Rechercher (N° Mission, Chauffeur)..." 
               style="width: 100%; padding: 12px 12px 12px 40px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; outline: none; box-sizing: border-box;">
    </div>
    <div style="width: 200px;">
        <select id="statusSelect" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; outline: none; background: white; cursor: pointer;">
            <option value="ALL" {{ request('status') == 'ALL' ? 'selected' : '' }}>Tous les statuts</option>
            <option value="Planifiée" {{ request('status') == 'Planifiée' ? 'selected' : '' }}>Planifiée</option>
            <option value="En cours" {{ request('status') == 'En cours' ? 'selected' : '' }}>En cours</option>
            <option value="Terminée" {{ request('status') == 'Terminée' ? 'selected' : '' }}>Terminée</option>
            <option value="Annulée" {{ request('status') == 'Annulée' ? 'selected' : '' }}>Annulée</option>
        </select>
    </div>
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

<!-- 1. تغيير overflow إلى auto في الحاوية -->
<div class="table-card" style="background: white; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow-x: auto;">
    
    <!-- 2. إضافة min-width للجدول وتقليل الهوامش داخل العناوين -->
    <table id="missionTable" style="width: 100%; min-width: 950px; border-collapse: collapse; text-align: left;">
        <thead style="background-color: #f8fafc; border-bottom: 2px solid #e2e8f0;">
            <tr>
                <th style="padding: 12px 10px; color: #ffffff; font-weight: 600;">N° Mission</th>
                <th style="padding: 12px 10px; color: #ffffff; font-weight: 600;">Départ / Retour Prévu</th>
                <th style="padding: 12px 10px; color: #ffffff; font-weight: 600;">Départ / Retour Réel</th>
                <th style="padding: 12px 10px; color: #ffffff; font-weight: 600;">Chauffeur</th>
                <th style="padding: 12px 10px; color: #ffffff; font-weight: 600;">Camion</th>
                <th style="padding: 12px 10px; color: #ffffff; font-weight: 600; text-align: center;">Nb Demandes</th>
                <th style="padding: 12px 10px; color: #ffffff; font-weight: 600;">Statut</th>
                <th style="padding: 12px 10px; color: #ffffff; font-weight: 600; text-align: center; white-space: nowrap; width: 1%;">Actions</th>
            </tr>
        </thead>
        <tbody id="missionsTbody">
            @include('collecte._missions_table')
        </tbody>
    </table>
</div>

<div id="paginationContainer" style="margin-top: 15px; display: flex; justify-content: flex-end;">
    {{ $missions->links() }}
</div>

<!-- Modal الرئيسية للإنشاء والتعديل والمعاينة -->
<div id="missionModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index:999; align-items:center; justify-content:center; padding: 20px; box-sizing: border-box;">
    <div style="background:white; width:100%; max-width:720px; padding: 25px 30px; border-radius: 20px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);">
        <h3 id="modalTitle" style="margin-top:0; color: #1e293b;">Nouvelle Mission</h3>
        <form id="missionForm" method="POST" style="display:flex; flex-direction:column; gap:12px; margin: 0;">
            @csrf
            <input type="hidden" id="formMethod" name="_method" value="POST">

            <div style="display: flex; gap: 15px;">
                <div style="flex: 1; display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 13px; font-weight: 600;">Date & Heure Départ Prévue *</label>
                    <input type="datetime-local" name="date_depart_prevue" id="m_date_depart_prevue" required class="modal-input" style="padding:10px; border:1px solid #e2e8f0; border-radius:10px; width:100%; box-sizing:border-box;">
                </div>
                <div style="flex: 1; display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 13px; font-weight: 600;">Date & Heure Retour Prévue *</label>
                    <input type="datetime-local" name="date_retour_prevue" id="m_date_retour_prevue" required class="modal-input" style="padding:10px; border:1px solid #e2e8f0; border-radius:10px; width:100%; box-sizing:border-box;">
                </div>
            </div>

            <div style="display: flex; gap: 15px;">
                <div style="flex: 1; display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 13px; font-weight: 600;">Chauffeur *</label>
                    <select name="chauffeur_id" id="m_chauffeur" required class="modal-input" style="padding:10px; border:1px solid #e2e8f0; border-radius:10px; background:white; width:100%;">
                        <option value="">Sélectionnez...</option>
                        @foreach($chauffeurs as $ch) <option value="{{ $ch->id }}">{{ $ch->nom }} {{ $ch->prenom }}</option> @endforeach
                    </select>
                </div>
                <div style="flex: 1; display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 13px; font-weight: 600;">Camion *</label>
                    <select name="equipement_id" id="m_equipement" required class="modal-input" style="padding:10px; border:1px solid #e2e8f0; border-radius:10px; background:white; width:100%;">
                        <option value="">Sélectionnez...</option>
                        @foreach($equipements as $eq) <option value="{{ $eq->id }}">{{ $eq->nom }} ({{ $eq->immatriculation }})</option> @endforeach
                    </select>
                </div>
            </div>

            <div style="display: flex; flex-direction: column; gap: 4px;">
                <label style="font-size: 13px; font-weight: 600;">Demandes validées *</label>
                <div id="demandesContainer" style="max-height: 140px; overflow-y: auto; border: 1px solid #e2e8f0; padding: 10px; border-radius: 10px;"></div>
            </div>

            <div style="display: flex; flex-direction: column; gap: 4px;">
                <label style="font-size: 13px; font-weight: 600;">Observation</label>
                <textarea name="observation" id="m_observation" rows="2" class="modal-input" style="padding:10px; border:1px solid #e2e8f0; border-radius:10px; resize:none; width:100%; box-sizing:border-box;"></textarea>
            </div>

            <div style="display:flex; justify-content: flex-end; gap:12px; margin-top:10px;">
                <button type="button" onclick="closeModal()" style="background:#f1f5f9; color:#475569; border:none; padding:11px 20px; border-radius:10px; cursor:pointer; font-weight:600;">Fermer</button>
                <button type="submit" id="btnSubmit" style="background:#C1121F; color:white; border:none; padding:11px 20px; border-radius:10px; cursor:pointer; font-weight:600;">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal DEPART -->
<div id="departModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index:999; align-items:center; justify-content:center; padding: 20px; box-sizing: border-box;">
    <div style="background:white; width:100%; max-width:450px; padding: 25px; border-radius: 20px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);">
        <h3 style="margin-top:0; color: #1e293b;">Départ de la Mission</h3>
        <form id="departForm" method="POST" style="display:flex; flex-direction:column; gap:15px; margin: 0;">
            @csrf
            @method('PATCH')
            
            <div style="display: flex; flex-direction: column; gap: 6px;">
                <label style="font-size: 13px; font-weight: 600;">N° Mission Officiel</label>
                <input type="text"  id="input_numero_mission" value="{{ $nextNumero ?? \App\Services\NumberingService::preview('compteur_mission') }}" readonly 
       style="padding:10px; border:1px solid #cbd5e1; border-radius:10px; width:100%; box-sizing:border-box; font-size:13px; outline:none; background-color:#f1f5f9; color:#0f172a; font-weight:bold; cursor: not-allowed;">
            </div>

            <div style="display: flex; flex-direction: column; gap: 6px;">
                <label style="font-size: 13px; font-weight: 600;">Date & Heure de Départ Réelle *</label>
                <input type="datetime-local" name="date_depart" id="input_date_depart" required style="padding:10px; border:1px solid #e2e8f0; border-radius:10px; width:100%; box-sizing:border-box;">
            </div>

            <div style="display:flex; justify-content: flex-end; gap:12px; margin-top:10px;">
                <button type="button" onclick="closeDepartModal()" style="background:#f1f5f9; color:#475569; border:none; padding:10px 18px; border-radius:10px; cursor:pointer; font-weight:600;">Annuler</button>
                <button type="submit" style="background:#854D0E; color:white; border:none; padding:10px 18px; border-radius:10px; cursor:pointer; font-weight:600;">Confirmer le Départ</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal CLOTURER -->
<div id="clotureModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index:999; align-items:center; justify-content:center; padding: 20px; box-sizing: border-box;">
    <div style="background:white; width:100%; max-width:450px; padding: 25px; border-radius: 20px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);">
        <h3 style="margin-top:0; color: #1e293b;">Clôturer la Mission</h3>
        <form id="clotureForm" method="POST" style="display:flex; flex-direction:column; gap:15px; margin: 0;">
            @csrf
            @method('PATCH')
            <div style="display: flex; flex-direction: column; gap: 6px;">
                <label style="font-size: 13px; font-weight: 600;">Date & Heure de Retour Réelle *</label>
                <input type="datetime-local" name="date_retour" id="input_date_retour" required style="padding:10px; border:1px solid #e2e8f0; border-radius:10px; width:100%; box-sizing:border-box;">
            </div>
            <div style="display:flex; justify-content: flex-end; gap:12px; margin-top:10px;">
                <button type="button" onclick="closeClotureModal()" style="background:#f1f5f9; color:#475569; border:none; padding:10px 18px; border-radius:10px; cursor:pointer; font-weight:600;">Annuler</button>
                <button type="submit" style="background:#10b981; color:white; border:none; padding:10px 18px; border-radius:10px; cursor:pointer; font-weight:600;">Valider la Clôture</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal ANNULER -->
<div id="annulerModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index:999; align-items:center; justify-content:center; padding: 20px; box-sizing: border-box;">
    <div style="background:white; width:100%; max-width:450px; padding: 25px; border-radius: 20px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);">
        <h3 style="margin-top:0; color: #991B1B;">Annuler la Mission</h3>
        <p style="font-size: 13px; color: #64748b; margin-bottom: 15px;">
            En annulant cette mission, les demandes de collecte associées repasseront au statut <b>Validée</b> pour être re-planifiées.
        </p>
        <form id="annulerForm" method="POST" style="display:flex; flex-direction:column; gap:15px; margin: 0;">
            @csrf
            @method('PATCH')
            <div style="display: flex; flex-direction: column; gap: 6px;">
                <label style="font-size: 13px; font-weight: 600;">Motif d'annulation *</label>
                <textarea name="motif_annulation" id="input_motif_annulation" required rows="3" placeholder="Raison de l'annulation (ex: Panne camion, Problème chauffeur...)" style="padding:10px; border:1px solid #e2e8f0; border-radius:10px; width:100%; box-sizing:border-box; resize:none;"></textarea>
            </div>
            <div style="display:flex; justify-content: flex-end; gap:12px; margin-top:10px;">
                <button type="button" onclick="closeAnnulerModal()" style="background:#f1f5f9; color:#475569; border:none; padding:10px 18px; border-radius:10px; cursor:pointer; font-weight:600;">Retour</button>
                <button type="submit" style="background:#ef4444; color:white; border:none; padding:10px 18px; border-radius:10px; cursor:pointer; font-weight:600;">Confirmer l'annulation</button>
            </div>
        </form>
    </div>
</div>

<script>
    const allDemandes = @json($allDemandes);
    
    // متغيرات التحكم بالبحث والطلبات المتزامنة
    let searchTimeout = null;
    let searchController = null;
    let lastSearchQuery = null;
    let lastStatusQuery = null;
    
    function getCurrentDateTime() {
        const now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        return now.toISOString().slice(0, 16);
    }
    
    function openDepartModal(missionId) {
        document.getElementById('departForm').action = '/collecte/missions/' + missionId + '/demarrer';
        document.getElementById('input_date_depart').value = getCurrentDateTime();
        document.getElementById('departModal').style.display = 'flex';
    }
    function closeDepartModal() {
        document.getElementById('departModal').style.display = 'none';
    }
    
    function openClotureModal(missionId, dateDepartReelle) {
        document.getElementById('clotureForm').action = '/collecte/missions/' + missionId + '/cloturer';
        const inputRetour = document.getElementById('input_date_retour');
        const nowStr = getCurrentDateTime();
        
        if (dateDepartReelle) {
            const minDateStr = dateDepartReelle.substring(0, 16).replace(' ', 'T');
            inputRetour.min = minDateStr;
            inputRetour.value = (nowStr < minDateStr) ? minDateStr : nowStr;
        } else {
            inputRetour.value = nowStr;
        }
        
        document.getElementById('clotureModal').style.display = 'flex';
    }
    function closeClotureModal() {
        document.getElementById('clotureModal').style.display = 'none';
    }
    
    function openAnnulerModal(missionId) {
        document.getElementById('annulerForm').action = '/collecte/missions/' + missionId + '/annuler';
        document.getElementById('input_motif_annulation').value = '';
        document.getElementById('annulerModal').style.display = 'flex';
    }
    function closeAnnulerModal() {
        document.getElementById('annulerModal').style.display = 'none';
    }
    
    document.addEventListener('DOMContentLoaded', function () {
        const startInput = document.getElementById('m_date_depart_prevue');
        const endInput = document.getElementById('m_date_retour_prevue');
    
        if(startInput && endInput) {
            startInput.addEventListener('change', function () {
                if (this.value) {
                    endInput.min = this.value;
                    if (endInput.value && endInput.value < this.value) {
                        endInput.value = this.value;
                    }
                }
            });
    
            endInput.addEventListener('change', function () {
                if (startInput.value && this.value < startInput.value) {
                    alert("La date de retour prévue ne peut pas être antérieure à la date de départ !");
                    this.value = startInput.value;
                }
            });
        }
    
        // --- سكربت البحث اللحظي والتصفيات عبر AJAX المحسّن ---
        const searchInput = document.getElementById('searchInput');
        const statusSelect = document.getElementById('statusSelect');
    
        function fetchFilteredData(pageUrl = null) {
            const search = searchInput ? searchInput.value.trim() : '';
            const status = statusSelect ? statusSelect.value : '';
    
            // منع تكرار الطلب إذا لم يتغير نص البحث أو الحالة (إلا في حال الانتقال بين الصفحات)
            if (!pageUrl && search === lastSearchQuery && status === lastStatusQuery) {
                return;
            }
            lastSearchQuery = search;
            lastStatusQuery = status;
    
            // إلغاء أي طلب بحث سابق لا يزال قيد المعالجة في الخلفية
            if (searchController) {
                searchController.abort();
            }
            searchController = new AbortController();
    
            let url = pageUrl || "{{ route('collecte.missions.index') }}";
            const params = new URLSearchParams({ search: search, status: status });
            const fetchUrl = url.includes('?') ? `${url}&${params.toString()}` : `${url}?${params.toString()}`;
    
            window.history.pushState({}, '', fetchUrl);
    
            fetch(fetchUrl, {
                signal: searchController.signal,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                // إعادة تحميل الصفحة فوراً إذا انتهت الجلسة (401 أو 419)
                if (response.status === 401 || response.status === 419 || response.redirected) {
                    window.location.reload();
                    return null;
                }
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                if (!data) return;
                if (data.html !== undefined) {
                    document.getElementById('missionsTbody').innerHTML = data.html;
                }
                if (data.pagination !== undefined && document.getElementById('paginationContainer')) {
                    document.getElementById('paginationContainer').innerHTML = data.pagination;
                }
            })
            .catch(error => {
                if (error.name === 'AbortError') return; // تجاهل خطأ الإلغاء الطبيعي
                console.error('Fetch Error:', error);
            });
        }
    
        // البحث أثناء الكتابة مع مهلة انتظار 450ms لتخفيف إغراق السيرفر
        if (searchInput) {
            searchInput.addEventListener('input', function () {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => fetchFilteredData(), 450);
            });
        }
    
        // الفلترة عند تغيير الحالة
        if (statusSelect) {
            statusSelect.addEventListener('change', () => {
                lastSearchQuery = null; // إعادة ضبط لإتاحة البحث بنفس الكلمة
                fetchFilteredData();
            });
        }
    
        // الترقيم (Pagination)
        const pagContainer = document.getElementById('paginationContainer');
        if (pagContainer) {
            pagContainer.addEventListener('click', function (e) {
                let link = e.target.closest('a');
                if (link) {
                    e.preventDefault();
                    fetchFilteredData(link.href);
                }
            });
        }
    });
    
    function renderDemandes(selectedIds = [], isReadonly = false) {
        const container = document.getElementById('demandesContainer');
        container.innerHTML = '';
        const filtered = allDemandes.filter(d => d.statut === 'Validée' || selectedIds.includes(d.id));
        
        if(filtered.length === 0) {
            container.innerHTML = '<p style="color:#888; font-size:13px; margin:0;">Aucune demande validée disponible.</p>';
            return;
        }
        filtered.forEach(d => {
            const checked = selectedIds.includes(d.id) ? 'checked' : '';
            const disabled = isReadonly ? 'disabled' : '';
            container.innerHTML += `
                <div style="display:flex; align-items:center; gap:10px; padding:5px 0;">
                    <input type="checkbox" name="demandes[]" value="${d.id}" ${checked} ${disabled}>
                    <label style="font-size:13px;"><b>${d.numero}</b> - ${d.fournisseur?.nom_fournisseur || '---'} (${d.materiau?.nom || '---'} : ${d.quantite_estimee} Kg)</label>
                </div>`;
        });
    }
    
    function openAddModal() {
        document.getElementById('modalTitle').innerText = "Nouvelle Mission";
        document.getElementById('missionForm').action = "{{ route('collecte.missions.store') }}";
        document.getElementById('formMethod').value = "POST";
        document.getElementById('btnSubmit').style.display = 'block';
        document.getElementById('m_date_depart_prevue').value = '';
        document.getElementById('m_date_retour_prevue').value = '';
        document.getElementById('m_date_retour_prevue').removeAttribute('min');
        document.getElementById('m_chauffeur').value = '';
        document.getElementById('m_equipement').value = '';
        document.getElementById('m_observation').value = '';
        toggleInputs(false);
        renderDemandes([], false);
        document.getElementById('missionModal').style.display = 'flex';
    }
    
    function openEditModal(m, selectedIds) {
        document.getElementById('modalTitle').innerText = "Modifier la Mission";
        document.getElementById('missionForm').action = '/collecte/missions/' + m.id;
        document.getElementById('formMethod').value = "PUT";
        document.getElementById('btnSubmit').style.display = 'block';
        setupModalData(m, selectedIds, false);
    }
    
    function openViewModal(m) {
        document.getElementById('modalTitle').innerText = "Consulter la Mission";
        document.getElementById('btnSubmit').style.display = 'none';
        const selectedIds = m.demandes.map(d => d.id);
        setupModalData(m, selectedIds, true);
    }
    
    function setupModalData(m, selectedIds, isReadonly) {
        if(m.date_depart_prevue) {
            const startVal = m.date_depart_prevue.substring(0,16).replace(' ', 'T');
            document.getElementById('m_date_depart_prevue').value = startVal;
            document.getElementById('m_date_retour_prevue').min = startVal;
        }
        if(m.date_retour_prevue) {
            document.getElementById('m_date_retour_prevue').value = m.date_retour_prevue.substring(0,16).replace(' ', 'T');
        }
        
        document.getElementById('m_chauffeur').value = m.chauffeur_id;
        document.getElementById('m_equipement').value = m.equipement_id;
        document.getElementById('m_observation').value = m.observation || '';
        
        toggleInputs(isReadonly);
        renderDemandes(selectedIds, isReadonly);
        document.getElementById('missionModal').style.display = 'flex';
    }
    
    function toggleInputs(isReadonly) {
        let inputs = document.getElementsByClassName('modal-input');
        for (let i = 0; i < inputs.length; i++) {
            inputs[i].disabled = isReadonly;
            inputs[i].style.background = isReadonly ? '#f8fafc' : 'white';
        }
    }
    
    function closeModal() { document.getElementById('missionModal').style.display = 'none'; }
    </script>
@endsection