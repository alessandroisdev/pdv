<x-layouts.pos>
    <div style="height: 100vh; display:flex; align-items:center; justify-content:center; background-color: #f1f5f9;">
        <div class="card" style="width: 450px; padding: 2rem; text-align:center; box-shadow: 0 10px 25px rgba(0,0,0,0.1); border-radius: 12px; background: white;">
            
            <div style="margin-bottom: 2rem;">
                <h1 style="color: var(--primary); font-size: 1.5rem; font-weight: 800; margin-bottom: 0.5rem;">Fechamento de Turno Cego</h1>
                <p style="color: var(--text-secondary); font-size: 0.9rem;">
                    Atenção Operator(a) <b>{{ session('pos_employee_name', 'Não Válido') }}</b>.<br> 
                    Conte fisicamente as notas e moedas da sua gaveta agora!
                </p>
            </div>

            <form action="{{ route('sales.pos.close') }}" method="POST" id="blindCloseForm">
                @csrf
                <div style="background: #e2e8f0; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
                    <label style="font-weight: 600; font-size: 0.95rem; color: #1e293b; display:block; margin-bottom: 0.35rem;">Total Contado (Boca do Caixa) (R$):</label>
                    <input type="text" name="reported_amount_cash" placeholder="0,00" required style="width: 100%; padding: 1rem; border: 2px solid #94a3b8; border-radius: 6px; font-size: 1.5rem; font-weight: bold; text-align: center; color: #0f172a;">
                    <small style="color: #475569; font-size:0.75rem; display:block; margin-top:0.5rem;">A quebra (diferença positiva ou negativa) será debitada automaticamente no relatório de segurança se este valor diferir do volume registrado no sistema ERP durante o dia.</small>
                </div>

                <button type="submit" class="btn" style="background: #ef4444; color:white; width: 100%; padding: 1rem; font-size: 1.1rem; border:none; cursor:pointer;" onclick="return confirm('ATENÇÃO: Você não poderá corrigir este valor após encerrar. Tem certeza?')">
                    🛑 GRAVAR E ENCERRAR TURNO
                </button>
            </form>
            
            <a href="{{ route('sales.pos.board') }}" style="display:block; margin-top: 1rem; color: var(--text-secondary); text-decoration: none;">&larr; Voltar para o Caixa (Não Fechar)</a>
        </div>
    </div>
</x-layouts.pos>
