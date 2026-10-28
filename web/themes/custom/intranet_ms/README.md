# Intranet MS

Tema customizado para a intranet do Governo do Estado de Mato Grosso do Sul,
compatível com Drupal 11.

## Estado atual

- Estrutura inicial do tema e regiões.
- Layout responsivo de cabeçalho, navegação, conteúdo, barra lateral e rodapé.
- Navegação móvel com estado acessível.
- Tokens semânticos centralizados em `css/base/tokens.css`.
- Base de foco visível, link de salto e mensagens do Drupal.
- Página inicial com regiões próprias para apresentação, busca, acessos rápidos,
  comunicados, notícias e agenda.
- Acessos rápidos editáveis por meio do bloco fornecido pelo módulo
  `intranet_core`.

## Design System MS

Os valores atuais em `tokens.css` formam uma base institucional acessível e não
devem ser tratados como homologação do Design System MS. Quando a fonte oficial
(manual, Figma, pacote ou repositório) estiver disponível, cores, tipografia,
espaçamento, raios e elevações devem ser reconciliados nesse arquivo antes da
homologação visual.

O tema não importa fontes externas. Fontes oficiais devem ser hospedadas
localmente para que a intranet funcione sem depender da internet pública.

## Ativação

1. Acesse `Aparência` no Drupal.
2. Instale e defina **Intranet MS** como tema padrão.
3. Em `Estrutura > Layout de blocos`, atribua os blocos às regiões do tema.
4. Reconstrua o cache após mudanças em arquivos Twig ou YAML.
