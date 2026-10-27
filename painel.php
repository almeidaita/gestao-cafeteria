<?php
require_once 'auth.php';
require_once 'config.php';

$mensagem = '';
$erro = '';
if (isset($_GET['status'])) {
    if ($_GET['status'] === 'cadastrado') $mensagem = 'Item cadastrado com sucesso!';
    if ($_GET['status'] === 'atualizado') $mensagem = 'Item atualizado com sucesso!';
    if ($_GET['status'] === 'excluido') $mensagem = 'Item excluído com sucesso!';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validar_csrf();
    $nome = trim($_POST['nome_item'] ?? '');
    $categoria = trim($_POST['categoria'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $preco = str_replace(',', '.', trim($_POST['preco'] ?? ''));
    $categorias = ['Café', 'Doce', 'Salgado', 'Bebida Gelada'];

    if ($nome === '' || !in_array($categoria, $categorias, true) || !is_numeric($preco) || (float)$preco < 0) {
        $erro = 'Confira os campos. O nome, uma categoria válida e um preço igual ou maior que zero são obrigatórios.';
    } else {
        $preco_float = (float)$preco;
        $stmt = mysqli_prepare($conn, 'INSERT INTO cardapio (nome_item, categoria, descricao, preco) VALUES (?, ?, ?, ?)');
        mysqli_stmt_bind_param($stmt, 'sssd', $nome, $categoria, $descricao, $preco_float);
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            header('Location: painel.php?status=cadastrado');
            exit;
        }
        $erro = 'Não foi possível cadastrar o item.';
        mysqli_stmt_close($stmt);
    }
}

$stmt = mysqli_prepare($conn, 'SELECT id, nome_item, categoria, descricao, preco, disponivel FROM cardapio ORDER BY nome_item ASC');
mysqli_stmt_execute($stmt);
$itens = mysqli_stmt_get_result($stmt);
?>
<!doctype html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Painel | Gestão da Cafeteria</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<header class="topbar">
    <a class="brand" href="painel.php">☕ Gestão da Cafeteria</a>
    <div class="top-actions"><span>Olá, <?= e($_SESSION['usuario_nome'] ?? 'Gerente') ?></span><a class="btn outline" href="logout.php">Sair</a></div>
</header>
<main class="container">
    <section class="page-heading">
        <div><p class="eyebrow">PAINEL ADMINISTRATIVO</p><h1>Cardápio</h1><p class="muted">Cadastre e gerencie os produtos da cafeteria.</p></div>
    </section>
    <?php if ($mensagem): ?><div class="alert success"><?= e($mensagem) ?></div><?php endif; ?>
    <?php if ($erro): ?><div class="alert error"><?= e($erro) ?></div><?php endif; ?>

    <section class="panel">
        <h2>Adicionar item</h2>
        <form method="post" action="painel.php" class="form-grid">
            <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
            <div class="field"><label for="nome_item">Nome do item *</label><input id="nome_item" name="nome_item" maxlength="100" required placeholder="Ex.: Cappuccino"></div>
            <div class="field"><label for="categoria">Categoria *</label>
                <select id="categoria" name="categoria" required>
                    <option value="">Selecione uma categoria</option>
                    <option value="Café">Café</option><option value="Doce">Doce</option>
                    <option value="Salgado">Salgado</option><option value="Bebida Gelada">Bebida Gelada</option>
                </select>
            </div>
            <div class="field"><label for="preco">Preço (R$) *</label><input id="preco" name="preco" type="number" min="0" step="0.01" required placeholder="0,00"></div>
            <div class="field span-2"><label for="descricao">Descrição</label><textarea id="descricao" name="descricao" rows="3" placeholder="Descreva o item (opcional)"></textarea></div>
            <div class="span-2"><button class="btn primary" type="submit">+ Cadastrar item</button></div>
        </form>
    </section>

    <section class="panel">
        <div class="section-title"><div><h2>Itens cadastrados</h2><p class="muted">Produtos registrados no banco de dados.</p></div></div>
        <div class="table-wrap">
        <table>
            <thead><tr><th>Item</th><th>Categoria</th><th>Descrição</th><th>Preço</th><th>Disponível</th><th>Ações</th></tr></thead>
            <tbody>
            <?php if (mysqli_num_rows($itens) === 0): ?>
                <tr><td colspan="6" class="empty">Nenhum item cadastrado ainda.</td></tr>
            <?php else: while ($item = mysqli_fetch_assoc($itens)): ?>
                <tr>
                    <td><strong><?= e($item['nome_item']) ?></strong></td>
                    <td><span class="category"><?= e($item['categoria']) ?></span></td>
                    <td class="description"><?= e($item['descricao']) ?></td>
                    <td class="price">R$ <?= number_format((float)$item['preco'], 2, ',', '.') ?></td>
                    <td><?= $item['disponivel'] ? 'Sim' : 'Não' ?></td>
                    <td><div class="actions">
                        <a class="btn small outline" href="editar_item.php?id=<?= (int)$item['id'] ?>">Editar</a>
                        <form method="post" action="excluir_item.php?id=<?= (int)$item['id'] ?>" onsubmit="return confirm('Tem certeza que deseja excluir este item?');">
                            <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
                            <button class="btn small danger" type="submit">Excluir</button>
                        </form>
                    </div></td>
                </tr>
            <?php endwhile; endif; ?>
            </tbody>
        </table>
        </div>
    </section>
</main>
<footer class="footer">Sistema de gestão do cardápio · Cafeteria</footer>
</body>
</html>
<?php mysqli_stmt_close($stmt); ?>
