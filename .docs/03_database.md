# 3. Modelagem de Dados e Banco

O banco de dados deve refletir o isolamento lógico das permissões. Porém, as tabelas físicas viverão no mesmo esquema do MariaDB. As migrations serão geradas junto aos seus respectivos módulos lógicos.

## Entidades Principais e Relacionamentos Base

### 3.1 Módulo: Core / ACL
- `users`: Armazena operadores e administradores do sistema. Identificador primário UUID/BigInt, `name`, `email`, `password`, `status`.
- `roles`: Tabelas de papéis. Ex: Gerente, Supervisor. `id`, `name`.
- `permissions`: Identificador textual de permissão, ex: `sales.create`, `inventory.delete`.
- `role_has_permissions`: Tabela pivot (role_id, permission_id).
- `user_has_permissions`: **Exceção Granular/Híbrida**. Pivot associando um User e uma Permission isoladamente. Pode servir tanto para adicionar uma atribuição que não está na sua Role, mas principalmente para a propriedade de negação direta anulando permissões providas pelo grupo.

### 3.2 Módulo: PDV (Perfil Funcional)
- `functional_profiles`: Funcionários que vão operar diretamente equipamentos de venda da loja (Caixa Frontal) mas não possuem dashboard ou login sistêmico de gerência.
Atributos centrais: `id`, `name`, `document` (CPF associado via VO), `pin_hash` (senha numérica restrita usada pelo terminal POS), `status`.
- `terminals` (Ou Caixas Físicos/Digitais): `id`, `identifier_name`, `status`.

### 3.3 Módulo: Finances e Transactions
Regra base: Valores monetários **declarados estritamente como `integer` (ou BIGINT, caso necessário por projeção de negócio)** representando o valor mínimo da moeda corrente (Centavos).
- `cash_registers` (Sessões de Abertura de Caixa): Rastreia o fluxo físico do dinheiro correspondente a uma abertura e fechamento humano.
  - Campos: `id`, `terminal_id`, `opened_by` (Relacionamento Polimórfico - pode ser Model `User` ou `FunctionalProfile`), `opened_at`, `closed_at`, `initial_cents`, `final_cents`, `current_status`.
- `transactions`: Lançamentos centralizados (Recebimento de venda, Pagamento de frete, sangria isolada, proventos, descontos de quebra de caixa).
  - Campos: `id`, `type` (EXPENSE/INCOME), `amount_cents`, `method_id`, `payable_id` / `payable_type` (Relacionamento Polimórfico - link pra venda processada, ou fatura de compra).

### 3.4 Módulo: Inventory
- `products`: Tabela central do estoque físico e produtos. `sku` e `barcode` serializados. `price_cents_cost`, `price_cents_sale`. Chaves estrangeiras de pertencimento a marca, categoria e variante se necessário.
- `stock_movements` (Kardex): Registo contínuo de rastreabilidade de estoque baseados em uma política do tipo *append-only log*. Os produtos não podem ser removidos pois o histórico transacional dependerá das variações quantitativas neste registro.
  - Campos: `product_id`, `actor_id` (User_or_Profile polimórfico), `quantity` (inteiro sinalizado, positivo entrada, negativo saída), `type` (IN, OUT, ADJUSTMENT, LOSS), `transaction_motive`.
O valor de Estoque Atual que é listado nas telas será na verdade um campo de sumário ou projeção consolidada/cacheada (`current_stock` no Product) em background da qualita real de `stock_movements` no banco.
