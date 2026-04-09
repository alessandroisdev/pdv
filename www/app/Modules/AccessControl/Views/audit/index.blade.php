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
            <x-ui.table>
                <x-slot name="head">
                    <tr>
                        <th class="p-4 text-left font-semibold" style="padding: 1rem;">Data/Hora</th>
                        <th class="p-4 text-left font-semibold" style="padding: 1rem;">Ator</th>
                        <th class="p-4 text-left font-semibold" style="padding: 1rem;">Ação</th>
                        <th class="p-4 text-left font-semibold" style="padding: 1rem;">Módulo Afetado</th>
                        <th class="p-4 text-left font-semibold" style="padding: 1rem; width: 40%;">Diferença (Valores)</th>
                    </tr>
                </x-slot>
                <x-slot name="body">
                    @forelse($audits as $audit)
                        <tr class="border-b transition hover:bg-slate-50" style="border-bottom: 1px solid #f1f5f9;">
                            <td class="p-4" style="padding: 1rem; font-size: 0.85rem; color: #64748b; white-space: nowrap;">
                                <div style="font-weight: bold; color: #1e293b;">{{ $audit->created_at->format('d/m/Y') }}</div>
                                <div>{{ $audit->created_at->format('H:i:s') }}</div>
                            </td>
                            <td class="p-4" style="padding: 1rem;">
                                <div style="font-weight: bold; color: #0f172a; display: flex; align-items: center; gap: 0.5rem;">
                                    <i class="fa fa-user-circle" style="color: #94a3b8; font-size: 1.25rem;"></i> 
                                    {{ $audit->user->name ?? 'Sistema / Externo' }}
                                </div>
                                <div style="font-size: 0.7rem; color: #94a3b8; font-family: monospace; margin-top: 0.25rem;">IP: {{ $audit->ip_address }}</div>
                            </td>
                            <td class="p-4" style="padding: 1rem;">
                                @php
                                    $color = match($audit->event) {
                                        'created' => '#10b981', // green
                                        'updated' => '#f59e0b', // orange
                                        'deleted' => '#ef4444', // red
                                        default => '#64748b'
                                    };
                                    $eventLabel = match($audit->event) {
                                        'created' => 'Criação',
                                        'updated' => 'Atualização',
                                        'deleted' => 'Exclusão',
                                        default => ucfirst($audit->event)
                                    };
                                @endphp
                                <span style="background: {{ $color }}15; color: {{ $color }}; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.7rem; font-weight: bold; border: 1px solid {{ $color }}40; display: inline-block;">
                                    {{ mb_strtoupper($eventLabel, 'UTF-8') }}
                                </span>
                            </td>
                            <td class="p-4" style="padding: 1rem;">
                                <div style="font-size: 0.85rem; font-weight: bold; color: #334155;">{{ class_basename($audit->auditable_type) }}</div>
                                <div style="font-size: 0.75rem; color: #94a3b8;"># ID: {{ $audit->auditable_id }}</div>
                            </td>
                            <td class="p-4" style="padding: 1rem; font-size: 0.8rem;">
                                @if(count($audit->old_values) > 0 || count($audit->new_values) > 0)
                                    <div style="background: #1e293b; color: #e2e8f0; padding: 0.75rem; border-radius: 0.5rem; font-family: monospace; overflow-y: auto; max-height: 150px; line-height: 1.5; box-shadow: inset 0 2px 4px 0 rgba(0,0,0,0.2);">
                                        @foreach($audit->new_values as $key => $newValue)
                                            @php $oldValue = $audit->old_values[$key] ?? null; @endphp
                                            @if($oldValue !== $newValue && !in_array($key, ['updated_at', 'created_at']))
                                                <div style="display: flex; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 0.25rem; word-break: break-all;">
                                                    <span style="color:#94a3b8; font-weight: bold;">{{ $key }}:</span> 
                                                    @if($audit->event !== 'created')
                                                        <del style="color:#ef4444">{{ is_array($oldValue) ? json_encode($oldValue) : $oldValue }}</del> 
                                                        <span style="color:#64748b;">&#10142;</span> 
                                                    @endif
                                                    <span style="color:#10b981">{{ is_array($newValue) ? json_encode($newValue) : $newValue }}</span>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                @else
                                    <span style="color:#94a3b8; font-style: italic;">Nenhum detalhe técnico</span>
                                @endif
                                
                                @if($audit->url)
                                    <div style="margin-top: 0.5rem; font-size: 0.7rem; color: #94a3b8;">
                                        <i class="fa fa-link"></i> {{ \Illuminate\Support\Str::limit($audit->url, 50) }}
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center" style="padding: 3rem; text-align: center;">
                                <div style="font-size: 3rem; color: #cbd5e1; margin-bottom: 1rem;"><i class="fa fa-search"></i></div>
                                <div style="font-weight: bold; color: #64748b;">Nenhuma alteração registrada ou encontrada no filtro.</div>
                            </td>
                        </tr>
                    @endforelse
                </x-slot>
            </x-ui.table>
            
            @if($audits->hasPages())
                <div class="p-4" style="padding: 1rem; border-top: 1px solid #e2e8f0; background: #f8fafc;">
                    {{ $audits->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
