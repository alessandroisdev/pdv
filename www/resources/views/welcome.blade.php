<x-layouts.app>
    <x-slot:title>Visão Geral</x-slot:title>

    <div class="flex justify-between items-center mb-4">
        <div>
            <h1 class="text-primary fw-bold" style="font-size: 1.75rem;">Dashboard</h1>
            <p class="text-light" style="margin-top: 0.25rem;">Resumo gerencial e saúde do seu negócio.</p>
        </div>
        <button class="btn btn-primary">
            + Nova Venda (PDV)
        </button>
    </div>

    <!-- Cards de Resumo -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
        <x-ui.card>
            <x-slot:header>Vendas Hoje (Bruto)</x-slot:header>
            <h2 class="text-contrast fw-bold" style="font-size: 2.25rem;">R$ 0,00</h2>
            <p class="text-light mt-4" style="font-size: 0.85rem;">Caixa principal fechado</p>
        </x-ui.card>

        <x-ui.card>
            <x-slot:header>Itens Únicos em Estoque</x-slot:header>
            <h2 class="text-primary fw-bold" style="font-size: 2.25rem;">0</h2>
            <p class="text-light mt-4" style="font-size: 0.85rem;">Nenhum produto cadastrado</p>
        </x-ui.card>

        <x-ui.card>
            <x-slot:header>Alerta de Ruptura (Estoque Baixo)</x-slot:header>
            <h2 class="text-secondary fw-bold" style="font-size: 2.25rem;">0 Itens</h2>
            <p class="text-light mt-4" style="font-size: 0.85rem;">Tudo regular ou vazio</p>
        </x-ui.card>
    </div>

    <!-- Seção de Ações e Histórico -->
    <x-ui.card>
        <x-slot:header>Status dos Módulos</x-slot:header>
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="border-bottom: 2px solid #e2e8f0; color: #455073;">
                    <th style="padding: 1rem 0;">Módulo ERP</th>
                    <th style="padding: 1rem 0;">Integração DB</th>
                    <th style="padding: 1rem 0;">Status Operacional</th>
                </tr>
            </thead>
            <tbody>
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 1rem 0; font-weight: 500;">📦 Estoque (Inventory)</td>
                    <td style="padding: 1rem 0; color: #16a34a;">Ativo (Kardex)</td>
                    <td style="padding: 1rem 0;">
                        <span style="background: rgba(22, 163, 74, 0.1); color: #16a34a; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.85rem;">Online</span>
                    </td>
                </tr>
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 1rem 0; font-weight: 500;">🛒 Compras (Purchasing)</td>
                    <td style="padding: 1rem 0; color: #16a34a;">Ativo</td>
                    <td style="padding: 1rem 0;">
                        <span style="background: rgba(22, 163, 74, 0.1); color: #16a34a; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.85rem;">Online</span>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 1rem 0; font-weight: 500;">💳 PDV & Financeiro</td>
                    <td style="padding: 1rem 0; color: #16a34a;">Ativo</td>
                    <td style="padding: 1rem 0;">
                        <span style="background: rgba(22, 163, 74, 0.1); color: #16a34a; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.85rem;">Online</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </x-ui.card>

</x-layouts.app>
