<x-layouts.app>
    <div class="p-6">
        <div class="mb-4 border-b border-slate-200 pb-4" style="margin-bottom: 2rem;">
            <h2 class="text-primary fw-bold" style="font-size: 1.75rem;">Standby & Digital Signage</h2>
            <p class="text-light" style="margin-top: 0.25rem;">Mídias do modo Ocioso / Painéis Comerciais exibidos no PDV após período de inatividade.</p>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 2rem;">
            <!-- Coluna de Configuração e Upload -->
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                
                <!-- Tempo Ocioso -->
                <div class="card bg-white border-0 shadow-sm" style="padding: 1.5rem; border-radius: 0.75rem;">
                    <h3 style="font-size: 1.125rem; font-weight: bold; color: #1e293b; margin-bottom: 1rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.5rem;">
                        <i class="fa fa-clock" style="color: #6366f1;"></i> Inatividade (Idle Timeout)
                    </h3>
                    <form action="{{ route('settings.standby.timeout') }}" method="POST">
                        @csrf
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #334155; margin-bottom: 0.5rem;">Segundos Ausentes para Ativar Vídeo</label>
                        <div style="display: flex; gap: 0.5rem;">
                            <input type="number" name="timeout" value="{{ $timeout }}" min="10" required class="form-control" style="flex: 1;">
                            <button type="submit" class="btn btn-primary" style="background: #4f46e5; border-color: #4f46e5;">Salvar</button>
                        </div>
                        <p style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.5rem;">Dica: 60 segundos (1 minuto) é o ideal para restaurantes.</p>
                    </form>
                </div>

                <!-- Nova Mídia -->
                <div class="card bg-white border-0 shadow-sm" style="padding: 1.5rem; border-radius: 0.75rem;">
                    <h3 style="font-size: 1.125rem; font-weight: bold; color: #1e293b; margin-bottom: 1rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.5rem;">
                        <i class="fa fa-upload" style="color: #6366f1;"></i> Enviar Nova Mídia
                    </h3>
                    <form action="{{ route('settings.standby.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div style="margin-bottom: 1rem;">
                            <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #334155; margin-bottom: 0.5rem;">Arquivo (.PNG, .JPEG, .MP4)</label>
                            <input type="file" name="file" accept="image/png, image/jpeg, video/mp4" required class="form-control" style="font-size: 0.875rem; padding: 0.5rem;">
                        </div>
                        <div style="margin-bottom: 1.5rem;">
                            <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #334155; margin-bottom: 0.5rem;">Espera na Tela (Segundos)</label>
                            <input type="number" name="duration_seconds" value="10" min="3" required class="form-control">
                            <p style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.5rem;">Imagens usarão este tempo. Vídeos ignoram e tocam até o fim.</p>
                        </div>
                        <button type="submit" class="btn btn-success" style="width: 100%; font-weight: bold; background: #059669; border-color: #059669;">
                            Adicionar ao Slide Show
                        </button>
                    </form>
                </div>
            </div>

            <!-- Coluna de Grade de Mídia Atual -->
            <div>
                <div class="card bg-white border-0 shadow-sm" style="padding: 1.5rem; border-radius: 0.75rem;">
                     <h3 style="font-size: 1.125rem; font-weight: bold; color: #1e293b; margin-bottom: 1rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.5rem;">
                        <i class="fa fa-film" style="color: #6366f1;"></i> Grade de Reprodução Sincronizada
                     </h3>
                     
                     <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">
                         @forelse($medias as $index => $media)
                            <div style="border: 1px solid #e2e8f0; border-radius: 0.5rem; overflow: hidden; display: flex; flex-direction: column;">
                                <!-- Preview -->
                                <div style="background: #0f172a; height: 10rem; position: relative; display: flex; align-items: center; justify-content: center;">
                                    @if($media->type === 'VIDEO')
                                        <div style="position: absolute; inset: 0; background: rgba(0,0,0,0.4); display: flex; align-items: center; justify-content: center; z-index: 10;">
                                            <i class="fa fa-play-circle" style="font-size: 2.5rem; color: rgba(255,255,255,0.8);"></i>
                                        </div>
                                        <video style="width: 100%; height: 100%; object-fit: cover; opacity: 0.6;" preload="none">
                                            <source src="{{ asset('storage/' . $media->file_path) }}#t=0.1" type="video/mp4">
                                        </video>
                                    @else
                                        <img src="{{ asset('storage/' . $media->file_path) }}" style="width: 100%; height: 100%; object-fit: cover;">
                                    @endif
                                    
                                    <div style="position: absolute; top: 0.5rem; left: 0.5rem; background: rgba(0,0,0,0.6); color: white; padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.75rem; font-weight: bold; z-index: 20;">
                                        #{{ $index + 1 }}
                                    </div>
                                </div>
                                <!-- Content Meta & Actions -->
                                <div style="padding: 0.75rem; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
                                    <div>
                                        <div style="font-weight: bold; color: #334155; font-size: 0.875rem;">{{ $media->type === 'VIDEO' ? 'Vídeo Institucional' : 'Slide Imagem' }}</div>
                                        <div style="font-size: 0.75rem; color: #64748b;"><i class="fa fa-hourglass-half"></i> {{ $media->duration_seconds }} segundos</div>
                                    </div>
                                    <div style="display: flex; gap: 0.5rem;">
                                        <!-- Controle de Ordem -->
                                        <div style="display: flex; flex-direction: column; gap: 0.125rem;">
                                            @if(!$loop->first)
                                                <form action="{{ route('settings.standby.move', $media->id) }}" method="POST" style="margin:0;">
                                                    @csrf <input type="hidden" name="direction" value="up">
                                                    <button type="submit" style="padding: 0; min-width: 1.5rem; height: 1.25rem; display: flex; align-items: center; justify-content: center; background: white; border: 1px solid #cbd5e1; border-radius: 0.25rem; font-size: 0.6rem; color: #475569; cursor: pointer;" title="Subir">▲</button>
                                                </form>
                                            @else
                                                <div style="height: 1.25rem; width: 1.5rem;"></div>
                                            @endif
                                            
                                            @if(!$loop->last)
                                                <form action="{{ route('settings.standby.move', $media->id) }}" method="POST" style="margin:0;">
                                                    @csrf <input type="hidden" name="direction" value="down">
                                                    <button type="submit" style="padding: 0; min-width: 1.5rem; height: 1.25rem; display: flex; align-items: center; justify-content: center; background: white; border: 1px solid #cbd5e1; border-radius: 0.25rem; font-size: 0.6rem; color: #475569; cursor: pointer;" title="Descer">▼</button>
                                                </form>
                                            @else
                                                <div style="height: 1.25rem; width: 1.5rem;"></div>
                                            @endif
                                        </div>

                                        <form action="{{ route('settings.standby.destroy', $media) }}" method="POST" onsubmit="return confirm('Excluir esta mídia definitivamente?')" style="margin: 0; display: flex; align-items: center;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" style="display: flex; align-items: center; justify-content: center; background: #fee2e2; border: 1px solid #fca5a5; color: #ef4444; border-radius: 0.25rem; width: 2.25rem; height: 2.25rem; cursor: pointer; font-size: 1rem; padding: 0;" title="Remover">✖</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                         @empty
                            <div style="grid-column: span 2; padding: 2rem; text-align: center; color: #94a3b8; background: #f8fafc; border-radius: 0.5rem; border: 1px dashed #cbd5e1;">
                                <i class="fa fa-film" style="font-size: 2.25rem; margin-bottom: 0.75rem; color: #cbd5e1;"></i>
                                <p>Nenhuma mídia inserida. A tela do PDV ficará preta durante o Standby.</p>
                            </div>
                         @endforelse
                     </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
