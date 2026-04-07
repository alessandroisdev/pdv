<x-layouts.app>
    <div class="card">
        <div class="card-header border-b p-4 flex justify-between items-center">
            <h3 class="fw-bold" style="color: var(--primary); font-size: 1.25rem;">Rastreabilidade Corporativa (Owen-It)</h3>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table" style="width: 100%; border-collapse: collapse; text-align: left;">
                    <thead style="background-color: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                        <tr>
                            <th style="padding: 12px; font-weight: 600; color: #475569;">Data/Hora</th>
                            <th style="padding: 12px; font-weight: 600; color: #475569;">Ator</th>
                            <th style="padding: 12px; font-weight: 600; color: #475569;">Ação</th>
                            <th style="padding: 12px; font-weight: 600; color: #475569;">Módulo Afetado</th>
                            <th style="padding: 12px; font-weight: 600; color: #475569;">Diferença (Valores)</th>
                            <th style="padding: 12px; font-weight: 600; color: #475569;">Rastro Digital</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($audits as $audit)
                            <tr style="border-bottom: 1px solid #f1f5f9; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#f8fafc'" onmouseout="this.style.backgroundColor='transparent'">
                                <td style="padding: 12px; font-size: 0.85rem; color: #64748b;">
                                    {{ $audit->created_at->format('d/m/Y H:i:s') }}
                                </td>
                                <td style="padding: 12px;">
                                    <span style="font-weight: 600; color: #0f172a;">{{ $audit->user->name ?? 'Sistema / Físico' }}</span>
                                </td>
                                <td style="padding: 12px;">
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
                                    <span style="background: {{ $color }}20; color: {{ $color }}; padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: bold; border: 1px solid {{ $color }}40;">
                                        {{ $eventLabel }}
                                    </span>
                                </td>
                                <td style="padding: 12px; font-size: 0.85rem;">
                                    {{ class_basename($audit->auditable_type) }} #{{ $audit->auditable_id }}
                                </td>
                                <td style="padding: 12px; font-size: 0.8rem;">
                                    @if(count($audit->old_values) > 0 || count($audit->new_values) > 0)
                                        <div style="background: #1e293b; color: #e2e8f0; padding: 8px; border-radius: 6px; font-family: monospace; max-width: 300px; max-height: 120px; overflow-y:auto; word-wrap: break-word;">
                                            @foreach($audit->new_values as $key => $newValue)
                                                @php $oldValue = $audit->old_values[$key] ?? null; @endphp
                                                @if($oldValue !== $newValue && !in_array($key, ['updated_at', 'created_at']))
                                                    <div>
                                                        <span style="color:#94a3b8">{{ $key }}:</span> 
                                                        @if($audit->event !== 'created')
                                                            <del style="color:#ef4444">{{ is_array($oldValue) ? json_encode($oldValue) : $oldValue }}</del> 
                                                            <span style="color:#64748b;">&rarr;</span> 
                                                        @endif
                                                        <span style="color:#10b981">{{ is_array($newValue) ? json_encode($newValue) : $newValue }}</span>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    @else
                                        <span style="color:#94a3b8">-</span>
                                    @endif
                                </td>
                                <td style="padding: 12px; font-size: 0.75rem; color: #94a3b8;">
                                    IP: {{ $audit->ip_address }}<br>
                                    URL: {{ \Illuminate\Support\Str::limit($audit->url, 25) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="padding: 24px; text-align: center; color: #64748b;">Nenhuma alteração registrada ainda.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($audits->hasPages())
                <div class="p-4 border-t">
                    {{ $audits->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
