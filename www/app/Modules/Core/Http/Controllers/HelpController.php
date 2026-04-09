<?php

namespace App\Modules\Core\Http\Controllers;

use App\Http\Controllers\Controller;

class HelpController extends Controller
{
    public function index()
    {
        $faqs = [
            [
                'title' => 'O que é Isolamento Multi-Filial (Tenant)?',
                'tags' => ['arquitetura', 'rede', 'filial'],
                'content' => 'O ERP possui arquitetura global Multi-Tenant de Rede. Uma filial enxerga apenas o próprio estoque, orçamentos, funil de vendas e relatórios de fluxo de caixa, enquanto a matriz (Super Admin) pode visionar transações unificadas através da gestão do `branch_id`.'
            ],
            [
                'title' => 'DRE Contábil e Classificação Financeira (EBITDA)',
                'tags' => ['financeiro', 'dre', 'contabilidade', 'ebitda'],
                'content' => 'Em `Financeiro > Relatórios > DRE`, não apresentamos apenas faturamento bruto. Lançamentos manuais ou liquidados no portal recebem classificações de Categorias Contábeis (CPV, Impostos, Folha OPEX, Marketing). O ERP calcula nativamente no formato Cascata para deduzir a Margem de Contribuição e reportar seu EBITDA Real Limpo e transparente.'
            ],
            [
                'title' => 'Como funciona o Motor Tributário (ICMS, ST e Fundos)?',
                'tags' => ['fiscal', 'nfe', 'impostos', 'ncm'],
                'content' => 'Para livrar o lojista do peso de tributar NF-e linha a linha, construímos o Motor Tributário. A Tabela Global de Impostos analisa o código NCM e o Tipo do Produto antes da emissão via APIs (ex: WebmaniaBR) disparadas de foma assíncrona.'
            ],
            [
                'title' => 'Como gerar Boletos e QRCodes de PIX Dinâmico (Portal do Cliente B2B)?',
                'tags' => ['cobrancas', 'pix', 'portal', 'crm'],
                'content' => 'Seu ERP detém uma via pública. Os contatos PJ e PF cadastrados podem logar usando CPF/CNPJ em `[URL]/portal`. Ao invés de você cobrar por WhatsApp, eles mesmos visualizam faturas em aberto. Clicando em Pagar PIX, o sistema gera dinamicamente QRCodes com API Asaas/Stripe integradas para abater boletos em tempo real dentro do Dashboard B2B.'
            ],
            [
                'title' => 'Avisos da Curva ABC Logística (WMS)',
                'tags' => ['estoque', 'wms', 'ruptura', 'abastecimento'],
                'content' => 'Com nosso Robô Autônomo de Inteligência (Command Job), a logística roda varreduras no Estoque na madrugada mensurando a velocidade de vendas dos últimos 30 dias. Produtos classificados como Categoria A (Alta Importância) geram relatórios imediatos se houver risco de Ruptura de Estoque (Out of Stock), permitindo provisionamento impecável da gestão de retaguarda.'
            ],
            [
                'title' => 'Pipeline de Vendas Avançado Drag-n-Drop (CRM)',
                'tags' => ['vendas', 'b2b', 'crm', 'oportunidades'],
                'content' => 'Esqueça listagens difíceis. Para fechamento de negócios B2B, você possui o Kanban Interativo no menu do CRM. Cadastre uma Oportunidade, precifique e mova a caixa amarela da coluna "Prospecção" para "Fechamento" com o mouse. Ao dar "Vendido!", a transação avança magicamente para a Receita Financeira do Caixa.'
            ],
            [
                'title' => 'Como funciona o Custo Médio na Entrada de Notas?',
                'tags' => ['compras', 'estoque', 'financeiro'],
                'content' => 'Sempre que uma Ordem de Compra (Purchasing) receber o status de RECEBIDO, o motor do sistema calcula o custo unitário da nova entrada e realiza uma Média Ponderada com o Custo anterior do Produto e seu volume em estoque. Isso garante que a precificação acompanhe a flutuação do mercado, estabilizando seus relatórios financeiros e lucratividade liquida de PDV sem você precisar digitar nada.'
            ],
            [
                'title' => 'O que acontece em um Fechamento de Caixa Cego?',
                'tags' => ['caixa', 'pdv', 'auditoria'],
                'content' => 'A regra de segurança número 01 do padrão internacional previne desvios. O caixa (Operador) informa EXATAMENTE quanto ele contou na gaveta, sem saber quanto o sistema registrou. A divergência positiva ou negativa só fica visível com o relatório gerado após a fechadura, ativando alertas para o Administrador caso existam desvios (Quebra de Caixa).'
            ],
            [
                'title' => 'Motor de Auditoria Passiva (Ghost Logs)',
                'tags' => ['segurança', 'logs', 'auditoria', 'admin'],
                'content' => 'O Gestão PDV conta com o padrão Owen-It ativado. Qualquer edição crítica (Preços, Usuários, Status) fica cravada em uma tabela oculta imutável. Você pode ir em "Auditoria & Segurança" para consultar com visão dif (ex: antes era R$ 5, virou R$ 10, e verificar o IP da ação e a data exata).'
            ],
            [
                'title' => 'O que é a TV Promocional (Sinalização Standby)?',
                'tags' => ['display', 'marketing', 'pdv', 'kiosk'],
                'content' => 'Ao subir vídeos (Vídeo Coca-Cola, MenuBoard) pelo painel, o App Desktop sincroniza todos os Media Files permanentemente para a gaveta cache (Offline) do Windows. Assim, se o Operador de Caixa soltar o mouse por 30 segundos, a tela vira uma imensa e maravilhosa vitrine Promocional dinâmica que não devora a Banda Larga e acoplada perfeitamente na frente de caixa!'
            ],
            [
                'title' => 'Como utilizar o Catálogo Eletrônico Omnichannel (Autoatendimento / Totem)?',
                'tags' => ['totem', 'vendas', 'ecommerce', 'catalogo'],
                'content' => 'Todo cliente pode apontar a câmera do celular para um QRCode impresso ou o restaurante pode ter Tablets presos na mesa! O caminho "http://[HOST]/catalogo" abre nossa PWA otimizada. Ao pedirem pelo cardápio digital, a venda abate o inventário (Pessimistic Locking) e escoa a transação diretamente nas artérias do Livro Razão Centralizado. Mágica absoluta!'
            ],
            [
                'title' => 'A Tesouraria e o Livro Razão trabalham juntos?',
                'tags' => ['financeiro', 'boletos', 'credito'],
                'content' => 'Sim! Contas a Pagar/Receber não são meros lembretes. Ao dar baixa num boleto (Installment) na interface "A Receber/Pagar", a sua ação automaticamente executa um fluxo trancado no Livro Caixa global do negócio, mantendo o controle total dos Faturamentos passivos/ativos e de Balcão todos harmonizados na Dashboard Financeira.'
            ]
        ];

        return view('core::help.index', compact('faqs'));
    }
}
