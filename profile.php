<?php
require_once __DIR__ . '/includes/bootstrap.php';
requireLogin();
$db = Database::getConnection();
$userId = (int) $_SESSION['user_id'];
$errors = [];
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'update_profile') {
        $fullName = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        if (strlen($fullName) < 2) $errors[] = 'Name is too short.';
        if (!isValidEmail($email)) $errors[] = 'Invalid email address.';

        if (empty($errors)) {
            $check = $db->prepare('SELECT id FROM users WHERE email = ? AND id != ?');
            $check->execute([$email, $userId]);
            if ($check->fetch()) {
                $errors[] = 'That email is already in use by another account.';
            } else {
                $db->prepare('UPDATE users SET full_name = ?, email = ? WHERE id = ?')->execute([$fullName, $email, $userId]);
                logActivity($userId, 'profile_update', 'Updated profile info');
                $success = 'Profile updated successfully.';
            }
        }
    }

    if ($action === 'change_password') {
        $current = $_POST['current_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        $stmt = $db->prepare('SELECT password_hash FROM users WHERE id = ?');
        $stmt->execute([$userId]);
        $row = $stmt->fetch();

        if (!password_verify($current, $row['password_hash'])) {
            $errors[] = 'Current password is incorrect.';
        } elseif (!isStrongPassword($new)) {
            $errors[] = 'New password must be at least 8 characters with a letter and a number.';
        } elseif ($new !== $confirm) {
            $errors[] = 'New passwords do not match.';
        } else {
            $hash = password_hash($new, PASSWORD_BCRYPT);
            $db->prepare('UPDATE users SET password_hash = ? WHERE id = ?')->execute([$hash, $userId]);
            logActivity($userId, 'password_change', 'Password changed');
            $success = 'Password changed successfully.';
        }
    }
}

$stmt = $db->prepare('SELECT * FROM users WHERE id = ?');
$stmt->execute([$userId]);
$user = $stmt->fetch();

$pageTitle = 'Profile';
include __DIR__ . '/includes/header.php';
?>

<div class="card-panel narrow-panel">
  <h3>Profile Information</h3>
  <?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
  <?php if ($errors): ?><div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul></div><?php endif; ?>

  <form method="POST">
    <?= csrfField() ?>
    <input type="hidden" name="action" value="update_profile">
    <div class="mb-3">
      <label class="form-label">Full Name</label>
      <input type="text" name="full_name" class="form-control" value="<?= e($user['full_name']) ?>" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Email</label>
      <input type="email" name="email" class="form-control" value="<?= e($user['email']) ?>" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Role</label>
      <input type="text" class="form-control" value="<?= e(ucfirst($user['role'])) ?>" disabled>
    </div>
    <button class="btn btn-primary">Save Changes</button>
  </form>
</div>

<div class="card-panel narrow-panel">
  <h3>Change Password</h3>
  <form method="POST">
    <?= csrfField() ?>
    <input type="hidden" name="action" value="change_password">
    <div class="mb-3">
      <label class="form-label">Current Password</label>
      <input type="password" name="current_password" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">New Password</label>
      <input type="password" name="new_password" class="form-control" required minlength="8">
    </div>
    <div class="mb-3">
      <label class="form-label">Confirm New Password</label>
      <input type="password" name="confirm_password" class="form-control" required minlength="8">
    </div>
    <button class="btn btn-primary">Update Password</button>
  </form>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
