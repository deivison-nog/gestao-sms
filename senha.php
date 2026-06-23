<?php
/**
 * senha.php — Utilitário de gerenciamento de usuários (uso local / XAMPP)
 *
 * Acesse via: http://localhost/gestao-sms/public/senha.php
 * Senha de acesso padrão: admin123  (altere a constante SCRIPT_PASSWORD abaixo)
 */

define('SCRIPT_PASSWORD', 'admin123');

session_start();

// ─── Helpers ─────────────────────────────────────────────────────────────────

function parse_env(string $path): array
{
    if (! file_exists($path)) {
        return [];
    }
    $vars = [];
    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }
        if (str_contains($line, '=')) {
            [$key, $value] = explode('=', $line, 2);
            $vars[trim($key)] = trim($value, " \t\n\r\0\x0B\"'");
        }
    }
    return $vars;
}

function get_pdo(array $env): PDO
{
    $host   = $env['DB_HOST']     ?? '127.0.0.1';
    $port   = $env['DB_PORT']     ?? '3306';
    $dbname = $env['DB_DATABASE'] ?? 'gestao_sms';
    $user   = $env['DB_USERNAME'] ?? 'root';
    $pass   = $env['DB_PASSWORD'] ?? '';

    $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";
    return new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
}

function h(string $s): string
{
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

function flash(string $type, string $msg): void
{
    $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
}

function get_flash(): ?array
{
    $f = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $f;
}

// ─── Autenticação simples ─────────────────────────────────────────────────────

if (isset($_POST['script_login'])) {
    if ($_POST['script_password'] === SCRIPT_PASSWORD) {
        $_SESSION['senha_auth'] = true;
    } else {
        $_SESSION['senha_error'] = 'Senha incorreta.';
    }
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

if (empty($_SESSION['senha_auth'])) {
    $error = $_SESSION['senha_error'] ?? null;
    unset($_SESSION['senha_error']);
    ?>
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Acesso — Gestão SMS</title>
        <style>
            * { box-sizing: border-box; margin: 0; padding: 0; }
            body { font-family: system-ui, sans-serif; background: #f0f4f8; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
            .card { background: #fff; border-radius: 10px; padding: 2rem; width: 340px; box-shadow: 0 4px 20px rgba(0,0,0,.1); }
            h1 { font-size: 1.25rem; margin-bottom: 1.5rem; color: #1e293b; text-align: center; }
            label { display: block; font-size: .875rem; color: #475569; margin-bottom: .25rem; }
            input[type=password] { width: 100%; padding: .6rem .75rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 1rem; }
            button { width: 100%; margin-top: 1rem; padding: .7rem; background: #2563eb; color: #fff; border: none; border-radius: 6px; font-size: 1rem; cursor: pointer; }
            button:hover { background: #1d4ed8; }
            .error { color: #dc2626; font-size: .875rem; margin-top: .75rem; text-align: center; }
        </style>
    </head>
    <body>
        <div class="card">
            <h1>🔐 Gestão SMS — Utilitário</h1>
            <form method="POST">
                <label for="pwd">Senha de acesso</label>
                <input type="password" id="pwd" name="script_password" autofocus required>
                <input type="hidden" name="script_login" value="1">
                <button type="submit">Entrar</button>
            </form>
            <?php if ($error): ?>
                <p class="error"><?= h($error) ?></p>
            <?php endif; ?>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// ─── Conexão com o banco ──────────────────────────────────────────────────────

$envPath = __DIR__ . '/.env';
$env = parse_env($envPath);

try {
    $pdo = get_pdo($env);
} catch (PDOException $e) {
    die('<pre style="color:red;padding:2rem">Erro ao conectar ao banco de dados:<br>' . h($e->getMessage()) . '</pre>');
}

// Verifica se as tabelas e colunas necessárias existem
$tablesReady = false;
try {
    $pdo->query('SELECT 1 FROM users LIMIT 1');
    $pdo->query('SELECT 1 FROM roles LIMIT 1');
    // role_id é adicionado por uma migration separada; verifica se já existe
    $pdo->query('SELECT role_id FROM users LIMIT 0');
    $tablesReady = true;
} catch (PDOException) {
    $tablesReady = false;
}

// ─── Ações POST ───────────────────────────────────────────────────────────────

if ($tablesReady && $_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_POST['action'] ?? '';

    // Criar usuário
    if ($action === 'create_user') {
        $name     = trim($_POST['name'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $role_id  = (int)($_POST['role_id'] ?? 0);

        if ($name && $email && $password && $role_id) {
            try {
                $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
                $stmt->execute([$email]);
                if ($stmt->fetch()) {
                    flash('error', 'Já existe um usuário com este e-mail.');
                } else {
                    $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
                    $now  = date('Y-m-d H:i:s');
                    $stmt = $pdo->prepare(
                        'INSERT INTO users (role_id, name, email, password, created_at, updated_at)
                         VALUES (?, ?, ?, ?, ?, ?)'
                    );
                    $stmt->execute([$role_id, $name, $email, $hash, $now, $now]);
                    flash('success', "Usuário \"{$name}\" criado com sucesso!");
                }
            } catch (PDOException $e) {
                flash('error', 'Erro ao criar usuário: ' . $e->getMessage());
            }
        } else {
            flash('error', 'Preencha todos os campos obrigatórios.');
        }
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }

    // Alterar senha
    if ($action === 'change_password') {
        $user_id      = (int)($_POST['user_id'] ?? 0);
        $new_password = $_POST['new_password'] ?? '';

        if ($user_id && strlen($new_password) >= 6) {
            try {
                $hash = password_hash($new_password, PASSWORD_BCRYPT, ['cost' => 12]);
                $stmt = $pdo->prepare('UPDATE users SET password = ?, updated_at = ? WHERE id = ?');
                $stmt->execute([$hash, date('Y-m-d H:i:s'), $user_id]);
                flash('success', 'Senha alterada com sucesso!');
            } catch (PDOException $e) {
                flash('error', 'Erro ao alterar senha: ' . $e->getMessage());
            }
        } else {
            flash('error', 'A senha deve ter pelo menos 6 caracteres.');
        }
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }

    // Alterar perfil (role)
    if ($action === 'change_role') {
        $user_id = (int)($_POST['user_id'] ?? 0);
        $role_id = (int)($_POST['role_id'] ?? 0);

        if ($user_id && $role_id) {
            try {
                $stmt = $pdo->prepare('UPDATE users SET role_id = ?, updated_at = ? WHERE id = ?');
                $stmt->execute([$role_id, date('Y-m-d H:i:s'), $user_id]);
                flash('success', 'Perfil atualizado com sucesso!');
            } catch (PDOException $e) {
                flash('error', 'Erro ao alterar perfil: ' . $e->getMessage());
            }
        }
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }

    // Excluir usuário
    if ($action === 'delete_user') {
        $user_id = (int)($_POST['user_id'] ?? 0);
        if ($user_id) {
            try {
                $pdo->prepare('DELETE FROM users WHERE id = ?')->execute([$user_id]);
                flash('success', 'Usuário excluído.');
            } catch (PDOException $e) {
                flash('error', 'Erro ao excluir: ' . $e->getMessage());
            }
        }
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}

// ─── Carregar dados ───────────────────────────────────────────────────────────

$roles = [];
$users = [];

if ($tablesReady) {
    $roles = $pdo->query('SELECT id, name, slug FROM roles ORDER BY name')->fetchAll();
    $users = $pdo->query(
        'SELECT u.id, u.name, u.email, u.created_at, r.name AS role_name, r.slug AS role_slug
         FROM users u
         LEFT JOIN roles r ON r.id = u.role_id
         ORDER BY u.name'
    )->fetchAll();
}

$flash = get_flash();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Usuários — Gestão SMS</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: system-ui, sans-serif; background: #f0f4f8; color: #1e293b; }
        header { background: #1e40af; color: #fff; padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; }
        header h1 { font-size: 1.2rem; }
        header a { color: #bfdbfe; font-size: .875rem; text-decoration: none; }
        header a:hover { color: #fff; }
        .container { max-width: 960px; margin: 2rem auto; padding: 0 1rem; }
        .card { background: #fff; border-radius: 10px; padding: 1.5rem; box-shadow: 0 2px 10px rgba(0,0,0,.07); margin-bottom: 1.5rem; }
        h2 { font-size: 1rem; font-weight: 600; margin-bottom: 1rem; color: #0f172a; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: .75rem; }
        .grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: .75rem; }
        label { display: block; font-size: .8rem; color: #64748b; margin-bottom: .2rem; }
        input[type=text], input[type=email], input[type=password], select {
            width: 100%; padding: .55rem .7rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: .9rem; background: #f8fafc;
        }
        input:focus, select:focus { outline: none; border-color: #2563eb; background: #fff; }
        .btn { display: inline-block; padding: .55rem 1.1rem; border: none; border-radius: 6px; font-size: .875rem; cursor: pointer; }
        .btn-primary { background: #2563eb; color: #fff; }
        .btn-primary:hover { background: #1d4ed8; }
        .btn-warning { background: #f59e0b; color: #fff; }
        .btn-warning:hover { background: #d97706; }
        .btn-danger { background: #ef4444; color: #fff; }
        .btn-danger:hover { background: #dc2626; }
        .btn-sm { padding: .35rem .7rem; font-size: .8rem; }
        .full { grid-column: 1 / -1; }
        .alert { padding: .75rem 1rem; border-radius: 6px; margin-bottom: 1rem; font-size: .9rem; }
        .alert-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        table { width: 100%; border-collapse: collapse; font-size: .875rem; }
        thead th { background: #f1f5f9; padding: .6rem .75rem; text-align: left; font-weight: 600; color: #475569; }
        tbody td { padding: .6rem .75rem; border-top: 1px solid #f1f5f9; vertical-align: middle; }
        tbody tr:hover td { background: #f8fafc; }
        .badge { display: inline-block; padding: .2rem .6rem; border-radius: 999px; font-size: .75rem; font-weight: 600; }
        .badge-admin { background: #dbeafe; color: #1d4ed8; }
        .badge-gestor { background: #d1fae5; color: #065f46; }
        .badge-tecnico { background: #fef9c3; color: #854d0e; }
        .badge-other { background: #f1f5f9; color: #475569; }
        .form-inline { display: flex; gap: .5rem; align-items: center; }
        .form-inline input, .form-inline select { flex: 1; }
        .warn-box { background: #fef9c3; border: 1px solid #fde047; border-radius: 8px; padding: .75rem 1rem; font-size: .85rem; color: #713f12; margin-bottom: 1rem; }
        .empty { text-align: center; color: #94a3b8; padding: 2rem; font-size: .9rem; }
    </style>
</head>
<body>

<header>
    <h1>🛠️ Gestão SMS — Gerenciamento de Usuários</h1>
    <a href="?logout=1">Sair</a>
</header>

<div class="container">

    <?php if ($flash): ?>
        <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'error' ?>">
            <?= h($flash['msg']) ?>
        </div>
    <?php endif; ?>

    <div class="warn-box">
        ⚠️ Este utilitário é apenas para uso local/desenvolvimento. Não deixe acessível em ambiente de produção.
    </div>

    <?php if (! $tablesReady): ?>
        <div class="card">
            <div class="alert alert-error">
                As tabelas do banco de dados ainda não existem. Execute as migrations primeiro:<br>
                <code style="display:block;margin-top:.5rem;font-size:.9rem">php artisan migrate</code>
                <code style="display:block;margin-top:.25rem;font-size:.9rem">php artisan db:seed</code>
            </div>
        </div>
    <?php else: ?>

    <!-- ─── Criar usuário ─────────────────────────────────────────────── -->
    <div class="card">
        <h2>➕ Criar novo usuário</h2>
        <form method="POST" autocomplete="off">
            <input type="hidden" name="action" value="create_user">
            <div class="grid-3">
                <div>
                    <label for="name">Nome completo *</label>
                    <input type="text" id="name" name="name" required placeholder="Ex: João da Silva">
                </div>
                <div>
                    <label for="email">E-mail *</label>
                    <input type="email" id="email" name="email" required placeholder="joao@gestaosms.local">
                </div>
                <div>
                    <label for="password">Senha *</label>
                    <input type="password" id="password" name="password" required minlength="6" placeholder="Mínimo 6 caracteres">
                </div>
                <div class="full">
                    <label for="role_id">Perfil de acesso *</label>
                    <select id="role_id" name="role_id" required>
                        <option value="">— selecione —</option>
                        <?php foreach ($roles as $role): ?>
                            <option value="<?= $role['id'] ?>">
                                <?= h($role['name']) ?> (<?= h($role['slug']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div style="margin-top:1rem">
                <button type="submit" class="btn btn-primary">Criar usuário</button>
            </div>
        </form>
    </div>

    <!-- ─── Lista de usuários ─────────────────────────────────────────── -->
    <div class="card">
        <h2>👥 Usuários cadastrados (<?= count($users) ?>)</h2>

        <?php if (empty($users)): ?>
            <div class="empty">Nenhum usuário cadastrado. Use o formulário acima para criar.</div>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Perfil</th>
                    <th>Criado em</th>
                    <th>Nova senha</th>
                    <th>Perfil</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($users as $user):
                $slug = $user['role_slug'] ?? '';
                $badgeClass = match($slug) {
                    'administrador' => 'badge-admin',
                    'gestor'        => 'badge-gestor',
                    'tecnico'       => 'badge-tecnico',
                    default         => 'badge-other',
                };
            ?>
                <tr>
                    <td><?= $user['id'] ?></td>
                    <td><?= h($user['name']) ?></td>
                    <td><?= h($user['email']) ?></td>
                    <td>
                        <span class="badge <?= $badgeClass ?>">
                            <?= h($user['role_name'] ?? '—') ?>
                        </span>
                    </td>
                    <td><?= h(substr($user['created_at'] ?? '', 0, 10)) ?></td>

                    <!-- Alterar senha -->
                    <td>
                        <form method="POST" class="form-inline">
                            <input type="hidden" name="action" value="change_password">
                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                            <input type="password" name="new_password" placeholder="Nova senha" minlength="6" required>
                            <button type="submit" class="btn btn-warning btn-sm">Alterar</button>
                        </form>
                    </td>

                    <!-- Alterar perfil -->
                    <td>
                        <form method="POST" class="form-inline">
                            <input type="hidden" name="action" value="change_role">
                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                            <select name="role_id" required>
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?= $role['id'] ?>" <?= $role['slug'] === $slug ? 'selected' : '' ?>>
                                        <?= h($role['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="btn btn-primary btn-sm">Salvar</button>
                        </form>
                    </td>

                    <!-- Excluir -->
                    <td>
                        <form method="POST" onsubmit="return confirm('Excluir o usuário <?= h(addslashes($user['name'])) ?>?')">
                            <input type="hidden" name="action" value="delete_user">
                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                            <button type="submit" class="btn btn-danger btn-sm">Excluir</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>

    <?php endif; ?>

    <p style="text-align:center;color:#94a3b8;font-size:.8rem;margin-top:1rem">
        Gestão SMS — Utilitário local &bull; As senhas são armazenadas com bcrypt (cost 12)
    </p>
</div>

</body>
</html>
