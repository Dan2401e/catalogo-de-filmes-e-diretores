# CineCatalog — Catálogo de Filmes e Diretores

Projeto acadêmico SENAI — 2º semestre de 2026.

## Tecnologias
- HTML5 e CSS3
- PHP 8+
- MySQL / MariaDB
- XAMPP
- MySQLi com prepared statements

## Como executar no XAMPP

1. Copie a pasta `catalogo-filmes` para `C:\xampp\htdocs\`.
2. Abra o XAMPP Control Panel e inicie **Apache** e **MySQL**.
3. Acesse `http://localhost/phpmyadmin`.
4. Vá em **Importar**, selecione `database/catalogo_filmes.sql` e execute.
5. Confira os dados de conexão em `config/conexao.php` (padrão XAMPP: usuário `root`, senha vazia).
6. Acesse `http://localhost/catalogo-filmes/criar_admin.php` uma única vez.
7. Faça login em `http://localhost/catalogo-filmes/login.php`:
   - Usuário: `admin`
   - Senha: `admin123`
8. **Apague `criar_admin.php` depois de criar o usuário.**
9. Abra a pasta `C:\xampp\htdocs\catalogo-filmes` no VS Code.

## Funcionalidades
- Login com senha armazenada usando `password_hash` (bcrypt) e validação com `password_verify`.
- Sessões protegidas e token CSRF nos formulários.
- CRUD de diretores com foto.
- CRUD de filmes com capa e validação de trailer do YouTube.
- Bloqueio de exclusão de diretor que tenha filmes vinculados.
- Biblioteca com busca geral e filtros combinados por diretor e gênero.
- Upload de imagem JPG, PNG ou WEBP, limitado a 3 MB.

## Estrutura
- `database/`: script SQL do banco.
- `config/`: conexão, configurações e funções comuns.
- `includes/`: autenticação e layout compartilhado.
- `diretores/`: cadastro, edição e exclusão de diretores.
- `filmes/`: cadastro, edição e exclusão de filmes.
- `biblioteca/`: listagem e filtros.
- `uploads/`: imagens enviadas pelo sistema.
- `assets/`: CSS e imagem padrão.

## Integração da equipe
Todos devem manter os nomes:
- Banco: `catalogo_filmes`
- Tabelas: `usuarios`, `diretores`, `filmes`
- Relação: `filmes.diretor_id` → `diretores.id`

A pasta `config/` deve ser compartilhada para que todos utilizem a mesma conexão e as mesmas funções. Não criem conexões duplicadas em cada página.

## Observações
- A aplicação é uma base inicial de projeto acadêmico e deve ser testada no ambiente da equipe.
- Para produção, configure credenciais fortes, HTTPS, política de permissões e validação adicional dos arquivos.
