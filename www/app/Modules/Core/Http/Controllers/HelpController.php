<?php

namespace App\Modules\Core\Http\Controllers;

use App\Http\Controllers\Controller;

class HelpController extends Controller
{
    public function index()
    {
        $faqs = [
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
                'title' => 'Como operadoras/caixas fazem logins físicos sem uma conta web?',
                'tags' => ['caixa', 'pdv', 'segurança', 'operadores'],
                'content' => 'O Módulo AccessControl permite cadastrar "Colaboradores (PIN)". Esse colaborador não digita Email/Senha na nuvem, ele só insere os 4 números na tela de Abertura do PDV local da máquina dele. Toda a rastreabilidade passará a usar o Nome gravado sobre este PIN para registrar Vendas e Auditorias.'
            ],
            [
                'title' => 'Motor de Auditoria Passiva (Ghost Logs)',
                'tags' => ['segurança', 'logs', 'auditoria', 'admin'],
                'content' => 'O Gestão PDV conta com o padrão Owen-It ativado. Qualquer edição crítica (Preços, Usuários, Status) fica cravada em uma tabela oculta imutável. Você pode ir em "Auditoria & Segurança" para consultar com visão dif (ex: antes era R$ 5, virou R$ 10, e verificar o IP da ação e a data exata).'
            ],
            [
                'title' => 'Como disparo campanhas em massa (CRM)?',
                'tags' => ['marketing', 'crm', 'retencao'],
                'content' => 'Dentro de "Marketing & CRM", você tem o botão de Disparo. O gatilho "Inativos" rastreia automaticamente quem comprou há mais de 60 dias (ou outro critério definido) usando o cruzamento de banco de dados do PDV, disparando E-mail/Notificações para recuperar a clientela.'
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
