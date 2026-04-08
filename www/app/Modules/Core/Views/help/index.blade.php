<x-layouts.app>
    <div class="p-6">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold text-slate-800">Manual e Regras de Negócio (FAQ)</h2>
                <p class="text-slate-500">Documentação imutável da inteligência do sistema para instrução da equipe.</p>
            </div>
            <div class="relative w-80">
                <input type="text" id="faq-search" class="w-full bg-white border border-slate-200 text-slate-700 rounded-lg pl-10 pr-4 py-2 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none" placeholder="Buscar dúvida..." onkeyup="filterFaqs()">
                <i class="fa fa-search absolute left-3 top-3 text-slate-400"></i>
            </div>
        </div>

        <div class="grid gap-4" id="faq-container">
            @foreach($faqs as $index => $faq)
            <div class="faq-item bg-white border border-slate-200 shadow-sm rounded-xl overflow-hidden transition-all duration-200" data-content="{{ e(strtolower($faq['title'] . ' ' . $faq['content'] . ' ' . implode(' ', $faq['tags']))) }}">
                <button class="w-full text-left p-5 flex justify-between items-center bg-white hover:bg-slate-50 focus:outline-none transition-colors" onclick="toggleAcc({{ $index }})">
                    <span class="font-bold text-lg text-slate-800">{{ $faq['title'] }}</span>
                    <i id="icon-{{ $index }}" class="fa fa-chevron-down text-slate-400 transform transition-transform duration-200"></i>
                </button>
                <div id="content-{{ $index }}" class="px-5 pb-5 bg-white hidden">
                    <div class="pt-4 border-t border-slate-100 text-slate-600 leading-relaxed text-sm md:text-base">
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
        
        <div id="no-results" class="hidden text-center py-16 bg-white border border-slate-200 rounded-xl mt-4 text-slate-500 shadow-sm">
            <div class="text-4xl mb-4 text-slate-300"><i class="fa fa-question-circle"></i></div>
            <h4 class="text-lg font-bold text-slate-700 mb-1">Nenhum resultado encontrado</h4>
            <p>Nenhuma ajuda encontrada para os termos digitados.</p>
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
