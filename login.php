<?php
session_start();
require_once 'config.php';

if (!empty($_SESSION['usuario_id'])) {
    header('Location: painel.php');
    exit;
}

$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if ($usuario === '' || $senha === '') {
        $erro = 'Preencha o usuário e a senha.';
    } else {
        $stmt = mysqli_prepare($conn, 'SELECT id, nome, usuario, senha FROM usuarios WHERE usuario = ? LIMIT 1');
        mysqli_stmt_bind_param($stmt, 's', $usuario);
        mysqli_stmt_execute($stmt);
        $resultado = mysqli_stmt_get_result($stmt);
        $dados = mysqli_fetch_assoc($resultado);
        mysqli_stmt_close($stmt);

        $senha_valida = false;
        if ($dados) {
            // Aceita a senha inicial em texto puro do SQL fornecido e a converte para hash no primeiro login.
            if (password_verify($senha, $dados['senha'])) {
                $senha_valida = true;
            } elseif (hash_equals((string)$dados['senha'], (string)$senha)) {
                $senha_valida = true;
                $hash = password_hash($senha, PASSWORD_DEFAULT);
                $up = mysqli_prepare($conn, 'UPDATE usuarios SET senha = ? WHERE id = ?');
                mysqli_stmt_bind_param($up, 'si', $hash, $dados['id']);
                mysqli_stmt_execute($up);
                mysqli_stmt_close($up);
            }
        }

        if ($senha_valida) {
            session_regenerate_id(true);
            $_SESSION['usuario_id'] = (int)$dados['id'];
            $_SESSION['usuario_nome'] = $dados['nome'];
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            header('Location: painel.php');
            exit;
        }
        $erro = 'Usuário ou senha incorretos.';
    }
}
?>
<!doctype html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login | Gestão da Cafeteria</title>
<link rel="stylesheet" href="style.css">
</head>
<body class="login-page">
<main class="login-card">
    <div class="brand-icon">☕</div>
    <h1>Gestão da Cafeteria</h1>
    <p class="muted">Entre para administrar o cardápio.</p>
    <?php if ($erro !== ''): ?><div class="alert error"><?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
    <form method="post" action="login.php">
        <label for="usuario">Usuário</label>
        <input id="usuario" name="usuario" type="text" autocomplete="username" required>
        <label for="senha">Senha</label>
        <input id="senha" name="senha" type="password" autocomplete="current-password" required>
        <button class="btn primary full" type="submit">Entrar</button>
    </form>
    <p class="hint">Acesso inicial: <strong>admin</strong> / <strong>admin123</strong></p>
</main>
</body>
</html>
