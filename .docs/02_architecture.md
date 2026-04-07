# 2. Arquitetura do Sistema e Padrões de Projeto

## 2.1 Visão Geral
Dado o requisito estrito para impedir a criação de MVC acoplados via pacotes genéricos, a aplicação utilizará a Modelagem Guiada a Domínio e Módulos. A pasta `/app` nativa do Laravel servirá apenas como bootstrapper e núcleo para serviços genéricos de infraestrutura.

A estrutura de negócios principal será `app/Modules/`.

### Fluxo de Componentes
- HTTP Requisições -> Rotas de cada Módulo -> Controller do Módulo -> Services/Actions -> Repository/Model.

## 2.2 Estrutura de Diretórios Modularizada
Cada módulo deve ter autonomia lógica completa.
```text
/www/app/Modules/
└── Estoque/
    ├── Controllers/         # Interface Http/API e Web
    ├── Services/            # Casos de Uso e Regras de Negócio
    ├── Models/              # Eloquent Models deste Contexto específico
    ├── Repositories/        # Padrão Repository para persistência (Opcional por complexidade)
    ├── ValueObjects/        # Tipos e Valores imutáveis
    ├── Routes/              # api.php e web.php do Módulo
    └── Views/               # (Opcional) Views isoladas se desejável
```

## 2.3 Provedor de Módulos (Service Providers)
O sistema terá um arquivo central estático e configurado:
`config/system_modules.php`
Este arquivo conterá um array listando os diretórios dos módulos ativos.
Existirá um `ModuleServiceProvider` no core do Laravel que percorrerá esse array dinamicamente registrando arquivos de rota, helpers e injetando dependências. Isso previne o acoplamento excessivo e permite um "liga/desliga" ou plug in de módulos adicionais (como Fidelidade ou NF-e).

## 2.4 Padrões SOLID e Object Calisthenics
Uma prioridade e exigência estrita do código é seguir boas práticas e Calisthenics. Destaca-se:

1. **Primitive Obsession**:
Evitaremos dados primitivos em elementos de negócio cruciais. No domínio raiz ou utilitário haverão definições de classes puras (`Value Objects`).
A mais importante é a classe `Money`. O banco salvará SEMPRE os valores em sub-unidade cêntimo (ex: `integer 25000` em vez de `float 250.00`). No entanto ao recuperar no PHP ou processar cálculos, isso será tratado estritamente através do object `new Money(25000)`.

A mesma regra é válida para outros V.O. sensíveis como e-mail (`EmailAddress`) e Identificadores Brasileiros (`DocumentNumber` - validará CPF ou CNPJ no setter).

2. **Single Responsibility & Dependency Inversion**:
Evitaremos código "Fat Model" (modelo gordo) e "Fat Controller". Entidades (Models Eloquent) tratarão primordialmente das queries e mapping do banco e relações. Regras compostas migram para "Services" ou "Actions" (ex: `RegisterSaleAction`), as quais serão mapeadas e resolvidas através do DI container do Laravel.

## 2.5 Componentização Frontend (Blade)
Embora as definições sejam em Blade, o ecossistema funcionará próximo a um framework reativo mantendo isolamento: resources em `resources/views/components/`.
- `layouts/` → Layout Base, e-mail templates, etc
- `ui/` -> Componentes base como inputs, modals, buttons (`<x-ui.button>`).
- `partials/` → Componentes estruturais e modulares como navbars, sidebars, pos-cards.

Scripts e Estilos serão mantidos compativelmente modularizados dentro da resource e então o Vite irá embretar como assets unificados. Nenhuma inserção paralela como Tailwind é permitida. Apenas SCSS próprio.
