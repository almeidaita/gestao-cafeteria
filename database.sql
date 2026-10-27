CREATE DATABASE IF NOT EXISTS gestao_cafeteria
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gestao_cafeteria;

CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS cardapio (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome_item VARCHAR(100) NOT NULL,
    categoria VARCHAR(50) NOT NULL,
    descricao TEXT,
    preco DECIMAL(10, 2) NOT NULL,
    disponivel BOOLEAN DEFAULT TRUE
);

-- Usuário inicial. A senha será transformada em hash no primeiro login.
INSERT INTO usuarios (nome, usuario, senha)
VALUES ('Gerente', 'admin', 'admin123')
ON DUPLICATE KEY UPDATE usuario = VALUES(usuario);
