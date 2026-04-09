<x-layouts.app>
    <x-slot:title>DRE - Demontração de Resultados</x-slot:title>

    <div class="flex justify-between items-end mb-6">
        <div>
            <h1 class="text-3xl fw-bold text-slate-800" style="letter-spacing: -0.025em;">Fechamento Contábil (DRE)</h1>
            <p class="text-slate-500 mt-1">Análise de Lucratividade, EBITDA e Absorção de Custos.</p>
        </div>
        <form action="{{ route('finance.dre.index') }}" method="GET" class="flex gap-2">
            <select name="month" class="form-control" onchange="this.form.submit()">
                @for($m=1; $m<=12; $m++)
                    <option value="{{ sprintf('%02d', $m) }}" {{ $month == sprintf('%02d', $m) ? 'selected' : '' }}>Mês {{ sprintf('%02d', $m) }}</option>
                @endfor
            </select>
            <select name="year" class="form-control" onchange="this.form.submit()">
                @for($y=date('Y'); $y>=2020; $y--)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
            <button class="btn btn-primary"><i class="fa fa-filter"></i></button>
        </form>
    </div>

    <!-- DRE Waterfall Layout -->
    <div class="card overflow-hidden" style="border-radius: 0.75rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
        <div class="p-6 bg-slate-800 text-white flex justify-between items-center">
            <span class="text-lg font-bold text-slate-300">Exercício: <span class="text-white">{{ $month }}/{{ $year }}</span></span>
            <span class="text-sm bg-slate-700 px-3 py-1 rounded-full"><i class="fa fa-info-circle text-indigo-400"></i> Baseado no Regime de Caixa Realizado</span>
        </div>
        
        <table class="w-full text-left" style="border-collapse: collapse;">
            <tbody>
                <!-- 1. Receita Bruta -->
                <tr class="border-b" style="background: #f8fafc;">
                    <td class="p-4 font-bold text-slate-700 w-3/4 text-lg">1. Receita Bruta de Vendas</td>
                    <td class="p-4 text-right font-bold text-emerald-600 text-lg">R$ {{ number_format($grossIncome / 100, 2, ',', '.') }}</td>
                </tr>

                <!-- Dedutores Diretos (CPV, Taxas) -->
                <tr class="border-b transition hover:bg-slate-50 text-slate-600">
                    <td class="p-4 pl-8"><i class="fa fa-minus text-red-400 mr-2"></i> (-) Impostos sobre Venda (TAX)</td>
                    <td class="p-4 text-right text-red-500">R$ {{ number_format($taxes / 100, 2, ',', '.') }}</td>
                </tr>
                <tr class="border-b transition hover:bg-slate-50 text-slate-600">
                    <td class="p-4 pl-8"><i class="fa fa-minus text-red-400 mr-2"></i> (-) Custos de Mercadorias / Insumos (CMV/CPV)</td>
                    <td class="p-4 text-right text-red-500">R$ {{ number_format($cogs / 100, 2, ',', '.') }}</td>
                </tr>

                <!-- Result Margem C -->
                <tr class="border-b" style="background: #eff6ff;">
                    <td class="p-4 font-bold text-indigo-900 text-md">Margem de Contribuição Bruta (=)</td>
                    <td class="p-4 text-right font-bold text-indigo-700 text-md">R$ {{ number_format($contributionMargin / 100, 2, ',', '.') }}</td>
                </tr>

                <!-- OPEX / Fixed Costs -->
                <tr class="transition hover:bg-slate-50 text-slate-600">
                    <td class="p-4 pl-8 pt-4 pb-2"><i class="fa fa-minus text-orange-400 mr-2"></i> (-) OPEX (Despesas com Pessoal / Salários)</td>
                    <td class="p-4 text-right text-orange-600 pt-4 pb-2">R$ {{ number_format($opexHr / 100, 2, ',', '.') }}</td>
                </tr>
                <tr class="transition hover:bg-slate-50 text-slate-600">
                    <td class="p-4 pl-8 py-2"><i class="fa fa-minus text-orange-400 mr-2"></i> (-) OPEX (Marketing / Comissões)</td>
                    <td class="p-4 text-right text-orange-600 py-2">R$ {{ number_format($opexMarketing / 100, 2, ',', '.') }}</td>
                </tr>
                <tr class="border-b transition hover:bg-slate-50 text-slate-600">
                    <td class="p-4 pl-8 pt-2 pb-4"><i class="fa fa-minus text-orange-400 mr-2"></i> (-) OPEX (Gerais e Administrativos - Água, Luz, etc)</td>
                    <td class="p-4 text-right text-orange-600 pt-2 pb-4">R$ {{ number_format($opexGeneral / 100, 2, ',', '.') }}</td>
                </tr>

                <!-- EBITDA -->
                <tr class="border-b" style="background: #f0fdf4;">
                    <td class="p-5">
                        <div class="font-black text-emerald-900 text-xl tracking-tight">EBITDA / LAJIDA</div>
                        <div class="text-xs font-bold uppercase tracking-wider text-emerald-600 mt-1">Lucro Operacional Antes de Impostos Renda</div>
                    </td>
                    <td class="p-5 text-right font-black text-emerald-700 text-2xl">
                        R$ {{ number_format($ebitda / 100, 2, ',', '.') }}
                    </td>
                </tr>

                <!-- Depreciação / Capex -->
                <tr class="border-b transition hover:bg-slate-50 text-slate-600">
                    <td class="p-4 pl-8"><i class="fa fa-minus text-purple-400 mr-2"></i> (-) CAPEX (Investimentos / Equipamentos)</td>
                    <td class="p-4 text-right text-purple-600">R$ {{ number_format($capex / 100, 2, ',', '.') }}</td>
                </tr>

                <!-- NET PROFIT -->
                <tr style="background: #1e293b; color: white;">
                    <td class="p-6">
                        <div class="font-black text-white text-2xl tracking-tight">LUCRO LÍQUIDO DO EXERCÍCIO</div>
                        <div class="text-sm font-medium text-slate-400 mt-1">Geração Limpa de Caixa Livre da Operação</div>
                    </td>
                    <td class="p-6 text-right font-black text-3xl {{ $netProfit >= 0 ? 'text-emerald-400' : 'text-red-400' }}">
                        R$ {{ number_format($netProfit / 100, 2, ',', '.') }}
                    </td>
                </tr>

            </tbody>
        </table>
    </div>

</x-layouts.app>
