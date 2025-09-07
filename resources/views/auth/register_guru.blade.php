<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Guru - KelasSatu</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.12.2/lottie.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #254768 0%, #1a3651 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #333;
        }

        .login-wrapper {
            display: flex;
            width: 100%;
            max-width: 1000px;
            min-height: 600px;
            margin: 20px;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-container {
            background: #f7f7f7;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .logo-side {
            flex: 1;
            background: linear-gradient(135deg, #3a6ea5 0%, #5a8dee 100%);
            display: flex;
            align-items: center;
            justify-content: flex-start;
            flex-direction: column;
            text-align: center;
            color: white;
            padding: 40px 20px 20px;
        }

        .logo-side h2 {
            font-size: 28px;
            font-weight: bold;
        }

        .logo-side p {
            margin-top: 10px;
            font-size: 16px;
            opacity: 0.9;
        }

        .header {
            text-align: center;
            padding: 15px 20px 10px 20px;
            /* atas lebih kecil */
            margin-bottom: 0;
        }

        .header .logo-img {
            width: 140px;
            height: auto;
            margin-bottom: 10px;
        }

        .form-container {
            padding: 10px 20px 20px 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }



        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: #254768;
            font-size: 14px;
        }

        .form-group label::after {
            content: " *";
            color: red;
            font-weight: bold;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 14px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            font-size: 15px;
            background: white;
            transition: all 0.3s ease;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #eb631d;
            box-shadow: 0 0 0 3px rgba(235, 99, 29, 0.1);
            transform: translateY(-2px);
        }

        .login-btn {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #eb631d, #ff7a35);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
            box-shadow: 0 6px 20px rgba(235, 99, 29, 0.3);
        }

        .login-btn:hover {
            transform: translateY(-2px);
            background: linear-gradient(135deg, #d55419, #eb631d);
        }

        .divider {
            text-align: center;
            margin: 20px 0;
            position: relative;
            color: #666;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #ddd;
            z-index: 1;
        }

        .divider span {
            background: #f7f7f7;
            padding: 0 15px;
            position: relative;
            z-index: 2;
            font-size: 14px;
        }

        @media (max-width: 768px) {
            .login-wrapper {
                flex-direction: column;
                max-width: 500px;
            }

            .logo-side {
                min-height: 200px;
            }
        }
    </style>
</head>

<body>
    <div class="login-wrapper">
        <!-- Form Register -->
        <div class="login-container">
            <div class="header">
                <img src="{{ asset('image/logo.png') }}" alt="Logo KelasSatu" class="logo-img">
                <p>Buat akun baru untuk bergabung</p>
            </div>
            <div class="form-container">
                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    <div class="form-group">
                        <label for="name">Nama Lengkap</label>
                        <input type="text" id="name" name="name" required
                            placeholder="Masukkan nama lengkap">
                    </div>
                    <div class="form-group">
                        <label for="no_hp">No HP</label>
                        <input type="text" id="no_hp" name="no_hp" required placeholder="08xxxxxxxxxx">
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required placeholder="contoh@email.com">
                    </div>
                    <div class="form-group">
                        <label for="kabupaten">Kabupaten</label>
                        <input type="text" id="kabupaten" name="kabupaten" required>
                    </div>
                    <div class="form-group">
                        <label for="kota">Kota</label>
                        <input type="text" id="kota" name="kota" required>
                    </div>
                    <div class="form-group">
                        <label for="instansi">Instansi</label>
                        <input type="text" id="instansi" name="instansi" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required placeholder="Masukkan password">
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation">Konfirmasi Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                            placeholder="Ulangi password">
                    </div>
                    <input type="hidden" id="role" name="role" value="guru" required>

                    <button type="submit" class="login-btn">Daftar</button>
                </form>
                <div class="divider"><span>Sudah punya akun?</span></div>
                <p style="text-align:center;">
                    <a href="{{ route('login') }}" style="color:#eb631d;text-decoration:none;font-weight:600;">Login di
                        sini</a>
                </p>
            </div>
        </div>

        <!-- Sisi Logo -->
        <div class="logo-side">
            <div id="lottie-logo" style="width:280px; height:280px; margin-bottom:20px;"></div>
            <h2>KelasSatu</h2>
            <p>Platform Pembelajaran Modern</p>
        </div>
    </div>

    <script>
        // Lottie animation
        lottie.loadAnimation({
            container: document.getElementById('lottie-logo'),
            renderer: 'svg',
            loop: true,
            autoplay: true,
            path: 'https://assets9.lottiefiles.com/packages/lf20_qp1q7mct.json'
        });
    </script>
</body>

</html>
