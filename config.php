<?php
// Configuração da conexão com o MySQL. Ajuste conforme seu ambiente.
$host = 'localhost';
$usuario_db = 'root';
$senha_db = '';
$banco = 'gestao_cafeteria';

$conn = mysqli_connect($host, $usuario_db, $senha_db, $banco);
if (!$conn) {
    die('Erro ao conectar ao banco de dados. Confira as configurações em config.php.');
}
mysqli_set_charset($conn, 'utf8mb4');
