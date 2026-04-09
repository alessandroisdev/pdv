<x-layouts.app>
    <div class="p-6">
        <div class="mb-4 flex flex-wrap justify-between items-center border-b border-slate-200 pb-4" style="margin-bottom: 1.5rem; padding-bottom: 1rem;">
            <div>
                <h2 class="text-primary fw-bold" style="font-size: 1.75rem;">Árvore de Categorias</h2>
                <p class="text-light" style="margin-top: 0.25rem;">Organização e Classificação de Produtos no Painel Fiscal e PDV.</p>
            </div>
            <div class="flex gap-2">
                <button onclick="document.getElementById('inventory-category-modal').showModal(); document.getElementById('cat-form').action='{{ route('inventory.categories.store') }}'; document.getElementById('cat-method').value='POST'; document.getElementById('cat-id').value=''; document.getElementById('cat-name').value=''; document.getElementById('cat-parent').value='';" class="btn btn-primary" style="background: #10b981; border-color: #10b981;">
                    <i class="fa fa-plus" style="margin-right: 0.5rem;"></i> Nova Categoria
                </button>
            </div>
        </div>

        @if(session('success'))
            <div style="background: #ecfdf5; color: #047857; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem; border: 1px solid #10b981;">
                <strong>Sucesso:</strong> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div style="background: #fef2f2; color: #b91c1c; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem; border: 1px solid #fecaca;">
                <strong>Impedido:</strong> {{ session('error') }}
            </div>
        @endif
        @if($errors->any())
            <div style="background: #fef2f2; color: #b91c1c; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem; border: 1px solid #fecaca;">
                <ul>
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card bg-white border-0 shadow-sm p-0 overflow-hidden" style="border-radius: 0.75rem;">
            <x-ui.table>
                <x-slot name="head">
                    <tr>
                        <th class="p-4 text-left font-semibold" style="padding: 1rem;">ID</th>
                        <th class="p-4 text-left font-semibold" style="padding: 1rem;">Nome da Categoria</th>
                        <th class="p-4 text-left font-semibold" style="padding: 1rem;">Pertence À (Categoria Pai)</th>
                        <th class="p-4 text-right font-semibold" style="padding: 1rem; width: 120px;">Ações</th>
                    </tr>
                </x-slot>
                <x-slot name="body">
                    @forelse($categories as $category)
                        <tr class="border-b transition hover:bg-slate-50" style="border-bottom: 1px solid #f1f5f9;">
                            <td class="p-4 text-slate-500" style="padding: 1rem;">#{{ $category->id }}</td>
                            <td class="p-4 font-bold text-slate-800" style="padding: 1rem; color: #1e293b;">
                                {{ $category->name }}
                            </td>
                            <td class="p-4 text-slate-600" style="padding: 1rem;">
                                @if($category->parent)
                                    <span style="background: #e0f2fe; color: #0284c7; padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.75rem; font-weight: bold;">
                                        {{ $category->parent->name }}
                                    </span>
                                @else
                                    <span style="color: #94a3b8; font-style: italic; font-size: 0.85rem;">Categoria Raiz Principal</span>
                                @endif
                            </td>
                            <td class="p-4 text-right" style="padding: 1rem; display: flex; justify-content: flex-end; gap: 0.5rem;">
                                <button onclick="editCat({{ $category->id }}, '{{ addslashes($category->name) }}', '{{ $category->parent_id }}')" class="btn btn-sm btn-outline" style="border-color: #cbd5e1; color: #475569;" title="Editar Categoria">
                                    Editar
                                </button>
                                
                                <form action="{{ route('inventory.categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Tem certeza? Isso pode afetar o cadastro de produtos que usam esta categoria. Se houver produtos, o sistema impedirá a exclusão.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm" style="background: #fee2e2; color: #ef4444; border: 1px solid #fecaca;" title="Excluir Categoria">
                                        Excluir
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-8 text-center text-slate-500" style="padding: 2rem; text-align: center; color: #64748b;">
                                <div class="text-4xl mb-4 text-slate-300" style="font-size: 2.25rem; margin-bottom: 1rem; color: #cbd5e1;"><i class="fa fa-tags"></i></div>
                                <p>Nenhuma categoria de produtos cadastrada.</p>
                            </td>
                        </tr>
                    @endforelse
                </x-slot>
            </x-ui.table>
        </div>

        <!-- Modal de Gerenciamento -->
        <dialog id="inventory-category-modal" style="padding: 0; border: none; border-radius: 0.75rem; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); width: 100%; max-width: 32rem; position: fixed; inset: 0; margin: auto; z-index: 9999;">
            <style>
                #inventory-category-modal::backdrop {
                    background: rgba(15, 23, 42, 0.5);
                    backdrop-filter: blur(2px);
                }
            </style>
            <form id="cat-form" action="{{ route('inventory.categories.store') }}" method="POST">
                @csrf
                <input type="hidden" name="_method" id="cat-method" value="POST">
                <input type="hidden" id="cat-id" value="">
                
                <div style="padding: 1.5rem; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; z-index: 10;">
                    <h3 style="font-size: 1.125rem; font-weight: bold; color: #1e293b; margin: 0;">Ficha da Categoria</h3>
                    <button type="button" onclick="document.getElementById('inventory-category-modal').close()" style="background: none; border: none; cursor: pointer; color: #94a3b8; font-size: 1.5rem; font-weight: bold; line-height: 1;">&times;</button>
                </div>
                
                <div style="padding: 1.5rem; display: flex; flex-direction: column; gap: 1rem;">
                    <div>
                        <label style="display: block; text-transform: uppercase; font-size: 0.75rem; font-weight: bold; color: #475569; margin-bottom: 0.5rem;">Nome da Categoria</label>
                        <input type="text" name="name" id="cat-name" required placeholder="Ex: Bebidas Quentes, Roupas, Higiene..." class="form-control hover-border transition" style="width: 100%; padding: 0.75rem;">
                    </div>
                    
                    <div>
                        <label style="display: block; text-transform: uppercase; font-size: 0.75rem; font-weight: bold; color: #475569; margin-bottom: 0.5rem;">Vincular como Sub-Categoria de:</label>
                        <select name="parent_id" id="cat-parent" class="form-control hover-border transition" style="width: 100%; padding: 0.75rem;">
                            <option value="">-- Categoria Principal (Raiz) --</option>
                            @foreach($categories as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                        <p style="font-size: 0.7rem; color: #94a3b8; margin-top: 0.35rem;">* Se for um Setor Principal, deixe como Raiz.</p>
                    </div>
                </div>
                
                <div style="display: flex; justify-content: flex-end; gap: 0.5rem; padding: 1rem 1.5rem; border-top: 1px solid #e2e8f0; background: #f8fafc;">
                    <button type="button" onclick="document.getElementById('inventory-category-modal').close()" class="btn btn-outline" style="background: white; border-color: #cbd5e1; color: #475569;">Cancelar</button>
                    <button type="submit" class="btn btn-primary" style="background: #4f46e5; border-color: #4f46e5; font-weight: bold;"><i class="fa fa-save" style="margin-right: 0.5rem;"></i> Salvar Categoria</button>
                </div>
            </form>
        </dialog>
    </div>

    <script>
        function editCat(id, name, parentId) {
            document.getElementById('cat-form').action = '/estoque/categorias/' + id;
            document.getElementById('cat-method').value = 'PUT';
            document.getElementById('cat-id').value = id;
            document.getElementById('cat-name').value = name;
            
            if(parentId) {
                document.getElementById('cat-parent').value = parentId;
            } else {
                document.getElementById('cat-parent').value = '';
            }

            document.getElementById('inventory-category-modal').showModal();
        }
    </script>
</x-layouts.app>
