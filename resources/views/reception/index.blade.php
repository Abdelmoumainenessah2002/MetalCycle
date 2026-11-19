@extends('layouts.app')

@section('content')
<style>
    .btn-action {
        border: none;
        padding: 8px 10px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        font-size: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }
    .btn-action:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .btn-primary-custom { background-color: #0f172a; color: white; }
    .btn-primary-custom:hover { background-color: #1e293b; }
    
    .btn-warning-custom { background-color: #f59e0b; color: white; }
    .btn-warning-custom:hover { background-color: #d97706; }
    
    .btn-success-custom { background-color: #10b981; color: white; }
    .btn-success-custom:hover { background-color: #059669; }
    
    .btn-info-custom { background-color: #f1f5f9; color: #475569; }
    .btn-info-custom:hover { background-color: #e2e8f0; color: #0f172a; }

    .badge-status {
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .stat-card {
        background: white;
        padding: 16px 20px;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        flex: 1;
        display: flex;
        align-items: center;
        gap: 15px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }

    .modal-section {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 12px;
    }
    .modal-section-title {
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        color: #64748b;
        letter-spacing: 0.5px;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
</style>

<!-- Header Section -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <div>
        <h2 style="margin: 0; color: #0f172a; font-weight: 700; font-size: 24px;">Gestion des Réceptions</h2>
        <p style="margin: 4px 0 0; color: #64748b; font-size: 14px;">Suivi et contrôle du pesage de matières premières</p>
    </div>
    
    @if(auth()->user()->canAccess($screen, 'create'))
        <button class="btn-action btn-primary-custom" style="padding: 10px 18px; font-size: 14px; gap: 6px;" onclick="openStage1Modal()">
            <i class="fa-solid fa-plus"></i> Nouvelle Réception
        </button>
    @endif
</div>

<!-- Stats Widgets -->
<div style="display: flex; gap: 15px; margin-bottom: 20px; flex-wrap: wrap;">
    <div class="stat-card">
        <div style="width: 42px; height: 42px; border-radius: 10px; background: #fef3c7; color: #d97706; display: flex; align-items: center; justify-content: center; font-size: 18px;">
            <i class="fa-solid fa-truck-moving"></i>
        </div>
        <div>
            <div style="font-size: 12px; color: #64748b; font-weight: 600;">En attente Tare</div>
            <div style="font-size: 18px; font-weight: 700; color: #0f172a;">{{ $countEnAttenteTare }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div style="width: 42px; height: 42px; border-radius: 10px; background: #e0f2fe; color: #0284c7; display: flex; align-items: center; justify-content: center; font-size: 18px;">
            <i class="fa-solid fa-clock-rotate-left"></i>
        </div>
        <div>
            <div style="font-size: 12px; color: #64748b; font-weight: 600;">En attente Validation</div>
            <div style="font-size: 18px; font-weight: 700; color: #0f172a;">{{ $countEnAttenteValidation }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div style="width: 42px; height: 42px; border-radius: 10px; background: #dcfce7; color: #16a34a; display: flex; align-items: center; justify-content: center; font-size: 18px;">
            <i class="fa-solid fa-circle-check"></i>
        </div>
        <div>
            <div style="font-size: 12px; color: #64748b; font-weight: 600;">Réceptions Validées</div>
            <div style="font-size: 18px; font-weight: 700; color: #0f172a;">{{ $countAcceptee }}</div>
        </div>
    </div>
</div>

@if(session('success'))
    <div style="background-color: #DCFCE7; color: #15803D; padding: 12px 16px; border-radius: 10px; margin-bottom: 20px; font-weight: 600; font-size: 14px; border: 1px solid #86EFAC;">
        <i class="fa-solid fa-circle-check" style="margin-right: 6px;"></i> {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div style="background-color: #FEE2E2; color: #991B1B; padding: 12px 16px; border-radius: 10px; margin-bottom: 20px; font-weight: 600; font-size: 14px; border: 1px solid #FCA5A5;">
        <ul style="margin:0; padding-left:18px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<!-- شريط البحث والتصفية -->
<div style="margin-bottom: 15px; display: flex; gap: 12px; max-width: 850px; flex-wrap: wrap;">
    <div style="position: relative; flex: 2; min-width: 220px;">
        <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 13px;"></i>
        <input type="text" id="searchMissionInput" value="{{ request('search') }}" placeholder="Rechercher par N° Mission ou Réception..." style="width: 100%; padding: 9px 12px 9px 36px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; outline: none;">
    </div>

    <div style="flex: 1; min-width: 170px;">
        <select id="filterStatutSelect" style="width: 100%; padding: 9px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; background: white; outline: none; cursor: pointer; font-weight: 500; color: #0f172a;">
            <option value="">Tous les statuts</option>
            <option value="En attente Tare" {{ request('statut') == 'En attente Tare' ? 'selected' : '' }}>1. En attente Tare</option>
            <option value="En attente Validation" {{ request('statut') == 'En attente Validation' ? 'selected' : '' }}>2. En attente Validation</option>
            <option value="Acceptée" {{ request('statut') == 'Acceptée' ? 'selected' : '' }}>3. Acceptée</option>
            <option value="Refusée" {{ request('statut') == 'Refusée' ? 'selected' : '' }}>3. Refusée</option>
        </select>
    </div>

    <div style="flex: 1; min-width: 150px;">
        <input type="date" id="filterDateInput" value="{{ request('date') }}" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; outline: none; color: #0f172a; background: white;">
    </div>
</div>

<!-- Main Table -->
<div style="background: white; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.02); overflow: hidden;">
    <table id="receptionsTable" style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;">
        <thead style="background-color: #f8fafc; border-bottom: 1px solid #e2e8f0;">
            <tr>
                <th style="padding: 12px 16px; color: #ffffff; font-weight: 700;">N° Réception</th>
                <th style="padding: 12px 16px; color: #ffffff; font-weight: 700;">Type / Mission</th>
                <th style="padding: 12px 16px; color: #ffffff; font-weight: 700;">Poids Fournisseur</th>
                <th style="padding: 12px 16px; color: #ffffff; font-weight: 700;">Poids Brut (Entrée)</th>
                <th style="padding: 12px 16px; color: #ffffff; font-weight: 700;">Poids Tare (Sortie)</th>
                <th style="padding: 12px 16px; color: #ffffff; font-weight: 700;">Poids Net</th>
                <th style="padding: 12px 16px; color: #ffffff; font-weight: 700;">Statut</th>
                <th style="padding: 12px 16px; color: #ffffff; font-weight: 700; text-align: center;">Actions</th>
            </tr>
        </thead>
        <tbody id="receptionsTbody">
            @include('reception._table')
        </tbody>
    </table>
</div>

<div id="paginationContainer" style="margin-top: 15px; display: flex; justify-content: flex-end;">
    {{ $receptions->links() }}
</div>

<!-- Modal -->
<div id="receptionModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(15, 23, 42, 0.5); backdrop-filter: blur(3px); z-index:999; align-items:center; justify-content:center; padding: 20px;">
    <div style="background:white; width:100%; max-width:800px; border-radius: 16px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); overflow: hidden; display: flex; flex-direction: column; max-height: 90vh;">
        
        <div style="padding: 18px 24px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; background: #f8fafc;">
            <h3 id="modalTitle" style="margin:0; color: #0f172a; font-size: 17px; font-weight: 700;">Titre Modal</h3>
            <button onclick="closeModal()" style="border:none; background:transparent; color:#94a3b8; font-size:18px; cursor:pointer;"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <div style="padding: 20px 24px; overflow-y: auto;">
            <form id="receptionForm" method="POST" style="display:flex; flex-direction:column; gap:12px;" novalidate>
                @csrf
                <input type="hidden" id="formMethod" name="_method" value="POST">
                <input type="hidden" name="date_reception" id="r_date_hidden">

                <!-- Validation Section -->
<div id="validationSection" class="modal-section" style="display:none; background: #eff6ff; border-color: #bfdbfe;">
    <div class="modal-section-title" style="color: #1e40af;"><i class="fa-solid fa-clipboard-check"></i> Étape De Validation Officielle</div>
    <div style="display: flex; gap: 12px; margin-bottom: 10px; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 180px; display: flex; flex-direction: column; gap: 4px;">
            <label style="font-size: 12px; font-weight: 700; color: #1e40af;">Décision *</label>
            <select name="statut" id="r_statut_validation" onchange="toggleNumeroRequirement()" style="padding:8px 12px; border:1px solid #93c5fd; border-radius:8px; background:white; font-weight:600; font-size:13px;">
                <option value="Acceptée">Acceptée (Approuvée)</option>
                <option value="Refusée">Refusée (Rejetée)</option>
            </select>
        </div>
        
        <div style="flex: 1; min-width: 180px; display: flex; flex-direction: column; gap: 4px;" id="numeroGroup">
            <label style="font-size: 12px; font-weight: 700; color: #1e40af;">N° Réception Officiel *</label>
            <!-- 🟢 تم تعديل الـ id إلى r_numero وإلغاء الاعتماد المباشر على $reception -->
            <input type="text" 
                   name="numero" 
                   id="r_numero" 
                   style="padding:8px 12px; border:1px solid #93c5fd; border-radius:8px; font-size:13px; background:white;"
                   placeholder="Généré automatiquement..." 
                  >
        </div>

        <div style="flex: 1; min-width: 180px; display: flex; flex-direction: column; gap: 4px;" id="dateValidationGroup">
            <label id="lbl_date_validation_modal" style="font-size: 12px; font-weight: 700; color: #1e40af;">Date de Validation</label>
            <input type="datetime-local" 
                   name="date_validation" 
                   id="r_date_validation" 
                   readonly 
                   style="padding:8px 12px; border:1px solid #cbd5e1; border-radius:8px; font-size:13px; background:#f1f5f9; color:#475569; pointer-events: none;">
        </div>
    </div>

    <div style="display: flex; flex-direction: column; gap: 4px;" id="zoneGroup">
        <label style="font-size: 12px; font-weight: 700; color: #1e40af;">Zone de Stockage *</label>
        <select name="zone_stockage_id" id="r_zone" style="padding:8px 12px; border:1px solid #93c5fd; border-radius:8px; background:white; font-size:13px;">
            <option value="">Sélectionnez la zone de stockage...</option>
            @foreach($zones as $zn) 
                <option value="{{ $zn->id }}">{{ $zn->nom }}</option> 
            @endforeach
        </select>
    </div>
</div>

                <!-- Logistics Section -->
                <div class="modal-section">
                    <div class="modal-section-title"><i class="fa-solid fa-truck"></i> Origine et Logistique</div>
                    <div style="display: flex; gap: 12px; margin-bottom: 10px;">
                        <div style="flex: 1; display: flex; flex-direction: column; gap: 4px;">
                            <label style="font-size: 12px; font-weight: 600;">Type Réception *</label>
                            <select name="type_reception" id="r_type" required onchange="toggleTypeFields()" style="padding:8px 12px; border:1px solid #cbd5e1; border-radius:8px; background:white; font-size:13px;">
                                <option value="Réception Directe">Réception Directe </option>
                                <option value="Collecte Interne">Collecte Interne </option>
                            </select>
                        </div>
                    </div>

                    <div style="display: flex; gap: 12px; margin-bottom: 10px;">
                        <div style="flex: 1; display: flex; flex-direction: column; gap: 4px;" id="missionGroup">
                            <label style="font-size: 12px; font-weight: 600;">Mission de Collecte *</label>
                            <select name="mission_collecte_id" id="r_mission" style="padding:8px 12px; border:1px solid #cbd5e1; border-radius:8px; background:white; font-size:13px;">
                                <option value="">Sélectionnez la mission...</option>
                                @foreach($availableMissions as $ms) 
                                    <option value="{{ $ms->id }}">{{ $ms->numero }}</option> 
                                @endforeach
                            </select>
                        </div>
                        
                        <div style="flex: 1; display: flex; flex-direction: column; gap: 4px;" id="singleFournisseurGroup">
                            <label style="font-size: 12px; font-weight: 600;">Fournisseur *</label>
                            <select name="fournisseur_id" id="r_fournisseur" style="padding:8px 12px; border:1px solid #cbd5e1; border-radius:8px; background:white; font-size:13px;">
                                <option value="">Sélectionnez le fournisseur...</option>
                                @foreach($fournisseurs as $fr) 
                                    <option value="{{ $fr->id }}">{{ $fr->nom_fournisseur }}</option> 
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div style="display: none; flex-direction: column; gap: 4px; margin-bottom: 10px;" id="multiFournisseursGroup">
                        <label style="font-size: 12px; font-weight: 700; color: #64748b;">Tous les Fournisseurs Associés</label>
                        <input type="text" id="r_multi_fournisseurs" readonly style="padding:8px 12px; border:1px solid #e2e8f0; border-radius:8px; background:#f1f5f9; color:#b91c1c; font-weight:700; font-size:13px;">
                    </div>

                    <div style="display: flex; gap: 12px;" id="logisticInfoGroup">
                        <div style="flex: 1; display: flex; flex-direction: column; gap: 4px;">
                            <label style="font-size: 12px; font-weight: 600; color:#64748b;">Chauffeur</label>
                            <input type="text" id="r_chauffeur_nom" readonly style="padding:8px 12px; border:1px solid #e2e8f0; border-radius:8px; background:#f1f5f9; font-size:13px;">
                        </div>
                        <div style="flex: 1; display: flex; flex-direction: column; gap: 4px;">
                            <label style="font-size: 12px; font-weight: 600; color:#64748b;">Camion / Matricule</label>
                            <input type="text" id="r_camion_mat" readonly style="padding:8px 12px; border:1px solid #e2e8f0; border-radius:8px; background:#f1f5f9; font-size:13px;">
                        </div>
                    </div>
                </div>

                <!-- Article & Stockage Section -->
                <div class="modal-section">
                    <div class="modal-section-title"><i class="fa-solid fa-boxes-stacked"></i> Article & Prix</div>
                    <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                        <div style="flex: 2; min-width: 180px; display: flex; flex-direction: column; gap: 4px;">
                            <label style="font-size: 12px; font-weight: 700; color: #0f172a;">Matière *</label>
                            <select name="materiau_id" id="r_materiau_id" required style="padding:8px 12px; border:1px solid #cbd5e1; border-radius:8px; background:white; font-size:13px;">
                                <option value="">Sélectionnez la matière...</option>
                                @foreach($materiaux as $m)
                                    <option value="{{ $m->id }}">{{ $m->nom }} ({{ $m->unite }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div style="flex: 1; min-width: 120px; display: flex; flex-direction: column; gap: 4px;">
                            <label style="font-size: 12px; font-weight: 700; color: #0f172a;">Prix Unitaire (DZD)</label>
                            <input type="number" step="0.01" name="prix_unitaire" id="r_prix_unitaire" placeholder="Ex: 150.00" style="padding:8px 12px; border:1px solid #cbd5e1; border-radius:8px; font-size:13px;">
                        </div>

                        <div style="flex: 1.5; min-width: 160px; display: none; flex-direction: column; gap: 4px;" id="displayZoneGroup">
                            <label style="font-size: 12px; font-weight: 700; color: #0369a1;">Zone de Stockage</label>
                            <input type="text" id="r_zone_display" readonly style="padding:8px 12px; border:1px solid #93c5fd; border-radius:8px; background:#f0f9ff; color:#0369a1; font-weight:700; font-size:13px;">
                        </div>

                        <div style="flex: 1.5; min-width: 160px; display: none; flex-direction: column; gap: 4px;" id="displayDateValGroup">
                            <label id="lbl_date_val_display" style="font-size: 12px; font-weight: 700; color: #0369a1;">Date de Validation</label>
                            <input type="text" id="r_date_val_display" readonly style="padding:8px 12px; border:1px solid #93c5fd; border-radius:8px; background:#f0f9ff; color:#0369a1; font-weight:700; font-size:13px;">
                        </div>
                    </div>
                </div>

                <!-- ALERT BOX -->
                <div id="weightComparisonAlert" style="display:none; padding: 12px 16px; border-radius: 10px; font-weight: 600; font-size: 13px; align-items: center; gap: 10px; margin-bottom: 4px;">
                    <i id="weightAlertIcon" class="fa-solid" style="font-size: 16px;"></i>
                    <span id="weightAlertText"></span>
                </div>

                <!-- Weights Section -->
                <div class="modal-section" style="background: #ffffff; border-color: #cbd5e1;">
                    <div class="modal-section-title"><i class="fa-solid fa-scale-balanced"></i> Mesures du Pesage (Kg)</div>
                    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                        <div style="flex: 1; min-width: 130px; display: flex; flex-direction: column; gap: 4px;">
                            <label style="font-size: 11px; font-weight: 600; color: #475569;">Poids Fournisseur</label>
                            <input type="number" step="0.01" name="poids_fournisseur" id="r_poids_fournisseur" placeholder="5000" oninput="calculateAndCompareWeights()" style="padding:8px 10px; border:1px solid #cbd5e1; border-radius:8px; font-size:13px;">
                        </div>

                        <div style="flex: 1; min-width: 130px; display: flex; flex-direction: column; gap: 4px;">
                            <label style="font-size: 11px; font-weight: 700; color: #0f172a;">Poids Entrée (Brut) *</label>
                            <input type="number" step="0.01" name="poids_entree" id="r_poids_entree" required oninput="calculateAndCompareWeights()" style="padding:8px 10px; border:1px solid #cbd5e1; border-radius:8px; font-size:13px;">
                        </div>
                        
                        <div style="flex: 1; min-width: 130px; display: flex; flex-direction: column; gap: 4px;">
                            <label style="font-size: 11px; font-weight: 700; color: #0f172a;">Poids Sortie (Tare)</label>
                            <input type="number" step="0.01" name="poids_sortie" id="r_poids_sortie" oninput="calculateAndCompareWeights()" style="padding:8px 10px; border:1px solid #cbd5e1; border-radius:8px; font-size:13px;">
                        </div>

                        <div style="flex: 1; min-width: 130px; display: flex; flex-direction: column; gap: 4px;">
                            <label style="font-size: 11px; font-weight: 700; color: #0f172a;">Poids Net Calculé</label>
                            <input type="text" id="r_poids_net_display" readonly placeholder="---" style="padding:8px 10px; border:1px solid #94a3b8; border-radius:8px; background: #f1f5f9; font-weight: 800; color: #0f172a; font-size:13px;">
                        </div>
                    </div>
                </div>

                <div style="display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 12px; font-weight: 600;">Observation</label>
                    <textarea name="observation" id="r_observation" rows="2" style="padding:8px 12px; border:1px solid #cbd5e1; border-radius:8px; font-size:13px; resize:none;"></textarea>
                </div>

                <div style="display:flex; justify-content: flex-end; gap:10px; margin-top:10px; padding-top: 10px; border-top: 1px solid #e2e8f0;">
                    <button type="button" onclick="closeModal()" class="btn-action btn-info-custom" style="padding: 8px 14px;">Fermer</button>
                    <button type="submit" id="btnSubmit" class="btn-action btn-primary-custom" style="padding: 8px 14px;">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const materiauxOptions = @json($materiaux);
    const missionsData = @json($missions);
    const availableMissionsData = @json($availableMissions);
    
    let searchTimeout = null;
    let searchController = null;
    let lastSearchQuery = null;
    let lastStatutQuery = null;
    let lastDateQuery = null;

    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('searchMissionInput');
        const statutSelect = document.getElementById('filterStatutSelect');
        const dateInput = document.getElementById('filterDateInput');
        const receptionForm = document.getElementById('receptionForm');

        if (receptionForm) {
            receptionForm.addEventListener('submit', function () {
                enableAllInputs();
            });
        }
    
        function fetchFilteredData(pageUrl = null) {
            const search = searchInput ? searchInput.value.trim() : '';
            const statut = statutSelect ? statutSelect.value : '';
            const date = dateInput ? dateInput.value : '';
    
            if (!pageUrl && search === lastSearchQuery && statut === lastStatutQuery && date === lastDateQuery) {
                return;
            }
            lastSearchQuery = search;
            lastStatutQuery = statut;
            lastDateQuery = date;
    
            if (searchController) {
                searchController.abort();
            }
            searchController = new AbortController();
    
            let url = pageUrl || "{{ route('reception.index') }}";
            const params = new URLSearchParams({ search: search, statut: statut, date: date });
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
                    document.getElementById('receptionsTbody').innerHTML = data.html;
                }
                if (data.pagination !== undefined && document.getElementById('paginationContainer')) {
                    document.getElementById('paginationContainer').innerHTML = data.pagination;
                }
            })
            .catch(error => {
                if (error.name === 'AbortError') return;
                console.error('Fetch Error:', error);
            });
        }
    
        if (searchInput) {
            searchInput.addEventListener('input', function () {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => fetchFilteredData(), 400);
            });
        }
    
        if (statutSelect) {
            statutSelect.addEventListener('change', () => {
                lastSearchQuery = null;
                fetchFilteredData();
            });
        }
    
        if (dateInput) {
            dateInput.addEventListener('change', () => {
                lastSearchQuery = null;
                fetchFilteredData();
            });
        }
    
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
    
    function calculateAndCompareWeights() {
    const pFournisseur = parseFloat(document.getElementById('r_poids_fournisseur').value) || 0;
    const pEntree = parseFloat(document.getElementById('r_poids_entree').value) || 0;
    const pSortie = parseFloat(document.getElementById('r_poids_sortie').value) || 0;
    
    const alertBox = document.getElementById('weightComparisonAlert');
    const alertIcon = document.getElementById('weightAlertIcon');
    const alertText = document.getElementById('weightAlertText');
    const netDisplay = document.getElementById('r_poids_net_display');
    
    // 🔘 زر الحفظ / الاستلام
    const btnSubmit = document.getElementById('btnSubmit');

    if (pEntree > 0 && pSortie > 0) {

        // ❌ حالة الخطأ: وزن الخروج أكبر من أو يساوي وزن الدخول
        if (pSortie >= pEntree) {
            netDisplay.value = '---';
            alertBox.style.display = 'flex';
            alertBox.style.background = '#FEE2E2';
            alertBox.style.color = '#991B1B';
            alertBox.style.border = '1px solid #FCA5A5';
            alertIcon.className = 'fa-solid fa-triangle-exclamation';
            alertText.innerHTML = `<strong>Erreur de Saisie :</strong> Le poids de sortie (${pSortie} Kg) ne peut pas être supérieur ou égal au poids d'entrée (${pEntree} Kg).`;
            
            // 🛑 تجميد الزر لمنع الحفظ الخاطئ
            if (btnSubmit) {
                btnSubmit.disabled = true;
                btnSubmit.style.opacity = '0.5';
                btnSubmit.style.cursor = 'not-allowed';
            }
            return;
        }

        // ✅ الأوزان منطقية الصحيحة: فك تجميد الزر
        if (btnSubmit) {
            btnSubmit.disabled = false;
            btnSubmit.style.opacity = '1';
            btnSubmit.style.cursor = 'pointer';
        }

        const pNet = pEntree - pSortie;
        netDisplay.value = pNet.toFixed(2) + " Kg";

        if (pFournisseur > 0) {
            const diff = pNet - pFournisseur;
            const percentage = ((Math.abs(diff) / pFournisseur) * 100).toFixed(2);

            alertBox.style.display = 'flex';

            if (diff === 0) {
                alertBox.style.background = '#DCFCE7';
                alertBox.style.color = '#15803D';
                alertBox.style.border = '1px solid #86EFAC';
                alertIcon.className = 'fa-solid fa-circle-check';
                alertText.innerHTML = `<strong>Poids Conforme :</strong> Le poids net (${pNet.toFixed(2)} Kg) est parfaitement identique au poids fournisseur.`;
            } else if (diff < 0) {
                alertBox.style.background = '#FEE2E2';
                alertBox.style.color = '#991B1B';
                alertBox.style.border = '1px solid #FCA5A5';
                alertIcon.className = 'fa-solid fa-triangle-exclamation';
                alertText.innerHTML = `<strong>Écart Détecté (Manque) :</strong> Le poids net (${pNet.toFixed(2)} Kg) est <strong>inférieur</strong> au poids fournisseur (${pFournisseur} Kg) de <strong>${Math.abs(diff).toFixed(2)} Kg</strong> (${percentage}% d'écart).`;
            } else {
                alertBox.style.background = '#FEF3C7';
                alertBox.style.color = '#92400E';
                alertBox.style.border = '1px solid #FCD34D';
                alertIcon.className = 'fa-solid fa-circle-info';
                alertText.innerHTML = `<strong>Écart Détecté (Surplus) :</strong> Le poids net (${pNet.toFixed(2)} Kg) est <strong>supérieur</strong> au poids fournisseur (${pFournisseur} Kg) de <strong>+${diff.toFixed(2)} Kg</strong> (+${percentage}% d'écart).`;
            }
        } else {
            alertBox.style.display = 'none';
        }
    } else {
        netDisplay.value = '---';
        alertBox.style.display = 'none';

        // 🟢 إعادة الزر للوضع الطبيعي إذا كانت الحقول لم تكتمل بعد
        if (btnSubmit) {
            btnSubmit.disabled = false;
            btnSubmit.style.opacity = '1';
            btnSubmit.style.cursor = 'pointer';
        }
    }
}
    
    function toggleTypeFields() {
        const type = document.getElementById('r_type').value;
        const missionGroup = document.getElementById('missionGroup');
        const singleFournisseurGroup = document.getElementById('singleFournisseurGroup');
        const multiFournisseursGroup = document.getElementById('multiFournisseursGroup');
        const logisticInfoGroup = document.getElementById('logisticInfoGroup');
    
        if(type === 'Collecte Interne') {
            missionGroup.style.display = 'flex';
            multiFournisseursGroup.style.display = 'flex';
            singleFournisseurGroup.style.display = 'none';
            logisticInfoGroup.style.display = 'flex';
            document.getElementById('r_mission').required = true;
            document.getElementById('r_fournisseur').required = false;
        } else {
            missionGroup.style.display = 'none';
            multiFournisseursGroup.style.display = 'none';
            singleFournisseurGroup.style.display = 'flex';
            logisticInfoGroup.style.display = 'none';
            document.getElementById('r_mission').required = false;
            document.getElementById('r_fournisseur').required = true;
            document.getElementById('r_mission').value = '';
            document.getElementById('r_multi_fournisseurs').value = '';
        }
    }
    
    document.getElementById('r_mission').addEventListener('change', function() {
        const missionId = this.value;
        const multiInput = document.getElementById('r_multi_fournisseurs');
        const chauffeurInput = document.getElementById('r_chauffeur_nom');
        const camionInput = document.getElementById('r_camion_mat');
    
        if(!missionId) {
            multiInput.value = ''; chauffeurInput.value = ''; camionInput.value = ''; return;
        }
    
        const selectedMission = missionsData.find(m => m.id == missionId);
        if(selectedMission) {
            multiInput.value = selectedMission.fournisseurs_text ? selectedMission.fournisseurs_text : 'Aucun fournisseur trouvé';
            chauffeurInput.value = selectedMission.chauffeur ? (selectedMission.chauffeur.nom + ' ' + selectedMission.chauffeur.prenom) : '---';
            camionInput.value = selectedMission.equipement ? selectedMission.equipement.matricule : '---';
        }
    });
    
    function toggleNumeroRequirement() {
        const decision = document.getElementById('r_statut_validation').value;
        const numeroInput = document.getElementById('r_numero');
        const numeroGroup = document.getElementById('numeroGroup');
        const zoneGroup = document.getElementById('zoneGroup');
        const zoneSelect = document.getElementById('r_zone');
        const dateValGroup = document.getElementById('dateValidationGroup');
        const dateValInput = document.getElementById('r_date_validation');
        const dateValLabel = document.getElementById('lbl_date_validation_modal');
    
        if (decision === 'Acceptée') {
            if (dateValLabel) dateValLabel.innerText = 'Date de Validation';
            numeroGroup.style.display = 'flex';
            zoneGroup.style.display = 'flex';
            dateValGroup.style.display = 'flex';


          // 🟢 جعل حقل الرقم للقراءة فقط وتجهيزه للتوليد التلقائي
        if (numeroInput) {
            numeroInput.required = false;
            numeroInput.readOnly = true;
            if (!numeroInput.value) {
                numeroInput.placeholder = 'Généré automatiquement par le système';
            }
            numeroInput.style.backgroundColor = '#f1f5f9';
            numeroInput.style.color = '#64748b';
        }


            zoneSelect.required = true;
            zoneSelect.disabled = false;
            dateValInput.required = true;
        } else if (decision === 'Refusée') {
        if (dateValLabel) dateValLabel.innerText = 'Date de Refus';
        numeroGroup.style.display = 'none';
        zoneGroup.style.display = 'none';
        dateValGroup.style.display = 'flex';
        if (numeroInput) {
            numeroInput.required = false;
            numeroInput.value = '';
        }
        zoneSelect.required = false;
        zoneSelect.value = '';
        dateValInput.required = true;
        } else {
            numeroGroup.style.display = 'none';
            zoneGroup.style.display = 'none';
            dateValGroup.style.display = 'none';
        }
    }

    // دالة مساعدة لإخفاء خانة عرض التاريخ المخصص
    function hideDisplayDateGroup() {
        const displayDateValGroup = document.getElementById('displayDateValGroup');
        if (displayDateValGroup) {
            displayDateValGroup.style.display = 'none';
        }
    }
    
    function openStage1Modal() {
        document.getElementById('modalTitle').innerText = "Nouveau Pesage Brut (Entrée Camion)";
        document.getElementById('receptionForm').action = "{{ route('reception.store') }}";
        document.getElementById('formMethod').value = "POST";
        
        document.getElementById('weightComparisonAlert').style.display = 'none';
        document.getElementById('validationSection').style.display = 'none';
        document.getElementById('displayZoneGroup').style.display = 'none';
        hideDisplayDateGroup();
        
        document.getElementById('btnSubmit').style.display = 'block';
        document.getElementById('btnSubmit').innerText = "Enregistrer l'entrée";
        
        document.getElementById('receptionForm').reset();
        resetMissionSelectOptions();
        enableAllInputs();
        
        document.getElementById('r_poids_sortie').readOnly = true;
        document.getElementById('r_poids_sortie').required = false;
        document.getElementById('r_zone').required = false;

        const numeroInput = document.getElementById('r_numero');
    if (numeroInput) numeroInput.required = false;
    
        const d = new Date();
        const formattedDate = d.getFullYear() + '-' + ('0' + (d.getMonth()+1)).slice(-2) + '-' + ('0' + d.getDate()).slice(-2) + ' ' + ('0' + d.getHours()).slice(-2) + ':' + ('0' + d.getMinutes()).slice(-2) + ':' + ('0' + d.getSeconds()).slice(-2);
        document.getElementById('r_date_hidden').value = formattedDate;
    
        toggleTypeFields();
        document.getElementById('receptionModal').style.display = 'flex';
    }

    function openEditModal(data) {
        document.getElementById('modalTitle').innerText = "Modifier la réception";
        document.getElementById('receptionForm').action = "/receptions/" + data.id;
        document.getElementById('formMethod').value = "PUT";
        
        document.getElementById('weightComparisonAlert').style.display = 'none';
        document.getElementById('validationSection').style.display = 'none';
        document.getElementById('displayZoneGroup').style.display = 'none';
        hideDisplayDateGroup();

        document.getElementById('btnSubmit').style.display = 'block';
        document.getElementById('btnSubmit').innerText = "Mettre à jour";

        enableAllInputs();
        fillBasicData(data);

        const tareInput = document.getElementById('r_poids_sortie');
        if (data.statut === 'En attente Tare') {
            tareInput.value = '';
            tareInput.readOnly = true;
            tareInput.style.backgroundColor = '#f1f5f9';
            // tareInput.placeholder = "En attente de la pesée Tare...";
        } else {
            tareInput.readOnly = false;
            tareInput.style.backgroundColor = '#ffffff';
            tareInput.placeholder = "";
        }

        document.getElementById('receptionModal').style.display = 'flex';
    }
    
    function openStage2Modal(r) {
        document.getElementById('modalTitle').innerText = "Pesage Tare (Sortie Camion)";
        document.getElementById('receptionForm').action = "/receptions/" + r.id + "/tare";
        document.getElementById('formMethod').value = "PUT";
    
        document.getElementById('weightComparisonAlert').style.display = 'none';
        document.getElementById('validationSection').style.display = 'none';
        document.getElementById('displayZoneGroup').style.display = 'none';
        hideDisplayDateGroup();

        document.getElementById('btnSubmit').style.display = 'block';
        document.getElementById('btnSubmit').innerText = "Valider la Tare";
    
        enableAllInputs();
        fillBasicData(r);
        disableStage1Inputs();
    

    const numeroInput = document.getElementById('r_numero');
    if (numeroInput) numeroInput.required = false;

    
        document.getElementById('r_poids_sortie').readOnly = false;
        document.getElementById('r_poids_sortie').required = true;
        document.getElementById('r_poids_sortie').focus();
    
        calculateAndCompareWeights();
        document.getElementById('receptionModal').style.display = 'flex';
    }
    
    function openStage3Modal(r) {
        document.getElementById('modalTitle').innerText = "Validation de la Réception";
        document.getElementById('receptionForm').action = "/receptions/" + r.id + "/validate";
        document.getElementById('formMethod').value = "PUT";

        document.getElementById('validationSection').style.display = 'block';
        document.getElementById('displayZoneGroup').style.display = 'none';
        hideDisplayDateGroup();

        document.getElementById('btnSubmit').style.display = 'block';
        document.getElementById('btnSubmit').innerText = "Clôturer la Réception";

        enableAllInputs();
        fillBasicData(r);
        disableStage1Inputs();
        
        const numeroInput = document.getElementById('r_numero');
    if (numeroInput) {
        numeroInput.value = r.numero_official || r.numero || '';
    }

        document.getElementById('r_zone').disabled = false;
        document.getElementById('r_statut_validation').disabled = false;
        document.getElementById('r_statut_validation').value = '';
        document.getElementById('r_poids_sortie').readOnly = true;

        const d = new Date();
        const formattedNow = d.getFullYear() + '-' + ('0' + (d.getMonth()+1)).slice(-2) + '-' + ('0' + d.getDate()).slice(-2) + 'T' + ('0' + d.getHours()).slice(-2) + ':' + ('0' + d.getMinutes()).slice(-2);
        
        const dateValInput = document.getElementById('r_date_validation');
        dateValInput.value = r.date_validation ? r.date_validation.replace(' ', 'T').substring(0, 16) : formattedNow;
        dateValInput.readOnly = true;
        dateValInput.style.pointerEvents = 'none';
        dateValInput.style.backgroundColor = '#f1f5f9';

        toggleNumeroRequirement();
        calculateAndCompareWeights();

        document.getElementById('receptionModal').style.display = 'flex';
    }
    
    function openViewModal(r) {
    try {
        document.getElementById('modalTitle').innerText = "Détails Réception " + (r.numero ? ("- " + r.numero) : "");
        document.getElementById('validationSection').style.display = 'none';
        document.getElementById('btnSubmit').style.display = 'none';

        enableAllInputs();
        fillBasicData(r);
        disableStage1Inputs();
        
        const tareInput = document.getElementById('r_poids_sortie');
        if (tareInput) tareInput.readOnly = true;

        // 1. عرض المنطقة إذا كانت متوفرة
        const displayZoneGroup = document.getElementById('displayZoneGroup');
        const zoneDisplayInput = document.getElementById('r_zone_display');

        if (r.statut === 'Acceptée' && r.zone_nom_calculated && zoneDisplayInput) {
            displayZoneGroup.style.display = 'flex';
            zoneDisplayInput.value = r.zone_nom_calculated;
        } else if (displayZoneGroup) {
            displayZoneGroup.style.display = 'none';
        }

        // 2. عرض تاريخ المصادقة / الرفض وتغيير العنوان
        const displayDateValGroup = document.getElementById('displayDateValGroup');
        const dateValDisplayInput = document.getElementById('r_date_val_display');
        const lblDateValDisplay = document.getElementById('lbl_date_val_display');

        if (r.date_validation && displayDateValGroup && dateValDisplayInput) {
            displayDateValGroup.style.display = 'flex';
            
            // تغيير عنوان وحقل التاريخ حسب حالة الرفض أو القبول
            if (lblDateValDisplay) {
                lblDateValDisplay.innerText = (r.statut === 'Refusée') ? 'Date de Refus' : 'Date de Validation';
                lblDateValDisplay.style.color = (r.statut === 'Refusée') ? '#b91c1c' : '#0369a1';
            }

            let rawDate = String(r.date_validation);
            let formattedDate = rawDate.replace('T', ' ').substring(0, 16);
            dateValDisplayInput.value = formattedDate;
        } else if (displayDateValGroup) {
            displayDateValGroup.style.display = 'none';
        }

        calculateAndCompareWeights();
    } catch (error) {
        console.error("Erreur dans openViewModal:", error);
    }

    // فتح النافذة المنبثقة دائماً بغض النظر عن أي خطأ ثانوي
    document.getElementById('receptionModal').style.display = 'flex';
}
    
    function fillBasicData(r) {
        document.getElementById('r_type').value = r.type_reception || '';
        toggleTypeFields();
    
        const missionSelect = document.getElementById('r_mission');
        const missionId = r.mission_collecte_id;
    
        if (missionId) {
            const selectedMission = missionsData.find(m => m.id == missionId);
            if (selectedMission) {
                let optionExists = Array.from(missionSelect.options).some(opt => opt.value == missionId);
                if (!optionExists) {
                    const newOption = new Option(selectedMission.numero, selectedMission.id, true, true);
                    missionSelect.add(newOption);
                }
    
                missionSelect.value = missionId;
                document.getElementById('r_multi_fournisseurs').value = selectedMission.fournisseurs_text || '---';
                document.getElementById('r_chauffeur_nom').value = selectedMission.chauffeur ? (selectedMission.chauffeur.nom + ' ' + selectedMission.chauffeur.prenom) : '---';
                document.getElementById('r_camion_mat').value = selectedMission.equipement ? selectedMission.equipement.matricule : '---';
            }
        } else {
            missionSelect.value = '';
        }
    
        if(r.type_reception === 'Réception Directe') {
            document.getElementById('r_fournisseur').value = r.fournisseur_id || '';
        }
    
        document.getElementById('r_poids_fournisseur').value = r.poids_fournisseur || '';
        document.getElementById('r_poids_entree').value = r.poids_entree || '';
        document.getElementById('r_poids_sortie').value = r.poids_sortie || '';
        document.getElementById('r_observation').value = r.observation || '';
    
        if(r.details && r.details.length > 0) {
            document.getElementById('r_materiau_id').value = r.details[0].materiau_id;
            document.getElementById('r_prix_unitaire').value = r.details[0].prix_unitaire || '';
        }
    }

    function enableAllInputs() {
        document.getElementById('r_type').disabled = false;
        document.getElementById('r_mission').disabled = false;
        document.getElementById('r_fournisseur').disabled = false;
        document.getElementById('r_materiau_id').disabled = false;
        document.getElementById('r_prix_unitaire').readOnly = false;
        document.getElementById('r_poids_fournisseur').readOnly = false;
        document.getElementById('r_poids_entree').readOnly = false;
        document.getElementById('r_poids_sortie').readOnly = false;
        document.getElementById('r_observation').readOnly = false;
        document.getElementById('r_zone').disabled = false;
        
        const dateValInput = document.getElementById('r_date_validation');
        if (dateValInput) {
            dateValInput.readOnly = true;
            dateValInput.style.pointerEvents = 'none';
            dateValInput.style.backgroundColor = '#f1f5f9';
        }
    }
    
    function disableStage1Inputs() {
        document.getElementById('r_type').disabled = true;
        document.getElementById('r_mission').disabled = true;
        document.getElementById('r_fournisseur').disabled = true;
        document.getElementById('r_materiau_id').disabled = true;
        document.getElementById('r_prix_unitaire').readOnly = true;
        document.getElementById('r_poids_fournisseur').readOnly = true;
        document.getElementById('r_poids_entree').readOnly = true;
        document.getElementById('r_zone').disabled = true;
    }

    function resetMissionSelectOptions() {
        const missionSelect = document.getElementById('r_mission');
        if (!missionSelect) return;
        
        missionSelect.innerHTML = '<option value="">Sélectionnez la mission...</option>';
        availableMissionsData.forEach(ms => {
            const opt = document.createElement('option');
            opt.value = ms.id;
            opt.textContent = ms.numero;
            missionSelect.appendChild(opt);
        });
    }

    function closeModal() { 
        document.getElementById('receptionModal').style.display = 'none'; 
    }
</script>
@endsection