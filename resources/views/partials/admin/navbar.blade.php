<header class="admin-navbar">

    <div class="navbar-left">
        <h4 class="navbar-title">
            @yield('page-title', 'Dashboard')
        </h4>

        <span class="navbar-subtitle">
            Selamat datang kembali, {{ auth()->user()->name }}
        </span>
    </div>

    <div class="navbar-right">

        <a href="{{ route('home') }}" class="btn-lihat-toko">
            <i class="fas fa-store me-2"></i>
            Lihat Toko
        </a>

        <div class="navbar-profile">
            <img
                src="{{ auth()->user()->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=2563eb&color=fff' }}"
                alt="Avatar"
            >

            <div class="profile-text">
                <span class="profile-name">
                    {{ auth()->user()->name }}
                </span>

                <span class="profile-role">
                    Administrator
                </span>
            </div>
        </div>

    </div>

</header>

<style>
/* =========================================
   NAVBAR
========================================= */
.admin-navbar{
    background: rgba(255,255,255,0.9);
    backdrop-filter: blur(14px);
    border-bottom: 1px solid rgba(0,0,0,0.05);

    padding: 18px 30px;

    display: flex;
    justify-content: space-between;
    align-items: center;

    position: sticky;
    top: 0;
    z-index: 50;
}

/* LEFT */
.navbar-left{
    display: flex;
    flex-direction: column;
}

.navbar-title{
    margin: 0;
    font-size: 24px;
    font-weight: 800;
    color: #0f172a;
    letter-spacing: -.5px;
}

.navbar-subtitle{
    font-size: 13px;
    color: #64748b;
    margin-top: 2px;
}

/* RIGHT */
.navbar-right{
    display: flex;
    align-items: center;
    gap: 18px;
}

/* BUTTON */
.btn-lihat-toko{
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    color: white;

    padding: 10px 18px;
    border-radius: 14px;

    text-decoration: none;
    font-size: 13px;
    font-weight: 700;

    display: inline-flex;
    align-items: center;

    transition: .25s ease;
    box-shadow: 0 10px 25px rgba(37,99,235,.25);
}

.btn-lihat-toko:hover{
    transform: translateY(-2px);
    color: white;

    box-shadow: 0 14px 30px rgba(37,99,235,.35);
}

/* PROFILE */
.navbar-profile{
    display: flex;
    align-items: center;
    gap: 12px;

    background: white;
    border: 1px solid #e2e8f0;

    padding: 8px 12px;
    border-radius: 16px;

    box-shadow: 0 8px 20px rgba(0,0,0,.04);
}

.navbar-profile img{
    width: 42px;
    height: 42px;

    border-radius: 50%;
    object-fit: cover;
}

.profile-text{
    display: flex;
    flex-direction: column;
    line-height: 1.2;
}

.profile-name{
    font-size: 13px;
    font-weight: 700;
    color: #0f172a;
}

.profile-role{
    font-size: 11px;
    color: #64748b;
}
</style>