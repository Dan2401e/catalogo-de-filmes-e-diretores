CREATE DATABASE IF NOT EXISTS catalogo_filmes
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE catalogo_filmes;

CREATE TABLE IF NOT EXISTS usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario VARCHAR(60) NOT NULL UNIQUE,
  senha VARCHAR(255) NOT NULL,
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS diretores (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100) NOT NULL,
  nacionalidade VARCHAR(60) DEFAULT NULL,
  data_nascimento DATE DEFAULT NULL,
  biografia TEXT DEFAULT NULL,
  foto VARCHAR(255) DEFAULT NULL,
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS filmes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  diretor_id INT NOT NULL,
  titulo VARCHAR(150) NOT NULL,
  ano YEAR NOT NULL,
  genero VARCHAR(60) NOT NULL,
  sinopse TEXT DEFAULT NULL,
  trailer_link VARCHAR(500) DEFAULT NULL,
  capa VARCHAR(255) DEFAULT NULL,
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_filmes_diretores
    FOREIGN KEY (diretor_id) REFERENCES diretores(id)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  INDEX idx_filmes_titulo (titulo),
  INDEX idx_filmes_diretor (diretor_id),
  INDEX idx_filmes_genero (genero),
  INDEX idx_filmes_ano (ano),
  INDEX idx_filmes_genero_ano (genero, ano)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Conta de demonstração exigida para avaliação local (usuário: admin / senha: 123).
-- Remova ou altere esta conta antes de disponibilizar o sistema fora do ambiente acadêmico.
INSERT IGNORE INTO usuarios (usuario, senha)
VALUES ('admin', '$2y$12$i/JB3JCFD3xrYSGb62jTouTiwlLKNe6Y0FoEO7NpkStnSmpdWGBSS');
