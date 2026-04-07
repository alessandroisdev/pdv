<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDV & ERP System</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    @vite(['resources/scss/app.scss', 'resources/ts/app.ts'])
</head>
<body class="bg-background">
    <div class="container">
        <header style="padding: 2rem 0; border-bottom: 2px solid #c0904d;">
            <h1 class="text-primary" style="font-weight: 700;">Gestão Operacional | PDV</h1>
            <p class="text-secondary">O sistema está ativo e configurado com Laravel e TypeScript.</p>
        </header>

        <main style="margin-top: 3rem;">
            <div style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                <h2 class="text-primary">Módulos Carregados</h2>
                <ul style="margin-top: 1rem; list-style-position: inside;">
                    <li>Core</li>
                    <li>Access Control</li>
                    <li style="color: #ccc;">(Módulos dinâmicos serão listados aqui)</li>
                </ul>
            </div>
        </main>
    </div>
</body>
</html>
