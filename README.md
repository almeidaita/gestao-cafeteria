# Gestão da Cafeteria (PHP + MySQLi)

## Como executar
1. Instale/abra XAMPP e inicie Apache e MySQL.
2. Copie a pasta `gestao_cafeteria` para `C:\\xampp\\htdocs\\`.
3. Abra `http://localhost/phpmyadmin`.
4. Importe o arquivo `database.sql` na aba **Importar**.
5. Se seu MySQL tiver outra configuração, ajuste `config.php` (host, usuário, senha e banco).
6. Acesse `http://localhost/gestao_cafeteria/login.php`.

## Login inicial
- Usuário: `admin`
- Senha: `admin123`

A senha inicial é atualizada para um hash seguro no primeiro login. Para publicar o projeto na internet, troque a senha inicial e configure credenciais próprias.

## Arquivos
- `login.php`: autenticação.
- `painel.php`: cadastro e listagem dos itens.
- `editar_item.php`: edição de um item existente.
- `excluir_item.php`: exclusão por ID, usando POST e token CSRF.
- `logout.php`: encerra a sessão.
- `config.php`: conexão MySQLi.
- `auth.php`: proteção de páginas e funções de segurança.
- `database.sql`: estrutura e usuário inicial.
- `style.css`: estilos responsivos.

O projeto usa consultas preparadas (`mysqli_prepare`), escape de saída HTML e token CSRF nas operações de alteração.
