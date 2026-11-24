# CineCatalog — Catálogo de Filmes e Diretores

Aplicação web acadêmica do Grupo 1 do curso Técnico em Desenvolvimento de Sistemas do SENAI. O CineCatalog organiza diretores e suas filmografias, com autenticação, gerenciamento de dados e biblioteca pesquisável.

**Identidade visual:** azul-marinho, azul-elétrico e preto.  
**Tecnologias:** HTML5, CSS3 puro, PHP 8.1+, MySQL 8+, `mysqli`, JavaScript e GD.  
**Repositório:** https://github.com/Dan2401e/catalogo-de-filmes-e-diretores

## Integração TMDB

O CineCatalog consulta o TMDB para popularidades, lançamentos, melhores avaliações, descoberta, pesquisa, detalhes, elenco, diretores e trailers.

A página inicial redireciona diretamente para a descoberta TMDB. Os módulos de cadastro local continuam disponíveis para atendimento aos requisitos acadêmicos de CRUD, mas não são mais a experiência principal.

1. Crie um token Read Access Token no painel do TMDB.
2. Configure `CINECATALOG_TMDB_TOKEN` no ambiente ou adicione o valor em `config/local.php` no campo `tmdb_token`.
3. O token nunca é enviado ao HTML ou JavaScript.
4. As respostas são armazenadas em `tmdb_cache` por seis horas.
5. Cada usuário possui favoritos individuais em `favoritos`.

**Atribuição obrigatória:** "This product uses the TMDB API but is not endorsed or certified by TMDB."

## Funcionalidades

- Cadastro de usuários com senha armazenada por `password_hash()` e login com `password_verify()`; conta de demonstração local `admin` / `123` para avaliação.
- Sessões protegidas e encerramento de sessão.
- CRUD de diretores, incluindo foto, biografia e nacionalidade.
- CRUD de filmes, incluindo diretor relacionado, sinopse, gênero, ano, capa e trailer.
- Bloqueio de exclusão de diretores que possuam filmes vinculados (`ON DELETE RESTRICT`).
- Upload de imagens JPG, PNG e WEBP, com validação de MIME e redimensionamento via GD para até 1600 × 1600 px.
- Biblioteca com busca geral e filtros combináveis por diretor, gênero e ano.
- Edição e exclusão a partir da biblioteca, preservando os filtros ativos.
- Consultas preparadas, token CSRF e escape de saída HTML.

## Estrutura

```text
.
├── assets/
│   ├── css/style.css
│   ├── img/sem-imagem.svg
│   └── js/
├── biblioteca/index.php
├── config/
│   ├── config.php
│   ├── conexao.php
│   ├── funcoes.php
│   └── local.example.php
├── database/catalogo_filmes.sql
├── diretores/
├── filmes/
├── includes/
├── uploads/
├── cadastro.php
├── index.php
├── login.php
└── logout.php
```

## Requisitos do ambiente

- XAMPP com Apache e MySQL/MariaDB.
- PHP 8.1 ou superior, com extensões `mysqli`, `fileinfo` e `gd` habilitadas.
- MySQL 8.0 ou versão compatível com `InnoDB` e `utf8mb4`.
- Navegador atualizado.

### Ativar extensões no XAMPP

Abra `C:\xampp\php\php.ini`, localize as linhas das extensões `mysqli`, `fileinfo` e `gd`, remova o `;` inicial caso exista e reinicie o Apache. Confirme em `http://localhost/dashboard/phpinfo.php` ou com `php -m` no terminal. A extensão GD é necessária para o redimensionamento dos uploads.

## Instalação local

1. Inicie Apache e MySQL no painel do XAMPP.
2. Clone o repositório para `C:\xampp\htdocs\catalogo-de-filmes-e-diretores`:

   ```bash
   git clone https://github.com/Dan2401e/catalogo-de-filmes-e-diretores.git
   ```

3. Abra `http://localhost/phpmyadmin`, importe `database/catalogo_filmes.sql` e confirme a criação do banco `catalogo_filmes`.
4. Copie `config/local.example.php` para `config/local.php` e ajuste host, usuário, senha, banco e porta conforme seu MySQL. O arquivo `local.php` é ignorado pelo Git.
5. Confirme que `uploads/diretores` e `uploads/filmes` possuem permissão de escrita para o Apache.
6. Acesse `http://localhost/catalogo-de-filmes-e-diretores/login.php`. O SQL cria uma conta de demonstração local: usuário `admin`, senha `123`.
7. Para uso da equipe, crie uma conta própria em `cadastro.php`. Remova ou altere a conta de demonstração antes de qualquer publicação fora do ambiente acadêmico..

> O projeto deve ser executado pelo Apache do XAMPP. O Live Server do VS Code não interpreta PHP.

## Banco de dados

O script SQL cria três tabelas:

- `usuarios`: credenciais e data de criação.
- `diretores`: dados biográficos e fotografia.
- `filmes`: dados cinematográficos, capa, trailer e chave estrangeira `diretor_id`.

A relação é de um diretor para muitos filmes. A chave estrangeira usa `ON DELETE RESTRICT` para preservar a integridade referencial.

## Segurança implementada

- Senhas com `password_hash()` e `password_verify()`.
- Regeneração do identificador de sessão após autenticação.
- Cookies de sessão `HttpOnly`, `SameSite=Lax` e `Secure` quando HTTPS está ativo.
- Modo estrito e uso exclusivo de cookies na sessão.
- Tokens CSRF nos formulários de alteração.
- Prepared statements em operações com dados recebidos.
- Validação de arquivos por MIME real, limite de tamanho e dimensões.
- Redimensionamento de imagens com GD e nomes aleatórios.
- Bloqueio de execução de scripts dentro de `uploads/` e acesso direto às pastas internas.
- Credenciais fora do repositório por arquivo local ou variáveis de ambiente.

## Testes de aceitação

Use esta lista durante a validação no XAMPP. Marque os itens somente depois de executá-los no ambiente local.

- [ ] Importar o banco e estabelecer conexão.
- [ ] Criar conta, entrar e sair.
- [ ] Confirmar que páginas internas redirecionam visitantes sem sessão.
- [ ] Cadastrar, editar e excluir um diretor sem filmes.
- [ ] Tentar excluir diretor com filme vinculado e confirmar o bloqueio.
- [ ] Cadastrar, editar e excluir filmes.
- [ ] Enviar imagens JPG, PNG e WEBP e confirmar o redimensionamento.
- [ ] Rejeitar arquivo que não seja imagem, arquivo acima de 3 MB e imagem com dimensões excessivas.
- [ ] Validar link de vídeo do YouTube e rejeitar outros domínios/links inválidos.
- [ ] Combinar busca, diretor, gênero e ano na biblioteca.
- [ ] Editar e excluir filme pela biblioteca e confirmar que os filtros permanecem ativos.
- [ ] Verificar a experiência em desktop e celular.

## Divisão da equipe

| Integrante | Responsabilidade principal |
|---|---|
| Ana Clara | Autenticação e segurança |
| Fernanda | Gerenciamento de diretores |
| Asaph | Gerenciamento de filmes |
| Daniel | Interface e identidade visual |
| Adrielly | Biblioteca, pesquisa e filtros |
| Kayke | Banco de dados, testes e documentação |

A equipe deve acompanhar as Sprints, integrar os módulos, registrar alterações no GitHub e participar da validação final.

## Protótipo Figma

Link compartilhado pela equipe: https://www.figma.com/proto/LLr9U5Hm5KBIAtBJKsUnYC/Cat%C3%A1logo-de-Filmes-e-Diretores-%E2%80%93-Prot%C3%B3tipo

A especificação de telas e estados está em `docs/ESPECIFICACAO-PROTOTIPO-FIGMA.md`. O protótipo deve ser conferido e atualizado no Figma com permissão de edição.

## Entregas do semestre

Conforme o escopo do SENAI: planejamento e requisitos; cronograma e divisão de tarefas; identidade visual; protótipo de alta fidelidade no Figma; frontend; modelagem MySQL; backend PHP/MySQL; sistema funcional; relatório técnico em padrão ABNT; e apresentação em PowerPoint, PDF ou Canva.

## Limitações conhecidas e próximos passos

- O protótipo Figma deve ser conferido junto às telas finais e aos requisitos da atividade.
- Os testes funcionais dependem de execução local com MySQL e GD habilitado.
- O projeto é acadêmico e não deve ser publicado em produção sem revisão adicional de segurança, backup e política de acesso.

---

**CineCatalog · Grupo 1 · SENAI · 2026**
