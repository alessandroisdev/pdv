<x-layouts.app>
    <div class="p-6">
        <div class="mb-4 border-b border-slate-200 pb-4 flex justify-between items-center" style="margin-bottom: 2rem;">
            <div>
                <h2 class="text-primary fw-bold" style="font-size: 1.75rem;">Manual e Regras de Negócio (FAQ)</h2>
                <p class="text-light" style="margin-top: 0.25rem;">Documentação imutável da inteligência do sistema para instrução da equipe.</p>
            </div>
            <div style="position: relative; width: 100%; max-width: 20rem;">
                <input type="text" id="faq-search" class="form-control hover-border transition" style="width: 100%; padding-left: 2.5rem; border-radius: 0.5rem;" placeholder="Buscar dúvida..." onkeyup="filterFaqs()">
                <i class="fa fa-search" style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
            </div>
        </div>

        <div style="display: flex; flex-direction: column; gap: 1rem;" id="faq-container">
            @foreach($faqs as $index => $faq)
            <div class="faq-item shadow-sm" data-content="{{ e(strtolower($faq['title'] . ' ' . $faq['content'] . ' ' . implode(' ', $faq['tags']))) }}" style="background: white; border: 1px solid #e2e8f0; border-radius: 0.75rem; overflow: hidden; transition: all 0.2s;">
                <button class="hover-bg-transition" style="width: 100%; text-align: left; padding: 1.25rem; display: flex; justify-content: space-between; items-center; background: white; border: none; cursor: pointer; outline: none; border-bottom: 1px solid transparent;" onclick="toggleAcc({{ $index }})">
                    <span style="font-weight: bold; font-size: 1.125rem; color: #1e293b;">{{ $faq['title'] }}</span>
                    <i id="icon-{{ $index }}" class="fa fa-chevron-down" style="color: #94a3b8; transition: transform 0.2s;"></i>
                </button>
                <div id="content-{{ $index }}" style="display: none; padding: 0 1.25rem 1.25rem 1.25rem; background: white;">
                    <div style="padding-top: 1rem; border-top: 1px solid #f1f5f9; color: #475569; line-height: 1.6; font-size: 0.9rem;">
                        {{ $faq['content'] }}
                    </div>
                    <div style="margin-top: 1rem; display: flex; gap: 0.5rem; flex-wrap: wrap;">
                        @foreach($faq['tags'] as $tag)
                            <span style="background: #eef2ff; color: #4338ca; font-size: 0.7rem; padding: 0.25rem 0.5rem; border-radius: 0.25rem; border: 1px solid #e0e7ff; font-weight: bold; text-transform: uppercase;">#{{ $tag }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        <div id="no-results" style="display: none; text-align: center; padding: 4rem 1rem; background: white; border: 1px solid #e2e8f0; border-radius: 0.75rem; margin-top: 1rem; box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05);">
            <div style="font-size: 3rem; color: #cbd5e1; margin-bottom: 1rem;"><i class="fa fa-question-circle"></i></div>
            <h4 style="font-size: 1.125rem; font-weight: bold; color: #334155; margin-bottom: 0.25rem;">Nenhum resultado encontrado</h4>
            <p style="color: #64748b; margin: 0;">Nenhuma ajuda encontrada para os termos digitados.</p>
        </div>
    </div>

    <style>
        .hover-bg-transition:hover {
            background-color: #f8fafc !important;
        }
    </style>

    <script>
        function toggleAcc(index) {
            const content = document.getElementById('content-' + index);
            const icon = document.getElementById('icon-' + index);
            const btn = icon.parentElement;
            
            if (content.style.display === 'none' || content.style.display === '') {
                content.style.display = 'block';
                icon.style.transform = 'rotate(180deg)';
                btn.style.borderBottomColor = '#f1f5f9';
            } else {
                content.style.display = 'none';
                icon.style.transform = 'rotate(0deg)';
                btn.style.borderBottomColor = 'transparent';
            }
        }

        function filterFaqs() {
            const input = document.getElementById('faq-search').value.toLowerCase();
            const items = document.querySelectorAll('.faq-item');
            let foundAny = false;

            items.forEach(item => {
                const searchableText = item.getAttribute('data-content');
                if (searchableText.includes(input)) {
                    item.style.display = 'block';
                    foundAny = true;
                } else {
                    item.style.display = 'none';
                }
            });

            document.getElementById('no-results').style.display = foundAny ? 'none' : 'block';
        }
    </script>
</x-layouts.app>
