<aside class="sidebar">
    <div class="logo">
        <i class="fa-solid fa-arrows-spin logo-icon"></i>
        {{-- <img src="{{ asset('images/logo.png') }}" alt="logo"> --}}
        <div class="logo-text">
            <h2>MetalCycle</h2>
            <small>SYSTÈME DE GESTION</small>
        </div>
    </div>

    <ul class="menu">
        <li>
            <a href="/dashboard" class="{{ Request::is('dashboard*') ? 'active' : '' }}">
                <i class="fa-solid fa-gauge"></i> Dashboard
            </a>
        </li>

        <li class="menu-section-title">COLLECTE</li>
        
        <!-- صفحة طلبات الجمع -->
        @if(auth()->user() && auth()->user()->canAccess('collecte_demandes', 'view'))
        <li>
            <a href="{{ route('collecte.demandes.index') }}" class="{{ Request::is('collecte/demandes*') ? 'active' : '' }}">
                <i class="fa-solid fa-recycle"></i> Demandes de Collecte
            </a>
        </li>
        @endif

        <!-- صفحة مهام الجمع -->
        @if(auth()->user() && auth()->user()->canAccess('collecte_missions', 'view'))
        <li>
            <a href="{{ route('collecte.missions.index') }}" class="{{ Request::is('collecte/missions*') ? 'active' : '' }}">
                <i class="fa-solid fa-truck"></i> Missions de Collecte
            </a>
        </li>
        @endif

        <li class="menu-section-title">RÉCEPTION</li>
        
        <!-- صفحة الاستقبال -->
        @if(auth()->user() && auth()->user()->canAccess('reception_receptions', 'view'))
        <li>
            <a href="{{ route('reception.index') }}" class="{{ Request::is('receptions*') ? 'active' : '' }}">
                <i class="fa-solid fa-scale-balanced"></i> Gestion des Réceptions
            </a>
        </li>
        @endif

        <!-- 🆕 قسم وصفحة المخزون المضافة حديثاً -->
        <li class="menu-section-title">STOCK</li>
        
        @if(auth()->user() && auth()->user()->canAccess('stock_mp', 'view'))
        <li>
            <a href="{{ route('stock.index') }}" class="{{ Request::is('stock-mp*') ? 'active' : '' }}">
                <i class="fa-solid fa-cubes"></i> Stock MP
            </a>
        </li>
        @endif

        <li class="menu-section-title">RÉFÉRENTIELS</li>
        @if(auth()->user() && auth()->user()->canAccess('personnel', 'view'))
        <li>
            <a href="{{ route('referentiels.people.index') }}" class="{{ request()->routeIs('referentiels.people.*') ? 'active' : '' }}" 
               style="display: flex; align-items: center; gap: 12px; padding: 12px 20px; color: #334155; text-decoration: none; font-weight: 600; font-size: 14px; border-radius: 8px; margin: 0 10px; transition: all 0.2s;">
                <i class="fa-solid fa-users" style="width: 20px; font-size: 20px; "></i>
                <span>Gestion du Personnel</span>
            </a>
        </li>
        @endif
        @if(auth()->user() && auth()->user()->canAccess('utilisateurs', 'view'))
        <li>
            <a href="{{ route('referentiels.users.index') }}" class="{{ Request::is('referentiels/utilisateurs*') ? 'active' : '' }}">
                <i class="fa-solid fa-user-gear"></i> Utilisateurs
            </a>
        </li>
        @endif
        @if(auth()->user() && auth()->user()->canAccess('roles', 'view'))
        <li>
            <a href="{{ route('referentiels.roles.index') }}" class="{{ request()->routeIs('referentiels.roles.*') ? 'active' : '' }}" 
               style="display: flex; align-items: center; gap: 12px; padding: 12px 20px; color: #334155; text-decoration: none; font-weight: 600; font-size: 14px; border-radius: 8px; margin: 0 10px; transition: all 0.2s;">
                <i class="fa-solid fa-shield-halved" style="width: 20px; font-size: 20px;"></i>
                <span>Gestion des Rôles</span>
            </a>
        </li>
        @endif
        @if(auth()->user() && auth()->user()->canAccess('equipements', 'view'))
        <li class="{{ Request::is('referentiels/equipements*') ? 'active' : '' }}">
            <a href="{{ route('referentiels.equipements.index') }}" class="{{ Request::is('referentiels/equipements*') ? 'active' : '' }}">
                <i class="fa-solid fa-truck-fast"></i> Camions & Engins
            </a>
        </li>
        @endif
        @if(auth()->user() && auth()->user()->canAccess('fournisseurs', 'view'))
        <li>
            <a href="{{ route('referentiels.fournisseurs.index') }}" class="{{ Request::is('referentiels/fournisseurs*') ? 'active' : '' }}">
                <i class="fa-solid fa-handshake"></i> Fournisseurs
            </a>
        </li>
        @endif
        
        @if(auth()->user() && auth()->user()->canAccess('zones', 'view'))
        <li>
            <a href="{{ route('referentiels.zones.index') }}" class="{{ Request::is('referentiels/zones*') ? 'active' : '' }}">
                <i class="fa-solid fa-warehouse"></i> Zones de Stockage
            </a>
        </li>
        @endif

        @if(auth()->user() && auth()->user()->canAccess('materiaux', 'view'))
        <li>
            <a href="{{ route('referentiels.materiaux.index') }}" class="{{ Request::is('referentiels/materiaux*') ? 'active' : '' }}">
                <i class="fa-solid fa-boxes-stacked"></i> Matériaux
            </a>
        </li>
        @endif

        @if(auth()->user() && auth()->user()->canAccess('filiales', 'view'))
        <li>
            <a href="{{ route('referentiels.filiales.index') }}" class="{{ Request::is('referentiels/filiales*') ? 'active' : '' }}">
                <i class="fa-solid fa-sitemap"></i> Filiales
            </a>
        </li>
        @endif



        <!-- 🆕 SECTION: RAPPORTS & ÉTATS (التقارير والأوضاع) -->
        <li class="menu-section-title">RAPPORTS & ÉTATS</li>

        @if(auth()->user() && auth()->user()->canAccess('etats_demande', 'view'))
        <li>
            <a href="{{ route('etats.index', 'demande') }}" class="{{ Request::is('etats/demande*') ? 'active' : '' }}">
                <i class="fa-solid fa-file-invoice"></i> État des Demandes
            </a>
        </li>
        @endif

        @if(auth()->user() && auth()->user()->canAccess('etats_mission', 'view'))
        <li>
            <a href="{{ route('etats.index', 'mission') }}" class="{{ Request::is('etats/mission*') ? 'active' : '' }}">
                <i class="fa-solid fa-file-lines"></i> État des Missions
            </a>
        </li>
        @endif

        @if(auth()->user() && auth()->user()->canAccess('etats_reception', 'view'))
        <li>
            <a href="{{ route('etats.index', 'reception') }}" class="{{ Request::is('etats/reception*') ? 'active' : '' }}">
                <i class="fa-solid fa-file-waveform"></i> État des Réceptions
            </a>
        </li>
        @endif

        @if(auth()->user() && auth()->user()->canAccess('etats_stock', 'view'))
        <li>
            <a href="{{ route('etats.index', 'stock') }}" class="{{ Request::is('etats/stock*') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-pie"></i> État du Stock
            </a>
        </li>
        @endif





        {{-- <li class="menu-section-title">ANALYTIQUES</li>
        <li>
            <a href="#">
                <i class="fa-solid fa-chart-column"></i> Rapports 
            </a>
        </li> --}}
    </ul>

    <div class="logout">
        <a href="/logout"> 
            <i class="fa-solid fa-right-from-bracket"></i> Déconnexion
        </a>
    </div>
</aside>