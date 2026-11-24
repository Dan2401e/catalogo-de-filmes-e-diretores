# Roteiro de testes manuais — CineCatalog

Execute no XAMPP com Apache, MySQL, PHP `mysqli`, `fileinfo` e `gd` habilitados. Registre capturas de tela e marque cada teste somente após executá-lo.

| Nº | Teste | Resultado esperado | Status |
|---:|---|---|---|
| 1 | Importar `database/catalogo_filmes.sql` | Banco e três tabelas criados | Pendente |
| 2 | Entrar com admin / 123 após importar o SQL | Conta de demonstração autentica | Pendente |
| 3 | Criar conta própria | Conta criada e sessão iniciada | Pendente |
| 4 | Entrar com senha errada | Mensagem de erro sem autenticar | Pendente |
| 5 | Abrir página interna sem login | Redirecionamento para login | Pendente |
| 6 | Cadastrar diretor com foto | Registro e imagem salvos | Pendente |
| 7 | Editar diretor | Dados atualizados e foto anterior removida quando substituída | Pendente |
| 8 | Cadastrar filme com diretor | Filme relacionado corretamente | Pendente |
| 9 | Tentar excluir diretor com filme | Exclusão bloqueada | Pendente |
| 10 | Cadastrar trailer válido do YouTube | Link aceito | Pendente |
| 11 | Enviar link de outro domínio | Link rejeitado | Pendente |
| 12 | Enviar imagem acima de 3 MB ou arquivo não-imagem | Upload rejeitado | Pendente |
| 13 | Enviar imagem grande válida | Imagem redimensionada para no máximo 1600 × 1600 | Pendente |
| 14 | Combinar busca, diretor, gênero e ano | Resultado respeita todos os filtros | Pendente |
| 15 | Editar filme pela biblioteca | Retorna à biblioteca com filtros preservados | Pendente |
| 16 | Excluir filme pela biblioteca | Filme e capa removidos; filtros preservados | Pendente |
| 17 | Testar em celular | Layout utilizável sem rolagem horizontal geral | Pendente |
| 18 | Sair do sistema | Sessão encerrada e páginas internas protegidas | Pendente |

## Evidências para a apresentação

Capture: login; painel; cadastro/listagem de diretores; cadastro/listagem de filmes; biblioteca com filtros; tentativa de excluir diretor vinculado; upload de imagem; e mensagem de logout.
