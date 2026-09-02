<?php
require_once __DIR__ . '/includes/bootstrap.php';
requireLogin();
$db = Database::getConnection();
$userId = (int) $_SESSION['user_id'];
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrf();
    $theme = ($_POST['theme'] ?? 'light') === 'dark' ? 'dark' : 'light';
    $db->prepare('UPDATE users SET theme = ? WHERE id = ?')->execute([$theme, $userId]);
    $success = 'Settings saved.';
}

$user = currentUser();
$pageTitle = 'Settings';
include __DIR__ . '/includes/header.php';
?>

<div class="card-panel narrow-panel">
  <h3>Appearance</h3>
  <?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
  <form method="POST">
    <?= csrfField() ?>
    <div class="mb-3">
      <label class="form-label d-block">Theme</label>
      <div class="btn-group" role="group">
        <input type="radio" class="btn-check" name="theme" id="themeLight" value="light" <?= ($user['theme'] ?? 'light') === 'light' ? 'checked' : '' ?>>
        <label class="btn btn-outline-secondary" for="themeLight"><i class="fa-solid fa-sun"></i> Light</label>

        <input type="radio" class="btn-check" name="theme" id="themeDark" value="dark" <?= ($user['theme'] ?? 'light') === 'dark' ? 'checked' : '' ?>>
        <label class="btn btn-outline-secondary" for="themeDark"><i class="fa-solid fa-moon"></i> Dark</label>
      </div>
    </div>
    <button class="btn btn-primary">Save Settings</button>
  </form>
</div>

<div class="card-panel narrow-panel">
  <h3>Data</h3>
  <p class="text-muted">Export or manage your chat history from the AI Chat page's per-conversation menu.</p>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
