<?php
require_once __DIR__ . '/includes/bootstrap.php';
if (isLoggedIn()) redirect(APP_URL . '/dashboard.php');

$errors = [];
$successMsg = getFlash('success');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrf();

    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Basic throttling: max 5 attempts per 15 minutes per session
    $_SESSION['login_attempts'] = $_SESSION['login_attempts'] ?? 0;
    $_SESSION['login_attempts_time'] = $_SESSION['login_attempts_time'] ?? time();
    if (time() - $_SESSION['login_attempts_time'] > 900) {
        $_SESSION['login_attempts'] = 0;
        $_SESSION['login_attempts_time'] = time();
    }

    if ($_SESSION['login_attempts'] >= 5) {
        $errors[] = 'Too many failed attempts. Please try again in a few minutes.';
    } elseif (!isValidEmail($email) || $password === '') {
        $errors[] = 'Please enter a valid email and password.';
    } else {
        $db = Database::getConnection();
        $stmt = $db->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && (int)$user['is_active'] === 1 && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['login_attempts'] = 0;
            logActivity($user['id'], 'login', 'User logged in');
            redirect(APP_URL . '/dashboard.php');
        } else {
            $_SESSION['login_attempts']++;
            $errors[] = 'Invalid email or password.';
            logActivity(null, 'login_failed', 'Failed login for ' . $email);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login · <?= e(APP_NAME) ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="auth-body">
  <div class="auth-card">
    <div class="auth-brand"><i class="fa-solid fa-graduation-cap"></i> <?= e(APP_NAME) ?></div>
    <h2>Welcome back</h2>
    <p class="text-muted">Log in to continue learning.</p>

    <?php if ($successMsg): ?><div class="alert alert-success"><?= e($successMsg) ?></div><?php endif; ?>
    <?php if ($errors): ?>
      <div class="alert alert-danger">
        <ul class="mb-0"><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul>
      </div>
    <?php endif; ?>

    <form method="POST" novalidate>
      <?= csrfField() ?>
      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" required value="<?= e($_POST['email'] ?? '') ?>">
      </div>
      <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <button type="submit" class="btn btn-primary w-100">Log In</button>
    </form>
    <p class="text-center mt-3">Don't have an account? <a href="register.php">Sign up</a></p>
  </div>
</body>
</html>
