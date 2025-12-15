<nav class="navbar navbar-expand bg-light navbar-light sticky-top px-4 py-0">
    
    {{-- LOGO MOBILE (Hanya muncul di layar kecil) --}}
    <a href="{{ route('dashboard') }}" class="navbar-brand d-flex d-lg-none me-4">
        <img src="{{ asset('assets/img/logo.png') }}" alt="Logo FDPR" style="height: 40px; width: auto;">
    </a>

    {{-- SIDEBAR TOGGLER --}}
    <a href="#" class="sidebar-toggler flex-shrink-0">
        <i class="fa fa-bars"></i>
    </a>

    {{-- NAVBAR KANAN (Hanya Profil User) --}}
    <div class="navbar-nav align-items-center ms-auto">
        
        <div class="nav-item dropdown">
            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                
                {{-- FOTO PROFIL --}}
                <img class="rounded-circle me-lg-2"
                     src="{{ Auth::user()->getProfilePictureUrl() }}"
                     alt="User"
                     style="width: 40px; height: 40px; object-fit: cover;">
                
                {{-- NAMA USER --}}
                <span class="d-none d-lg-inline-flex">{{ Auth::user()->name }}</span>
            </a>
            
            <div class="dropdown-menu dropdown-menu-end bg-light border-0 rounded-0 rounded-bottom m-0">
                <a href="{{ route('pages.profile.show') }}" class="dropdown-item">
                    <i class="fas fa-user me-2"></i>My Profile
                </a>
                
               
                
                <div class="dropdown-divider"></div>
                
                <form action="{{ route('pages.auth.logout') }}" method="POST" class="d-inline w-100">
                    @csrf
                    <button type="submit" class="dropdown-item text-danger w-100 border-0 bg-transparent"
                            onclick="return confirm('Yakin ingin logout?')">
                        <i class="fas fa-sign-out-alt me-2"></i>Log Out
                    </button>
                </form>
            </div>
        </div>

    </div>
</nav>