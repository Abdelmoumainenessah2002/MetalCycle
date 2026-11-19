@php
    $user = auth()->user();
    
    // 1. جلب اسم المستخدم ودوره من قاعدة البيانات
    $userName = $user->nom ?? $user->name ?? session('user_name', 'Utilisateur');
    $userRole = $user->role_name;
    // 2. حساب الأحرف الأولى تلقائياً
    $words = explode(' ', trim($userName));
    $initials = '';
    if (count($words) >= 2) {
        $initials = mb_substr($words[0], 0, 1) . mb_substr($words[1], 0, 1);
    } else {
        $initials = mb_substr($userName, 0, 2);
    }
    $initials = strtoupper($initials);

    // 3. فحص التنبيهات بشكل آمن لعدم حدوث خطأ عند القيمة null
    $unreadNotificationsCount = 0;
    if ($user && isset($user->unreadNotifications)) {
        $unreadNotificationsCount = is_countable($user->unreadNotifications) ? count($user->unreadNotifications) : 0;
    }
@endphp

<nav class="navbar">
    <div class="navbar-left">
        <button class="menu-btn" id="sidebarToggle">
            <i class="fa-solid fa-bars"></i>
        </button>
    </div>

    <div class="navbar-right">
        {{-- <!-- 🔔 التنبيهات -->
        <div class="notification" style="position: relative; cursor: pointer;">
            <i class="fa-regular fa-bell"></i>
            @if($unreadNotificationsCount > 0)
                <span class="badge" style="position: absolute; top: -5px; right: -5px; background: #C1121F; color: white; border-radius: 50%; padding: 2px 6px; font-size: 11px; font-weight: bold;">
                    {{ $unreadNotificationsCount > 99 ? '99+' : $unreadNotificationsCount }}
                </span>
            @endif
        </div> --}}
        
        <!-- 👤 بروفايل المستخدم -->
        <div class="profile" style="display: flex; align-items: center; gap: 10px; cursor: pointer; position: relative;">
            
            <div class="avatar-circle" style="width: 38px; height: 38px; border-radius: 50%; background: #0284c7; color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 14px;">
                {{ $initials }}
            </div>
            
            <div class="profile-info">
                <h6 style="margin: 0; font-size: 14px; font-weight: 600; color: #0f172a;">{{ $userName }}</h6>
                <small style="color: #64748b; font-size: 12px; display: flex; align-items: center; gap: 4px;">
                    {{ $userRole }} <i class="fa-solid fa-chevron-down" style="font-size: 10px;"></i>
                </small>
            </div>

            <!-- قائمة منسدلة لتسجيل الخروج -->
            <div class="profile-dropdown" style="display: none; position: absolute; top: 110%; right: 0; background: white; border: 1px solid #e2e8f0; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); width: 180px; z-index: 1000; padding: 6px 0;">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" style="width: 100%; background: none; border: none; text-align: left; display: flex; align-items: center; gap: 8px; padding: 10px 16px; color: #e11d48; cursor: pointer; font-size: 13px; font-weight: 600;">
                        <i class="fa-solid fa-right-from-bracket"></i> Déconnexion
                    </button>
                </form>
            </div>

        </div>
    </div>
</nav>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const profile = document.querySelector('.profile');
    const dropdown = document.querySelector('.profile-dropdown');

    if(profile && dropdown) {
        profile.addEventListener('click', function (e) {
            e.stopPropagation();
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
        });

        document.addEventListener('click', function () {
            dropdown.style.display = 'none';
        });
    }
});
</script>