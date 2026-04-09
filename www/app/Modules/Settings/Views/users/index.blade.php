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
            <div style="overflow-x: auto; padding: 1.5rem;">
                <table class="display w-100" id="settings-users-table" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid #e2e8f0; text-align: left; color: #64748b;">
                            <th style="padding: 1rem;">Identificação</th>
                            <th style="padding: 1rem;">E-mail</th>
                            <th style="padding: 1rem;">Hierarquia</th>
                            <th style="padding: 1rem; text-align: right;">Ações</th>
                        </tr>
                    </thead>
                </table>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const initUsersTable = () => {
                        if (typeof window.AppServerTable !== 'function') {
                            setTimeout(initUsersTable, 100);
                            return;
                        }
                        
                        new window.AppServerTable('#settings-users-table', '{{ route('settings.users.datatable') }}', [
                            { data: 'm_name', name: 'name', searchable: true },
                            { data: 'email', searchable: true },
                            { data: 'role', searchable: false },
                            { data: 'acoes', searchable: false, orderable: false, className: 'text-right' }
                        ], [[0, 'asc']]);
                    };
                    initUsersTable();
                });
            </script>

            <!-- Render modals explicitly so JS logic can show them (Note: the list of modais requires a DB query anyway if not rendered via Ajax) -->
            @php $allUsers = \App\Models\User::all(); @endphp
            @foreach($allUsers as $u)
                <!-- Modal Edit User -->
                <dialog id="modal-edit-user-{{ $u->id }}" style="padding: 0; border: none; border-radius: 0.75rem; background: transparent; width: 100%; max-width: 32rem; position: fixed; inset: 0; margin: auto; z-index: 1050; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);">
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
                                    <label style="display: block; font-size: 0.875rem; font-weight: bold; color: #475569; margin-bottom: 0.5rem;">Nova Senha <span style="font-size: 0.7rem; color: #94a3b8; font-weight: normal;">(Opcional)</span></label>
                                    <input type="password" name="password" class="form-control" style="width: 100%; padding: 0.75rem;">
                                </div>
                                <div>
                                    <label style="display: block; font-size: 0.875rem; font-weight: bold; color: #475569; margin-bottom: 0.5rem;">Nível de Acesso</label>
                                    <select name="role" required class="form-control" style="width: 100%; padding: 0.75rem;">
                                        <option value="CASHIER" {{ $u->role == 'CASHIER' ? 'selected' : '' }}>Caixista / Operador de Loja</option>
                                        <option value="MANAGER" {{ $u->role == 'MANAGER' ? 'selected' : '' }}>Gestor Administrativo</option>
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
