<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SIP-RPS')</title>
    
    <!-- Favicon (Logo di Tab Browser) -->
    <link rel="icon" href="{{ asset('images/logo-favicon.png') }}" type="image/png">

    <!-- Menggunakan asset() agar path CSS benar -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    
    <!-- CDN Chart.js (Untuk Dashboard) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        /* --- PERBAIKAN LAYOUT HEADER AGAR SEJAJAR SIDEBAR --- */
        
        /* 1. Reset Container Header */
        header .container {
            max-width: 100%;
            padding: 0;
            margin: 0;
        }

        /* 2. Navbar Flexbox */
        .navbar {
            display: flex;
            align-items: center;
            height: 60px; /* Tinggi header tetap */
            /* Background color dihapus agar mengikuti style.css */
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding-right: 20px; 
        }

        /* 3. Logo dengan Lebar Tetap (Sejajar Sidebar) */
        .logo {
            width: 250px; /* Lebar Sidebar */
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            /* Background & Border dihapus agar mengikuti style.css */
        }

        .logo img {
            height: 30px;
            margin-right: 10px;
        }

        .logo h1 {
            font-size: 1.2rem;
            margin: 0;
            /* Color dihapus agar mengikuti style.css */
            font-weight: bold;
        }

        /* 4. Navigasi mengisi sisa ruang */
        .nav-links {
            flex-grow: 1;
            display: flex;
            gap: 20px;
            padding-left: 20px;
        }

        /* 5. Sidebar Fixed */
        .sidebar {
            width: 250px;
            position: fixed;
            top: 60px; 
            bottom: 0;
            left: 0;
            overflow-y: auto;
            /* Background & Border dihapus agar mengikuti style.css */
        }

        /* 6. Konten Utama digeser */
        .main-container {
            display: flex;
            margin-top: 60px;
        }
        
        .main-content {
            margin-left: 250px; 
            width: calc(100% - 250px);
            padding: 2rem;
        }

        /* --- CSS Tambahan untuk Modal & Utilitas --- */
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; }
        .modal-content { background: white; border-radius: 8px; width: 90%; max-width: 600px; max-height: 90vh; overflow-y: auto; display: flex; flex-direction: column;}
        .modal-header { padding: 1.5rem; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; }
        .modal-body { padding: 1.5rem; }
        .modal-footer { padding: 1rem 1.5rem; border-top: 1px solid #eee; display: flex; justify-content: flex-end; gap: 1rem; }
        
        /* Helper status colors */
        .status.selesai { background-color: #d4edda; color: #155724; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.85rem; }
        .status.berjalan { background-color: #cce5ff; color: #004085; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.85rem; }
        .status.terlambat { background-color: #f8d7da; color: #721c24; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.85rem; }
        .status.warning { background-color: #fff3cd; color: #856404; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.85rem; }
    </style>
</head>
<body>
    {{-- Ambil Role User untuk mempersingkat pengecekan --}}
    @php
        $role = Auth::user()->role->nama_role ?? '';
    @endphp

    <header>
        <!-- Navbar -->
        <div class="navbar">
            <!-- Bagian Logo (Lebar Fixed 250px) -->
            <div class="logo">
                <img src="{{ asset('images/logo.png') }}" alt="Logo">
                <h1>SIP-RPS</h1>
            </div>
            
            <!-- Menu Navigasi -->
            <nav class="nav-links">
                {{-- 1. Dashboard: Hanya Admin & Pimpinan --}}
                @if(in_array($role, ['Admin', 'Pimpinan']))
                    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>
                @endif

                {{-- 2. Kelola Proyek: SEMUA (Admin, Pimpinan, PJL) --}}
                <a href="{{ route('proyek.index') }}" class="{{ request()->routeIs('proyek.*') ? 'active' : '' }}">Kelola Proyek</a>
                
                {{-- 3. Kelola Pengguna: HANYA Admin --}}
                @if($role === 'Admin')
                    <a href="{{ route('pengguna.index') }}" class="{{ request()->routeIs('pengguna.*') ? 'active' : '' }}">Kelola Pengguna</a>
                @endif

                {{-- 4. Kelola Sekolah: HANYA Admin --}}
                @if($role === 'Admin')
                    <a href="{{ route('sekolah.index') }}" class="{{ request()->routeIs('sekolah.*') ? 'active' : '' }}">Kelola Sekolah</a>
                @endif

                {{-- 5. Ekspor Laporan: Hanya Admin & Pimpinan --}}
                @if(in_array($role, ['Admin', 'Pimpinan']))
                    <a href="{{ route('ekspor.index') }}" class="{{ request()->routeIs('ekspor.*') ? 'active' : '' }}">Ekspor Laporan</a>
                @endif
            </nav>
            
            <!-- User Menu (Kanan) -->
            <div class="user-menu">
                <div class="user-info">
                    <div class="avatar">{{ substr(Auth::user()->nama, 0, 1) }}</div>
                    <span>{{ Auth::user()->nama }} ({{ $role }})</span>
                </div>
                <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-outline" style="border:none; background:transparent; cursor:pointer; color:inherit; font-weight:bold;">Logout</button>
                </form>
            </div>
        </div>
    </header>
    
    <div class="main-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <ul class="sidebar-menu">
                {{-- Menu Sidebar Sama Seperti Sebelumnya --}}
                @if(in_array($role, ['Admin', 'Pimpinan']))
                    <li>
                        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <i>📊</i> Dashboard
                        </a>
                    </li>
                @endif

                <li>
                    <a href="{{ route('proyek.index') }}" class="{{ request()->routeIs('proyek.*') ? 'active' : '' }}">
                        <i>🏗️</i> Kelola Proyek
                    </a>
                </li>

                @if($role === 'Admin')
                    <li>
                        <a href="{{ route('pengguna.index') }}" class="{{ request()->routeIs('pengguna.*') ? 'active' : '' }}">
                            <i>👥</i> Kelola Pengguna
                        </a>
                    </li>
                @endif
                
                @if($role === 'Admin')
                    <li>
                        <a href="{{ route('sekolah.index') }}" class="{{ request()->routeIs('sekolah.*') ? 'active' : '' }}">
                            <i>🏫</i> Kelola Sekolah
                        </a>
                    </li>
                @endif

                @if(in_array($role, ['Admin', 'Pimpinan']))
                    <li>
                        <a href="{{ route('ekspor.index') }}" class="{{ request()->routeIs('ekspor.*') ? 'active' : '' }}">
                            <i>📄</i> Ekspor Laporan
                        </a>
                    </li>
                @endif
            </ul>
        </aside>
        
        <!-- Konten Utama -->
        <main class="main-content">
            @if(session('success'))
                <div style="background: #d4edda; color: #155724; padding: 15px; margin-bottom: 20px; border-radius: 5px; border: 1px solid #c3e6cb;">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div style="background: #f8d7da; color: #721c24; padding: 15px; margin-bottom: 20px; border-radius: 5px; border: 1px solid #f5c6cb;">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>
    
    <script src="{{ asset('js/script.js') }}"></script>
</body>
</html>