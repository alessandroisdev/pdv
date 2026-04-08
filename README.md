<div align="center">
    <img src="https://img.shields.io/badge/Laravel-11%2B-red?style=for-the-badge&logo=laravel" alt="Laravel">
    <img src="https://img.shields.io/badge/PHP-8.4-blue?style=for-the-badge&logo=php" alt="PHP">
    <img src="https://img.shields.io/badge/MariaDB-10.4-orange?style=for-the-badge&logo=mariadb" alt="MariaDB">
    <img src="https://img.shields.io/badge/Electron-28.0-47848F?style=for-the-badge&logo=electron" alt="Electron">
    <img src="https://img.shields.io/badge/Tailwind-CSS-38B2AC?style=for-the-badge&logo=tailwind-css" alt="Tailwind CSS">
    
    <h1>🚀 GestãoPDV Enterprise ERP & Omnichannel</h1>
    <h3>A Arquitetura Definitiva para Automação Comercial de Alto Desempenho</h3>
</div>

---

O **GestãoPDV ERP** é um ecosistema massivo composto pelo *Back-End Matriz Central* e pelo container *Frente de Caixa (Desktop Electron/Totem)*. Desenvolvido para funcionar 100% offline via rede local (Pessimistic Locking de banco) ou hospedado de forma Cloud via NGINX/DigitalOcean, ele varre todos os fluxos de um Supermercado, Restaurante ou Varejo Avançado. 

---

## 🏗️ 1. Instalação e Inicialização do Servidor (Matriz Back-End)

O servidor dita todo o fluxo da loja. Seja instalado num "Computador dos Fundos" da lanchonete (Servidor Local) ou numa VPS Online, é aqui que as APIs, Banco de Dados, Mídias e Configurações Vitais residirão. Recomendamos a arquitetura Docker.

### 🐧 Ambiente Produção em Linux (Ou WSL2 Windows)
Dentro da pasta `www` (Raiz Web do seu projeto Laravel), execute:

```bash
# 1. Copie o arquivo de variáveis de ambiente e crie suas senhas MySQL / JWT Secret.
cp .env.example .env

# 2. Inicialize o Docker Engine orquestrando (Nginx, MariaDB, Redis OPcional)
./vendor/bin/sail up -d
# (Use `docker compose up -d` se já estiver em infraestrutura Linux pura sem Sail).

# 3. Baixe os pacotes Composer e instale as engrenagens base.
./vendor/bin/sail composer install --no-dev --optimize-autoloader

# 4. Gere Chaves e Link Simbólico (CRÍTICO para ler Caches/Fotos do Standby)
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan storage:link

# 5. Implante O Banco de Dados Completo com permissões de ACL (Manager/Caixa)
./vendor/bin/sail artisan migrate --seed

# 6. OTIMIZAÇÃO CRÍTICA PARA PRODUÇÃO - Compilar Rotas, Views e Configs
./vendor/bin/sail artisan config:cache
./vendor/bin/sail artisan route:cache
./vendor/bin/sail artisan view:cache
```

O Back-End ERP web com todos os seus 7 Módulos agora roda firme através de sua rede em `http://localhost:80/` (Ou Pelo IP da sua Lan, Ex: `http://192.168.1.15/...`).

---

## 🛠️ 2. Gerenciando as 7 Engrenagens do Negócio (Walkthrough)

Sua Dashboard Web agora gerencia praticamente a Alma da loja. O menu lateral à sua esquerda será seu mapa de navegação:
*   **A Tesouraria (Contas a Pagar/Receber):** Em *Financeiro -> Relatórios / Tesouraria*, adicione boletos a pagar ou contas a receber (Fiado de clientes). Cada baixa de pagamento entra em lock transacional enviando o dinheiro ao Livro Razão Centralizado de seu Negócio.
*   **Fiscal NFe/NFCe (Sefaz Homologação -> Produção):** Em *Configurações Globais*, suba o arquivo `.PFX` (Formato A1 PKCS12 do seu cliente) e insira a senha. Mude a Chave Geral de Ambiente 2 (Homologação Fake) para Ambiente 1 (Produção). Os Cupons NFCe serão automaticamente montados pelo NfePhp baseados no *NCM* e *CFOP* do Cadastro de Produto.
*   **Máquina CRM & Broadcast:** Fique de olho na aba *Marketing & CRM*. Todo cliente que informar o CPF no checkout ou no Mobile cai na tabela base. Pessoas sem comprar há 60 dias ficam sublinhadas em vermelho e podem tomar um gatilho *Push/Email massivo* informando promoções.

---

## 📱 3. O Totem Digital Self-Service & API Pública

Nós implementamos a "Fase 26". Isso abriu o ERP nativamente para o Lado Consumidor sem precisar mexer em códigos complicados de React de terceiros!

**Gerando o Cardápio Virtual (QRCode)**
Direto de um Celular ou de um Tablet fixado na parede do salão, navegue para o IP `http://192.168.1.x/catalogo`. 
Ele carrega uma bela versão Tailwind Mobile. O cliente põe compras no carrinho, seleciona como quer fechar, e aperta "Comprar".

>  **A Magia:** O Totem envia os pacotes usando a rota Omnichannel Protegida. O ERP capta os itens, abate o estoque sob trancas de sistema sem bugar dois clientes pedindo ao mesmo tempo, e manda a grana bater no livro caixa! Tudo instantâneo! 🛡️

---

## 📺 4. App Desktop Kiosk e A TV Promocional (Digital Signage)

Esqueça Google Chrome no terminal de Frente de Loja (Ponto de Venda onde fica o Atendente/Opérador). Eles poderão fechar abas e mexer no Windows aleatoriamente.
Foi embalado na pasta `./desktop-pos/` a versão robusta nativa em C++/Chromium Kiosk!

### Instalação (Build do App)
Para entregar para o dono do supermercado o arquivo de Instalação Real:
```bash
cd desktop-pos
npm install
npm run build:win   # Gera um belo instalador .EXE para Windows!
# ou
npm run build:linux # Gera arquivo flatpak/Deb.
```
Após instalado no PC, execute o aplicativo _GestãoPDV_.

### A Funcionalidade "Standby Mídia Invisível (Caching Puro)"
A aba Mídia (`standby.html` no Electron core) possui autonomia plena de Wi-Fi e Banda:
1. No Backend Laravel web (Como Gestor), vá em Mídia Standby, determine que "Se inativo após 30 segundos, exiba Mídias". Faça upload de MP4s Pesados de promoção da Coca-Cola e Imagens `JPEG` definindo quantos Segundos de Fade e Carregamento (Duração do Slide) cada Painel rodará em loop.
2. No App Desktop `./desktop-pos`, quando a tela carregar, um túnel em plano de fundo via `Axios` fará download permanente dos MP4 pesados salvando no Disco local `(AppData\Roaming)`. O Backend pode até cair ou ficar sem internet, a Loja NUNCA parará de exibir promoções em FulHD! Todo Movimento de mouse do operador, tira a proteção Digital e faz voltar à caixa registradora de luz nativa!

---

## 🖨️ 5. Impressora ESC/POS USB x TCP (Fiscal x Não Fiscal)

Para instalar a Impressora corretamente, vá ao Menu Lateral Web: Configurações Gerais.
*   **Terminal Local (USB Spooler):** Na configuração da loja, configure a largura para 58mm ou 80mm e informe o nome compartilhado no Windows (Ex: `EPSON TM-20`). Modos Websockets do PHP rodarão e despacharão impressão sem o irritante "_Abrir Diálogo de PDF Chrome_" nativamente, cimentando o ERP na classe A corporativa.
*   **Terminal Distante (Cozinha/TCP):** Caso a impressora (Térmica de Comanda não Fiscal) resida em outro canto (Balcão de salgados) e ligada pelo RJ45 e com um IP 192.168.1.50, basta editar os endpoints de socket raw nas configurações que nosso pacote já abraçará todo o ecossistema e cuspirá comandos brutos nativos!

***"Construído meticulosamente sob arquitetura modular robusta e princípios orientados à Escalabilidade."***
