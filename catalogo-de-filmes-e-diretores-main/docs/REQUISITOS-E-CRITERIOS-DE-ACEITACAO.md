# Requisitos e critérios de aceitação — CineCatalog

## Requisitos funcionais

| ID | Requisito | Critério de aceitação |
|---|---|---|
| RF01 | Criar conta e autenticar usuário | Usuário consegue cadastrar conta, entrar com credenciais corretas e receber mensagem de erro com credenciais inválidas. |
| RF02 | Proteger páginas internas | Acesso sem sessão ativa redireciona para o login. |
| RF03 | CRUD de diretores | Criar, listar, editar e excluir diretor sem obras vinculadas. |
| RF04 | CRUD de filmes | Criar, listar, editar e excluir filme com diretor existente. |
| RF05 | Relacionar filme a diretor | O formulário oferece diretores cadastrados e grava a chave estrangeira. |
| RF06 | Restringir exclusão de diretor vinculado | A tentativa de excluir diretor com filmes deve ser bloqueada. |
| RF07 | Upload de imagens | JPG, PNG e WEBP válidos são processados e redimensionados; arquivos inválidos são rejeitados. |
| RF08 | Validar trailer | Aceitar URL de vídeo válido do YouTube e rejeitar outros domínios ou identificadores inválidos. |
| RF09 | Biblioteca com filtros | Busca geral, diretor, gênero e ano podem ser combinados. |
| RF10 | Editar/excluir pela biblioteca | As ações devem funcionar e retornar à consulta com os filtros preservados. |

## Requisitos não funcionais

- HTML5 e CSS3 puro, sem framework CSS.
- PHP com mysqli e prepared statements.
- MySQL com InnoDB e `utf8mb4`.
- Senhas com `password_hash()` e `password_verify()`.
- Proteção CSRF, validação de dados e escape de saída.
- Interface responsiva e navegação acessível por teclado.
- Uploads processados com GD e bloqueio de execução de scripts na pasta pública.

## Prioridades

**Must Have:** login, sessões, CRUD de diretores e filmes, banco relacional, vínculo e integridade referencial.  
**Should Have:** busca, filtros combináveis, validações, uploads e responsividade.  
**Could Have:** favoritos, avaliações, paginação e integração com APIs externas (fora do escopo mínimo atual).
