<?php
require_once 'auth.php';
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: painel.php');
    exit;
}
validar_csrf();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    header('Location: painel.php');
    exit;
}

$stmt = mysqli_prepare($conn, 'DELETE FROM cardapio WHERE id = ?');
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$excluido = mysqli_stmt_affected_rows($stmt) > 0;
mysqli_stmt_close($stmt);

header('Location: painel.php?status=' . ($excluido ? 'excluido' : ''));
exit;
