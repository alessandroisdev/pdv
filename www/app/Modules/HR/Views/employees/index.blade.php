<x-layouts.app>
    <div class="p-6">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Recursos Humanos</h1>
                <p class="text-slate-500 text-sm">Gestão de Funcionários, Folha e Acessos</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('hr.employees.export') }}" class="btn btn-outline bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100 flex items-center justify-center gap-2 px-4 py-2 font-bold shadow-sm rounded-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Exportar Folha (CSV)
                </a>
                <button onclick="document.getElementById('add-employee-modal').showModal()" class="btn btn-primary bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-4 py-2 shadow-md rounded-lg transition-transform hover:-translate-y-0.5">
                    + Novo Colaborador
                </button>
            </div>
        </div>

        @if(session('success'))
        <div class="bg-emerald-50 text-emerald-600 p-4 rounded-lg mb-6 shadow-sm border border-emerald-100">
            {{ session('success') }}
        </div>
        @endif

        <div class="card bg-transparent border-0 shadow-none">
            <div class="card-body p-0">
                <x-ui.table>
                    <x-slot name="head">
                        <tr>
                            <th class="p-4 text-left">Código/PIN</th>
                            <th class="p-4 text-left">Nome & Contato</th>
                            <th class="p-4 text-left">Cargo</th>
                            <th class="p-4 text-left">Salário Base</th>
                            <th class="p-4 text-center">Status</th>
                            <th class="p-4 text-right">Ações</th>
                        </tr>
                    </x-slot>
                    <x-slot name="body">
                        @forelse($employees as $emp)
                        <tr class="border-b transition hover:bg-slate-50">
                            <td class="p-4 text-sm">
                                <span class="text-slate-800 font-bold">#{{ str_pad($emp->id, 3, '0', STR_PAD_LEFT) }}</span><br>
                                <span class="text-slate-500 font-mono text-xs">PIN: {{ $emp->pin ?? 'N/A' }}</span>
                            </td>
                            <td class="p-4">
                                <div class="font-bold text-slate-800">{{ $emp->name }}</div>
                                <div class="text-slate-500 text-xs mt-1">CPF: {{ $emp->cpf ?: 'Não Informado' }}</div>
                            </td>
                            <td class="p-4 text-sm text-slate-700">
                                {{ $emp->role_description ?? 'Operador Padrão' }}<br>
                                <span class="text-xs text-slate-400">Admissão: {{ $emp->admission_date ? $emp->admission_date->format('d/m/Y') : '--' }}</span>
                            </td>
                            <td class="p-4 text-sm font-bold text-slate-800">
                                R$ {{ number_format($emp->base_salary_cents / 100, 2, ',', '.') }}
                            </td>
                            <td class="p-4 text-center">
                                @if($emp->status == 1)
                                    <span class="bg-emerald-100 text-emerald-800 text-xs font-bold px-2 py-1 rounded">Ativo</span>
                                @else
                                    <span class="bg-rose-100 text-rose-800 text-xs font-bold px-2 py-1 rounded">Desligado</span>
                                @endif
                            </td>
                            <td class="p-4 text-right">
                                <a href="{{ route('hr.employees.edit', $emp->id) }}" class="btn text-sm py-1 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold border-slate-200 rounded shadow-sm mr-2 inline-block">
                                    Editar
                                </a>
                                <form action="{{ route('hr.employees.destroy', $emp->id) }}" method="POST" id="remove-emp-{{ $emp->id }}" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" onclick="confirmRemoval({{ $emp->id }})" class="btn text-sm py-1 px-3 bg-rose-50 hover:bg-rose-100 text-rose-600 font-semibold border-rose-200 rounded shadow-sm">
                                        Desligar
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-500">Nenhum funcionário cadastrado.</td>
                        </tr>
                        @endforelse
                    </x-slot>
                </x-ui.table>
            </div>
        </div>
    </div>

    <!-- Modal Novo Empregado -->
    <dialog id="add-employee-modal" class="p-0 border-0 rounded-xl shadow-2xl backdrop:bg-slate-900/50 w-full max-w-4xl">
        <div class="p-6 bg-slate-50 border-b border-slate-200 flex justify-between items-center sticky top-0 z-10">
            <h3 class="text-lg font-bold text-slate-800">FICHA DE REGISTRO DO COLABORADOR</h3>
            <button onclick="document.getElementById('add-employee-modal').close()" type="button" class="text-slate-400 hover:text-slate-600 font-bold text-xl">&times;</button>
        </div>
        <form action="{{ route('hr.employees.store') }}" method="POST" class="p-6 overflow-y-auto max-h-[75vh]">
            @csrf

            <!-- Seção 1: Dados Pessoais -->
            <fieldset class="mb-6 p-4 border border-slate-200 rounded bg-white">
                <legend class="text-slate-600 font-bold px-2 py-1 bg-slate-50 border border-slate-200 rounded text-sm uppercase">Dados Pessoais</legend>
                <div class="grid grid-cols-3 gap-4 mb-3">
                    <div class="col-span-2">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nome Completo *</label>
                        <input type="text" name="name" required class="w-full p-2 border border-slate-300 rounded text-sm focus:ring-primary focus:border-primary">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Data Nascimento</label>
                        <input type="date" name="birth_date" class="w-full p-2 border border-slate-300 rounded text-sm focus:ring-primary focus:border-primary">
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Gênero</label>
                        <select name="gender" class="w-full p-2 border border-slate-300 rounded text-sm focus:ring-primary focus:border-primary">
                            <option value="">--</option>
                            <option value="MASCULINO">Masculino</option>
                            <option value="FEMININO">Feminino</option>
                            <option value="OUTROS">Outros</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Estado Civil</label>
                        <select name="marital_status" class="w-full p-2 border border-slate-300 rounded text-sm focus:ring-primary focus:border-primary">
                            <option value="">--</option>
                            <option value="SOLTEIRO">Solteiro(a)</option>
                            <option value="CASADO">Casado(a)</option>
                            <option value="DIVORCIADO">Divorciado(a)</option>
                            <option value="VIUVO">Viúvo(a)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Telefone Principal</label>
                        <input type="text" name="contact_phone" placeholder="(11) 99999-9999" class="w-full p-2 border border-slate-300 rounded text-sm focus:ring-primary focus:border-primary">
                    </div>
                </div>
            </fieldset>

            <!-- Seção 2: Documentação -->
            <fieldset class="mb-6 p-4 border border-slate-200 rounded bg-white">
                <legend class="text-slate-600 font-bold px-2 py-1 bg-slate-50 border border-slate-200 rounded text-sm uppercase">Documentação Legal</legend>
                <div class="grid grid-cols-4 gap-4 mb-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">CPF</label>
                        <input type="text" name="cpf" class="w-full p-2 border border-slate-300 rounded text-sm focus:ring-primary focus:border-primary">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">RG</label>
                        <input type="text" name="rg" class="w-full p-2 border border-slate-300 rounded text-sm focus:ring-primary focus:border-primary">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Órgão Emissor</label>
                        <input type="text" name="rg_issuer" placeholder="SSP/PR" class="w-full p-2 border border-slate-300 rounded text-sm focus:ring-primary focus:border-primary">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">PIS / PASEP</label>
                        <input type="text" name="pis_pasep" class="w-full p-2 border border-slate-300 rounded text-sm focus:ring-primary focus:border-primary">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Número CTPS</label>
                        <input type="text" name="ctps_number" class="w-full p-2 border border-slate-300 rounded text-sm focus:ring-primary focus:border-primary">
                    </div>
                </div>
            </fieldset>

            <!-- Seção 3: Endereço -->
            <fieldset class="mb-6 p-4 border border-slate-200 rounded bg-white">
                <legend class="text-slate-600 font-bold px-2 py-1 bg-slate-50 border border-slate-200 rounded text-sm uppercase">Endereço (Residência)</legend>
                <div class="grid grid-cols-4 gap-4 mb-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">CEP</label>
                        <input type="text" name="cep" class="w-full p-2 border border-slate-300 rounded text-sm focus:ring-primary focus:border-primary">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Rua / Logradouro</label>
                        <input type="text" name="address" class="w-full p-2 border border-slate-300 rounded text-sm focus:ring-primary focus:border-primary">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Número</label>
                        <input type="text" name="address_number" class="w-full p-2 border border-slate-300 rounded text-sm focus:ring-primary focus:border-primary">
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Bairro</label>
                        <input type="text" name="neighborhood" class="w-full p-2 border border-slate-300 rounded text-sm focus:ring-primary focus:border-primary">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Cidade</label>
                        <input type="text" name="city" class="w-full p-2 border border-slate-300 rounded text-sm focus:ring-primary focus:border-primary">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">UF (Estado)</label>
                        <input type="text" name="state" maxlength="2" placeholder="Ex: PR" class="w-full p-2 border border-slate-300 rounded text-sm focus:ring-primary focus:border-primary uppercase">
                    </div>
                </div>
            </fieldset>

            <!-- Seção 4: Contrato & Financeiro -->
            <fieldset class="mb-6 p-4 border border-[var(--primary)] rounded bg-white relative">
                <legend class="text-white font-bold px-3 py-1 bg-[var(--primary)] rounded text-sm shadow-sm uppercase">Vínculo & Regras Financeiras</legend>
                
                <div class="grid grid-cols-3 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Departamento</label>
                        <input type="text" name="department" placeholder="Ex: Operações Frontend" class="w-full p-2 border border-slate-300 rounded text-sm focus:ring-primary focus:border-primary">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Função/Cargo</label>
                        <input type="text" name="role_description" class="w-full p-2 border border-slate-300 rounded text-sm focus:ring-primary focus:border-primary">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Tipo de Contrato</label>
                        <select name="contract_type" class="w-full p-2 border border-slate-300 rounded text-sm focus:ring-primary focus:border-primary">
                            <option value="CLT">CLT (Consolidação via CLT)</option>
                            <option value="PJ">PJ (Prestador Terceirizado)</option>
                            <option value="ESTAGIO">Estágio</option>
                            <option value="Temporario">Temporário</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Data de Admissão</label>
                        <input type="date" name="admission_date" class="w-full p-2 border border-slate-300 rounded text-sm focus:ring-primary focus:border-primary">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Jornada de Trabalho</label>
                        <input type="text" name="work_schedule" placeholder="Ex: Seg-Sex (08h as 18h)" class="w-full p-2 border border-slate-300 rounded text-sm focus:ring-primary focus:border-primary">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-800 mb-1">Salário Base (Centavos) *</label>
                        <input type="number" name="base_salary_cents" required placeholder="Ex: 350000 para R$ 3.500,00" class="w-full p-2 border border-slate-300 rounded text-sm bg-slate-50 focus:bg-white focus:ring-primary focus:border-primary shadow-inner">
                    </div>
                </div>

                <hr class="border-slate-200 my-4">

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Dados Bancários / Depósito</label>
                        <input type="text" name="bank_account_info" placeholder="Banco Bradesco | Ag: 1234 | Cc: 12345-6" class="w-full p-2 border border-slate-300 rounded text-sm focus:ring-primary focus:border-primary">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Chave Pix (Alternativa)</label>
                        <input type="text" name="pix_key" class="w-full p-2 border border-slate-300 rounded text-sm focus:ring-primary focus:border-primary">
                    </div>
                </div>
            </fieldset>

            <!-- Seção 5: Segurança e PDV -->
            <fieldset class="mb-4 p-4 border border-rose-200 rounded bg-rose-50/50">
                <legend class="text-rose-600 font-bold px-2 py-1 bg-rose-100 border border-rose-200 rounded text-sm uppercase"><i class="fa fa-shield"></i> Credenciais de Sistema</legend>
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div class="bg-white p-3 rounded border border-rose-100">
                        <label class="block text-xs font-bold text-slate-800 mb-1"><i class="fa fa-lock text-rose-400"></i> PIN de Acesso ao Caixa Numérico</label>
                        <input type="password" name="pin" placeholder="****" maxlength="8" class="w-full p-2 border border-slate-300 rounded focus:ring-rose-500 focus:border-rose-500 font-mono text-center tracking-widest text-lg shadow-sm">
                    </div>
                    <div class="bg-white p-3 rounded border border-rose-100">
                        <label class="block text-xs font-bold text-slate-800 mb-1">Nível de Permissão (Sistema)</label>
                        <select name="level" class="w-full p-2 border border-slate-300 rounded focus:ring-rose-500 focus:border-rose-500 font-semibold text-slate-700">
                            <option value="OPERATOR">CAIXA / OPERADOR (Limitado)</option>
                            <option value="SUPERVISOR">SUPERVISOR DE LOJA</option>
                            <option value="ADMIN">ADMINISTRADOR MASTER</option>
                        </select>
                    </div>
                </div>
            </fieldset>

            <!-- Seção 6: Contato de Emergência -->
            <details class="mb-6 cursor-pointer text-sm">
                <summary class="font-bold text-slate-600 outline-none select-none">Adicionar Contato de Emergência</summary>
                <div class="mt-3 p-4 bg-slate-100 rounded border border-slate-200 grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nome/Grau de Parentesco</label>
                        <input type="text" name="emergency_contact_name" class="w-full p-2 border border-slate-300 rounded focus:ring-primary focus:border-primary">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Telefone (Emergência)</label>
                        <input type="text" name="emergency_contact_phone" class="w-full p-2 border border-slate-300 rounded focus:ring-primary focus:border-primary">
                    </div>
                </div>
            </details>

            <div class="flex justify-end pt-4 mt-2 border-t border-slate-200 sticky bottom-0 bg-slate-50/90 backdrop-blur pb-2">
                <button type="submit" class="px-8 py-3 bg-[var(--primary)] text-white font-bold rounded shadow-lg hover:opacity-90 transition transform hover:-translate-y-1">
                    CADASTRAR FICHA (SALVAR)
                </button>
            </div>
        </form>
    </dialog>
    <script>
        function confirmRemoval(id) {
            if (window.Swal) {
                window.Swal.fire({
                    title: 'Atenção Rh!',
                    text: 'Deseja realmente desligar/remover o acesso deste colaborador permanentemente?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e11d48',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Sim, Desligar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('remove-emp-' + id).submit();
                    }
                });
            } else {
                if (confirm('ATENÇÃO: Deseja realmente desligar este funcionário do acesso?')) {
                    document.getElementById('remove-emp-' + id).submit();
                }
            }
        }
    </script>
</x-layouts.app>
