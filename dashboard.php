<?php
require_once __DIR__ . '/includes/bootstrap.php';
requireLogin();
$db = Database::getConnection();
$userId = (int) $_SESSION['user_id'];
$user = currentUser();

$stats = [];
$stats['chats'] = $db->prepare('SELECT COUNT(*) c FROM chat_sessions WHERE user_id = ?');
$stats['chats']->execute([$userId]);
$stats['chats'] = $stats['chats']->fetch()['c'];

$stats['messages'] = $db->prepare('SELECT COUNT(*) c FROM messages m JOIN chat_sessions cs ON cs.id = m.chat_session_id WHERE cs.user_id = ?');
$stats['messages']->execute([$userId]);
$stats['messages'] = $stats['messages']->fetch()['c'];

$stats['quizzes'] = $db->prepare('SELECT COUNT(*) c FROM quizzes WHERE user_id = ?');
$stats['quizzes']->execute([$userId]);
$stats['quizzes'] = $stats['quizzes']->fetch()['c'];

$stats['uploads'] = $db->prepare('SELECT COUNT(*) c FROM uploads WHERE user_id = ?');
$stats['uploads']->execute([$userId]);
$stats['uploads'] = $stats['uploads']->fetch()['c'];

$recentChats = $db->prepare('SELECT id, title, updated_at FROM chat_sessions WHERE user_id = ? ORDER BY updated_at DESC LIMIT 5');
$recentChats->execute([$userId]);
$recentChats = $recentChats->fetchAll();

$pageTitle = 'Dashboard';
include __DIR__ . '/includes/header.php';
?>

<div class="welcome-banner">
  <div>
    <h2>Welcome back, <?= e(explode(' ', $user['full_name'])[0]) ?> 👋</h2>
    <p>Ready to learn something new today?</p>
  </div>
  <a href="chat.php" class="btn btn-light"><i class="fa-solid fa-comments"></i> Start a Chat</a>
</div>

<div class="stat-grid">
  <div class="stat-card">
    <i class="fa-solid fa-comments"></i>
    <div><span class="stat-number"><?= (int)$stats['chats'] ?></span><span class="stat-label">Conversations</span></div>
  </div>
  <div class="stat-card">
    <i class="fa-solid fa-message"></i>
    <div><span class="stat-number"><?= (int)$stats['messages'] ?></span><span class="stat-label">Messages Sent</span></div>
  </div>
  <div class="stat-card">
    <i class="fa-solid fa-circle-question"></i>
    <div><span class="stat-number"><?= (int)$stats['quizzes'] ?></span><span class="stat-label">Quizzes Generated</span></div>
  </div>
  <div class="stat-card">
    <i class="fa-solid fa-file-pdf"></i>
    <div><span class="stat-number"><?= (int)$stats['uploads'] ?></span><span class="stat-label">PDFs Uploaded</span></div>
  </div>
</div>

<div class="dashboard-grid">
  <div class="card-panel">
    <h3>Quick Tools</h3>
    <div class="quick-tools">
      <a href="notes/index.php" class="quick-tool"><i class="fa-solid fa-file-lines"></i> Notes Summarizer</a>
      <a href="quiz/index.php" class="quick-tool"><i class="fa-solid fa-circle-question"></i> Quiz Generator</a>
      <a href="flashcards/index.php" class="quick-tool"><i class="fa-solid fa-clone"></i> Flashcards</a>
      <a href="pdf/index.php" class="quick-tool"><i class="fa-solid fa-file-pdf"></i> PDF Q&amp;A</a>
      <a href="chat.php?mode=code" class="quick-tool"><i class="fa-solid fa-code"></i> Code Generator</a>
      <a href="chat.php?mode=math" class="quick-tool"><i class="fa-solid fa-square-root-variable"></i> Math Solver</a>
    </div>
  </div>

  <div class="card-panel">
    <h3>Recent Conversations</h3>
    <?php if (empty($recentChats)): ?>
      <p class="text-muted">No conversations yet. Start chatting!</p>
    <?php else: ?>
      <ul class="recent-chat-list">
        <?php foreach ($recentChats as $c): ?>
          <li><a href="chat.php?id=<?= (int)$c['id'] ?>"><i class="fa-regular fa-message"></i> <?= e($c['title']) ?></a>
              <span class="text-muted small"><?= e(date('M j, g:ia', strtotime($c['updated_at']))) ?></span></li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
