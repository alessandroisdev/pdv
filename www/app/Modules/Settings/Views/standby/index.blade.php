<x-layouts.app>
    <div class="p-6">
        <div class="flex justify-between items-end mb-6">
            <div>
                <h2 class="text-2xl fw-bold text-slate-800">Standby & Digital Signage</h2>
                <p class="text-slate-500">Mídias do modo Ocioso / Painéis Comerciais exibidos no PDV após período de inatividade.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Coluna de Configuração e Upload -->
            <div class="col-span-1 flex flex-col gap-6">
                <!-- Tempo Ocioso -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                    <h3 class="text-lg font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2"><i class="fa fa-clock text-indigo-500"></i> Inatividade (Idle Timeout)</h3>
                    <form action="{{ route('settings.standby.timeout') }}" method="POST">
                        @csrf
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Segundos Ausentes para Ativar o Vídeo</label>
                        <div class="flex gap-2">
                            <input type="number" name="timeout" value="{{ $timeout }}" min="10" required class="form-control w-full bg-slate-50 border border-slate-200 p-2 rounded">
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-4 rounded shadow">Salvar</button>
                        </div>
                        <p class="text-xs text-slate-400 mt-2">Dica: 60 segundos (1 minuto) é o ideal para restaurantes.</p>
                    </form>
                </div>

                <!-- Nova Mídia -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                    <h3 class="text-lg font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2"><i class="fa fa-upload text-indigo-500"></i> Enviar Nova Mídia</h3>
                    <form action="{{ route('settings.standby.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Arquivo (.PNG, .JPEG, .MP4)</label>
                            <input type="file" name="file" accept="image/png, image/jpeg, video/mp4" required class="form-control w-full bg-slate-50 border border-slate-200 p-2 rounded text-sm">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Espera na Tela (Segundos)</label>
                            <input type="number" name="duration_seconds" value="10" min="3" required class="form-control w-full bg-slate-50 border border-slate-200 p-2 rounded">
                            <p class="text-xs text-slate-400 mt-1">Imagens usarão este tempo. Vídeos ignoram e tocam até o fim.</p>
                        </div>
                        <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 rounded shadow">
                            Adicionar ao Slide Show
                        </button>
                    </form>
                </div>
            </div>

            <!-- Coluna de Grade de Mídia Atual -->
            <div class="col-span-2">
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                     <h3 class="text-lg font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2"><i class="fa fa-film text-indigo-500"></i> Grade de Reprodução Sincronizada</h3>
                     
                     <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                         @forelse($medias as $media)
                            <div class="border border-slate-200 rounded-lg overflow-hidden flex flex-col group relative">
                                <!-- Preview -->
                                <div class="bg-slate-900 h-40 flex items-center justify-center relative">
                                    @if($media->type === 'VIDEO')
                                        <div class="absolute inset-0 bg-black/40 flex items-center justify-center z-10"><i class="fa fa-play-circle text-4xl text-white/80"></i></div>
                                        <video class="w-full h-full object-cover opacity-60">
                                            <source src="{{ asset('storage/' . $media->file_path) }}" type="video/mp4">
                                        </video>
                                    @else
                                        <img src="{{ asset('storage/' . $media->file_path) }}" class="w-full h-full object-cover">
                                    @endif
                                </div>
                                <!-- Content Meta -->
                                <div class="p-3 bg-slate-50 border-t border-slate-200 flex justify-between items-center">
                                    <div>
                                        <div class="font-bold text-slate-700 text-sm">{{ $media->type === 'VIDEO' ? 'Vídeo Institucional' : 'Slide Imagem' }}</div>
                                        <div class="text-xs text-slate-500"><i class="fa fa-hourglass-half"></i> {{ $media->duration_seconds }} segundos</div>
                                    </div>
                                    <form action="{{ route('settings.standby.destroy', $media) }}" method="POST" onsubmit="return confirm('Excluir esta mídia?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-500 hover:bg-rose-100 p-2 rounded transition-colors" title="Remover"><i class="fa fa-trash"></i></button>
                                    </form>
                                </div>
                            </div>
                         @empty
                            <div class="col-span-2 p-8 text-center text-slate-400 bg-slate-50 rounded-lg border border-dashed border-slate-300">
                                <i class="fa fa-film text-4xl mb-3 text-slate-300"></i>
                                <p>Nenhuma mídia inserida. A tela do PDV ficará preta durante o Standby.</p>
                            </div>
                         @endforelse
                     </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
