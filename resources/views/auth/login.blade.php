<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIP-RPS</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <style>
        .password-container {
            position: relative;
        }
        .password-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #666;
            padding: 0;
            display: flex;
            align-items: center;
        }
        .password-toggle:hover {
            color: #333;
        }

        #password {
            padding-right: 40px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <img src="{{ asset('images/logo.png') }}" alt="Logo SIP-RPS">
                <h1>Sistem Informasi Pengelolaan Proyek Rehabilitasi Sekolah</h1>
                <p>Dinas Pendidikan dan Kebudayaan Provinsi Kalimantan Selatan</p>
            </div>
            
            @if ($errors->any())
                <div style="color: red; text-align: center; margin-bottom: 10px;">
                    {{ $errors->first() }}
                </div>
            @endif

            <form class="login-form" method="POST" action="{{ route('login.post') }}">
                @csrf 
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required>
                </div>
    
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="password-container">
                        <input type="password" name="password" id="password" class="form-control" required>
                        
                        <!-- Tombol Toggle Visibility -->
                        <button type="button" class="password-toggle" id="togglePassword" title="Lihat Password">
                            <!-- Ikon Mata Terbuka (Default) -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </button>
                    </div>
                </div>
    
                <button type="submit" class="login-btn">Login</button>
            </form>
            
            <div style="margin-top: 1.5rem; text-align: center; font-size: 0.9rem; color: #666;">
                <p><strong>Akun Testing</strong></p>
                <p>Admin: dosenadmin@gmail.com / admin123</p>
                <p>Pimpinan: dosenpimpinan@gmail.com / pimpinan123</p>
                <p>PJL: dosenpjl@gmail.com / pjl123</p>
            </div>
        </div>
    </div>
    
    <script src="{{ asset('js/script.js') }}"></script>
    
    <!-- Script Toggle Password -->
    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');

        togglePassword.addEventListener('click', function (e) {
            // toggle tipe atribut input
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            
            // toggle ikon
            if (type === 'text') {
                this.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                        <line x1="1" y1="1" x2="23" y2="23"></line>
                    </svg>`;
                this.setAttribute('title', 'Sembunyikan Password');
            } else {
                this.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>`;
                this.setAttribute('title', 'Lihat Password');
            }
        });
    </script>
</body>
</html>