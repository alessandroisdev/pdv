<aside class="app-layout__sidebar">
    <div class="sidebar-brand">
        <!-- Logo SVG Omitido -->
        <span style="color: #c0904d;">Gestão</span><span style="color: #fff;">PDV</span>
    </div>
    <ul class="sidebar-nav">
        <!-- 1. VISÃO GERAL -->
        <li>
            <a href="/" class="sidebar-link {{ request()->is('/') ? 'sidebar-link--active' : '' }}">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                <span>Visão Geral</span>
            </a>
        </li>

        <!-- 2. OPERAÇÕES DE LOJA -->
        <li class="sidebar-category mt-4 pt-4 border-t border-slate-700/50" style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid rgba(255,255,255,0.1); margin-left:1rem; font-size:0.75rem; color:#64748b; font-weight:700; text-transform:uppercase; letter-spacing:1px;">Operações & Vendas</li>
        <li>
            <a href="{{ route('sales.cash_registers.index') }}" class="sidebar-link {{ request()->routeIs('sales.cash_registers.*') ? 'sidebar-link--active' : '' }}">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                <span>Painel de Vendas (PDV)</span>
            </a>
        </li>
        <li>
            <a href="{{ route('crm.customers.index') }}" class="sidebar-link {{ request()->routeIs('crm.customers.*') ? 'sidebar-link--active' : '' }}">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
                <span>Relacionamento (CRM)</span>
            </a>
        </li>

        <!-- 3. SUPRIMENTOS -->
        <li class="sidebar-category mt-4 pt-4 border-t border-slate-700/50" style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid rgba(255,255,255,0.1); margin-left:1rem; font-size:0.75rem; color:#64748b; font-weight:700; text-transform:uppercase; letter-spacing:1px;">Logística Interna</li>
        <li>
            <a href="{{ route('inventory.products.index') }}" class="sidebar-link {{ request()->routeIs('inventory.products.*') ? 'sidebar-link--active' : '' }}">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                <span>Catálogo & Estoque</span>
            </a>
        </li>
        <li>
            <a href="{{ route('purchasing.orders.index') }}" class="sidebar-link {{ request()->routeIs('purchasing.*') ? 'sidebar-link--active' : '' }}">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                <span>Compras Oficiais (Fornecedor)</span>
            </a>
        </li>

        <!-- 4. GESTÃO TÉCNICA -->
        <li class="sidebar-category mt-4 pt-4 border-t border-slate-700/50" style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid rgba(255,255,255,0.1); margin-left:1rem; font-size:0.75rem; color:#64748b; font-weight:700; text-transform:uppercase; letter-spacing:1px;">Inteligência de Negócio</li>
        <li>
            <a href="{{ route('finance.dashboard') }}" class="sidebar-link {{ request()->routeIs('finance.*') ? 'sidebar-link--active' : '' }}">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>Painel Financeiro Geral</span>
            </a>
        </li>
        <li>
            <a href="{{ route('hr.employees.index') }}" class="sidebar-link {{ request()->routeIs('hr.*') ? 'sidebar-link--active' : '' }}">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                <span>Colaboradores (RH)</span>
            </a>
        </li>

        <!-- 5. CONFIGURAÇÕES -->
        <li style="margin-top: 3rem;">
            <a href="{{ route('settings.index') }}" class="sidebar-link {{ request()->routeIs('settings.*') || request()->routeIs('fiscal.*') || request()->routeIs('audit.*') || request()->routeIs('core.help.*') ? 'sidebar-link--active' : '' }}" style="border-top: 1px solid rgba(255,255,255, 0.1); border-radius: 0;">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                <span>Configurações Adicionais</span>
            </a>
        </li>
    </ul>
</aside>
