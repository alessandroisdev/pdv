# Desenvolvimento de Sistema ERP/PDV Modular

Este documento define o planejamento e arquitetura para a construção do sistema de gestão, financeiro e controle de estoque voltado para o varejo, conforme os requisitos solicitados.

## 1. Visão Geral do Sistema e Objetivos

O sistema terá uma estrutura robusta, modular e escalável, utilizando as tecnologias:
- backend: Laravel 12x (versão standalone mais recente), MariaDB e Redis.
- Infraestrutura: Dockerisolado na pasta `./.docker` e o source code em `./www`.
- Frontend: Nativo com componentes do motor Blade, estilização em SCSS e comportamento em TypeScript compilado via Vite. Nenhum framework JS ou starter kit PHP será utilizado.
- API: Base isolada com documentação automatizada via Open-API (Swagger), preparada para um futuro terminal PDV em Electron no Desktop.

---

## 2. User Review Required

> [!IMPORTANT]
> Verifique os pontos abaixo para confirmar se estamos perfeitamente alinhados antes de iniciar o desenvolvimento ativo (criação de arquivos no projeto):
> 
> 1. **Estruturas de documentação em `.docs/`**: Eu vou criar 5 arquivos contendo todas as regras detalhadas da engenharia do sistema, posso criar os arquivos?
> 2. **Modularidade**: O painel modular que idealizará a verificação constante de `config/system_modules.php`.
> 3. **Identidade Visual**: Confirmar a utilização das cores primárias: Azul Elegante (`#455073`), Ouro/Ocre de contraste (`#c0904d`) e Variação Complementar (`#6077c0`).

---

## 3. Proposed Changes

### [Docs] Documentação Técnica do Projeto
Após a sua aprovação criarei na pasta **`./.docs/`** um diretório robusto e estruturado contendo:
#### [NEW] .docs/01_requirements.md
- Levantamento de Requisitos Funcionais e Não Funcionais.
#### [NEW] .docs/02_architecture.md
- Definições da arquitetura modular, Design Patterns como Injeção de Dependências, e Object Calisthenics (Value Objects para Money, Email, Phone, CPF/CNPJ).
#### [NEW] .docs/03_database.md
- Relacionamentos (ERD em formato descritivo) entre as entidades: Users, Roles, Permissions, Funcional Profiles e Módulos.
#### [NEW] .docs/04_business_rules.md
- Lógica de restrição do Perfil Funcional onde o funcionário utiliza acesso por biometria/pin ou caixa sem ser um `User` logado formal do sistema, regras híbridas de ACL.
#### [NEW] .docs/05_action_plan.md
- Divisão dos módulos (Estoque, Compras, Vendas, PDV, Financeiro) em fases de entrega.

---

### [Infra] Docker e Ambiente
#### [NEW] .docker/docker-compose.yml
#### [NEW] .docker/nginx/default.conf
#### [NEW] .docker/php/Dockerfile
- Instalação contendo o Nginx, PHP-FPM, extensões essenciais para Laravel e banco MariaDB e Redis.

---

### [Backend] Laravel Modular System
Serão instalados e modificados posteriormente na pasta `./www/`:
- **Estruturação `app/Modules/`**: Retirando foco da pasta app/Http padrão e movendo para os domínios core da aplicação (ex: `app/Modules/POS`).
- **Swagger Integration**: Anotações geradoras pela dependência l5-swagger.

---

### [Frontend] Blade Components e SCSS
- Componentização extensiva através na pasta `resources/views/components`.
- Utilização de classes BEM ou orientadas a utility isoladas mapeadas por mixins do SASS.

## 4. Open Questions

> [!WARNING]
> Você prefere que a autenticação do Perfil Funcional (funcionários não usuários) seja através de uma tela de "Código/PIN" unificada antes do PDV ou deseja que o administrador escolha o caixa e atribua um funcionário temporariamente na abertura?

## 5. Verification Plan
- A primeira validação será garantir a viabilidade das docs e diretórios base.
- Garantir que o conteiner do banco de dados interage corretamente com o Laravel pela porta correspondente.
- Conferir que o design (SCSS) está renderizando de forma ergonômica sem dependências externas.
