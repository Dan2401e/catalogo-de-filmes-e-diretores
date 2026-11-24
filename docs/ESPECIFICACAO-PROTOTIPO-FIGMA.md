# Especificação do protótipo de alta fidelidade — CineCatalog

Este documento orienta a validação/atualização do arquivo Figma. O protótipo deve representar a mesma arquitetura visual e os mesmos fluxos do sistema implementado.

## Direção visual

- Fundo principal: azul-marinho muito escuro (`#07111F`).
- Superfícies: preto azulado (`#0D1828`) e azul-marinho (`#12233B`).
- Cor de ação: azul-elétrico (`#2563EB` ou `#3478F6`).
- Texto principal: quase branco (`#F4F7FB`); texto secundário: cinza azulado (`#A8B5C7`).
- Tipografia sugerida: Inter; hierarquia consistente entre título, subtítulo, rótulos e texto auxiliar.
- Componentes: botões com estados padrão/hover/desabilitado, campos com foco/erro, cartões com bordas discretas, alertas e navegação responsiva.

## Telas obrigatórias do escopo

1. **Login:** marca CineCatalog, campo usuário, campo senha, ação Entrar e mensagem de erro.
2. **Menu principal:** saudação com usuário autenticado e três acessos: Diretores, Filmes e Biblioteca.
3. **Cadastro de diretor:** nome, nacionalidade, data de nascimento, biografia, upload de foto, salvar e cancelar; área de listagem com ações editar/excluir.
4. **Cadastro de filme:** diretor vinculado, título, ano, gênero, sinopse, link de trailer e upload de capa; listagem com ações editar/excluir.
5. **Biblioteca:** busca geral, filtros por diretor, gênero e ano, cards de filmes, detalhes e ações editar/excluir. Representar o estado sem resultados e filtros combinados.

## Estados e responsividade

- Criar estados de sucesso, erro, vazio, carregamento e confirmação de exclusão.
- Desktop: navegação horizontal e grade de cartões.
- Tablet: filtros em duas colunas e cartões em grade reduzida.
- Mobile: menu recolhível, formulários em uma coluna, botões com área de toque confortável e cartões em uma coluna.
- Manter contraste legível, foco visível por teclado e rótulos associados aos campos.

## Fluxo principal

Login → Menu principal → Diretores ou Filmes → Salvar cadastro → Biblioteca → aplicar filtros → editar/excluir → retornar à biblioteca preservando os filtros.

## Checklist de conformidade

- [ ] As cinco telas exigidas estão presentes.
- [ ] A paleta azul-marinho, azul-elétrico e preto está consistente.
- [ ] Componentes reutilizáveis têm variantes de estado.
- [ ] Os formulários representam os campos reais do sistema.
- [ ] A biblioteca representa filtros combináveis por busca, diretor e gênero; o sistema também possui filtro por ano.
- [ ] A tabela/listagem geral apresenta editar e excluir.
- [ ] Os frames desktop e mobile foram prototipados.
- [ ] As interações principais estão conectadas no modo Prototype.
- [ ] O link de compartilhamento está configurado para visualização.

> O arquivo Figma original ainda precisa de acesso de edição para que esta especificação seja aplicada e validada diretamente nele.
