# Levantamento de Requisitos e Escopo do Sistema

## 1. Objetivo do Projeto
Desenvolver um sistema robusto e ágil de controle de estoque, financeiro e Frente de Caixa (PDV) voltado para pequenas mercearias e lojas em geral. O sistema centralizará a retaguarda administrativa (ERP) e a ponta de venda num único ecossistema.

## 2. Requisitos Funcionais (RF)

### 2.1 Autenticação, Perfis e Acesso (ACL Híbrido)
- **RF-01**: O sistema deve possuir controle de acesso baseado em grupos (Roles) e permissões (Permissions).
- **RF-02**: Deve ser possível sobrescrever permissões individualmente por usuário, de forma que as permissões do usuário tenham maior precedência (override) em relação ao seu grupo.
- **RF-03**: O sistema deve possuir "Perfis Funcionais", onde um funcionário não precisa ser um usuário com login e e-mail no sistema; ele operará vinculado a um caixa específico através de PIN, crachá biometria (se no futuro implementado).
- **RF-04**: Deve existir um papel de Super Administrador inalterável, com total gerência sobre os módulos, sistema e inquilinos.

### 2.2 Gerenciamento Modular
- **RF-05**: Toda a aplicação deve ser desenvolvida de forma modular (por domínio).
- **RF-06**: Os módulos ativos devem ser lidos globalmente através de um arquivo de configuração `config/system_modules.php`, permitindo habilitar, desabilitar e no futuro plugar novos módulos (ex: Módulo de Fidelidade) sem alterar o core.

### 2.3 Domínios (Módulos Base)
- **RF-07 - Core**: Gerencia configurações, ACL, logging e utilitários globais.
- **RF-08 - Estoque (Inventory)**: Gestão de Categorias, Produtos, Unidades, Códigos de Barras, controle de movimentação (Kardex) e contagem/inventário.
- **RF-09 - Compras (Purchasing)**: Gestão de Fornecedores, Pedidos de compra e entradas para atualização automática de custo e estoque.
- **RF-10 - Vendas (Retaguarda)**: Gestão de Clientes, orçamentos, pedidos de faturamento direto e expedição.
- **RF-11 - PDV (Frente de Caixa)**: Interface rápida projetada ergonomicamente para operação contínua (teclado + leitor). Suporte a Abertura de Caixa, Reforço/Suprimento, Sangria, Fechamento e múltiplos métodos de pagamento.
- **RF-12 - Financeiro (Finance)**: Gestão de Fluxo de Caixa, Contas a Pagar (vinculado a compras), Contas a Receber, Planos de Conta, e fechamento.

### 2.4 API Pública e Documentação
- **RF-13**: O sistema inteiro deve utilizar abordagens híbridas (SSR para views) ou expor API (Padrão JSON BREAD).
- **RF-14**: Exigência de possuir API completamente documentada usando OpenAPI / Swagger (via L5-Swagger).
- **RF-15**: A API deve ser versionada para que no futuro seja consumida por um app Desktop (Electron/React).

## 3. Requisitos Não Funcionais (RNF)

- **RNF-01**: Estrutura física em ambiente Docker contido na pasta local `./.docker` (Nginx, PHP-FPM, MariaDB, Redis).
- **RNF-02**: O código-fonte rodará na pasta local `./www`.
- **RNF-03**: Backend será Laravel versão standalone (latest), puro. **Nenhum uso** de Filament, Breeze, Livewire, ou UI Kits prontos de terceiros.
- **RNF-04**: Aplicação de Padrões: SOLID e Object Calisthenics estrito, utilizando Value Objects para dinheiro, telefones, e-mails, documentos (CPF/CNPJ).
- **RNF-05**: Frontend feito puramente em motor Blade. A arquitetura visual será estritamente componentizada (`<x-button>`, `<x-modal>`, etc).
- **RNF-06**: Folha de estilo manual através de SASS/SCSS. JS customizado e modular com TypeScript compilado via Vite. Sem frameworks JS na visualização do dashboard.
- **RNF-07**: Interface moderna, premium, ágil. Uso mandatório de paleta de cores envolvendo Azul primário (`#455073`), contraste em tons quentes ocre/ouro (`#c0904d`) e azul compatível (`#6077c0`).
