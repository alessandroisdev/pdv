<x-layouts.app>
    <div class="mb-4 flex flex-wrap justify-between items-center border-b border-slate-200 pb-4" style="margin-bottom: 1.5rem; padding-bottom: 1rem;">
        <div>
            <h2 class="text-primary fw-bold" style="font-size: 1.75rem;">Editar Ficha do Colaborador</h2>
            <p class="text-light" style="margin-top: 0.25rem;">Alterar dados cadastrais e acessos do funcionário #{{ $employee->id }}</p>
        </div>
        <a href="{{ route('hr.employees.index') }}" class="btn btn-outline" style="background: white;">
            <i class="fa fa-arrow-left"></i> Voltar à Listagem
        </a>
    </div>

    @if($errors->any())
        <div class="bg-red-50 text-red-600 p-4 rounded-lg mb-6 shadow-sm border border-red-200">
            <strong>Erros:</strong>
            <ul class="list-disc ml-5 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('hr.employees.update', $employee->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="card p-6 border-0 shadow-sm bg-white" style="border-radius: 0.75rem; padding: 1.5rem; border: 1px solid #e2e8f0;">
            <!-- Dados Pessoais -->
            <fieldset style="margin-bottom: 1.5rem; padding: 1rem; border: 1px solid #e2e8f0; border-radius: 0.375rem; background: white;">
                <legend style="color: #475569; font-weight: bold; padding: 0.25rem 0.5rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 0.25rem; font-size: 0.875rem; text-transform: uppercase;">Dados Pessoais</legend>
                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #334155; margin-bottom: 0.25rem;">Nome Completo *</label>
                        <input type="text" name="name" value="{{ old('name', $employee->name) }}" required class="form-control" style="width: 100%;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #334155; margin-bottom: 0.25rem;">CPF</label>
                        <input type="text" name="cpf" value="{{ old('cpf', $employee->cpf) }}" class="form-control" style="width: 100%;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem;">
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #334155; margin-bottom: 0.25rem;">RG</label>
                        <input type="text" name="rg" value="{{ old('rg', $employee->rg) }}" class="form-control" style="width: 100%;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #334155; margin-bottom: 0.25rem;">Data Nascimento</label>
                        <input type="date" name="birth_date" value="{{ old('birth_date', optional($employee->birth_date)->format('Y-m-d')) }}" class="form-control" style="width: 100%;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #334155; margin-bottom: 0.25rem;">Telefone / Fone Contato</label>
                        <input type="text" name="contact_phone" value="{{ old('contact_phone', $employee->contact_phone) }}" class="form-control" style="width: 100%;">
                    </div>
                </div>
            </fieldset>

            <!-- Vínculo e Financeiro -->
            <fieldset style="margin-bottom: 1.5rem; padding: 1rem; border: 1px solid #e2e8f0; border-radius: 0.375rem; background: white;">
                <legend style="color: #475569; font-weight: bold; padding: 0.25rem 0.5rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 0.25rem; font-size: 0.875rem; text-transform: uppercase;">Contrato & Financeiro</legend>
                
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 1.5rem;">
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #334155; margin-bottom: 0.25rem;">Status Empregatício</label>
                        <select name="status" class="form-control" style="width: 100%;">
                            <option value="1" {{ $employee->status == 1 ? 'selected' : '' }}>🟢 Ativo</option>
                            <option value="0" {{ $employee->status == 0 ? 'selected' : '' }}>🔴 Inativo / Desligado</option>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #334155; margin-bottom: 0.25rem;">Cargo / Função</label>
                        <input type="text" name="role_description" value="{{ old('role_description', $employee->role_description) }}" class="form-control" style="width: 100%;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #334155; margin-bottom: 0.25rem;">Salário Base (Centavos) *</label>
                        <input type="number" name="base_salary_cents" required value="{{ old('base_salary_cents', $employee->base_salary_cents) }}" class="form-control" style="width: 100%;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #334155; margin-bottom: 0.25rem;">Dados Bancários</label>
                        <input type="text" name="bank_account_info" value="{{ old('bank_account_info', $employee->bank_account_info) }}" class="form-control" style="width: 100%;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #334155; margin-bottom: 0.25rem;">Chave Pix</label>
                        <input type="text" name="pix_key" value="{{ old('pix_key', $employee->pix_key) }}" class="form-control" style="width: 100%;">
                    </div>
                </div>
            </fieldset>

            <!-- Acesso ao Sistema -->
            <fieldset style="margin-bottom: 1.5rem; padding: 1rem; border: 1px solid #c7d2fe; border-radius: 0.375rem; background: #e0e7ff;">
                <legend style="color: #4f46e5; font-weight: bold; padding: 0.25rem 0.5rem; background: #c7d2fe; border: 1px solid #a5b4fc; border-radius: 0.25rem; font-size: 0.875rem; text-transform: uppercase;"><i class="fa fa-lock"></i> Acessos e Sistema</legend>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div style="background: white; padding: 0.75rem; border-radius: 0.25rem; border: 1px solid #c7d2fe;">
                        <label style="display: block; font-size: 0.875rem; font-weight: bold; color: #1e293b; margin-bottom: 0.25rem;">PIN de Acesso PDV</label>
                        <input type="password" name="pin" value="{{ old('pin', $employee->pin) }}" maxlength="8" class="form-control" style="width: 100%; text-align: center; font-family: monospace; letter-spacing: 0.5em;">
                    </div>
                    <div style="background: white; padding: 0.75rem; border-radius: 0.25rem; border: 1px solid #c7d2fe;">
                        <label style="display: block; font-size: 0.875rem; font-weight: bold; color: #1e293b; margin-bottom: 0.25rem;">Nível de Permissão WEB</label>
                        <select name="level" class="form-control" style="width: 100%; font-weight: bold;">
                            <option value="OPERATOR" {{ $employee->level === 'OPERATOR' ? 'selected' : '' }}>OPERATOR (Apenas Caixa)</option>
                            <option value="SUPERVISOR" {{ $employee->level === 'SUPERVISOR' ? 'selected' : '' }}>SUPERVISOR (Média Gestão)</option>
                            <option value="ADMIN" {{ $employee->level === 'ADMIN' ? 'selected' : '' }}>ADMIN (Acesso Total)</option>
                        </select>
                    </div>
                </div>
            </fieldset>

            <div style="display: flex; justify-content: flex-end; padding-top: 1rem; border-top: 1px solid #f1f5f9; margin-top: 2rem;">
                <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2rem; font-weight: bold; font-size: 1rem; background: #6366f1; border-color: #6366f1;">
                    💾 Atualizar Ficha Colaborador
                </button>
            </div>
        </div>
    </form>
</x-layouts.app>
