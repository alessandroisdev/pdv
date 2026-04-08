@php
    $prefixPath = request()->is('terminal*') ? '/terminal' : '/vendas/pdv';
@endphp
<x-layouts.pos>
    <div style="height: 100vh; display:flex; align-items:center; justify-content:center; background-color: #0f172a;">
        <div class="card" style="width: 450px; padding: 2rem; text-align:center; box-shadow: 0 10px 25px rgba(0,0,0,0.5); border-radius: 12px; background: rgba(30,41,59,0.8); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1);">
            
            <div style="margin-bottom: 2rem;">
                <h1 style="color: #f87171; font-size: 1.5rem; font-weight: 800; margin-bottom: 0.5rem; text-transform:uppercase; letter-spacing:0.05em;">Fechamento de Turno Cego</h1>
                <p style="color: #94a3b8; font-size: 0.9rem;">
                    Atenção Operator(a) <b style="color:white;">{{ session('pos_employee_name', 'Não Válido') }}</b>.<br> 
                    Conte fisicamente as notas e moedas da sua gaveta agora!
                </p>
            </div>

            <form action="{{ $prefixPath }}/fechar" method="POST" id="blindCloseForm">
                @csrf
                <div style="background: rgba(0,0,0,0.2); padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem; border: 1px solid rgba(255,255,255,0.05);">
                    <label style="font-weight: 600; font-size: 0.95rem; color: #cbd5e1; display:block; margin-bottom: 0.5rem;">Cédulas e Moedas Encontradas (R$):</label>
                    <input type="text" name="reported_amount_cash" placeholder="0,00" required style="width: 100%; padding: 1rem; border: 2px solid #334155; border-radius: 6px; font-size: 2rem; font-weight: bold; text-align: center; color: #a7f3d0; background: #0f172a; outline: none; box-shadow: inset 0 2px 4px 0 rgba(0,0,0,0.5);">
                    <small style="color: #64748b; font-size:0.75rem; display:block; margin-top:0.75rem;">A conferência contra os extratos digitais de Cartão/Pix será gerada automaticamente no relatório matriz do sistema.</small>
                </div>

                <button type="button" class="btn" style="background: linear-gradient(135deg, rgba(239,68,68,0.2) 0%, rgba(185,28,28,0.4) 100%); color:#fca5a5; width: 100%; padding: 1rem; font-size: 1.1rem; border: 1px solid rgba(239,68,68,0.5); border-radius: 8px; cursor:pointer; font-weight:bold; letter-spacing: 0.05em;" onclick="confirmCloseShift(event)">
                    🛑 GRAVAR E ENCERRAR
                </button>
            </form>

            <script>
                function confirmCloseShift(e) {
                    if (window.Swal) {
                        window.Swal.fire({
                            title: 'Encerrar Turno?',
                            text: 'ATENÇÃO: Você não poderá corrigir este valor após encerrar. Tem certeza?',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#ef4444',
                            cancelButtonColor: '#475569',
                            confirmButtonText: 'Sim, Encerrar',
                            cancelButtonText: 'Cancelar'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                document.getElementById('blindCloseForm').submit();
                            }
                        });
                    } else {
                        if (confirm('ATENÇÃO: Você não poderá corrigir este valor após encerrar. Tem certeza?')) {
                            document.getElementById('blindCloseForm').submit();
                        }
                    }
                }
            </script>
            
            <a href="{{ $prefixPath }}" style="display:block; margin-top: 1.5rem; color: #64748b; text-decoration: none; font-size:0.9rem;">&larr; Voltar para o Caixa (Não Fechar)</a>
        </div>
    </div>
</x-layouts.pos>
