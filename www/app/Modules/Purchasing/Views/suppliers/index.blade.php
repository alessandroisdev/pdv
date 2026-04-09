<x-layouts.app>
    <div class="mb-4 flex justify-between items-center">
        <h2 class="text-2xl fw-bold text-primary">Gestão de Fornecedores</h2>
        <button class="btn btn-primary" onclick="document.getElementById('modal-add-supplier').showModal()">
            + Novo Parceiro Mestre
        </button>
    </div>

    <div class="card">
        <div class="card-body" style="padding: 0; overflow-x: auto;">
            <table class="display responsive nowrap w-100" id="purchasing-suppliers-table" style="width: 100%; text-align: left; border-collapse: collapse;">
                <thead>
                    <tr style="background-color: #f8fafc; border-bottom: 1px solid #e2e8f0; color: #64748b; font-size: 0.875rem;">
                        <th style="padding: 1rem; text-align: left;">Razão Social (Empresa)</th>
                        <th style="padding: 1rem; text-align: left;">CNPJ / CPF</th>
                        <th style="padding: 1rem; text-align: left;">Contato</th>
                        <th style="padding: 1rem; text-align: left;">Status</th>
                    </tr>
                </thead>
            </table>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const initSuppTable = () => {
                    if (typeof window.AppServerTable !== 'function') {
                        setTimeout(initSuppTable, 100);
                        return;
                    }
                    new window.AppServerTable('#purchasing-suppliers-table', '{{ route('purchasing.suppliers.datatable') }}', [
                        { data: 'razao', name: 'company_name', searchable: true },
                        { data: 'documento', name: 'cnpj_cpf', searchable: true },
                        { data: 'contato', name: 'email', searchable: true },
                        { data: 'status', searchable: false, orderable: false }
                    ], [[0, 'asc']]);
                };
                initSuppTable();
            });
        </script>
    </div>

    <!-- Modal Novo Fornecedor -->
    <dialog id="modal-add-supplier" class="modal rounded-lg shadow-xl" style="width: 600px; padding: 2rem; border: none; outline: none;">
        <h3 class="text-xl fw-bold text-primary mb-4">Credenciar Indústria / Distribuidor</h3>
        <form action="{{ route('purchasing.suppliers.store') }}" method="POST">
            @csrf
            <div class="form-group mb-4">
                <label>Razão Social (Obrigatório)</label>
                <input type="text" name="company_name" class="form-control w-full" required>
            </div>
            
            <div class="grid" style="grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group">
                    <label>Nome Fantasia</label>
                    <input type="text" name="trade_name" class="form-control w-full">
                </div>
                <div class="form-group">
                    <label>CPNJ ou CPF (Obrigatório)</label>
                    <input type="text" name="cnpj_cpf" class="form-control w-full" required>
                </div>
            </div>

            <div class="grid" style="grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label>E-mail P/ Notas Fiscais</label>
                    <input type="email" name="email" class="form-control w-full">
                </div>
                <div class="form-group">
                    <label>Telefone / WhatsApp</label>
                    <input type="text" name="phone" class="form-control w-full">
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-4 pt-4 border-t border-slate-200">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('modal-add-supplier').close()">Cancelar</button>
                <button type="submit" class="btn btn-primary" style="background:#10b981; border:none;">Registrar Fornecedor</button>
            </div>
        </form>
    </dialog>
</x-layouts.app>
