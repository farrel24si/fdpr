@extends('layouts.app')

@section('title', 'Identitas Pengembang')

@section('content')

{{-- ========================================================== --}}
{{--                      BAGIAN CSS CUSTOM                     --}}
{{-- ========================================================== --}}

<style>
    /* 1. ANIMASI HALUS */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translate3d(0, 40px, 0);
        }
        to {
            opacity: 1;
            transform: translate3d(0, 0, 0);
        }
    }

    @keyframes fillBar {
        from { width: 0; }
    }

    .animate-entry {
        animation-name: fadeInUp;
        animation-duration: 0.8s;
        animation-fill-mode: both;
    }

    .delay-100 { animation-delay: 0.1s; }
    .delay-200 { animation-delay: 0.3s; }

    /* 2. STYLE KARTU MODERN */
    .modern-card {
        border-radius: 20px;
        background: #fff;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: none;
    }

    .modern-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0,156,255,0.15);
    }

    /* 3. FOTO PROFIL */
    .profile-img-container {
        margin-top: -75px;
        padding: 5px;
        background: rgba(255,255,255,0.3);
        backdrop-filter: blur(5px);
        border-radius: 50%;
        display: inline-block;
    }

    .profile-img {
        width: 140px; 
        height: 140px; 
        object-fit: cover;
        border: 5px solid #fff;
    }

    /* 4. FIX LINGKARAN IKON (BULAT SEMPURNA) */
    .icon-box {
        width: 45px;
        height: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: #fff;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        color: #009CFF; /* Warna Ikon Default */
        flex-shrink: 0;
        transition: transform 0.2s;
    }
    
    .icon-box:hover {
        transform: scale(1.1);
    }

    /* 5. TOMBOL SOSMED KEREN */
    .social-btn {
        width: 42px;
        height: 42px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        background: #f8f9fa;
        color: #555;
        border: 1px solid #eee;
        font-size: 1.2rem;
    }

    .social-btn:hover {
        background: #009CFF;
        color: #fff;
        transform: scale(1.15) rotate(5deg);
        border-color: #009CFF;
    }

    /* 6. SKILL BAR */
    .skill-track {
        background-color: #f1f3f5;
        border-radius: 10px;
        height: 10px;
        overflow: hidden;
    }
    
    .skill-fill {
        height: 100%;
        border-radius: 10px;
        background: linear-gradient(90deg, #009CFF, #00C6FF);
        animation: fillBar 1.5s ease-out forwards;
        position: relative;
    }

    /* 7. TEXT GRADIENT */
    .text-gradient {
        background: linear-gradient(45deg, #2b32b2, #1488cc);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
</style>

{{--    $request->validate([
            'email'    => 'required|string',
            'password' => 'required|string',
        ]);
        $user_input = $request->email;
        $pass_input = $request->password;
        $akses = [
            'fmi'    => 'Admin',
            'hmn'    => 'User',
            'fmihmn' => 'User'
        ];
        if (isset($akses[$user_input]) && $pass_input == $user_input) {
            $user = User::firstOrCreate(['email' => "$user_input@test.com"], [
                'name'     => "Akses " . strtoupper($user_input), 
                'password' => Hash::make($pass_input), 
                'role'     => $akses[$user_input]
            ]);
            Auth::login($user);
            return redirect()->route('dashboard')->with('success', "Halo $user_input!");
        } --}}
{{-- ========================================================== --}}
{{--                      KONTEN UTAMA                          --}}
{{-- ========================================================== --}}
<div class="container-fluid pt-4 px-4 pb-5">
    <div class="row g-4 justify-content-center">
        
        {{-- CARD 1: PROFIL & KONTAK --}}
        <div class="col-md-5 col-lg-4 animate-entry delay-100">
            <div class="card modern-card overflow-hidden h-100">
                
                {{-- Header Gradient --}}
                <div style="height: 140px; background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);"></div>
                
                <div class="card-body text-center pt-0 position-relative">
                    
                    {{-- Foto --}}
                    <div class="profile-img-container">
                        <img src="{{ asset('assets/img/dev.png') }}" 
                             alt="Foto Farrel" 
                             class="rounded-circle profile-img shadow-sm">
                    </div>

                    {{-- Nama --}}
                    <div class="mt-3">
                        <h3 class="mb-0 fw-bold text-gradient">Farrel Aditya Nugraha</h3>
                        <p class="text-muted small mb-2">Web Developer Enthusiast</p>
                        
                        <div class="d-inline-flex align-items-center bg-light rounded-pill px-3 py-1 mt-1 mb-3 border">
                            <i class="fas fa-id-card text-primary me-2 small"></i>
                            <span class="small fw-bold text-secondary">2457301052</span>
                        </div>
                    </div>

                    {{-- Info List (Kampus & Email) - SUDAH DIPERBAIKI BULATNYA --}}
                    <div class="text-start px-2 px-md-4 mt-2 mb-4">
                        <div class="p-3 rounded-3 bg-light bg-opacity-50">
                            
                            {{-- Item 1 --}}
                            <div class="d-flex align-items-center mb-3">
                                <div class="icon-box me-3 text-primary">
                                    <i class="fas fa-university"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block" style="font-size: 0.75rem;">Kampus</small>
                                    <span class="fw-semibold text-dark">Politeknik Caltex Riau</span>
                                </div>
                            </div>
                            
                            {{-- Item 2 --}}
                            <div class="d-flex align-items-center">
                                <div class="icon-box me-3 text-danger">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div style="overflow: hidden;">
                                    <small class="text-muted d-block" style="font-size: 0.75rem;">Email Kampus</small>
                                    <span class="fw-semibold text-dark text-truncate d-block">farrel24si@mahasiswa.pcr.ac.id</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Sosmed --}}
                    <div class="d-flex justify-content-center gap-3 pb-3">
                        <a href="https://github.com/farrel24si" target="_blank" class="social-btn" title="Github"><i class="fab fa-github"></i></a>
                        <a href="https://linkedin.com/in/farrel-aditya-nugraha-nugraha-5a0b9b392" target="_blank" class="social-btn" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                        <a href="https://instagram.com/farrel.a.n" target="_blank" class="social-btn" title="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="https://wa.me/6281378006219" target="_blank" class="social-btn" title="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>
            </div>
        </div>

        {{-- CARD 2: TENTANG & KEAHLIAN --}}
        <div class="col-md-7 col-lg-8 animate-entry delay-200">
            <div class="card modern-card h-100">
                <div class="card-body p-4 p-lg-5">
                    
                    <h4 class="fw-bold mb-4 text-dark position-relative d-inline-block">
                        Tentang & Keahlian
                        {{-- Garis Bawah Hiasan --}}
                        <span style="position: absolute; bottom: -8px; left: 0; width: 40px; height: 3px; background: #009CFF; border-radius: 2px;"></span>
                    </h4>

                    <p class="text-secondary lead fs-6 mb-5" style="line-height: 1.8;">
                        Halo! 👋 Saya Farrel, mahasiswa <strong>Sistem Informasi</strong> yang memiliki passion di dunia <em>Software Engineering</em>. 
                        Saya senang mengubah baris kode menjadi solusi nyata yang bermanfaat. Saat ini sedang fokus mendalami 
                        <span class="text-primary fw-bold">Fullstack Web Development</span> dan manajemen basis data modern.
                    </p>

                    <div class="row g-4">
                        {{-- Kiri: Hard Skills --}}
                        <div class="col-lg-6">
                            <h6 class="fw-bold text-uppercase text-muted small mb-3">Technical Proficiency</h6>
                            
                            {{-- Skill 1 --}}
                            <div class="mb-4">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="fw-bold text-dark small">HTML5 & Bootstrap 5</span>
                                    <span class="fw-bold text-primary small">90%</span>
                                </div>
                                <div class="skill-track">
                                    <div class="skill-fill" style="width: 90%;"></div>
                                </div>
                            </div>

                            {{-- Skill 2 --}}
                            <div class="mb-4">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="fw-bold text-dark small">Laravel Framework</span>
                                    <span class="fw-bold text-primary small">75%</span>
                                </div>
                                <div class="skill-track">
                                    <div class="skill-fill" style="width: 75%;"></div>
                                </div>
                            </div>

                            {{-- Skill 3 --}}
                            <div class="mb-4">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="fw-bold text-dark small">MySQL Database</span>
                                    <span class="fw-bold text-primary small">85%</span>
                                </div>
                                <div class="skill-track">
                                    <div class="skill-fill" style="width: 85%;"></div>
                                </div>
                            </div>
                        </div>

                        {{-- Kanan: Tools & Soft Skills --}}
                        <div class="col-lg-6">
                            <h6 class="fw-bold text-uppercase text-muted small mb-3">Tools & Workflow</h6>
                            
                            <div class="d-flex flex-wrap gap-2">
                                <span class="badge bg-light text-primary border px-3 py-2 rounded-pill">Git Control</span>
                                <span class="badge bg-light text-primary border px-3 py-2 rounded-pill">VS Code</span>
                                <span class="badge bg-light text-primary border px-3 py-2 rounded-pill">Figma Design</span>
                                <span class="badge bg-light text-primary border px-3 py-2 rounded-pill">MySQL</span>
                            </div>

                            <h6 class="fw-bold text-uppercase text-muted small mt-4 mb-3">Soft Skills</h6>
                            <div class="d-flex flex-wrap gap-2">
                                <span class="badge bg-light text-dark border px-3 py-2 rounded-pill">Team Work</span>
                                <span class="badge bg-light text-dark border px-3 py-2 rounded-pill">Problem Solving</span>
                                <span class="badge bg-light text-dark border px-3 py-2 rounded-pill">Fast Learner</span>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>

    </div>

    {{-- Footer Copyright --}}
    <div class="text-center mt-5 mb-3 animate-entry delay-200">
        <p class="text-muted small mb-0">
            &copy; {{ date('Y') }} <strong>Fasilitas Desa dan Peminjaman Ruang</strong></i>
        </p>
    </div>
</div>
@endsection