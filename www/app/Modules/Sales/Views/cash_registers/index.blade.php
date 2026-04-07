<x-layouts.app>
    <x-slot:title>Vendas | Caixas Operacionais</x-slot:title>

    <div class="flex justify-between items-center mb-4">
        <div>
            <h1 class="text-primary fw-bold" style="font-size: 1.75rem;">Gestão de Caixas (PDV)</h1>
            <p class="text-light" style="margin-top: 0.25rem;">Monitoramento de turnos e aberturas de caixa em tempo real.</p>
        </div>
        <div>
            <button class="btn btn-outline" style="background-color: white;" onclick="alert('Funcionalidade de Relatórios consolidada nas telas do Operador.')">Relatório de Fechamentos</button>
        </div>
    </div>

    <!-- Filtros Básicos -->
    <x-ui.card class="mb-4">
        <div class="flex gap-4 items-center">
            <select style="padding: 0.6rem 2rem 0.6rem 1rem; border: 1px solid #e2e8f0; border-radius: 6px; outline: none; background: white;">
                <option value="">Status: Todos</option>
                <option value="open">Abertos</option>
                <option value="closed">Fechados</option>
            </select>
            <button class="btn btn-primary" style="background: #e2e8f0; color: #455073;">Aplicar Filtro</button>
        </div>
    </x-ui.card>

    <x-ui.card>
        <x-slot:header>Histórico de Status dos Caixas</x-slot:header>
        
        <x-ui.table>
            <x-slot:head>
                <th>Turno #ID</th>
                <th>Operador de Frente</th>
                <th>Status Atual</th>
                <th>Abertura</th>
                <th>Fundo de Troco</th>
                <th>Fechamento</th>
                <th style="text-align: right;">Opções</th>
            </x-slot:head>
            
            @forelse($registers as $register)
                @php
                    $isOpen = is_null($register->closed_at);
                @endphp
                <tr>
                    <td>#{{ str_pad($register->id, 5, '0', STR_PAD_LEFT) }}</td>
                    <td class="fw-semibold">{{ $register->user->name ?? 'PDV User' }}</td>
                    <td>
                        <span style="display: inline-block; padding: 0.2rem 0.6rem; border-radius: 4px; font-size: 0.75rem; font-weight: 600; {{ $isOpen ? 'background: #dcfce7; color: #166534;' : 'background: #e2e8f0; color: #475569;' }}">
                            {{ $isOpen ? 'ABERTO' : 'FECHADO' }}
                        </span>
                    </td>
                    <td>{{ $register->opened_at->format('d/m/Y H:i') }}</td>
                    <td>{{ clone $register->opening_balance }}</td>
                    <td class="text-light">{{ $register->closed_at ? $register->closed_at->format('d/m/Y H:i') : '---' }}</td>
                    <td style="text-align: right;">
                        <button class="btn" style="padding: 0.25rem 0.5rem; border: 1px solid #e2e8f0; color: #455073; font-size: 0.75rem;">Auditar</button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 3rem;">
                        <div style="color: #64748b; margin-bottom: 1rem;">
                            <!-- Cash register SVG icon -->
                            <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24" style="margin: 0 auto;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        </div>
                        <h4 style="font-size: 1.1rem; color: #455073; margin-bottom: 0.5rem;">Nenhum caixa foi registrado no sistema.</h4>
                        <p style="color: #64748b; font-size: 0.9rem;">Para iniciar operaciones de venda, seus caixas precisarão ser abertos pelo Frente de Loja.</p>
                    </td>
                </tr>
            @endforelse
        </x-ui.table>
        
        <div style="margin-top: 1.5rem;">
            {{ $registers->links() }}
        </div>
    </x-ui.card>
</x-layouts.app>
