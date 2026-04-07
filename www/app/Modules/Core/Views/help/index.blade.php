<x-layouts.app>
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl fw-bold text-primary">Manual e Regras de Negócio (FAQ)</h2>
            <p class="text-slate-500">Documentação imutável da inteligência do sistema para instrução da equipe.</p>
        </div>
        <div class="relative w-72">
            <input type="text" id="faq-search" class="form-control w-full pl-10" placeholder="Buscar dúvida..." onkeyup="filterFaqs()">
            <svg class="absolute left-3 top-3 text-slate-400" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        </div>
    </div>

    <div class="card bg-transparent border-0 shadow-none">
        <div class="grid gap-4" id="faq-container">
            @foreach($faqs as $index => $faq)
            <div class="faq-item card border shadow-sm rounded-lg overflow-hidden transition-all duration-200" data-content="{{ strtolower($faq['title'] . ' ' . $faq['content'] . ' ' . implode(' ', $faq['tags'])) }}">
                <button class="w-full text-left p-5 flex justify-between items-center bg-white hover:bg-slate-50 focus:outline-none" onclick="toggleAcc({{ $index }})">
                    <span class="fw-bold text-lg text-slate-800">{{ $faq['title'] }}</span>
                    <svg id="icon-{{ $index }}" class="text-slate-400 transform transition-transform duration-200" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div id="content-{{ $index }}" class="px-5 pb-5 bg-white hidden">
                    <div class="pt-3 border-t border-slate-100 text-slate-600 leading-relaxed text-sm md:text-base">
                        {{ $faq['content'] }}
                    </div>
                    <div class="mt-4 flex gap-2 flex-wrap">
                        @foreach($faq['tags'] as $tag)
                            <span class="bg-indigo-50 text-indigo-700 text-xs px-2 py-1 rounded shadow-sm border border-indigo-100 font-semibold uppercase">#{{ $tag }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        <div id="no-results" class="hidden text-center py-10 text-slate-500">
            Nenhuma ajuda encontrada para os termos digitados.
        </div>
    </div>

    <script>
        function toggleAcc(index) {
            const content = document.getElementById('content-' + index);
            const icon = document.getElementById('icon-' + index);
            
            if (content.classList.contains('hidden')) {
                content.classList.remove('hidden');
                icon.style.transform = 'rotate(180deg)';
            } else {
                content.classList.add('hidden');
                icon.style.transform = 'rotate(0deg)';
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
