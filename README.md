# 🚀 GestãoPDV ERP & Frente de Caixa - Enterprise Edition

![GestãoPDV ERP](https://img.shields.io/badge/Laravel-11%2B-red?style=for-the-badge&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.4-blue?style=for-the-badge&logo=php)
![MySQL](https://img.shields.io/badge/MariaDB-10.4-orange?style=for-the-badge&logo=mariadb)
![Docker](https://img.shields.io/badge/Docker-Sail-2496ED?style=for-the-badge&logo=docker)

Uma plataforma robusta, elegante e ultra-rápida, desenvolvida sob os mais sofisticados patrões de Orientação a Objetos, Design System (Tailwind CSS) e Arquitetura Modular (DDM). Este sistema foi projetado para atuar tanto como um ERP Cloud Backoffice, quanto um Servidor Local On-Premise atuando como Ponto de Venda de Alta Performance.

---

## 🏗️ Solução Técnica: Multi-Caixas em Rede

O GestãoPDV possui **Pessimistic Locking** (`lockForUpdate`) nativo em nível de Banco de Dados. Isso significa que ele foi estruturalmente projetado para que **Você instale este sistema em apenas UM computador servidor (Matriz)** e acesse ele simultaneamente através do IP / Navegador em diversos outros Computadores Clients (Caixas) - sem nenhum risco de que 2 caixas vendendo o mesmo produto no mesmo milissegundo resultem em furo de estoque!

### Topologia de Rede Sugerida:
1. **PC "Servidor/Caixa Central" (IP Fixo Ex: `192.168.1.10`)**: Onde o Docker/XAMPP roda e a Impressora USB primária / Minitérmica TCP é conectada. O Banco de Dados e Certificado A1 residem aqui.
2. **PC "Caixa 02/Mobile/Tablets"**: Acessam via Google Chrome o endereço `http://192.168.1.10/vendas/pdv/terminal`. Tudo funcionará de forma mágica, sincronizando finanças e estoque com a Matriz sem latência.

---

## ⚙️ Implantação Rápida em Produção (Guia Definitivo)

### Passo 1: Preparação do Ambiente Host (Servidor/Matriz)
Certifique-se de que a máquina servidora possui os requesitos básicos para suportar o motor:
- Instalar **Docker Desktop** (com integração WSL2 ativada no Windows).
- (Alternativamente, se não utilizar Docker: Servidor NGINX/Apache nativo, PHP 8.4+ completo com ext-soap, mbstring, dom, ssl e GD ativados, e um MariaDB/MySQL).

### Passo 2: Clonando e Inicializando (Ambiente Docker Sail)
Abra seu terminal `bash` ou `PowerShell` na pasta escolhida para o repositório (`C:\GestaoPDV`):

```bash
# 1. Copie o .env caso não exista
cp .env.example .env

# 2. Suba a Arquitetura de Contêineres Docker (Via Laravel Sail)
./vendor/bin/sail up -d
# Caso o Sail não esteja acessível, suba com: docker compose up -d

# 3. Instale Dependências do Composer Modernas (Pode demorar 1-2 min)
./vendor/bin/sail composer install

# 4. Gere a Chave Criptográfica Mestra e Link de Storage Público
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan storage:link

# 5. Crie as Fundações de Banco de Dados e Popule com Administradores Nativos
./vendor/bin/sail artisan migrate --seed
```

O sistema já está no ar em `http://localhost/`!

### Passo 3: Configurar Certificado Sefaz e Impressão Térmica
Muitos sistemas de Caixa exigem complexidade de DLLs, este **NÃO!**

1. Faça login usando o Admnistrador (Criado pela Seeder ou manualmente).
2. Acesse a barra lateral esquerda **"Configurações Globais" -> "Dados Fiscais (NFC-e)"**.
3. Faça o UPLOAD do seu Arquivo `.PFX` (Certificado A1 oficial) emitido pela integradora e digite a senha real no campo. Sem este arquivo, os XMLs ficarão trancados sem validade.
4. Mude de "Homologação (Ambiente 2)" para "Produção (Ambiente 1)".
5. Navegue até a Aba **"Hardware PDV"**. Confirme a largura da bobina da sua máquina (Geralmente 80mm Epson/Elgin ou 58mm Genérica).
6. Digite o IP da Impressora de Rede na aba correspondente (Ex: `192.168.1.100`) ou configure o nome no spool. Caso tenha app PWA/Desktop Local compilado, a impressão se dará silenciosa sem diálogos CHROME.

---

## 🖥 Aplicativo Desktop Nativo (Para Filiais "Sujas")

Garantindo máxima produtividade para caixas que esbarram no mouse causando interrupções, embalamos um cliente local via Node.js/Electron. Ele esconde a barra de endereços (Travando O PDV no quiosque nativo):

```bash
# Dentro do terminal, navegue para a pasta que contém o core Desktop
cd desktop-pos

# Instale os motores de compilação C++ / Chromium Node
npm install

# Gere o Executável .EXE instalador local! O arquivo sairá na pasta /dist
npm run build:win
```

*Instale o gerado `.exe` em todos os seus terminais Client Windows.* Certifique-se de acessar o arquivo `main.js` antes e ditar o IP fixo que eles olharão pra se conectarem à Matriz! (Aponte-os para o `192.168.x.x`).

---

## 🔒 Segurança em Ambientes Cloud Expostos
Caso tire o sistema do ambiente de loja fisíco offline (Servidor Local On-Premise) e aplique na **Digital Ocean** ou **AWS EC2**, execute as otimizações restritas antes publicacao:

```bash
./vendor/bin/sail artisan config:cache
./vendor/bin/sail artisan route:cache
./vendor/bin/sail artisan view:cache
# Garanta em seu NGINX que a porta 9100 TCP de impressão Minitérmica só possa ser acessada pelo firewall IP fixo da sua Matriz de Loja.
```

---
*GestãoPDV ERP & Sefaz Engine.* Construído para impactar, blindado para crescer.
