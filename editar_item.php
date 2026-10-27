<?php
require_once 'auth.php';
require_once 'config.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    header('Location: painel.php');
    exit;
}

$stmt = mysqli_prepare($conn, 'SELECT id, nome_item, categoria, descricao, preco FROM cardapio WHERE id = ? LIMIT 1');
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$item = mysqli_fetch_assoc($resultado);
mysqli_stmt_close($stmt);

if (!$item) {
    header('Location: painel.php');
    exit;
}

$erro = '';
$categorias = ['Café', 'Doce', 'Salgado', 'Bebida Gelada'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validar_csrf();
    $nome = trim($_POST['nome_item'] ?? '');
    $categoria = trim($_POST['categoria'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $preco = str_replace(',', '.', trim($_POST['preco'] ?? ''));

    if ($nome === '' || !in_array($categoria, $categorias, true) || !is_numeric($preco) || (float)$preco < 0) {
        $erro = 'Confira os campos. Informe nome, categoria válida e preço igual ou maior que zero.';
        $item = array_merge($item, ['nome_item' => $nome, 'categoria' => $categoria, 'descricao' => $descricao, 'preco' => $preco]);
    } else {
        $preco_float = (float)$preco;
        $update = mysqli_prepare($conn, 'UPDATE cardapio SET nome_item = ?, categoria = ?, descricao = ?, preco = ? WHERE id = ?');
        mysqli_stmt_bind_param($update, 'sssdi', $nome, $categoria, $descricao, $preco_float, $id);
        if (mysqli_stmt_execute($update)) {
            mysqli_stmt_close($update);
            header('Location: painel.php?status=atualizado');
            exit;
        }
        $erro = 'Não foi possível atualizar o item.';
        mysqli_stmt_close($update);
    }
}
?>
<!doctype html>
<html lang="pt-br">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Editar item | Cafeteria</title><link rel="stylesheet" href="style.css"></head>
<body>
<header class="topbar"><a class="brand" href="painel.php">☕ Gestão da Cafeteria</a><div class="top-actions"><a class="btn outline" href="logout.php">Sair</a></div></header>
<main class="container narrow">
    <p class="eyebrow">GERENCIAR CARDÁPIO</p><h1>Editar item</h1><p class="muted">Altere os dados do produto e salve as mudanças.</p>
    <?php if ($erro): ?><div class="alert error"><?= e($erro) ?></div><?php endif; ?>
    <section class="panel">
        <form method="post" action="editar_item.php?id=<?= (int)$id ?>" class="form-grid">
            <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
            <div class="field span-2"><label for="nome_item">Nome do item *</label><input id="nome_item" name="nome_item" maxlength="100" required value="<?= e($item['nome_item']) ?>"></div>
            <div class="field"><label for="categoria">Categoria *</label><select id="categoria" name="categoria" required>
                <?php foreach ($categorias as $cat): ?><option value="<?= e($cat) ?>" <?= $item['categoria'] === $cat ? 'selected' : '' ?>><?= e($cat) ?></option><?php endforeach; ?>
            </select></div>
            <div class="field"><label for="preco">Preço (R$) *</label><input id="preco" name="preco" type="number" min="0" step="0.01" required value="<?= e($item['preco']) ?>"></div>
            <div class="field span-2"><label for="descricao">Descrição</label><textarea id="descricao" name="descricao" rows="4"><?= e($item['descricao']) ?></textarea></div>
            <div class="span-2 actions"><button class="btn primary" type="submit">Salvar alterações</button><a class="btn outline" href="painel.php">Cancelar</a></div>
        </form>
    </section>
</main>
</body>
</html>
