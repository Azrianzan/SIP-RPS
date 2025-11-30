<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIP-RPS</title>
    <link rel="icon" href="{{ asset('images/logo-favicon.png') }}" type="image/png">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            
            background-image: url("{{ asset('images/DinasPendidikanProvKalsel.png') }}");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }

        body::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(
                to bottom,
                rgba(0, 51, 102, 0.8),
                rgba(0, 100, 200, 0.5)
            );
            z-index: 1;
        }

        .login-container {
            width: 100%;
            display: flex;
            justify-content: center;
            padding: 20px;
            position: relative; 
            z-index: 2;
        }

        .login-box {
            background: rgba(255, 255, 255, 0.95);
            padding: 2.5rem;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        /* Style Input & Tombol lainnya tetap sama */
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
        
        .login-header img {
            max-width: 80px;
            margin-bottom: 1rem;
        }
        .login-header h1 {
            font-size: 1.2rem;
            color: #333;
            margin-bottom: 0.5rem;
            line-height: 1.4;
        }
        .login-header p {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 1.5rem;
        }
        
        .login-btn {
            width: 100%; 
            padding: 12px; 
            background: #0056b3; 
            color: white; 
            border: none; 
            border-radius: 5px; 
            font-size: 1rem; 
            cursor: pointer; 
            transition: background 0.3s;
        }
        .login-btn:hover {
            background: #004494;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <!-- Gunakan onerror untuk debug jika gambar tidak muncul -->
                <img src="{{ asset('images/logo.png') }}" alt="Logo SIP-RPS" onerror="console.log('Logo gagal load')">
                <h1>Sistem Informasi Pengelolaan Proyek Rehabilitasi Sekolah</h1>
                <p>Dinas Pendidikan dan Kebudayaan Provinsi Kalimantan Selatan</p>
            </div>
            
            @if ($errors->any())
                <div style="color: #721c24; background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-size: 0.9rem;">
                    {{ $errors->first() }}
                </div>
            @endif

            <form class="login-form" method="POST" action="{{ route('login.post') }}">
                @csrf 
                
                <div class="form-group" style="margin-bottom: 1rem; text-align: left;">
                    <label for="email" style="display: block; margin-bottom: 5px; font-weight: 500; color: #444;">Email</label>
                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required 
                           style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box;">
                </div>
    
                <div class="form-group" style="margin-bottom: 1.5rem; text-align: left;">
                    <label for="password" style="display: block; margin-bottom: 5px; font-weight: 500; color: #444;">Password</label>
                    <div class="password-container">
                        <input type="password" name="password" id="password" class="form-control" required
                               style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box;">
                        
                        <button type="button" class="password-toggle" id="togglePassword" title="Lihat Password">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </button>
                    </div>
                </div>
    
                <button type="submit" class="login-btn">Login</button>
            </form>
            
            <div style="margin-top: 1.5rem; text-align: center; font-size: 0.85rem; color: #666; border-top: 1px solid #eee; padding-top: 1rem;">
                <p style="margin-bottom: 5px;"><strong>Akun Testing</strong></p>
                <div style="display: flex; flex-direction: column; gap: 2px;">
                    <span>Admin: dosenadmin@gmail.com / admin123</span>
                    <span>Pimpinan: dosenpimpinan@gmail.com / pimpinan123</span>
                    <span>PJL: dosenpjl@gmail.com / pjl123</span>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');

        togglePassword.addEventListener('click', function (e) {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            
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