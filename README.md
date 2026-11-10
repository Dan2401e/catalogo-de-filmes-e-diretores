# 🎬 CineCatalog
### Sistema de gerenciamento de filmes e diretores

<p align="center">
  Uma aplicação web desenvolvida para cadastrar, organizar, administrar e consultar informações cinematográficas em um único ambiente.
</p>

<p align="center">
  <img src="https://img.shields.io/badge/PHP-8.1%2B-777BB4?style=flat-square&logo=php&logoColor=white" alt="PHP">
  <img src="https://img.shields.io/badge/MySQL-Database-4479A1?style=flat-square&logo=mysql&logoColor=white" alt="MySQL">
  <img src="https://img.shields.io/badge/HTML5-Markup-E34F26?style=flat-square&logo=html5&logoColor=white" alt="HTML5">
  <img src="https://img.shields.io/badge/CSS3-Styles-1572B6?style=flat-square&logo=css3&logoColor=white" alt="CSS3">
  <img src="https://img.shields.io/badge/Environment-XAMPP-FB7A24?style=flat-square&logo=xampp&logoColor=white" alt="XAMPP">
  <img src="https://img.shields.io/badge/Version-Development-orange?style=flat-square" alt="Development">
</p>

---

## 📖 Sobre o projeto

O **CineCatalog** é um sistema web de gerenciamento de conteúdo cinematográfico, desenvolvido com o objetivo de centralizar informações sobre filmes e diretores por meio de uma aplicação organizada, intuitiva e funcional.

A plataforma permite administrar registros cinematográficos, relacionar filmes aos seus respectivos diretores e consultar informações por meio de uma biblioteca com recursos de pesquisa e filtragem.

O sistema utiliza PHP para processamento no servidor, MySQL para armazenamento e gerenciamento dos dados e HTML5 e CSS3 para estruturação e apresentação da interface.

A arquitetura foi organizada em módulos, buscando facilitar a manutenção, a integração das funcionalidades e o desenvolvimento colaborativo.

## 🎯 Objetivos do projeto

- Desenvolver uma aplicação web integrada a um banco de dados relacional.
- Implementar operações CRUD para filmes e diretores.
- Construir relacionamentos entre entidades utilizando chaves estrangeiras.
- Implementar autenticação e controle de acesso.
- Desenvolver mecanismos de pesquisa e filtragem.
- Aplicar práticas de segurança no processamento de dados.
- Organizar o código em módulos reutilizáveis.
- Trabalhar com versionamento e colaboração utilizando Git e GitHub.

## ✨ Funcionalidades

### 🔐 Autenticação e controle de acesso

- Sistema de login.
- Validação de credenciais.
- Armazenamento de senhas utilizando hash.
- Gerenciamento de sessões.
- Proteção de páginas restritas.
- Validação CSRF em operações protegidas.

### 🎥 Gerenciamento de filmes

- Cadastro de filmes.
- Consulta e listagem de registros.
- Edição de informações.
- Exclusão de filmes.
- Associação de filmes aos diretores.
- Cadastro de título, ano, gênero e sinopse.
- Registro de links de trailers.
- Upload de capas.

### 🎬 Gerenciamento de diretores

- Cadastro de diretores.
- Consulta e listagem.
- Edição de informações.
- Exclusão de registros.
- Cadastro de nome e nacionalidade.
- Registro de data de nascimento e biografia.
- Upload de fotografias.
- Restrição de exclusão quando existem filmes associados.

### 🔎 Biblioteca cinematográfica

- Exibição dos filmes cadastrados.
- Pesquisa por título.
- Filtragem por diretor.
- Filtragem por gênero.
- Combinação de filtros.
- Consultas ao banco utilizando prepared statements.

## 🧰 Stack tecnológica

| Tecnologia | Finalidade |
|---|---|
| PHP 8.1+ | Backend e processamento das requisições |
| MySQL | Banco de dados relacional |
| HTML5 | Estrutura da aplicação |
| CSS3 | Estilização e responsividade |
| XAMPP | Ambiente local de desenvolvimento |
| Apache | Servidor web local |
| phpMyAdmin | Administração do banco de dados |
| Git | Controle de versão |
| GitHub | Hospedagem do código e colaboração |
| Visual Studio Code | Ambiente de desenvolvimento |

## 🏛️ Arquitetura do projeto

O projeto segue uma organização modular, separando as responsabilidades de configuração, autenticação, persistência, funcionalidades e interface.

### Estrutura do repositório

```text
catalogo-de-filmes-e-diretores/
│
├── README.md
│
└── catalogo-filmes/
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
    └── .gitignore
```

### Responsabilidade dos diretórios

| Diretório | Descrição |
|---|---|
| `assets/` | Recursos visuais e folhas de estilo |
| `biblioteca/` | Consulta e apresentação dos filmes |
| `config/` | Configurações e conexão com o banco |
| `database/` | Scripts SQL de estruturação do banco |
| `diretores/` | Funcionalidades de gerenciamento de diretores |
| `filmes/` | Funcionalidades de gerenciamento de filmes |
| `includes/` | Componentes compartilhados e autenticação |
| `uploads/` | Armazenamento de imagens enviadas |

## 🗄️ Banco de dados

O CineCatalog utiliza o MySQL como sistema de gerenciamento de banco de dados relacional.

**Nome do banco:** `catalogo_filmes`

A estrutura principal é composta por três tabelas:

### 1. usuarios

Responsável pelo armazenamento das informações de autenticação.

| Campo | Descrição |
|---|---|
| `id` | Identificador único |
| `usuario` | Nome de usuário |
| `senha` | Hash da senha |
| `criado_em` | Data de criação |

### 2. diretores

Armazena as informações dos diretores cadastrados.

| Campo | Descrição |
|---|---|
| `id` | Identificador único |
| `nome` | Nome do diretor |
| `nacionalidade` | Nacionalidade |
| `data_nascimento` | Data de nascimento |
| `biografia` | Informações biográficas |
| `foto` | Caminho da imagem |
| `criado_em` | Data de cadastro |

### 3. filmes

Armazena as informações dos filmes e seus relacionamentos.

| Campo | Descrição |
|---|---|
| `id` | Identificador único |
| `diretor_id` | Referência ao diretor |
| `titulo` | Título do filme |
| `ano` | Ano de lançamento |
| `genero` | Gênero cinematográfico |
| `sinopse` | Descrição do filme |
| `trailer_link` | Endereço do trailer |
| `capa` | Caminho da imagem de capa |
| `criado_em` | Data de cadastro |

### Relacionamento entre tabelas

A modelagem utiliza o relacionamento **um para muitos (1:N)** entre diretores e filmes.

```text
       DIRETORES
           |
           | 1
           |
           | N
           |
         FILMES
```

Um diretor pode possuir vários filmes cadastrados, enquanto cada filme está associado a um diretor por meio do campo `diretor_id`.

A integridade referencial é garantida pela chave estrangeira:

```sql
FOREIGN KEY (diretor_id)
REFERENCES diretores(id)
ON UPDATE CASCADE
ON DELETE RESTRICT
```

Essa configuração impede a exclusão de um diretor enquanto existirem filmes associados a ele.

## 🔐 Segurança e boas práticas

A implementação contempla mecanismos destinados a proteger o processamento e a integridade das informações.

| Recurso | Aplicação |
|---|---|
| Password Hashing | Proteção das senhas com `password_hash()` |
| Password Verification | Verificação com `password_verify()` |
| Prepared Statements | Redução dos riscos de SQL Injection |
| CSRF Token | Proteção de operações por formulário |
| Session Management | Controle de autenticação |
| Output Escaping | Uso de `htmlspecialchars()` |
| File Validation | Validação de imagens enviadas |
| Foreign Keys | Integridade dos relacionamentos |

**Nota de segurança:** o projeto está em desenvolvimento. Antes de uma implantação em produção, é necessário revisar as configurações do servidor, as permissões, as credenciais, os uploads e os mecanismos de autenticação.

## 🚀 Instalação e execução

### Pré-requisitos

- Windows ou ambiente compatível.
- XAMPP com Apache e MySQL.
- PHP 8.1 ou superior.
- Visual Studio Code.
- Navegador atualizado.

### 1. Clonar o repositório

Abra o terminal e execute:

```bash
git clone https://github.com/Dan2401e/catalogo-de-filmes-e-diretores.git
```

Entre na pasta do repositório:

```bash
cd catalogo-de-filmes-e-diretores
```

### 2. Configurar o ambiente XAMPP

Copie a pasta `catalogo-filmes` para:

```text
C:\xampp\htdocs\
```

A estrutura deverá ficar assim:

```text
C:\xampp\htdocs\catalogo-filmes\
```

Abra o painel de controle do XAMPP e inicie:

- Apache
- MySQL

### 3. Importar o banco de dados

Acesse o phpMyAdmin:

```text
http://localhost/phpmyadmin
```

Em seguida:

1. Clique em **Importar**.
2. Selecione o arquivo `catalogo_filmes.sql`, localizado em `catalogo-filmes/database/`.
3. Execute a importação.
4. Verifique se o banco `catalogo_filmes` foi criado.

### 4. Configurar a conexão PHP/MySQL

Abra o arquivo:

```text
catalogo-filmes/config/conexao.php
```

Confira as configurações de acesso ao banco de dados.

Exemplo para uma instalação padrão do XAMPP:

```php
$host = "localhost";
$usuario = "root";
$senha = "";
$banco = "catalogo_filmes";
```

Caso seu MySQL utilize outras credenciais, ajuste os valores conforme o ambiente.

### 5. Criar o usuário administrador

O projeto possui um script de inicialização para criação do primeiro administrador.

Com Apache e MySQL ativos, acesse:

```text
http://localhost/catalogo-filmes/criar_admin.php
```

A configuração inicial disponibilizada é:

```text
Usuário: admin
Senha: admin123
```

Após a criação:

1. Acesse a tela de login.
2. Entre utilizando as credenciais iniciais.
3. Altere a senha padrão antes de qualquer uso compartilhado.
4. Exclua imediatamente o arquivo `criar_admin.php`.

**Importante:** não mantenha esse script disponível em um ambiente publicado.

### 6. Executar a aplicação

Acesse:

```text
http://localhost/catalogo-filmes/
```

A aplicação deve ser executada pelo Apache do XAMPP. O Live Server do Visual Studio Code não interpreta arquivos PHP.

## 👥 Equipe e divisão de responsabilidades

O desenvolvimento foi organizado em módulos, permitindo a divisão das atividades e a integração entre os integrantes.

| Integrante | Responsabilidade |
|---|---|
| Pessoa 1 | Autenticação, login e segurança |
| Pessoa 2 | Cadastro e gerenciamento de diretores |
| Pessoa 3 | Cadastro e gerenciamento de filmes |
| Pessoa 4 | Biblioteca, pesquisa e filtros |
| Pessoa 5 | Interface, identidade visual e responsividade |
| Pessoa 6 | Modelagem do banco, conexão e integração |

### Estratégia de integração

A integração é realizada por meio de uma estrutura compartilhada de banco de dados e arquivos comuns.

Para manter a compatibilidade entre os módulos, devem ser respeitados:

- Nomes das tabelas e dos campos.
- Estrutura de diretórios.
- Conexão centralizada com o banco.
- Padrões de validação.
- Utilização de consultas preparadas.
- Organização das funções compartilhadas.

Alterações estruturais devem ser comunicadas à equipe para evitar incompatibilidades.

## 🧪 Validação e testes

A validação do sistema deve contemplar os seguintes cenários:

- [ ] Importação e criação do banco de dados.
- [ ] Conexão entre PHP e MySQL.
- [ ] Autenticação e encerramento de sessão.
- [ ] Proteção das páginas restritas.
- [ ] Cadastro, edição e exclusão de diretores.
- [ ] Cadastro, edição e exclusão de filmes.
- [ ] Associação entre filmes e diretores.
- [ ] Restrição de exclusão de diretores vinculados.
- [ ] Pesquisa e combinação de filtros.
- [ ] Upload e validação das imagens.
- [ ] Integração entre os módulos da equipe.

Os itens devem ser marcados conforme os testes forem efetivamente realizados.

## 🗺️ Roadmap

### Implementação inicial

- [x] Estrutura inicial de diretórios.
- [x] Modelagem inicial do banco de dados.
- [x] Script SQL de criação das tabelas.
- [x] Estrutura de conexão PHP/MySQL.
- [x] Base de autenticação.
- [x] Estrutura inicial dos módulos de filmes e diretores.
- [x] Estrutura inicial da biblioteca e filtros.

### Próximas etapas

- [ ] Finalizar integração dos módulos.
- [ ] Executar testes completos.
- [ ] Revisar segurança da aplicação.
- [ ] Aprimorar responsividade e acessibilidade.
- [ ] Melhorar tratamento de erros.
- [ ] Preparar documentação técnica final.
- [ ] Avaliar implantação em ambiente de produção.

## 🔭 Melhorias futuras

O projeto poderá receber novas funcionalidades conforme sua evolução:

- Sistema de favoritos.
- Avaliações de filmes.
- Comentários dos usuários.
- Paginação da biblioteca.
- Filtros avançados.
- Integração com APIs cinematográficas.
- Diferentes níveis de permissão.
- Histórico de alterações.
- Testes automatizados.
- Configuração por variáveis de ambiente.
- Implantação em servidor de hospedagem.

## 🌿 Versionamento e colaboração

O Git é utilizado para controle de versões, enquanto o GitHub centraliza o código-fonte e facilita a colaboração.

Sugestão de padrão para mensagens de commit:

```text
feat: adiciona nova funcionalidade
fix: corrige comportamento inesperado
docs: atualiza documentação
refactor: reorganiza estrutura do código
style: ajusta interface e estilos
test: adiciona ou atualiza testes
```

Recomenda-se utilizar branches para desenvolver funcionalidades separadamente e realizar a integração por meio de Pull Requests quando possível.

## 📄 Licença

Projeto acadêmico desenvolvido para fins educacionais. A licença de distribuição ainda não foi definida.

---

<p align="center">
  <strong>CineCatalog</strong>
  <br>
  Tecnologia, organização e cinema em um só lugar.
  <br><br>
  Desenvolvido como projeto acadêmico no SENAI.
</p>
