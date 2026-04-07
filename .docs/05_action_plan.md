# 5. Plano de Ação Estrutural Fasiado

A implementação ocorrerá num formato linear priorizando a montagem dos containers como fundação até o topo ergonômico visual. Toda a execução correrá em `./.docker` e `./www`.

## Fase 1: Startup & Infraestrutura (Bootstrap)
- Criação dos assets Docker (`docker-compose.yml`, pasta PHP, pasta nginx, mariadb conf).
- Compilação dos serviços sem travas de portas ou bindings errôneos em backgrounds.
- Init de scaffolding do Laravel (com flags sem testes ou pacotes excessivos `latest`) para `./www/`.
- Limpeza inicial de migrations genéricas.
- Configuração do design base do `Vite` (Criação de folha `app.scss` em cores Azul #455073 e contraste Ocre #c0904d).
- Implementação inicial da injeção dependente modular (`ModuleServiceProvider` e o config `system_modules.php`).

## Fase 2: Core, Objetos e Autenticação ACL
- Ingestão dos `Value Objects` bases no caminho isolado do Core module (Validação de tipo de Email rigorosa, Documentos Brasil (CPF) iterativos).
- Tratativas universais para inteiros cêntimos sob a manipulação segura da grana e valores com `Domain/Money`.
- Criação e montagem do CRUD Web base para Users, Roles e a intricada modelagem de `user_has_permissions` com os testes pertinentes em controller base (ou simulação visual manual) em cima do painel blade Super Admin.

## Fase 3: Operadores Ponto-De-Venda (Perfis Funcionais)
- Geração da arquitetura de login isolada "Sem Email" via PIN/Hash atrelado.
- Estruturação do Model polimórfico provando que um Operator pode ser Profile ou User para fins operacionais em futuros comandos.

## Fase 4: O Coração Físico (Estoque e Compras)
- Conclusão do Módulo `Inventory`: Entidades `Product`, `Category`.
- Submódulo logístico (Kardex auditável). As visualizações CRUD completas baseadas nos componentes Blade SCSS projetados na Fase 1.
- Módulo Simples `Purchasing` gerando aumento quantitativo do estoque pela rotina de ação "ApprovePurchaseAction".

## Fase 5: Operacional Monético (Financeiro)
- Tabelas e fluxos monetários puros sem vinculo físico, as Transações. Entradas de capital, Saídas.

## Fase 6: Retaguarda de Vendas e PDV Client (Front Desk)
- Aplicação das rotinas combinando Operador, Transaction e Products num registro de Pedido/Venda finalizado.
- Elaboração do PDV ágil: Tela otimizada utilizando eventos keypress de navegação Javascript para buscar Produtos, Selecionar Meio de Pagamento numa folha layout separada do dashboard com as devidas transições de state para agilizar frentes longas de clientes.
- Consolidação e validação de todas as tags Swagger injetadas, com liberação da rota visual `l5-swagger` para acesso e debug do Super Administrador ou desenvolvedor Electron futuro.
