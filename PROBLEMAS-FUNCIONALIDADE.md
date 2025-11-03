# Problemas de Funcionalidade - Página de Produtos

## Problemas Identificados ao Clicar nos Produtos

### 1. **MODAL APARECENDO INCORRETAMENTE / OVERLAY AZUL "SELECIONE A UNIDADE..."**
**Severidade: CRÍTICA**

- **Problema**: Um overlay azul grande com o texto "Selecione a unidade..." aparece na parte inferior da tela e cobre outros elementos (cards de produtos), bloqueando a interação do usuário.
- **Causa Provável**: 
  - O select `<select id="unitSelect">` pode estar aberto/pendurado
  - O modal pode não estar fechando corretamente
  - Problema de z-index fazendo o select aparecer fora do modal
  - O modal pode estar sendo exibido incorretamente (position/display)
- **Localização**: 
  - `resources/views/products/compra.blade.php` (linhas 178-183, 506-531)
  - Função `openProductModal()` não está fechando o modal anterior corretamente
  - CSS do modal pode ter problemas de z-index/positioning (linhas 1230-1412)

### 2. **MÚLTIPLOS EVENT LISTENERS SENDO ADICIONADOS**
**Severidade: ALTA**

- **Problema**: Event listeners podem estar sendo adicionados múltiplas vezes, causando comportamentos duplicados.
- **Causa**: 
  - `setupProductCardListeners()` é chamada sem garantir que listeners anteriores sejam removidos corretamente
  - `removeEventListener` pode não funcionar se a função não for a mesma referência
- **Localização**: `resources/views/products/compra.blade.php` (linhas 308-331, 569-604)

### 3. **MODAL NÃO FECHA CORRETAMENTE**
**Severidade: CRÍTICA**

- **Problema**: Quando o modal é fechado (cancelar ou adicionar ao carrinho), ele pode não estar sendo removido completamente do DOM ou pode estar deixando resíduos visuais.
- **Causa**: 
  - Bootstrap Modal pode não estar gerenciando corretamente o `aria-hidden`
  - Event listeners do modal (`shown.bs.modal`, `hidden.bs.modal`) podem estar sendo adicionados múltiplas vezes
  - O select pode não estar sendo resetado quando o modal fecha
- **Localização**: 
  - `resources/views/products/compra.blade.php` (linhas 569-616, 700-701)
  - Função `addToCartFromModal()` fecha o modal, mas pode não estar limpando o estado

### 4. **SELECT DE UNIDADE PODE FICAR "PENDUrado"**
**Severidade: ALTA**

- **Problema**: O select de unidade pode ficar aberto e visível mesmo quando não deveria estar.
- **Causa**: 
  - O select pode não estar sendo fechado corretamente quando o modal fecha
  - Problema de z-index fazendo o dropdown do select aparecer acima de outros elementos
  - O select pode estar fora do modal visualmente mas ainda interativo
- **Localização**: 
  - `resources/views/products/compra.blade.php` (linha 180-183, 506-536)
  - CSS do select pode não estar limitando o dropdown ao modal

### 5. **PROBLEMAS DE Z-INDEX E POSICIONAMENTO**
**Severidade: ALTA**

- **Problema**: Elementos do modal podem aparecer em z-index incorreto ou posicionamento errado.
- **Causa**: 
  - Múltiplos z-index sendo definidos (2100, 2101, 2102, 2103, 2104, 2105)
  - Modal pode estar sendo renderizado fora do viewport correto
  - Backdrop pode estar interferindo com outros elementos
- **Localização**: 
  - `resources/views/products/compra.blade.php` (linhas 1232-1412)
  - Especialmente linhas 1388-1411 com múltiplos z-index

### 6. **Bootstrap PODE NÃO ESTAR CARREGADO**
**Severidade: MÉDIA**

- **Problema**: Há verificações extensivas para Bootstrap, mas pode falhar silenciosamente.
- **Causa**: 
  - Bootstrap JS carregado no final do body (linha 245 de `app.blade.php`)
  - Página de compra pode tentar usar Bootstrap antes de estar completamente carregado
  - Race condition entre carregamento do DOM e carregamento do Bootstrap
- **Localização**: 
  - `resources/views/products/compra.blade.php` (linhas 280-297, 443-449)
  - `resources/views/layouts/app.blade.php` (linha 245)

### 7. **IMAGENS INCONSISTENTES DOS PRODUTOS**
**Severidade: MÉDIA**

- **Problema**: Vários produtos (Feijão, Frango, Leite Integral) exibem a mesma imagem (pacotes vermelhos com hambúrguer/sanduíche).
- **Causa**: 
  - Falta de validação de imagens no banco de dados
  - Imagem padrão sendo usada incorretamente
  - Problema no upload/associação de imagens aos produtos
- **Localização**: 
  - Model `Product` pode não estar validando imagens corretamente
  - Controller pode não estar associando imagens corretamente

### 8. **PROBLEMA COM POINTER-EVENTS**
**Severidade: MÉDIA**

- **Problema**: Elementos podem não ser clicáveis devido a problemas de pointer-events.
- **Causa**: 
  - CSS define `pointer-events: none` em filhos de `.product-clickable` (linha 1081-1082)
  - Mas alguns elementos precisam ser clicáveis (botões dentro do modal)
  - Pode estar bloqueando cliques no modal
- **Localização**: 
  - `resources/views/products/compra.blade.php` (linhas 1080-1087, 1388-1411)

### 9. **MODAL INSTANCE PODE NÃO SER RECRIADA**
**Severidade: MÉDIA**

- **Problema**: A instância do modal Bootstrap pode estar sendo reutilizada incorretamente.
- **Causa**: 
  - `bootstrap.Modal.getInstance()` pode retornar instância antiga com estado corrompido
  - Nova instância só é criada se não existir (linha 555-562)
  - Event listeners podem estar acumulando
- **Localização**: 
  - `resources/views/products/compra.blade.php` (linhas 554-565)

### 10. **VALIDAÇÃO INSUFICIENTE DE DADOS DO PRODUTO**
**Severidade: BAIXA**

- **Problema**: Alguns produtos podem não ter todos os dados necessários (ID, nome, etc.).
- **Causa**: 
  - Validação acontece apenas quando o modal é aberto (linha 434-440)
  - Não há validação preventiva antes de tentar abrir o modal
  - Produtos podem estar vindo do banco sem dados completos
- **Localização**: 
  - `resources/views/products/compra.blade.php` (linhas 353-381, 434-440)

### 11. **PROBLEMA COM LAZY LOADING DE IMAGENS**
**Severidade: BAIXA**

- **Problema**: Imagens podem não estar carregando corretamente com lazy loading.
- **Causa**: 
  - Lazy loading pode estar interferindo com o carregamento inicial
  - Imagens podem não estar sendo observadas corretamente pelo IntersectionObserver
- **Localização**: 
  - `public/js/app.js` (linhas 182-237)
  - `resources/views/products/index.blade.php` (linhas 105-110)

## Resumo de Prioridades

### 🔴 CRÍTICO (Resolver Imediatamente):
1. Modal aparecendo incorretamente / Overlay azul
2. Modal não fecha corretamente

### 🟠 ALTA (Resolver Logo):
3. Múltiplos event listeners
4. Select de unidade "pendurado"
5. Problemas de z-index e posicionamento

### 🟡 MÉDIA (Resolver em Seguida):
6. Bootstrap pode não estar carregado
7. Imagens inconsistentes
8. Problema com pointer-events
9. Modal instance pode não ser recriada

### 🟢 BAIXA (Melhorias):
10. Validação insuficiente de dados
11. Problema com lazy loading

