<x-layouts.app>
    <div class="p-6">
        <div class="mb-4 flex flex-wrap justify-between items-center border-b border-slate-200 pb-4" style="margin-bottom: 1.5rem; padding-bottom: 1rem;">
            <div>
                <h2 class="text-primary fw-bold" style="font-size: 1.75rem;">Marketing & CRM (Clube de Vantagens)</h2>
                <p class="text-light" style="margin-top: 0.25rem;">Gestão de Consumidores, Fidelidade e Automação de Retenção.</p>
            </div>
            <div class="flex gap-2">
                <button onclick="document.getElementById('crm-broadcast-modal').showModal()" class="btn btn-primary" style="background: #4f46e5; border-color: #4f46e5;">
                    <i class="fa fa-bullhorn" style="margin-right: 0.5rem;"></i> Disparo em Massa
                </button>
            </div>
        </div>

        <!-- Modal de Broadcast -->
        <dialog id="crm-broadcast-modal" style="padding: 0; border: none; border-radius: 0.75rem; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); width: 100%; max-width: 32rem; max-height: 90vh; position: fixed; inset: 0; margin: auto; z-index: 9999;">
            <style>
                #crm-broadcast-modal::backdrop {
                    background: rgba(15, 23, 42, 0.5);
                    backdrop-filter: blur(2px);
                }
            </style>
            <form action="{{ route('crm.customers.broadcast') }}" method="POST">
                @csrf
                <div style="padding: 1.5rem; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; z-index: 10;">
                    <h3 style="font-size: 1.125rem; font-weight: bold; color: #1e293b; margin: 0;">Disparo de E-mail/WhatsApp</h3>
                    <button type="button" onclick="document.getElementById('crm-broadcast-modal').close()" style="background: none; border: none; cursor: pointer; color: #94a3b8; font-size: 1.5rem; font-weight: bold; line-height: 1;">&times;</button>
                </div>
                <div style="padding: 1.5rem; display: flex; flex-direction: column; gap: 1rem;">
                    <div>
                        <label style="display: block; text-transform: uppercase; font-size: 0.75rem; font-weight: bold; color: #475569; margin-bottom: 0.5rem;">Público-Alvo (Audiência)</label>
                        <select name="audience" required class="form-control" style="width: 100%;">
                            <option value="ALL" {{ old('audience') == 'ALL' ? 'selected' : '' }}>Todo Mundo (Promoção Geral)</option>
                            <option value="INACTIVE" {{ old('audience') == 'INACTIVE' ? 'selected' : '' }}>Inativos (Não compram há 60 dias)</option>
                        </select>
                        @error('audience')
                            <div style="color: #e11d48; font-size: 0.75rem; margin-top: 0.25rem; font-weight: bold;">{{ $message }}</div>
                        @enderror
                    </div>
                    <div style="margin-top: 1rem;">
                        <label style="display: block; text-transform: uppercase; font-size: 0.75rem; font-weight: bold; color: #475569; margin-bottom: 0.5rem;">Mensagem da Campanha</label>
                        <textarea name="message" required rows="4" placeholder="Ex: Estamos com saudades! Use o cupom VOLTA20..." class="form-control" style="width: 100%;">{{ old('message') }}</textarea>
                        @error('message')
                            <div style="color: #e11d48; font-size: 0.75rem; margin-top: 0.25rem; font-weight: bold;">Atenção: {{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 0.5rem; padding: 1rem 1.5rem; border-top: 1px solid #e2e8f0; background: #f8fafc;">
                    <button type="button" onclick="document.getElementById('crm-broadcast-modal').close()" class="btn btn-outline" style="background: white; border-color: #cbd5e1; color: #475569;">Cancelar</button>
                    <button type="submit" class="btn btn-primary" style="background: #4f46e5; border-color: #4f46e5; font-weight: bold;"><i class="fa fa-paper-plane" style="margin-right: 0.5rem;"></i> Lançar Campanha</button>
                </div>
            </form>
        </dialog>

        @if($errors->any())
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    document.getElementById('crm-broadcast-modal').showModal();
                });
            </script>
        @endif

        @if(session('success'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    if (window.Swal) {
                        window.Swal.fire({
                            title: 'Sucesso!',
                            text: '{{ session('success') }}',
                            icon: 'success',
                            confirmButtonColor: '#4f46e5'
                        });
                    } else {
                        alert('{{ session('success') }}');
                    }
                });
            </script>
        @endif

        <div class="card bg-white border-0 shadow-sm p-0 overflow-hidden" style="border-radius: 0.75rem;">
            <div style="overflow-x: auto; padding: 1.5rem;">
                <table class="display responsive nowrap w-100" id="crm-customers-table" style="width: 100%; text-align: left; border-collapse: collapse;">
                    <thead>
                        <tr style="background-color: #f8fafc; border-bottom: 1px solid #e2e8f0; color: #64748b; font-size: 0.875rem;">
                            <th style="padding: 1rem; text-align: left;">Cliente</th>
                            <th style="padding: 1rem; text-align: left;">Contato</th>
                            <th style="padding: 1rem; text-align: left;">Pontos (Nível)</th>
                            <th style="padding: 1rem; text-align: left;">Última Compra</th>
                        </tr>
                    </thead>
                    <!-- As linhas de clientes serão desenhadas dinamicamente pelo TS -->
                </table>
            </div>
        </div>
    </div>

    <!-- Inicializador Server-Side Datatable -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const initCrmTable = () => {
                if (typeof window.AppServerTable !== 'function') {
                    setTimeout(initCrmTable, 100);
                    return;
                }
                
                new window.AppServerTable('#crm-customers-table', '{{ route('crm.customers.datatable') }}', [
                    { data: 'cliente', name: 'name', searchable: true },
                    { data: 'contato', name: 'email', searchable: true },
                    { data: 'pontos_html', searchable: false, orderable: false },
                    { data: 'data_html', searchable: false, orderable: false }
                ], [[0, 'asc']]); // Ordenar por NOME padrão
            };
            initCrmTable();
        });
    </script>
</x-layouts.app>
