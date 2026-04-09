<x-layouts.app>
    <div class="p-6">
        <div class="mb-4 border-b border-slate-200 pb-4" style="margin-bottom: 1.5rem;">
            <h2 class="text-primary fw-bold" style="font-size: 1.75rem;">Auditoria Global & Segurança</h2>
            <p class="text-light" style="margin-top: 0.25rem;">Rastreabilidade Corporativa, Transações e Registros do Sistema.</p>
        </div>

        <!-- Filtros de Busca Avançada -->
        <div class="card bg-white border-0 shadow-sm" style="padding: 1.5rem; margin-bottom: 2rem; border-radius: 0.75rem;">
            <form action="{{ route('audit.index') }}" method="GET">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; align-items: end;">
                    
                    <!-- Busca Livre -->
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: bold; color: #475569; text-transform: uppercase; margin-bottom: 0.5rem;">Buscar por Palavra</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Conteúdo JSON, Nome, IP..." class="form-control hover-border transition">
                    </div>

                    <!-- Módulo/Tipo -->
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: bold; color: #475569; text-transform: uppercase; margin-bottom: 0.5rem;">Módulo Alvo</label>
                        <select name="type" class="form-control hover-border transition">
                            <option value="">-- Todos --</option>
                            @foreach($models as $model)
                                <option value="{{ $model }}" {{ request('type') == $model ? 'selected' : '' }}>{{ class_basename($model) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Evento -->
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: bold; color: #475569; text-transform: uppercase; margin-bottom: 0.5rem;">Ação / Evento</label>
                        <select name="event" class="form-control hover-border transition">
                            <option value="">-- Todos --</option>
                            @foreach($eventTypes as $evt)
                                <option value="{{ $evt }}" {{ request('event') == $evt ? 'selected' : '' }}>{{ ucfirst($evt) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Período Start -->
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: bold; color: #475569; text-transform: uppercase; margin-bottom: 0.5rem;">Data Desde</label>
                        <input type="date" name="date_start" value="{{ request('date_start') }}" class="form-control hover-border transition">
                    </div>

                    <!-- Período End -->
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: bold; color: #475569; text-transform: uppercase; margin-bottom: 0.5rem;">Data Até</label>
                        <input type="date" name="date_end" value="{{ request('date_end') }}" class="form-control hover-border transition">
                    </div>

                    <div style="display: flex; gap: 0.5rem;">
                        <button type="submit" class="btn text-white w-full shadow cursor-pointer transition-transform" style="background: #4f46e5; border-color: #4f46e5; flex: 1;"><i class="fa fa-search mr-2"></i> Filtrar</button>
                        <a href="{{ route('audit.index') }}" class="btn cursor-pointer transition-colors" style="background: white; border: 1px solid #cbd5e1; color: #475569; display: flex; align-items: center; justify-content: center;" title="Limpar"><i class="fa fa-times"></i></a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Grade de Logs -->
        <div class="card bg-white border-0 shadow-sm p-0 overflow-hidden" style="border-radius: 0.75rem;">
            <div style="overflow-x: auto; padding: 1.5rem;">
                <table class="display responsive nowrap w-100" id="audit-logs-table" style="width: 100%; text-align: left; border-collapse: collapse;">
                    <thead>
                        <tr style="background-color: #f8fafc; border-bottom: 1px solid #e2e8f0; color: #64748b; font-size: 0.875rem;">
                            <th style="padding: 1rem; text-align: left;">Data/Hora</th>
                            <th style="padding: 1rem; text-align: left;">Ator</th>
                            <th style="padding: 1rem; text-align: left;">Ação</th>
                            <th style="padding: 1rem; text-align: left;">Módulo Afetado</th>
                            <th style="padding: 1rem; text-align: left; width: 40%;">Diferença (Valores)</th>
                        </tr>
                    </thead>
                </table>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const initAuditTable = () => {
                        if (typeof window.AppServerTable !== 'function') {
                            setTimeout(initAuditTable, 100);
                            return;
                        }

                        const urlParams = new URLSearchParams(window.location.search);
                        let queryStr = '';
                        if(urlParams.toString().length > 0) {
                            queryStr = '?' + urlParams.toString();
                        }
                        
                        const ajaxUrl = '{{ route('audit.datatable') }}' + queryStr;

                        new window.AppServerTable('#audit-logs-table', ajaxUrl, [
                            { data: 'datahora', name: 'created_at', searchable: false },
                            { data: 'ator', searchable: false, orderable: false },
                            { data: 'acao', name: 'event', searchable: false },
                            { data: 'modulo', name: 'auditable_type', searchable: false },
                            { data: 'diff', searchable: false, orderable: false, className: 'text-left' }
                        ], [[0, 'desc']]); // Ordenar por Data (mais recentes) padrão
                    };
                    initAuditTable();
                });
            </script>
        </div>
    </div>
</x-layouts.app>
