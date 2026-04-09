# 🚀 ERP Premium Enterprise SaaS

Este sistema é um poderoso ERP, CRM e PDV Multi-Filial de alta performance. Desenvolvido para escalar, suportar milhares de requisições simultâneas e governança de ponta.

## 🌟 Arquitetura e Módulos Principais

Construímos um autêntico colosso corporativo usando os melhores design patterns:
- **Assíncrono (Rabbit/Redis):** Integrado via `laravel/horizon`. Toda a pesada camada de Emissão Fiscal (`ProcessFiscalDocumentDispatch`) flui sem travar a interface do usuário.
- **Websockets Real-Time:** Motor acionado por `Laravel Reverb`. Fila do caixa e retaguarda conversam bidirecionalmente sem depender de Reloads ou F5.
- **Multi-Branch (Tenant by Branch):** Banco de dados inteligente que através de um *Global Scope* (`HasBranchScope`) separa o contexto da Filial A da Filial B nativamente, isolando Vendas, Estoque, Clientes e Caixas.
- **Motor Tributário Independente:** A tabela `tax_rules` substituiu as alíquotas limitadas antigas. Regras estritas por NCM/ICMS dinâmicas.

## 📦 Extensões de Negócio

1. **Inteligência Logística (WMS):** Rastreabilidade de validades de Lote, interligada ao *ProcurementService*. A Cron avalia a Curva ABC de vendas (últimos 30 dias) e dispara alertas se o estoque vai secar por velocidade de venda.
2. **CRM Avançado (B2B Pipelines):** Um Kanban Visual Drag & Drop maravilhoso onde os diretores arrastam *Opportunities* entre fases para acompanhar as Vendas de Grande Porte.
3. **Controladoria e DRE Financeiro (Fase 4):** Lançamentos classificados nativamente. Painel gerencial que calcula EBITDA isolando Custos Fixos (OPEX) do Custo da Mercadoria (CPV) baseado em regime de caixa.
4. **Portal do Cliente B2B/B2C (Self-Service):** Seus clientes acessam a Rota Web Segura, listam as próprias Faturas e emitem o PIX sem auxílio técnico!
5. **Integração de PIX (Engine Strategy):** Padrão de Gateway agnóstico. Hoje plugado no `MockAsaasGateway` construindo QRCodes reais instantaneamente na tela do cliente final via AJAX!

## 🔐 Segurança Nativa (RBAC)
Todo acesso gerencial é fiscalizado por `Laravel Gates`. Módulos `Finance` e `Settings` possuem fechaduras blindades baseadas nos cargos Super Admin e Gestores - um vendedor de PDV não pode farejar a DRE.

## 🚀 Como iniciar
```bash
composer install
php artisan migrate --seed
php artisan reverb:start
php artisan horizon
php artisan serve
```

Desenvolvido para ser Implacável, Escalável e Lucrativo.
