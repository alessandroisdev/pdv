# 4. Regras de Negócios e Constraints

## 4.1 Permissões Sobrescritas e ACL Híbrida
A arquitetura se baseia em Role-Based Access Control (RBAC) mas com uma exceção permissiva/restritiva granular conhecida como "User Override Constraint".
- **Comportamento Misto de Policy/Gate do Laravel**: Durante verificações de can() ou nas Policies, a precedência é do elo explícito do usuário.
  1. O gate primeiro deve verificar se existe algum link de permissão direto em `user_has_permissions`. Se ali existir para o context solicitado, a regra de negação ou permissão é retornada imediatamente determinando o futuro do check.
  2. Somente e se a permissão individual for inexistente (nula), o check passa para os papéis (Roles) aos quais o operador encontra-se alocado.
  3. Super Administrador (Hardcoded bypass na regra root).

## 4.2 Caixa e Perfil Funcional
O `Perfil Funcional` tem autonomia unicamente dentro do domínio do PDV (Front Desk/Sales). O Operador Funcional não consegue logar via rotas Web de gestão ERP, gerar relatórios de DRE, ou consultar fornecimento no dashboard administrativo. Seu acesso requer apenas o id do terminal e seu PIN hash.
Entretanto, para evitar perdas de rastreabilidade, qualquer apontamento transacional do PDV (Abertura de Caixa e Registro de Venda na session) detém um registro duplo no banco indicando quem a executou – através de **polimorfismo**:
A referência `operator` salva o tipo `App\Models\User` ou `App\Models\FunctionalProfile`. Legalmente um dono gerente (`User`) ou a funcionária temporária (`Profile`) respondem sob os mesmos trâmites ao realizar transações do terminal, o código e as regras de logs se unem sobre eles de maneira genérica (`abstract Operator`).

## 4.3 Imutabilidade do Estoque (Movimentação)
O pilar de consistência financeira e fiscal requer que o inventário primário parta por lançamentos (Kardex) no `stock_movements`.
Se um produto foi cadastrado e existe movimentação (comprou de fornecedor, vendeu no PDV, ajustou quebra), aquele `Product ID` jamais poderá sofrer *Hard Delete* via SQL DELETE. É mandatório possuir o tratamento e o trait de *SoftDelete* ativo sobre as entidades pai.

## 4.4 Tratamento Rigoroso de Numéricos Sensíveis (Object Calisthenics)
Usar float é banido em propriedades monetárias. Como contramedida de divisibilidade e representação binária, qualquer operação do sistema:
- Recebimento
- Troco Decimal
- Divisão de comissão e parcelamentos, descontos nominais.
Serão armazenados na tabela SQL em seu menor expoente cabível, como Inteiros representando centavos (`cents`, Multiplicador * 100).
A mutabilidade deles internamente utiliza factories de Value Object do domínio `Money`. Se um controlador recebe "20.10" da requisição blade, antes de operar comissões sobre ele tal valor será cooptado sob uma instância imutável do VO, mantendo as frações protegidas.

### 4.5 APIs Totalmente Documentadas e Segurança Open API
Seja a listagem gerencial da web ou a ação do botão no ecrã de toque do caixa, o processador e as lógicas estarão contidas nos Service Classes e todos eles serão roteados sob conectores API RESTful com JSON payload.
Todo este ecossistema necessita de documentação *Self-Generated* através do L5-Swagger (OpenAPI v3 object). Um novo endpoint, nova mutation, nova tipagem, deve resultar na geração da nova doc antes dos commits de release.
Futuras interfaces como App Mobile ou PDV Desktop Electron apenas consumirão a spec estática sem atritos do que há do lado do servidor.
