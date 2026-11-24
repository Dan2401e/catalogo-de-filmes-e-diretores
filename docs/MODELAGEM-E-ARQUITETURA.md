# Modelagem e arquitetura — CineCatalog

## Arquitetura lógica

```text
Navegador (HTML5 + CSS3 + JavaScript)
                |
                v
       Apache / PHP (XAMPP)
                |
        mysqli + prepared statements
                |
                v
          MySQL / InnoDB
          /      |      \\
     usuarios diretores filmes
                         |
                   diretor_id (FK)
```

## Entidades

### usuarios
- `id`: chave primária auto incremento.
- `usuario`: nome de usuário único.
- `senha`: hash da senha.
- `criado_em`: data de criação.

### diretores
- `id`: chave primária auto incremento.
- `nome`, `nacionalidade`, `data_nascimento`, `biografia`, `foto`.
- `criado_em`: data de criação.

### filmes
- `id`: chave primária auto incremento.
- `diretor_id`: chave estrangeira para `diretores.id`.
- `titulo`, `ano`, `genero`, `sinopse`, `trailer_link`, `capa`.
- `criado_em`: data de criação.

## Cardinalidade

`DIRETORES (1) ---- (N) FILMES`.

Cada filme pertence a um diretor. Um diretor pode possuir nenhum, um ou vários filmes. A regra `ON DELETE RESTRICT` impede remover um diretor enquanto houver filmes associados.

## Consultas da biblioteca

A biblioteca monta uma consulta SQL dinâmica, mas todos os valores recebidos do usuário são enviados por prepared statements. Os filtros são independentes e combináveis: busca textual, diretor, gênero e ano.
