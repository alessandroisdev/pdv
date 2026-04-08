<x-layouts.app>
    <div class="mb-6 flex justify-between items-end border-b border-slate-200 pb-4">
        <div>
            <h2 class="text-2xl fw-bold text-slate-800">Editar Ficha do Colaborador</h2>
            <p class="text-slate-500">Alterar dados cadastrais e acessos do funcionário #{{ $employee->id }}</p>
        </div>
        <a href="{{ route('hr.employees.index') }}" class="btn btn-outline border-slate-300 text-slate-600 hover:bg-slate-50 font-semibold px-4 py-2 rounded-lg flex items-center gap-2">
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

        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
            <!-- Dados Pessoais -->
            <fieldset class="mb-6 border border-slate-200 p-4 rounded-lg">
                <legend class="bg-slate-50 px-2 py-1 font-bold text-slate-600 text-sm uppercase rounded">Dados Pessoais</legend>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Nome Completo *</label>
                        <input type="text" name="name" value="{{ old('name', $employee->name) }}" required class="form-control w-full bg-slate-50 focus:bg-white">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">CPF</label>
                        <input type="text" name="cpf" value="{{ old('cpf', $employee->cpf) }}" class="form-control w-full bg-slate-50 focus:bg-white">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">RG</label>
                        <input type="text" name="rg" value="{{ old('rg', $employee->rg) }}" class="form-control w-full bg-slate-50 focus:bg-white">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Data Nascimento</label>
                        <input type="date" name="birth_date" value="{{ old('birth_date', optional($employee->birth_date)->format('Y-m-d')) }}" class="form-control w-full bg-slate-50 focus:bg-white">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Telefone / Fone Contato</label>
                        <input type="text" name="contact_phone" value="{{ old('contact_phone', $employee->contact_phone) }}" class="form-control w-full bg-slate-50 focus:bg-white">
                    </div>
                </div>
            </fieldset>

            <!-- Vínculo e Financeiro -->
            <fieldset class="mb-6 border border-slate-200 p-4 rounded-lg">
                <legend class="bg-slate-50 px-2 py-1 font-bold text-slate-600 text-sm uppercase rounded">Contrato & Financeiro</legend>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Status Empregatício</label>
                        <select name="status" class="form-control w-full bg-slate-50 focus:bg-white">
                            <option value="1" {{ $employee->status == 1 ? 'selected' : '' }}>🟢 Ativo</option>
                            <option value="0" {{ $employee->status == 0 ? 'selected' : '' }}>🔴 Inativo / Desligado</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Cargo / Função</label>
                        <input type="text" name="role_description" value="{{ old('role_description', $employee->role_description) }}" class="form-control w-full bg-slate-50 focus:bg-white">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Salário Base (Centavos) *</label>
                        <input type="number" name="base_salary_cents" required value="{{ old('base_salary_cents', $employee->base_salary_cents) }}" class="form-control w-full bg-slate-50 focus:bg-white">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Dados Bancários</label>
                        <input type="text" name="bank_account_info" value="{{ old('bank_account_info', $employee->bank_account_info) }}" class="form-control w-full bg-slate-50 focus:bg-white">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Chave Pix</label>
                        <input type="text" name="pix_key" value="{{ old('pix_key', $employee->pix_key) }}" class="form-control w-full bg-slate-50 focus:bg-white">
                    </div>
                </div>
            </fieldset>

            <!-- Acesso ao Sistema -->
            <fieldset class="mb-6 border border-indigo-100 bg-indigo-50/30 p-4 rounded-lg">
                <legend class="bg-indigo-100 px-2 py-1 font-bold text-indigo-700 text-sm uppercase rounded"><i class="fa fa-lock"></i> Acessos e Sistema</legend>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-800 mb-1">PIN de Acesso PDV</label>
                        <input type="password" name="pin" value="{{ old('pin', $employee->pin) }}" maxlength="8" class="form-control w-full tracking-[0.5em] font-mono text-center shadow-inner">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-800 mb-1">Nível de Permissão WEB</label>
                        <select name="level" class="form-control w-full font-semibold">
                            <option value="OPERATOR" {{ $employee->level === 'OPERATOR' ? 'selected' : '' }}>OPERATOR (Apenas Caixa)</option>
                            <option value="SUPERVISOR" {{ $employee->level === 'SUPERVISOR' ? 'selected' : '' }}>SUPERVISOR (Média Gestão)</option>
                            <option value="ADMIN" {{ $employee->level === 'ADMIN' ? 'selected' : '' }}>ADMIN (Acesso Total)</option>
                        </select>
                    </div>
                </div>
            </fieldset>

            <div class="flex justify-end pt-4 border-t border-slate-100">
                <button type="submit" class="btn btn-primary bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-8 rounded-lg shadow-md transform hover:-translate-y-0.5 transition-all">
                    💾 Atualizar Ficha Colaborador
                </button>
            </div>
        </div>
    </form>
</x-layouts.app>
