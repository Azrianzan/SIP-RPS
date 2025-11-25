<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIP-RPS</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
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
                @csrf <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required>
                </div>
    
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" class="form-control" required>
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
</body>
</html>