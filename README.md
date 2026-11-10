# 🎬 CineCatalog — Catálogo de Filmes e Diretores

<p align="center">
  <strong>Uma aplicação web para gerenciamento, organização e consulta de filmes e diretores.</strong>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/PHP-8%2B-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP">
  <img src="https://img.shields.io/badge/MySQL-Database-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
  <img src="https://img.shields.io/badge/HTML5-Frontend-E34F26?style=for-the-badge&logo=html5&logoColor=white" alt="HTML5">
  <img src="https://img.shields.io/badge/CSS3-Styling-1572B6?style=for-the-badge&logo=css3&logoColor=white" alt="CSS3">
  <img src="https://img.shields.io/badge/Status-In%20Development-orange?style=for-the-badge" alt="Em desenvolvimento">
</p>

---

## 📌 Sobre o projeto

O **CineCatalog** é um sistema web desenvolvido para centralizar o cadastro, o gerenciamento e a consulta de informações sobre filmes e seus respectivos diretores.

A aplicação foi idealizada para oferecer uma experiência organizada e intuitiva, permitindo que usuários autorizados administrem o catálogo por meio de operações de cadastro, visualização, atualização e exclusão de registros.

O projeto utiliza PHP no processamento das informações, MySQL para persistência dos dados e HTML/CSS para construção da interface.

Além da implementação das funcionalidades, a arquitetura foi organizada para facilitar a manutenção do código, a integração entre os módulos desenvolvidos pelos integrantes da equipe e a evolução futura do sistema.

## 🎯 Objetivos

- Desenvolver uma aplicação web funcional para gerenciamento de filmes e diretores.
- Implementar persistência de dados utilizando MySQL.
- Estabelecer relacionamentos entre filmes e seus respectivos diretores.
- Aplicar operações CRUD de maneira estruturada.
- Implementar autenticação e controle de acesso.
- Utilizar consultas preparadas para interação segura com o banco de dados.
- Desenvolver uma estrutura modular que facilite a colaboração entre os integrantes.
- Aplicar boas práticas de organização, segurança e manutenção de software.

## ⚙️ Funcionalidades

### 🔐 Autenticação e segurança
- Tela de login para acesso ao sistema.
- Validação das credenciais do usuário.
- Armazenamento seguro de senhas com `password_hash()`.
- Verificação de senhas com `password_verify()`.
- Gerenciamento de sessões PHP.
- Proteção de páginas restritas.
- Validação de formulários com token CSRF.

### 🎥 Gerenciamento de filmes
- Cadastro de filmes.
- Listagem dos registros cadastrados.
- Edição das informações.
- Exclusão de registros.
- Associação entre filmes e diretores.
- Cadastro de título, ano, gênero e sinopse.
- Armazenamento de links de trailers.
- Upload de imagens de capa.

### 🎬 Gerenciamento de diretores
- Cadastro de diretores.
- Listagem de diretores.
- Atualização de informações.
- Exclusão de registros.
- Cadastro de nome, nacionalidade e data de nascimento.
- Armazenamento de biografia e fotografia.
- Restrição de exclusão quando existem filmes associados ao diretor.

### 🔎 Biblioteca e pesquisa
- Exibição dos filmes cadastrados.
- Pesquisa por título.
- Filtro por diretor.
- Filtro por gênero.
- Combinação de filtros.
- Consultas ao banco utilizando prepared statements.

## 🛠️ Tecnologias utilizadas

| Tecnologia | Aplicação no projeto |
|---|---|
| PHP 8+ | Lógica de backend e processamento das requisições |
| MySQL | Banco de dados relacional |
| HTML5 | Estrutura das páginas |
| CSS3 | Estilização e responsividade |
| JavaScript | Recursos de interação, quando utilizados |
| XAMPP | Ambiente local de desenvolvimento |
| phpMyAdmin | Administração e importação do banco de dados |
| Visual Studio Code | Ambiente de desenvolvimento |
| Git e GitHub | Versionamento e colaboração |

## 🏗️ Arquitetura e organização

O projeto utiliza uma organização modular, separando configurações, funcionalidades, páginas, recursos visuais e persistência de dados.

```text
catalogo-filmes/
│
├── assets/
│   ├── css/
│   │   └── style.css
│   └── img/
│       └── sem-imagem.svg
│
├── biblioteca/
│   └── index.php
│
├── config/
│   ├── config.php
│   ├── conexao.php
│   └── funcoes.php
│
├── database/
│   └── catalogo_filmes.sql
│
├── diretores/
│   ├── index.php
│   ├── salvar.php
│   └── excluir.php
│
├── filmes/
│   ├── index.php
│   ├── salvar.php
│   └── excluir.php
│
├── includes/
│   ├── auth.php
│   ├── header.php
│   └── footer.php
│
├── uploads/
│   ├── diretores/
│   └── filmes/
│
├── criar_admin.php
├── index.php
├── login.php
├── logout.php
├── README.md
└── .gitignore
```

### Responsabilidade dos diretórios

| Diretório | Responsabilidade |
|---|---|
| `assets/` | Arquivos visuais e estilos da aplicação |
| `biblioteca/` | Consulta e exibição do catálogo |
| `config/` | Configurações, conexão e funções compartilhadas |
| `database/` | Script SQL para criação da estrutura do banco |
| `diretores/` | Operações de gerenciamento dos diretores |
| `filmes/` | Operações de gerenciamento dos filmes |
| `includes/` | Componentes reutilizáveis e autenticação |
| `uploads/` | Armazenamento das imagens enviadas |

## 🗄️ Modelagem do banco de dados

O banco de dados utiliza o modelo relacional, com três entidades principais: usuários, diretores e filmes.

**Banco de dados:** `catalogo_filmes`

### Tabela: `usuarios`

Responsável por armazenar as informações de autenticação.

| Campo | Tipo | Descrição |
|---|---|---|
| id | INT | Identificador do usuário |
| usuario | VARCHAR | Nome de usuário |
| senha | VARCHAR | Hash da senha |
| criado_em | TIMESTAMP | Data de criação |

### Tabela: `diretores`

Armazena os dados dos diretores cadastrados.

| Campo | Tipo | Descrição |
|---|---|---|
| id | INT | Identificador do diretor |
| nome | VARCHAR | Nome completo |
| nacionalidade | VARCHAR | Nacionalidade |
| data_nascimento | DATE | Data de nascimento |
| biografia | TEXT | Informações biográficas |
| foto | VARCHAR | Caminho da fotografia |
| criado_em | TIMESTAMP | Data de cadastro |

### Tabela: `filmes`

Armazena os dados dos filmes e sua associação com os diretores.

| Campo | Tipo | Descrição |
|---|---|---|
| id | INT | Identificador do filme |
| diretor_id | INT | Referência ao diretor |
| titulo | VARCHAR | Título do filme |
| ano | INT | Ano de lançamento |
| genero | VARCHAR | Gênero cinematográfico |
| sinopse | TEXT | Descrição do filme |
| trailer_link | VARCHAR | Link do trailer |
| capa | VARCHAR | Caminho da imagem de capa |
| criado_em | TIMESTAMP | Data de cadastro |

### Relacionamento entre as entidades

A estrutura utiliza uma relação de **um para muitos (1:N)** entre diretores e filmes.

- Um diretor pode estar associado a vários filmes.
- Cada filme possui uma referência a um diretor.
- A chave estrangeira `diretor_id` garante a integridade referencial.
- A exclusão de um diretor é restringida enquanto existirem filmes associados a ele.

```text
DIRETORES
    |
    | 1
    |
    | N
    |
FILMES
```

A tabela `filmes` utiliza uma chave estrangeira que referencia `diretores.id`, com as regras `ON UPDATE CASCADE` e `ON DELETE RESTRICT`.

## 🔒 Segurança e integridade

A segurança é considerada uma parte importante da implementação.

Entre os mecanismos utilizados estão:

- **Hash de senhas:** armazenamento com `password_hash()` e verificação com `password_verify()`.
- **Consultas preparadas:** utilização de prepared statements para reduzir riscos de SQL Injection.
- **Proteção CSRF:** validação de tokens em operações que modificam dados.
- **Controle de sessão:** gerenciamento da autenticação e proteção de páginas restritas.
- **Escape de saída:** utilização de `htmlspecialchars()` para reduzir riscos de XSS em conteúdos exibidos.
- **Validação de arquivos:** verificação de imagens enviadas, formatos permitidos e tamanho máximo.
- **Integridade referencial:** utilização de chaves estrangeiras para preservar os relacionamentos.

> **Observação:** a implementação atual é uma base de desenvolvimento. Antes de qualquer publicação em ambiente de produção, devem ser realizados testes de segurança, revisão das permissões, configuração de credenciais e revisão dos procedimentos de upload.

## 🚀 Como executar o projeto

### 1. Requisitos

Antes de iniciar, tenha instalado:

- XAMPP.
- PHP 8 ou superior.
- MySQL.
- Visual Studio Code.
- Navegador atualizado.

### 2. Clonar o repositório

No terminal, execute:

```bash
git clone URL_DO_REPOSITORIO
```

Entre na pasta:

```bash
cd catalogo-filmes
```

### 3. Configurar o XAMPP

1. Abra o painel de controle do XAMPP.
2. Inicie os serviços Apache e MySQL.
3. Coloque a pasta do projeto dentro de:

```text
C:\xampp\htdocs\
```

O caminho final deverá ser:

```text
C:\xampp\htdocs\catalogo-filmes
```

### 4. Criar o banco de dados

Acesse o phpMyAdmin:

```text
http://localhost/phpmyadmin
```

Em seguida:

1. Acesse a opção **Importar**.
2. Selecione o arquivo `database/catalogo_filmes.sql`.
3. Execute a importação.
4. Verifique se o banco `catalogo_filmes` foi criado corretamente.

### 5. Configurar a conexão

Abra o arquivo:

```text
config/conexao.php
```

Confira as configurações de conexão com o MySQL.

Exemplo para a configuração padrão do XAMPP:

```php
$host = "localhost";
$usuario = "root";
$senha = "";
$banco = "catalogo_filmes";
```

Caso seu ambiente utilize outra senha ou usuário, ajuste os valores.

### 6. Criar o primeiro usuário administrador

Com Apache e MySQL iniciados, acesse:

```text
http://localhost/catalogo-filmes/criar_admin.php
```

A configuração inicial disponibilizada pelo projeto utiliza:

```text
Usuário: admin
Senha: admin123
```

Após a criação do usuário:

1. Acesse a tela de login.
2. Entre com as credenciais iniciais.
3. Exclua imediatamente o arquivo `criar_admin.php`.

**Importante:** essas credenciais são apenas para inicialização local. Altere a senha antes de utilizar o sistema de forma compartilhada e não mantenha o script de criação de administrador disponível.

### 7. Acessar a aplicação

Abra no navegador:

```text
http://localhost/catalogo-filmes
```

O projeto deve ser executado pelo Apache do XAMPP. Não utilize o Live Server do VS Code para executar arquivos PHP.

## 👥 Organização e colaboração da equipe

O desenvolvimento foi dividido em módulos para permitir que os integrantes trabalhem em partes específicas da aplicação, mantendo uma estrutura comum de integração.

| Integrante | Responsabilidade |
|---|---|
| Pessoa 1 | Login, autenticação e segurança |
| Pessoa 2 | Gerenciamento de diretores |
| Pessoa 3 | Gerenciamento de filmes |
| Pessoa 4 | Biblioteca, pesquisa e filtros |
| Pessoa 5 | Interface, identidade visual e responsividade |
| Pessoa 6 | Banco de dados, conexão e integração |

### Integração entre os módulos

O banco de dados funciona como a estrutura central de comunicação entre as funcionalidades.

Os módulos de filmes e diretores compartilham os relacionamentos definidos no MySQL. A biblioteca consulta essas informações para apresentar os registros e permitir pesquisas.

A conexão centralizada em `config/conexao.php` evita que cada módulo precise implementar uma conexão independente.

### Padrões de desenvolvimento

Para facilitar a integração, recomenda-se:

- Preservar os nomes das tabelas e dos campos definidos no banco.
- Utilizar a conexão centralizada.
- Manter a organização de diretórios.
- Utilizar consultas preparadas.
- Validar os dados antes de armazená-los.
- Evitar duplicação de funções compartilhadas.
- Documentar alterações na estrutura do banco.
- Testar as funcionalidades após integrar novos arquivos.

## 🧪 Testes e validação

Os testes devem verificar o funcionamento individual e integrado dos módulos.

| Área | Validação esperada |
|---|---|
| Banco de dados | Criação das tabelas e relacionamentos |
| Conexão | Comunicação entre PHP e MySQL |
| Autenticação | Login, logout e proteção de páginas |
| Diretores | Cadastro, edição, consulta e exclusão |
| Filmes | Cadastro, edição, consulta e exclusão |
| Relacionamentos | Associação entre filmes e diretores |
| Integridade | Restrição de exclusão de diretores associados |
| Biblioteca | Pesquisa e combinação de filtros |
| Uploads | Validação e armazenamento das imagens |

## 🗺️ Roadmap

O projeto poderá evoluir por meio das seguintes etapas:

- [x] Estrutura inicial do projeto.
- [x] Script inicial de criação do banco de dados.
- [x] Conexão PHP com MySQL.
- [x] Estrutura inicial de autenticação.
- [x] Estrutura inicial de gerenciamento de filmes e diretores.
- [x] Estrutura inicial da biblioteca e dos filtros.
- [ ] Integração definitiva dos módulos da equipe.
- [ ] Testes completos de integração.
- [ ] Revisão geral de segurança.
- [ ] Melhorias de usabilidade e acessibilidade.
- [ ] Preparação para publicação em ambiente de produção.

*Os itens marcados representam a estrutura implementada na versão inicial do projeto; a validação final depende dos testes no ambiente de desenvolvimento e da integração da equipe.*

## 📈 Possíveis melhorias futuras

- Paginação dos resultados.
- Sistema de favoritos.
- Avaliações e comentários.
- Filtros avançados por período e classificação.
- Integração com APIs externas de informações cinematográficas.
- Histórico de alterações.
- Diferentes níveis de permissão para usuários.
- Testes automatizados.
- Configuração de ambiente por variáveis externas.
- Implantação em servidor de hospedagem.

## 📁 Versionamento

O projeto utiliza Git para controle de versões e GitHub para armazenamento remoto do código-fonte.

As alterações devem ser organizadas em commits objetivos e, preferencialmente, desenvolvidas em branches específicas.

Exemplos de mensagens de commit:

```text
feat: adiciona cadastro de diretores
feat: implementa gerenciamento de filmes
fix: corrige conexão com banco de dados
fix: ajusta validação de formulários
docs: atualiza documentação do projeto
refactor: organiza funções compartilhadas
```

## 👨‍💻 Desenvolvimento

Projeto acadêmico desenvolvido em equipe como parte das atividades de desenvolvimento de sistemas do SENAI.

**Projeto:** CineCatalog — Catálogo de Filmes e Diretores  
**Área:** Desenvolvimento Web / Backend / Banco de Dados  
**Instituição:** SENAI

## 📄 Licença

Este projeto foi desenvolvido para fins educacionais. Uma licença de distribuição ainda não foi definida.

---

<p align="center">
  <strong>CineCatalog</strong><br>
  Organização, tecnologia e cinema em um só lugar.
</p>
