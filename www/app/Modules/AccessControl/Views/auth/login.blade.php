<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Seguro | Gestão PDV</title>
    @vite(['resources/scss/app.scss'])
    <style>
        .login-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background-color: #f8fafc;
        }
        .login-box {
            background: #ffffff;
            padding: 2.5rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            width: 100%;
            max-width: 400px;
            border-top: 4px solid #c0904d;
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-box">
            <div style="text-align: center; margin-bottom: 2rem;">
                <h1 style="font-size: 1.5rem; font-weight: 700;">
                    <span style="color: #455073;">Gestão</span><span style="color: #c0904d;">PDV</span>
                </h1>
                <p style="color: #64748b; font-size: 0.9rem; margin-top: 0.5rem;">Acesso Administrativo Autenticado</p>
            </div>

            <form action="{{ route('login') }}" method="POST">
                @csrf

                @error('email')
                    <div style="background: #fee2e2; color: #b91c1c; padding: 0.75rem; border-radius: 6px; font-size: 0.85rem; margin-bottom: 1rem;">
                        {{ $message }}
                    </div>
                @enderror

                <div style="margin-bottom: 1.25rem;">
                    <label style="display: block; font-weight: 600; color: #455073; margin-bottom: 0.5rem;">E-mail do Administrador</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="alessandro.souza@norte.dev.br"
                           style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 6px; outline: none; transition: border-color 0.2s;">
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-weight: 600; color: #455073; margin-bottom: 0.5rem;">Senha Segura</label>
                    <input type="password" name="password" required placeholder="********"
                           style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 6px; outline: none;">
                </div>

                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem;">
                    <label style="display: flex; align-items: center; gap: 0.5rem; color: #64748b; font-size: 0.85rem; cursor: pointer;">
                        <input type="checkbox" name="remember"> Manter minha sessão ligada
                    </label>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.85rem; font-size: 1rem;">Acessar Sistema</button>
            </form>
        </div>
    </div>
</body>
</html>
