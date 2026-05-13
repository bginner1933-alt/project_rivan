<div class="sidebar">

    {{-- LOGO --}}
    <div class="sidebar-logo">
        <h3>
            <i class="fas fa-store"></i>
            Admin Panel
        </h3>
    </div>

    {{-- PROFILE --}}
    <div class="profile-dropdown" id="profileDropdown">

        <div class="profile-trigger" onclick="toggleMenu(event)">

            <img
                src="{{ auth()->user()->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=2563eb&color=fff' }}"
                class="rounded-circle profile-img"
            >

            <div class="admin-info">
                <span class="admin-name">
                    {{ auth()->user()->name }}
                </span>

                <span class="admin-status">
                    <span class="status-dot"></span>
                    Online
                </span>
            </div>

            <i class="fas fa-chevron-down dropdown-arrow"></i>

        </div>

        {{-- DROPDOWN --}}
        <div class="dropdown-content">

            <a href="{{ route('profile.edit') }}" class="dropdown-item">
                <i class="fas fa-user-circle"></i>
                Pengaturan Profil
            </a>

            <hr style="margin:0; opacity:.08;">

            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <button type="submit" class="dropdown-item text-danger">
                    <i class="fas fa-sign-out-alt"></i>
                    Keluar
                </button>
            </form>

        </div>
    </div>

    {{-- MENU --}}
    <div class="sidebar-menu">

        <span class="menu-title">
            MENU UTAMA
        </span>

        <nav>

            <a href="{{ route('admin.dashboard') }}"
               class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-gauge"></i>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('admin.categories.index') }}"
               class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                <i class="fas fa-folder"></i>
                <span>Kategori</span>
            </a>

            <a href="{{ route('admin.products.index') }}"
               class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                <i class="fas fa-box"></i>
                <span>Produk</span>
            </a>

            <a href="{{ route('admin.orders.index') }}"
               class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                <i class="fas fa-receipt"></i>
                <span>Pesanan</span>
            </a>

            <a href="{{ route('admin.users.index') }}"
               class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="fas fa-users"></i>
                <span>Pengguna</span>
            </a>

            <a href="{{ route('admin.laporan.index') }}"
               class="nav-link {{ request()->routeIs('admin.laporan.*') ? 'active' : '' }}">
                <i class="fas fa-chart-line"></i>
                <span>Laporan</span>
            </a>

        </nav>

        <hr class="sidebar-divider">

        <span class="menu-title">
            FEEDBACK
        </span>

        <nav>

            <a href="{{ route('admin.laporanpengaduan.index') }}"
               class="nav-link {{ request()->routeIs('admin.laporanpengaduan.*') ? 'active' : '' }}">
                <i class="fas fa-flag"></i>
                <span>Pengaduan</span>
            </a>

            <a href="{{ route('admin.ratings.index') }}"
               class="nav-link {{ request()->routeIs('admin.ratings.*') ? 'active' : '' }}">
                <i class="fas fa-star"></i>
                <span>Rating Toko</span>
            </a>

        </nav>

    </div>

</div>

<style>
.sidebar {
    width: 270px;
    height: 100vh; /* ganti min-height */
    
    background: #1e3a8a;
    color: white;

    display: flex;
    flex-direction: column;

    position: fixed;
    top: 0;
    left: 0;

    z-index: 100;

    overflow-y: auto;
    overflow-x: hidden;

    box-shadow: 10px 0 30px rgba(0,0,0,0.08);
}

.sidebar-logo {
    padding: 24px 24px 10px;
}

.sidebar-logo h3 {
    margin: 0;
    font-size: 22px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 10px;
}

.profile-dropdown {
    position: relative;
    padding: 20px;
}

.profile-trigger {
    display: flex;
    align-items: center;
    gap: 12px;
    background: rgba(255,255,255,0.08);
    padding: 12px;
    border-radius: 16px;
    cursor: pointer;
    transition: .2s;
}

.profile-trigger:hover {
    background: rgba(255,255,255,0.12);
}

.profile-img {
    width: 45px;
    height: 45px;
    object-fit: cover;
    border: 2px solid rgba(255,255,255,0.2);
}

.admin-info {
    flex: 1;
}

.admin-name {
    display: block;
    font-size: 14px;
    font-weight: 700;
}

.admin-status {
    font-size: 12px;
    color: #bfdbfe;
    display: flex;
    align-items: center;
    gap: 5px;
}

.status-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #22c55e;
}

.dropdown-arrow {
    font-size: 12px;
}

.dropdown-content {
    display: none;
    position: absolute;
    top: 90px;
    left: 20px;
    right: 20px;
    background: white;
    border-radius: 16px;
    overflow: hidden;
    z-index: 999;
    box-shadow: 0 15px 40px rgba(0,0,0,0.2);
}

.profile-dropdown.show .dropdown-content {
    display: block;
}

.dropdown-item {
    width: 100%;
    border: none;
    background: none;
    padding: 14px 18px;
    display: flex;
    align-items: center;
    gap: 12px;
    text-decoration: none;
    color: #334155;
    font-size: 14px;
    transition: .2s;
}

.dropdown-item:hover {
    background: #f1f5f9;
}

.sidebar-menu {
    padding: 10px 14px 30px;
}

.menu-title {
    display: block;
    font-size: 11px;
    font-weight: 700;
    color: rgba(255,255,255,0.5);
    margin: 15px 12px 10px;
    letter-spacing: 1px;
}

.nav-link {
    color: rgba(255,255,255,0.75);
    padding: 13px 16px;
    margin-bottom: 6px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    gap: 14px;
    text-decoration: none;
    transition: .2s;
    font-weight: 500;
}

.nav-link:hover {
    background: rgba(255,255,255,0.08);
    color: white;
    transform: translateX(3px);
}

.nav-link.active {
    background: #2563eb;
    color: white;
    font-weight: 600;
    box-shadow: 0 8px 20px rgba(37,99,235,0.3);
}

.nav-link i {
    width: 18px;
    text-align: center;
}

.sidebar-divider {
    border-color: rgba(255,255,255,0.08);
    margin: 18px 10px;
}

.sidebar::-webkit-scrollbar {
    width: 6px;
}

.sidebar::-webkit-scrollbar-thumb {
    background: rgba(255,255,255,0.25);
    border-radius: 10px;
}

.sidebar::-webkit-scrollbar-track {
    background: transparent;
}
</style>