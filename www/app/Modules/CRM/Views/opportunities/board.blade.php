<x-layouts.app>
    <x-slot:title>Pipeline B2B (Kanban)</x-slot:title>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h1 class="fw-bold" style="font-size: 1.75rem; color: #455073; letter-spacing: -0.025em;">Pipeline B2B</h1>
            <p class="text-light" style="margin-top: 0.25rem;">Gestão de Negociações e Contratos</p>
        </div>
        <button class="btn btn-primary" onclick="alert('Feature Nova Oportunidade não implemetada no MVP!')">
            <i class="fa fa-plus"></i> Novo Lead
        </button>
    </div>

    <div class="kanban-board" style="display: flex; gap: 1.5rem; overflow-x: auto; padding-bottom: 1rem;">
        
        @foreach($lanes as $key => $lane)
            <div class="kanban-lane" data-stage="{{ $key }}" style="background: #f1f5f9; border-radius: 0.5rem; padding: 1rem; width: 320px; min-width: 320px; display: flex; flex-direction: column;">
                <div class="lane-header" style="font-weight: 700; color: #334155; margin-bottom: 1rem; border-bottom: 2px solid #cbd5e1; padding-bottom: 0.5rem; display: flex; justify-content: space-between;">
                    <span>{{ $lane['label'] }}</span>
                    <span style="background: #e2e8f0; padding: 0.1rem 0.5rem; border-radius: 999px; font-size: 0.75rem;">{{ count($lane['items']) }}</span>
                </div>

                <div class="lane-cards" style="flex: 1; min-height: 200px; display: flex; flex-direction: column; gap: 0.75rem;">
                    @foreach($lane['items'] as $item)
                        <div class="kanban-card" draggable="true" data-id="{{ $item->id }}" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 0.5rem; padding: 1rem; box-shadow: 0 1px 2px rgba(0,0,0,0.05); cursor: grab;">
                            <div style="font-size: 0.9rem; font-weight: 600; color: #0f172a; margin-bottom: 0.25rem;">
                                {{ $item->title }}
                            </div>
                            <div style="font-size: 0.75rem; color: #64748b; margin-bottom: 0.5rem;">
                                <i class="fa fa-building"></i> {{ $item->customer->name ?? 'Cliente Desconhecido' }}
                            </div>
                            <div style="font-weight: 700; color: var(--success); font-size: 0.85rem;">
                                R$ {{ number_format($item->amount_cents / 100, 2, ',', '.') }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

    </div>

    <!-- Script HTML5 Drag & Drop Nativo -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const cards = document.querySelectorAll('.kanban-card');
            const lanes = document.querySelectorAll('.lane-cards');

            cards.forEach(card => {
                card.addEventListener('dragstart', () => {
                    card.classList.add('dragging');
                    card.style.opacity = '0.5';
                });

                card.addEventListener('dragend', () => {
                    card.classList.remove('dragging');
                    card.style.opacity = '1';

                    // Update via fetch API
                    const newStage = card.closest('.kanban-lane').getAttribute('data-stage');
                    const oppId = card.getAttribute('data-id');
                    
                    fetch(`/crm/opportunities/${oppId}/stage`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ stage: newStage })
                    }).then(res => res.json()).then(data => {
                        window.toast.fire({ icon: 'success', title: 'Fase Atualizada!' });
                    });
                });
            });

            lanes.forEach(lane => {
                lane.addEventListener('dragover', e => {
                    e.preventDefault();
                    const afterElement = getDragAfterElement(lane, e.clientY);
                    const draggable = document.querySelector('.dragging');
                    if (afterElement == null) {
                        lane.appendChild(draggable);
                    } else {
                        lane.insertBefore(draggable, afterElement);
                    }
                });
            });

            // Helping find the closest card to push aside
            function getDragAfterElement(container, y) {
                const draggableElements = [...container.querySelectorAll('.kanban-card:not(.dragging)')];

                return draggableElements.reduce((closest, child) => {
                    const box = child.getBoundingClientRect();
                    const offset = y - box.top - box.height / 2;
                    if (offset < 0 && offset > closest.offset) {
                        return { offset: offset, element: child }
                    } else {
                        return closest;
                    }
                }, { offset: Number.NEGATIVE_INFINITY }).element;
            }
        });
    </script>
</x-layouts.app>
