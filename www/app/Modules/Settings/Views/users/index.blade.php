<x-layouts.app>
    <div class="p-6">
        <div class="mb-4 border-b border-slate-200 pb-4 flex justify-between items-end" style="margin-bottom: 2rem;">
            <div>
                <h2 class="text-primary fw-bold" style="font-size: 1.75rem;">Controle de Usuários e Permissões</h2>
                <p class="text-light" style="margin-top: 0.25rem;">Gestão de caixistas, gerentes e administradores do ERP.</p>
            </div>
            
            <div>
                <button type="button" onclick="document.getElementById('modal-new-user').showModal()" class="btn shadow" style="background: #4f46e5; border: none; color: white; padding: 0.75rem 1.25rem; font-weight: bold; border-radius: 0.5rem; cursor: pointer;">
                    <i class="fa fa-user-plus mr-2"></i> Novo Usuário
                </button>
            </div>
        </div>

        <div class="card bg-white border-0 shadow-sm p-0 overflow-hidden" style="border-radius: 0.75rem;">
            <x-ui.table>
                <x-slot name="head">
                    <tr>
                        <th class="p-4 text-left font-semibold" style="padding: 1rem;">Identificação</th>
                        <th class="p-4 text-left font-semibold" style="padding: 1rem;">E-mail</th>
                        <th class="p-4 text-left font-semibold" style="padding: 1rem;">Hierarquia</th>
                        <th class="p-4 text-right font-semibold" style="padding: 1rem;">Ações</th>
                    </tr>
                </x-slot>
                <x-slot name="body">
                    @foreach($users as $u)
                        <tr class="border-b transition hover:bg-slate-50" style="border-bottom: 1px solid #f1f5f9;">
                            <td class="p-4" style="padding: 1rem;">
                                <div style="font-weight: bold; color: #1e293b;">{{ $u->name }}</div>
                                <div style="font-size: 0.75rem; color: #94a3b8;">Desde {{ $u->created_at->format('d/m/Y') }}</div>
                            </td>
                            <td class="p-4 text-slate-600" style="padding: 1rem;">
                                {{ $u->email }}
                            </td>
                            <td class="p-4" style="padding: 1rem;">
                                @if($u->role === 'MANAGER')
                                    <span style="display: inline-flex; align-items: center; padding: 0.25rem 0.5rem; border-radius: 9999px; font-size: 0.7rem; font-weight: bold; background: #e0e7ff; color: #4338ca;">
                                        <i class="fa fa-shield-alt mr-1"></i> GESTOR
                                    </span>
                                @else
                                    <span style="display: inline-flex; align-items: center; padding: 0.25rem 0.5rem; border-radius: 9999px; font-size: 0.7rem; font-weight: bold; background: #f1f5f9; color: #475569;">
                                        CAIXISTA
                                    </span>
                                @endif
                            </td>
                            <td class="p-4 text-right" style="padding: 1rem; text-align: right; white-space: nowrap;">
                                <div style="display: flex; justify-content: flex-end; gap: 0.5rem; align-items: center;">
                                    <button type="button" onclick="document.getElementById('modal-edit-user-{{ $u->id }}').showModal()" style="background: white; border: 1px solid #4338ca; color: #4338ca; padding: 0.35rem 0.75rem; border-radius: 0.35rem; font-size: 0.75rem; font-weight: bold; cursor: pointer; transition: all 0.2s;" title="Editar">
                                        Editar
                                    </button>

                                    @if($u->id !== auth()->id())
                                        <form action="{{ route('settings.users.destroy', $u) }}" method="POST" style="margin: 0;" onsubmit="return confirm('Deseja excluir permanentemente este usuário? Removê-lo impedirá que o mesmo acesse o PDV.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" style="background: white; border: 1px solid #ef4444; color: #ef4444; padding: 0.35rem 0.75rem; border-radius: 0.35rem; font-size: 0.75rem; font-weight: bold; cursor: pointer; transition: all 0.2s;" title="Bloquear / Remover">
                                                Remover
                                            </button>
                                        </form>
                                    @else
                                        <span style="font-size: 0.75rem; color: #10b981; font-weight: bold; border: 1px solid #10b981; background: #ecfdf5; padding: 0.35rem 0.75rem; border-radius: 0.35rem; margin-left: 0.5rem;">
                                            Sessão Atual
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        <!-- Modal Edit User -->
                        <dialog id="modal-edit-user-{{ $u->id }}" style="padding: 0; border: none; border-radius: 0.75rem; background: transparent; width: 100%; max-width: 32rem; position: fixed; inset: 0; margin: auto; z-index: 1050; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);">
                            <!-- Backdrop Blur Fix -->
                            <div style="position: fixed; inset: 0; background: rgba(15, 23, 42, 0.7); backdrop-filter: blur(4px); z-index: -1;" onclick="document.getElementById('modal-edit-user-{{ $u->id }}').close()"></div>
                            
                            <div style="background: white; border-radius: 0.75rem; overflow: hidden; width: 100%;">
                                <form action="{{ route('settings.users.update', $u) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div style="padding: 1.25rem; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
                                        <h3 style="font-size: 1.125rem; font-weight: bold; color: #1e293b; margin: 0;">Editar Conta: {{ explode(' ', $u->name)[0] }}</h3>
                                        <button type="button" onclick="document.getElementById('modal-edit-user-{{ $u->id }}').close()" style="background: transparent; border: none; color: #94a3b8; cursor: pointer; font-size: 1.25rem;"><i class="fa fa-times"></i></button>
                                    </div>
                                    
                                    <div style="padding: 1.5rem; display: flex; flex-direction: column; gap: 1rem; text-align: left;">
                                        <div>
                                            <label style="display: block; font-size: 0.875rem; font-weight: bold; color: #475569; margin-bottom: 0.5rem;">Nome Completo</label>
                                            <input type="text" name="name" value="{{ $u->name }}" required class="form-control" style="width: 100%; padding: 0.75rem;">
                                        </div>
                                        <div>
                                            <label style="display: block; font-size: 0.875rem; font-weight: bold; color: #475569; margin-bottom: 0.5rem;">E-mail de Login</label>
                                            <input type="email" name="email" value="{{ $u->email }}" required class="form-control" style="width: 100%; padding: 0.75rem;">
                                        </div>
                                        <div>
                                            <label style="display: block; font-size: 0.875rem; font-weight: bold; color: #475569; margin-bottom: 0.5rem;">Nova Senha <span style="font-size: 0.7rem; color: #94a3b8; font-weight: normal;">(Opcional: preencha para alterar)</span></label>
                                            <input type="password" name="password" class="form-control" style="width: 100%; padding: 0.75rem;">
                                        </div>
                                        <div>
                                            <label style="display: block; font-size: 0.875rem; font-weight: bold; color: #475569; margin-bottom: 0.5rem;">Nível de Acesso (Papel)</label>
                                            <select name="role" required class="form-control" style="width: 100%; padding: 0.75rem;">
                                                <option value="CASHIER" {{ $u->role == 'CASHIER' ? 'selected' : '' }}>Caixista / Operador de Loja</option>
                                                <option value="MANAGER" {{ $u->role == 'MANAGER' ? 'selected' : '' }}>Gestor Administrativo (Configurações)</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div style="padding: 1.25rem; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; gap: 0.5rem;">
                                        <button type="button" onclick="document.getElementById('modal-edit-user-{{ $u->id }}').close()" class="btn" style="background: white; border: 1px solid #cbd5e1; color: #475569; padding: 0.5rem 1rem; border-radius: 0.5rem; font-weight: bold; cursor: pointer;">Cancelar</button>
                                        <button type="submit" class="btn shadow" style="background: #4f46e5; border: none; color: white; padding: 0.5rem 1rem; border-radius: 0.5rem; font-weight: bold; cursor: pointer;">Salvar Modificações</button>
                                    </div>
                                </form>
                            </div>
                        </dialog>
                    @endforeach
                </x-slot>
            </x-ui.table>
            
            @if($users->hasPages())
                <div class="p-4" style="padding: 1rem; border-top: 1px solid #e2e8f0; background: #f8fafc;">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Modal Novo User -->
    <dialog id="modal-new-user" style="padding: 0; border: none; border-radius: 0.75rem; background: transparent; width: 100%; max-width: 32rem; position: fixed; inset: 0; margin: auto; z-index: 1050; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);">
        <!-- Backdrop Blur Fix -->
        <div style="position: fixed; inset: 0; background: rgba(15, 23, 42, 0.7); backdrop-filter: blur(4px); z-index: -1;" onclick="document.getElementById('modal-new-user').close()"></div>
        
        <div style="background: white; border-radius: 0.75rem; overflow: hidden; width: 100%;">
            <form action="{{ route('settings.users.store') }}" method="POST">
                @csrf
                <div style="padding: 1.25rem; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
                    <h3 style="font-size: 1.125rem; font-weight: bold; color: #1e293b; margin: 0;">Adicionar Usuário ao Sistema</h3>
                    <button type="button" onclick="document.getElementById('modal-new-user').close()" style="background: transparent; border: none; color: #94a3b8; cursor: pointer; font-size: 1.25rem;"><i class="fa fa-times"></i></button>
                </div>
                
                <div style="padding: 1.5rem; display: flex; flex-direction: column; gap: 1rem;">
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: bold; color: #475569; margin-bottom: 0.5rem;">Nome Completo</label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="form-control" style="width: 100%; padding: 0.75rem;">
                        @error('name')<div style="color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: bold; color: #475569; margin-bottom: 0.5rem;">E-mail de Login</label>
                        <input type="email" name="email" value="{{ old('email') }}" required class="form-control" style="width: 100%; padding: 0.75rem;">
                        @error('email')<div style="color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: bold; color: #475569; margin-bottom: 0.5rem;">Senha Inicial Padrão</label>
                        <input type="password" name="password" required class="form-control" style="width: 100%; padding: 0.75rem;">
                        @error('password')<div style="color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: bold; color: #475569; margin-bottom: 0.5rem;">Nível de Acesso (Papel)</label>
                        <select name="role" required class="form-control" style="width: 100%; padding: 0.75rem;">
                            <option value="CASHIER">Caixista / Operador de Loja</option>
                            <option value="MANAGER">Gestor Administrativo (Configurações)</option>
                        </select>
                        @error('role')<div style="color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</div>@enderror
                    </div>
                </div>
                
                <div style="padding: 1.25rem; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; gap: 0.5rem;">
                    <button type="button" onclick="document.getElementById('modal-new-user').close()" class="btn" style="background: white; border: 1px solid #cbd5e1; color: #475569; padding: 0.5rem 1rem; border-radius: 0.5rem; font-weight: bold; cursor: pointer;">Cancelar</button>
                    <button type="submit" class="btn shadow" style="background: #4f46e5; border: none; color: white; padding: 0.5rem 1rem; border-radius: 0.5rem; font-weight: bold; cursor: pointer;">Criar Usuário</button>
                </div>
            </form>
        </div>
    </dialog>

    <!-- Handle validation errors reopening modal -->
    @if($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('modal-new-user').showModal();
        });
    </script>
    @endif
</x-layouts.app>
