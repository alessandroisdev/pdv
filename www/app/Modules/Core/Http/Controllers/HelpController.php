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
                'title' => 'Autorizações Restritas (Sangria, Reforço, Excluir Item)',
                'tags' => ['pdv', 'caixa', 'autorizacao', 'supervisor'],
                'content' => 'Para evitar fraudes no caixa aberto, ações como Sangria (Tirar dinheiro para pagamentos/depósito), Reforço (Colocar troco inicial) ou Excluir Produto da nota trancam a tela imediatamente. Somente um Colaborador com cargo `SUPERVISOR` pode digitar o seu próprio PIN como segunda chave para injetar permissão temporária e confirmar a operação.'
            ]
        ];

        return view('core::help.index', compact('faqs'));
    }
}
