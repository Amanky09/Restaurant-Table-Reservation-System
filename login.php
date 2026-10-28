<?php
require_once __DIR__ . '/auth.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    $configured = getenv('ADMIN_PASSWORD') ?: '';
    if ($configured !== '' && hash_equals($configured, (string)($_POST['password'] ?? ''))) {
        session_regenerate_id(true);
        $_SESSION['admin'] = true;
        header('Location: admin.php');
        exit;
    }
    $error = $configured === '' ? 'Set ADMIN_PASSWORD before using the admin panel.' : 'Incorrect password.';
}
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Admin login</title><link rel="stylesheet" href="style.css"></head><body>
<main class="container" style="max-width:500px;padding-top:4rem"><section class="form-section"><h1>Admin login</h1>
<?php if ($error): ?><p class="alert alert-error"><?= h($error) ?></p><?php endif; ?>
<form method="post"><input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>"><div class="form-group"><label for="password">Password</label><input id="password" type="password" name="password" required autofocus></div><button class="btn btn-primary" type="submit">Sign in</button></form>
</section><p><a href="index.php">← Return to reservations</a></p></main></body></html>
