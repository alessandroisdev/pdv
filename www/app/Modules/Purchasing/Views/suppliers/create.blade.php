<x-layouts.app>
    <x-slot:title>Novo Fornecedor</x-slot:title>

    <div class="mb-4">
        <a href="{{ route('purchasing.suppliers.index') }}" class="text-light fw-semibold" style="text-decoration: none; font-size: 0.85rem;">&larr; Voltar para a lista</a>
        <h1 class="text-primary fw-bold mt-4" style="font-size: 1.75rem;">Cadastrar Novo Fornecedor</h1>
    </div>

    <x-ui.card>
        <x-slot:header>Dados Cadastrais da Empresa</x-slot:header>

        <form action="{{ route('purchasing.suppliers.store') }}" method="POST">
            @csrf

            <!-- Aplicando Componente de UI Simplificado -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                
                <x-ui.input name="name" label="Razão Social / Nome Fantasia" required="true" placeholder="Ex: Distribuidora Nacional S.A" />
                
                <x-ui.input name="document" label="CNPJ / Documento legal" placeholder="00.000.000/0001-00" />
                
                <x-ui.input type="email" name="email" label="Endereço de E-mail" placeholder="contato@empresa.com.br" />
                
                <x-ui.input name="phone" label="Telefone Comercial" placeholder="(00) 0000-0000" />
                
            </div>

            <div style="text-align: right; border-top: 1px solid #e2e8f0; padding-top: 1.5rem;">
                <a href="{{ route('purchasing.suppliers.index') }}" class="btn btn-outline" style="margin-right: 1rem;">Cancelar Operação</a>
                <button type="submit" class="btn btn-primary" style="padding: 0.6rem 2rem;">Salvar Fornecedor</button>
            </div>
        </form>
    </x-ui.card>
</x-layouts.app>
