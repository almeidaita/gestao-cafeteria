<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (empty($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function validar_csrf(): void {
    $token = $_POST['csrf_token'] ?? '';
    if (!is_string($token) || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(403);
        exit('Solicitação inválida. Volte à página anterior e tente novamente.');
    }
}

function e($valor): string {
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
}
