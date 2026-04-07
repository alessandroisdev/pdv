<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Gestão PDV' }} | ERP Modular</title>
    
    <!-- Vite Directives -->
    @vite(['resources/scss/app.scss', 'resources/ts/app.ts'])
</head>
<body>
    <div class="app-layout">
        <!-- Sidebar -->
        <x-ui.sidebar />

        <!-- Main Content Area -->
        <div class="app-layout__content">
            <!-- Top Navigation -->
            <x-ui.topbar />

            <!-- Page Content -->
            <main class="app-layout__main">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
