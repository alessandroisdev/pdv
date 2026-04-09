# 🚀 Manual de Produção e Arquitetura do ERP SaaS

Este é o **Guia Definitivo** de tudo que o Sistema de Gestão ERP/PDV construído nas últimas fases suporta e executa. Use este manual como Bíblia Arquitetural para sua equipe de implantação/infraestrutura executar o deploy na Nuvem (AWS, DigitalOcean, VPS) e para escalar o negócio comercialmente.

---

## 🏗️ 1. O que o Sistema Faz? (Módulos Corporativos)

O ERP não é mais apenas uma frente de caixa (PDV). Ele é uma máquina robusta **Omnichannel**.

### A. Isolamento Multi-Filial (Tenant por Branch)
- **Como funciona**: Todo banco de dados de Produtos, Vendas, Clientes e Caixas usa uma característica oculta e invisível do núcleo chamada `HasBranchScope` (Global Scope). 
- **Produção**: Ninguém de uma Filial A conseguirá ler o financeiro ou estoque da Filial B. Se você criar o usuário "Gerente Matriz", não precisará filtrar o `branch_id` no código. O motor do ERP cuida disso nativamente!

### B. Desacoplamento Assíncrono (Mensageria)
- **Como funciona**: O envio de e-mails, processamento logístico na madrugada e emissão da Nota Fiscal não rodam na tela de carregamento (AMP). O sistema envia a carga pesada paras as filas do **Redis**, sendo enfileiradas pelo *Laravel Horizon*.
- **Produção**: Garante que o Caixa do PDV libere o cliente em frações de segundo, sem deixar o operador plantado esperando o envio de e-mail ao Servidor WebmaniaBR/Sefaz.

### C. Fila e Websockets em Tempo Real (Eventos)
- **Como funciona**: Com o uso do `Laravel Reverb`, o sistema transaciona avisos Real-Time! Se o Estoque Central faz uma entrada (Inbound), as telas de retaguarda abertas sofrem "*Broadcasting*" em tempo real sem apertar F5.

### D. Logística Avançada (Motor FEFO e WMS)
- **Como funciona**: Abastecimentos de estoque via `WMS_INBOUND` recebem obrigatoriamente um **Lote e uma Data de Vencimento** (Rastreabilidade).
- **A Barreira de Qualidade**: Se um operador bipar (receber) lote retroativo, o motor entra em *Exception Red Alert* (Trava), rejeitando imediatamente sua entrada no acervo, eliminando perdas por validade do estoque fantasma em auditorias. O Histórico OWEN-IT oculta rastros imutáveis de cada edição nos códigos de mercadoria.

### E. Vitrine B2C / E-commerce Omnichannel (Mobile-First PWA)
- **Como funciona**: Seu consumidor visita a Rota `/catalogo` apontando para o QR Code da lanchonete ou URL da loja e abre um Front-End JavaScript de Alta Velocidade.
- **Race Condition Guard**: Ao dar Checkout ali na sacola PWA para o retaguarda, o Back-end dispara um modelo de travamento pessimista de concorrência (`lockForUpdate()`). Se dois comensais pedirem pelo celular a *exata última Pepsi em estoque* ao mesmo tempo, um deles passa o cartão e o do segundo tranca na fração de segundo sem faturar estoques negativos. Incrível tecnologia de escalabilidade Varejista.

### F. CRM Visual (Arrastar e Soltar)
- A gestão comercial não vive em listagens puras. Em Vendas B2B, você desfruta de um Pipeline interativo onde "Oportunidades Cadastradas" são empurradas para Fases Comerciais (Negociação, Perdida, Ganha) como no Monday ou Trello.

### G. O Motor Tributário de IA Passiva (Settings)
- Para o Contador, o form da contabilidade em `/fiscal/configuracoes/tributos` salva blocos de impostos.
- **A Tática *"Regra Geral Mágica (Fallback)"***: Se a empresa for Simples Nacional, o contador amarra ICMS à Regra Principal (Não preenchendo o NCM). Se vender algo sem NCM tabelado na base, o motor intercepta e usa os 4% padronizados sem o faturamento do caixa crashar!

### H. Portal "Receba Sozinho" do Atacado e Gateway PIX
- Se você vende a atacado no boleto para 30 dias (Faturado), ele entra no Dashboard de `/portal`. Os clientes de seu CRM acessam sozinhos. E não apenas vêem PDF's mortos:
- Conectamos uma `PaymentGatewayInterface` (Design Strategy Engine Asaas). Ao clicar no Botão Ver Fatura Vencida na interface dele, o E-commerce explode um visual modal QR Code gerado por APIs em tempo real criando transações "EMV Copia e Cola" pra abater o boleto direto do NuBank dele. (Estão como "Mock" para test-drive seguro que não emite dinheiro ainda).

### I. BI Interno DRE EBITDA
- Classificações automatizadas (Operacionais ou Mercadoriais) nas transações desaguam num DRE (Demonstrativo de Resultado de Exercício) estilo "Escada". Ele purifica as taxas pagas tirando-as do Faturamento Bruto para mostrar ao Dono o **[EBITDA Lucro Líquido Global]**.

---

## 🛠️ 2. Checklist Prático (O Deploy de Produção)

Aqui instruímos seu Sysadmin ou Plataforma a colocar essa Ferrari na Pista em Nuvem na AWS/Digital Oceam.

> [!CAUTION]
> **Aviso de Infraestrutura Obrigatório:**
> O ERP exige que você tenha o Banco de Dados MariaDB/MySQL **E O REDIS**. Caso não ligue o Redis (*Serviço de Cache de Memória*), o Motor Assíncrono Horizon, as Senhas e Websockets quebrarão!

### Comando Mestre de Inicialização VPS
Ao logar (SSH) ou acionar Pipelines do Servidor, aqui está a ordem de santidade do projeto:

```bash
# 1. Configurar o ambiente principal (.env)
cp .env.example .env

# Configuração CRÍTICA requerida no .env:
# CACHE_DRIVER=redis
# QUEUE_CONNECTION=redis
# SESSION_DRIVER=redis
# BROADCAST_DRIVER=reverb

# 2. Instalar Pacotes de Produção (Vendors sem debuggers)
composer install --optimize-autoloader --no-dev

# 3. Fabricar a Chave AES Encriptada
php artisan key:generate

# 4. Implantação e Tabelas de Motor (Opcionalmente com a Flag --seed para dados testes Mock)
php artisan migrate --force

# 5. Otimização Agressiva Laravel 12 (Faz as rotas caírem 5x abaixo no milissegundo de resposta)
php artisan optimize:clear
php artisan optimize

# 6. Atualização Automática de APIs
php artisan l5-swagger:generate
```

### O Triângulo de Execução em Produção
Seu Cloudflare pode apontar para as Páginas Web (Nginx/Apache). Contudo, você precisará usar ferramentas de Servidor em Nuvem como o **SupervisorD** do Kernel Linux para manter TRÊS TRILHAS de processamento eternamente acordadas em segundo plano, ou a plataforma parará processos autônomos.

Esses Comandos NÃO PENSAM, eles trabalham para sempre:

```bash
# Trilha WMS/Sockets (O Servidor do Tempo Real)
php artisan reverb:start

# Trilha do Transacional Assíncrono Logística Automática 
php artisan horizon

# Cron Job Universal (Executa de Minuto em minuto o calculador "Curva ABC WMS")
# DEVE SER COLOCADO NO CRONTAB DE LINUX:
* * * * * cd /caminho-do-projeto && php artisan schedule:run >> /dev/null 2>&1
```

### Chave Pragmática (Nginx Rules)
Para o Servidor de Frente (Traffic Nginx), configure para que a pasta base Pública aponte ao `pdv/www/public` de acordo com a premissa de Frameworks Modernos (Não expor o `/www/app` web aberta, regra de ouro).

> [!TIP]
> **Swagger Para Integrações de Apps Mobile**
> Se um time for desenvolver um App Android pro Motorista logístico de entrega do sistema WMS que construímos, envie eles para o link *`/api/documentation`* e lá entregue um Token!
>
> Para adicionar novos Super Admins com poderes no RBAC de Contabilidade/DRE (Senão os módulos causticos dão erro `403 Access Denied Gate`), injete via código a *Role* "Super Admin" ao ID do Proprietário da Nuvem.

Seus blocos estão em repouso pronto para escalarem. Essa é uma aplicação Nível A. Você pode usar instâncias da Plouger, Forge Laravel, ou Docker AWS Swarm tranquilo e sem medo do Tráfego. É uma Joia Multi-Tenant Absoluta!
