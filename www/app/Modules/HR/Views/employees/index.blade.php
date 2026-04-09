<x-layouts.app>
    <div class="p-6">
        <div class="flex flex-wrap justify-between items-center mb-4" style="margin-bottom: 1.5rem;">
            <div>
                <h1 class="text-primary fw-bold" style="font-size: 1.75rem;">Recursos Humanos</h1>
                <p class="text-light" style="margin-top: 0.25rem;">Gestão de Funcionários, Folha e Acessos</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('hr.employees.export') }}" class="btn btn-outline" style="background: white;">
                    <i class="fa fa-file-csv"></i> Exportar Folha (CSV)
                </a>
                <button onclick="document.getElementById('add-employee-modal').showModal()" class="btn btn-primary" style="background: #4f46e5; border-color: #4f46e5;">
                    <i class="fa fa-plus"></i> Novo Colaborador
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
                                    <span style="display: inline-block; padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.75rem; font-weight: bold; background: #d1fae5; color: #065f46;">Ativo</span>
                                @else
                                    <span style="display: inline-block; padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.75rem; font-weight: bold; background: #ffe4e6; color: #9f1239;">Desligado</span>
                                @endif
                            </td>
                            <td class="p-4 text-right">
                                <a href="{{ route('hr.employees.edit', $emp->id) }}" class="btn btn-outline" style="padding: 0.25rem 0.75rem; font-size: 0.875rem;">
                                    Editar
                                </a>
                                <form action="{{ route('hr.employees.destroy', $emp->id) }}" method="POST" id="remove-emp-{{ $emp->id }}" style="display: inline-block; margin-left: 0.5rem;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" onclick="confirmRemoval({{ $emp->id }})" class="btn" style="background: #fff1f2; border: 1px solid #fecdd3; color: #e11d48; padding: 0.25rem 0.75rem; font-size: 0.875rem; font-weight: bold;">
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
    <dialog id="add-employee-modal" style="padding: 0; border: none; border-radius: 0.75rem; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); width: 100%; max-width: 56rem; max-height: 90vh; position: fixed; inset: 0; margin: auto; z-index: 9999;">
        <style>
            #add-employee-modal::backdrop {
                background: rgba(15, 23, 42, 0.5); /* backdrop:bg-slate-900/50 */
                backdrop-filter: blur(2px);
            }
        </style>
        <div style="padding: 1.5rem; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; z-index: 10;">
            <h3 style="font-size: 1.125rem; font-weight: bold; color: #1e293b; margin: 0;">FICHA DE REGISTRO DO COLABORADOR</h3>
            <button onclick="document.getElementById('add-employee-modal').close()" type="button" style="background: none; border: none; cursor: pointer; color: #94a3b8; font-size: 1.5rem; font-weight: bold; line-height: 1;">&times;</button>
        </div>
        <form action="{{ route('hr.employees.store') }}" method="POST" style="padding: 1.5rem; overflow-y: auto;">
            @csrf

            <!-- Seção 1: Dados Pessoais -->
            <fieldset style="margin-bottom: 1.5rem; padding: 1rem; border: 1px solid #e2e8f0; border-radius: 0.375rem; background: white;">
                <legend style="color: #475569; font-weight: bold; padding: 0.25rem 0.5rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 0.25rem; font-size: 0.875rem; text-transform: uppercase;">Dados Pessoais</legend>
                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1rem; margin-bottom: 0.75rem;">
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #334155; margin-bottom: 0.25rem;">Nome Completo *</label>
                        <input type="text" name="name" required class="form-control" style="width: 100%;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #334155; margin-bottom: 0.25rem;">Data Nascimento</label>
                        <input type="date" name="birth_date" class="form-control" style="width: 100%;">
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem;">
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #334155; margin-bottom: 0.25rem;">Gênero</label>
                        <select name="gender" class="form-control" style="width: 100%;">
                            <option value="">--</option>
                            <option value="MASCULINO">Masculino</option>
                            <option value="FEMININO">Feminino</option>
                            <option value="OUTROS">Outros</option>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #334155; margin-bottom: 0.25rem;">Estado Civil</label>
                        <select name="marital_status" class="form-control" style="width: 100%;">
                            <option value="">--</option>
                            <option value="SOLTEIRO">Solteiro(a)</option>
                            <option value="CASADO">Casado(a)</option>
                            <option value="DIVORCIADO">Divorciado(a)</option>
                            <option value="VIUVO">Viúvo(a)</option>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #334155; margin-bottom: 0.25rem;">Telefone Principal</label>
                        <input type="text" name="contact_phone" placeholder="(11) 99999-9999" class="form-control" style="width: 100%;">
                    </div>
                </div>
            </fieldset>

            <!-- Seção 2: Documentação -->
            <fieldset style="margin-bottom: 1.5rem; padding: 1rem; border: 1px solid #e2e8f0; border-radius: 0.375rem; background: white;">
                <legend style="color: #475569; font-weight: bold; padding: 0.25rem 0.5rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 0.25rem; font-size: 0.875rem; text-transform: uppercase;">Documentação Legal</legend>
                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 0.75rem;">
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #334155; margin-bottom: 0.25rem;">CPF</label>
                        <input type="text" name="cpf" class="form-control" style="width: 100%;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #334155; margin-bottom: 0.25rem;">RG</label>
                        <input type="text" name="rg" class="form-control" style="width: 100%;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #334155; margin-bottom: 0.25rem;">Órgão Emissor</label>
                        <input type="text" name="rg_issuer" placeholder="SSP/PR" class="form-control" style="width: 100%;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #334155; margin-bottom: 0.25rem;">PIS / PASEP</label>
                        <input type="text" name="pis_pasep" class="form-control" style="width: 100%;">
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #334155; margin-bottom: 0.25rem;">Número CTPS</label>
                        <input type="text" name="ctps_number" class="form-control" style="width: 100%;">
                    </div>
                </div>
            </fieldset>

            <!-- Seção 3: Endereço -->
            <fieldset style="margin-bottom: 1.5rem; padding: 1rem; border: 1px solid #e2e8f0; border-radius: 0.375rem; background: white;">
                <legend style="color: #475569; font-weight: bold; padding: 0.25rem 0.5rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 0.25rem; font-size: 0.875rem; text-transform: uppercase;">Endereço (Residência)</legend>
                <div style="display: grid; grid-template-columns: 1fr 2fr 1fr; gap: 1rem; margin-bottom: 0.75rem;">
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #334155; margin-bottom: 0.25rem;">CEP</label>
                        <input type="text" name="cep" class="form-control" style="width: 100%;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #334155; margin-bottom: 0.25rem;">Rua / Logradouro</label>
                        <input type="text" name="address" class="form-control" style="width: 100%;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #334155; margin-bottom: 0.25rem;">Número</label>
                        <input type="text" name="address_number" class="form-control" style="width: 100%;">
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem;">
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #334155; margin-bottom: 0.25rem;">Bairro</label>
                        <input type="text" name="neighborhood" class="form-control" style="width: 100%;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #334155; margin-bottom: 0.25rem;">Cidade</label>
                        <input type="text" name="city" class="form-control" style="width: 100%;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #334155; margin-bottom: 0.25rem;">UF (Estado)</label>
                        <input type="text" name="state" maxlength="2" placeholder="Ex: PR" class="form-control" style="text-transform: uppercase; width: 100%;">
                    </div>
                </div>
            </fieldset>

            <!-- Seção 4: Contrato & Financeiro -->
            <fieldset style="margin-bottom: 1.5rem; padding: 1rem; border: 1px solid var(--primary); border-radius: 0.375rem; background: white; margin-top: 1rem;">
                <legend style="color: white; font-weight: bold; padding: 0.25rem 0.75rem; background: var(--primary); border-radius: 0.25rem; font-size: 0.875rem; text-transform: uppercase;">Vínculo & Regras Financeiras</legend>
                
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #334155; margin-bottom: 0.25rem;">Departamento</label>
                        <input type="text" name="department" placeholder="Ex: Operações Frontend" class="form-control" style="width: 100%;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #334155; margin-bottom: 0.25rem;">Função/Cargo</label>
                        <input type="text" name="role_description" class="form-control" style="width: 100%;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #334155; margin-bottom: 0.25rem;">Tipo de Contrato</label>
                        <select name="contract_type" class="form-control" style="width: 100%;">
                            <option value="CLT">CLT (Consolidação via CLT)</option>
                            <option value="PJ">PJ (Prestador Terceirizado)</option>
                            <option value="ESTAGIO">Estágio</option>
                            <option value="Temporario">Temporário</option>
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #334155; margin-bottom: 0.25rem;">Data de Admissão</label>
                        <input type="date" name="admission_date" class="form-control" style="width: 100%;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #334155; margin-bottom: 0.25rem;">Jornada de Trabalho</label>
                        <input type="text" name="work_schedule" placeholder="Ex: Seg-Sex (08h as 18h)" class="form-control" style="width: 100%;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #1e293b; margin-bottom: 0.25rem;">Salário Base (Centavos) *</label>
                        <input type="number" name="base_salary_cents" required placeholder="Ex: 350000 para R$ 3.500,00" class="form-control" style="width: 100%; background: #f8fafc;">
                    </div>
                </div>

                <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 1rem 0;">

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #334155; margin-bottom: 0.25rem;">Dados Bancários / Depósito</label>
                        <input type="text" name="bank_account_info" placeholder="Banco Bradesco | Ag: 1234 | Cc: 12345-6" class="form-control" style="width: 100%;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #334155; margin-bottom: 0.25rem;">Chave Pix (Alternativa)</label>
                        <input type="text" name="pix_key" class="form-control" style="width: 100%;">
                    </div>
                </div>
            </fieldset>

            <!-- Seção 5: Segurança e PDV -->
            <fieldset style="margin-bottom: 1rem; padding: 1rem; border: 1px solid #fecdd3; border-radius: 0.375rem; background: #fff1f2;">
                <legend style="color: #e11d48; font-weight: bold; padding: 0.25rem 0.5rem; background: #ffe4e6; border: 1px solid #fecdd3; border-radius: 0.25rem; font-size: 0.875rem; text-transform: uppercase;"><i class="fa fa-shield"></i> Credenciais de Sistema</legend>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div style="background: white; padding: 0.75rem; border-radius: 0.25rem; border: 1px solid #ffe4e6;">
                        <label style="display: block; font-size: 0.75rem; font-weight: bold; color: #1e293b; margin-bottom: 0.25rem;"><i class="fa fa-lock" style="color: #fb7185;"></i> PIN de Acesso ao Caixa Numérico</label>
                        <input type="password" name="pin" placeholder="****" maxlength="8" class="form-control" style="width: 100%; text-align: center; font-family: monospace; letter-spacing: 0.25em;">
                    </div>
                    <div style="background: white; padding: 0.75rem; border-radius: 0.25rem; border: 1px solid #ffe4e6;">
                        <label style="display: block; font-size: 0.75rem; font-weight: bold; color: #1e293b; margin-bottom: 0.25rem;">Nível de Permissão (Sistema)</label>
                        <select name="level" class="form-control" style="width: 100%; font-weight: bold;">
                            <option value="OPERATOR">CAIXA / OPERADOR (Limitado)</option>
                            <option value="SUPERVISOR">SUPERVISOR DE LOJA</option>
                            <option value="ADMIN">ADMINISTRADOR MASTER</option>
                        </select>
                    </div>
                </div>
            </fieldset>

            <!-- Seção 6: Contato de Emergência -->
            <details style="margin-bottom: 1.5rem; cursor: pointer; font-size: 0.875rem;">
                <summary style="font-weight: bold; color: #475569; outline: none; user-select: none;">Adicionar Contato de Emergência</summary>
                <div style="margin-top: 0.75rem; padding: 1rem; background: #f1f5f9; border-radius: 0.25rem; border: 1px solid #e2e8f0; display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #334155; margin-bottom: 0.25rem;">Nome/Grau de Parentesco</label>
                        <input type="text" name="emergency_contact_name" class="form-control" style="width: 100%;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #334155; margin-bottom: 0.25rem;">Telefone (Emergência)</label>
                        <input type="text" name="emergency_contact_phone" class="form-control" style="width: 100%;">
                    </div>
                </div>
            </details>

            <div style="display: flex; justify-content: flex-end; padding-top: 1rem; margin-top: 0.5rem; border-top: 1px solid #e2e8f0;">
                <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2rem; font-weight: bold;">
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
