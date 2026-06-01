<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selowa | Login</title>

    <link rel="icon" href="{{ asset('selowa.webp') }}">
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/vendor.css') }}" rel="stylesheet">

    <style>
        * { box-sizing: border-box; }
        body {
            min-height: 100vh;
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            color: #1f2937;
            background: #eef7f5;
        }
        .login-shell {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 28px 18px;
            background:
                radial-gradient(circle at 18% 18%, rgba(255,255,255,.28), transparent 28%),
                linear-gradient(135deg, #e7f7f2 0%, #cbeee6 42%, #f7fbff 100%);
        }
        .login-wrap {
            width: 100%;
            max-width: 920px;
            min-height: 520px;
            display: grid;
            grid-template-columns: 1fr 420px;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 22px 55px rgba(6, 44, 69, .25);
        }
        .login-info {
            padding: 54px 48px;
            color: #fff;
            background:
                linear-gradient(145deg, rgba(10, 92, 112, .96), rgba(13, 141, 112, .94)),
                #0e7490;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        .login-info:after {
            content: "";
            position: absolute;
            width: 360px;
            height: 360px;
            right: -180px;
            bottom: -150px;
            border-radius: 50%;
            background: rgba(255,255,255,.09);
        }
        .gallon-row {
            display: flex;
            gap: 18px;
            margin-bottom: 28px;
            position: relative;
            z-index: 1;
        }
        .gallon {
            width: 58px;
            height: 92px;
            border: 3px solid rgba(255,255,255,.85);
            border-radius: 18px 18px 24px 24px;
            position: relative;
            background: rgba(255,255,255,.14);
        }
        .gallon:before {
            content: "";
            position: absolute;
            top: -18px;
            left: 16px;
            width: 20px;
            height: 18px;
            border: 3px solid rgba(255,255,255,.85);
            border-bottom: 0;
            border-radius: 8px 8px 0 0;
        }
        .gallon:after {
            content: "";
            position: absolute;
            left: 8px;
            right: 8px;
            bottom: 13px;
            height: 32px;
            border-radius: 0 0 16px 16px;
            background: rgba(255,255,255,.32);
        }
        .login-info h1 {
            margin: 0 0 12px;
            font-size: 42px;
            line-height: 1.05;
            font-weight: 800;
            color: #fff;
        }
        .login-info p {
            margin: 0;
            font-size: 17px;
            line-height: 1.55;
            color: rgba(255,255,255,.88);
            max-width: 420px;
        }
        .login-panel {
            width: 100%;
            padding: 48px 38px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .brand-logo-small {
            width: 118px;
            height: 118px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
        }
        .brand-logo-small img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        .login-panel h1 {
            margin: 0 0 8px;
            font-size: 25px;
            font-weight: 700;
            color: #1f2937;
        }
        .login-panel .subtitle {
            margin-bottom: 24px;
            color: #6b7280;
        }
        .form-control {
            height: 42px;
            border-radius: 6px;
            box-shadow: none;
        }
        .btn-login {
            height: 42px;
            border-radius: 6px;
            font-weight: 700;
            background: #0e7490;
            border-color: #0e7490;
        }
        .btn-login:hover, .btn-login:focus {
            background: #0f647c;
            border-color: #0f647c;
        }
        .developer-signature {
            margin-top: 24px;
            padding-top: 18px;
            border-top: 1px solid #edf0f2;
            text-align: center;
            color: #8a96a3;
            font-size: 11px;
        }
        .developer-signature img {
            display: block;
            max-width: 132px;
            max-height: 42px;
            object-fit: contain;
            margin: 8px auto 0;
        }
        @media (max-width: 760px) {
            body {
                background: #f3fbf8;
            }

            .login-shell {
                min-height: 100svh;
                align-items: flex-start;
                padding: 16px 12px;
                background: linear-gradient(180deg, #dff5ef 0%, #f8fcff 100%);
            }

            .login-wrap {
                display: block;
                max-width: 430px;
                min-height: auto;
                border-radius: 10px;
                box-shadow: 0 16px 34px rgba(6, 44, 69, .18);
            }

            .login-info {
                padding: 22px 22px 20px;
                min-height: 132px;
            }

            .login-info:after {
                width: 180px;
                height: 180px;
                right: -90px;
                bottom: -90px;
            }

            .gallon-row {
                display: none;
            }

            .login-info h1 {
                font-size: 31px;
                margin-bottom: 8px;
            }

            .login-info p {
                font-size: 14px;
                line-height: 1.45;
                max-width: 300px;
            }

            .login-panel {
                padding: 24px 20px 22px;
            }

            .brand-logo-small {
                width: 150px;
                height: 70px;
                margin-bottom: 8px;
            }

            .login-panel h1 {
                font-size: 21px;
                margin-bottom: 4px;
            }

            .login-panel .subtitle {
                margin-bottom: 18px;
                font-size: 13px;
            }

            .form-group {
                margin-bottom: 14px;
            }

            .form-control,
            .btn-login {
                height: 44px;
                font-size: 15px;
            }

            .checkbox {
                margin-top: 0;
                margin-bottom: 14px;
            }

            .developer-signature {
                margin-top: 18px;
                padding-top: 14px;
            }

            .developer-signature img {
                max-width: 116px;
                max-height: 36px;
            }
        }

        @media (max-width: 360px) {
            .login-shell {
                padding: 10px 8px;
            }

            .login-info {
                padding: 18px;
            }

            .login-panel {
                padding: 20px 16px;
            }
        }
    </style>
</head>
<body>
    <div class="login-shell">
        <div class="login-wrap">
            <div class="login-info">
                <div class="gallon-row">
                    <span class="gallon"></span>
                    <span class="gallon"></span>
                    <span class="gallon"></span>
                </div>
                <h1>Selowa</h1>
                <p>Platform operasional untuk layanan isi ulang air minum yang tertata, cepat, dan mudah.</p>
            </div>

            <div class="login-panel">
                <div class="text-center">
                    <div class="brand-logo-small">
                        <img src="{{ asset('selowa.webp') }}" alt="Logo Selowa">
                    </div>
                    <h1>LOGIN</h1>
                    <div class="subtitle">Masuk ke dashboard Selowa</div>
                </div>

                @if (isset($errors) && $errors->any())
                    <div class="alert alert-danger">{{ $errors->first() }}</div>
                @endif

                <form method="POST" action="{{ route('login.attempt') }}">
                    @csrf
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input id="email" name="email" type="email" class="form-control" value="{{ old('email') }}" autofocus required>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input id="password" name="password" type="password" class="form-control" required>
                    </div>

                    <div class="checkbox">
                        <label><input type="checkbox" name="remember" value="1"> Ingat saya</label>
                    </div>

                    <button type="submit" class="btn btn-primary btn-login btn-block">Masuk</button>
                </form>

                <div class="developer-signature">
                    <img src="{{ asset('piclogo.png') }}" alt="Logo pengembang">
                </div>
            </div>
        </div>
    </div>
</body>
</html>
