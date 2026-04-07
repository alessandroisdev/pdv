<header class="app-layout__topbar">
    <div class="topbar-search">
        <!-- Input placeholder for future search -->
        <span class="text-light fw-semibold">Retaguarda Administrativa</span>
    </div>
    
    <div class="topbar-user flex items-center gap-4">
        <span class="fw-semibold">{{ auth()->user()->name ?? 'Visitante' }}</span>
        
        @auth
        <form action="{{ route('logout') }}" method="POST" style="display:inline; margin-left: 0.5rem;">
            @csrf
            <button type="submit" class="btn btn-outline" style="padding: 0.25rem 0.75rem; font-size: 0.75rem; color: #ef4444; border-color: #ef4444;">Sair</button>
        </form>
        @endauth
    </div>
</header>
