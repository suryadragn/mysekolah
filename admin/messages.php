<?php
require_once 'db.php';
if (!isLoggedIn()) redirect('login.php');

$appName = $_ENV['APP_NAME'] ?? 'MySekolah';

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM ms_messages WHERE id = ?");
    $stmt->execute([$id]);
    redirect('messages.php');
}

// Fetch all messages
$messages = $pdo->query("SELECT * FROM ms_messages ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesan Masuk | <?php echo $appName; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        :root { --sidebar-width: 280px; }
        body { display: flex; min-height: 100vh; background: #050810; }
        aside { width: var(--sidebar-width); background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(20px); border-right: 1px solid var(--glass-border); padding: 2rem; display: flex; flex-direction: column; position: fixed; height: 100vh; overflow-y: auto; }
        aside::-webkit-scrollbar { display: none; }
        aside { -ms-overflow-style: none; scrollbar-width: none; }
        .admin-nav { margin-top: 3rem; display: flex; flex-direction: column; gap: 0.5rem; }
        .admin-nav-item { padding: 1rem 1.5rem; border-radius: 12px; color: var(--text-muted); text-decoration: none; transition: 0.3s; display: flex; align-items: center; gap: 12px; }
        .admin-nav-item:hover, .admin-nav-item.active { background: var(--glass); color: var(--secondary); border: 1px solid var(--glass-border); }
        main { margin-left: var(--sidebar-width); flex: 1; padding: 3rem; }
        .msg-card { background: var(--glass); padding: 2rem; border-radius: 20px; border: 1px solid var(--glass-border); margin-bottom: 1.5rem; }
    </style>
</head>
<body>
    <?php 
    $page = 'messages';
    require 'layout/sidebar.php'; 
    ?>

    <main>
        <h1 style="margin-bottom: 2rem;">Pesan Masuk</h1>
        <?php foreach ($messages as $msg): ?>
        <div class="msg-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <div>
                    <h4 style="font-size: 1.25rem;"><?php echo $msg['name']; ?></h4>
                    <p style="color: var(--secondary); font-size: 0.85rem;"><?php echo $msg['email']; ?> • <?php echo date('d M Y H:i', strtotime($msg['created_at'])); ?></p>
                </div>
                <a href="messages.php?delete=<?php echo $msg['id']; ?>" class="btn btn-glass" style="color: var(--accent); font-size: 0.8rem;" onclick="return confirm('Hapus pesan ini?')">Hapus</a>
            </div>
            <p style="color: var(--text-muted);"><?php echo nl2br($msg['message']); ?></p>
        </div>
        <?php endforeach; ?>
        <?php if (empty($messages)): ?>
            <div style="text-align: center; color: var(--text-muted); padding: 5rem;">Belum ada pesan masuk.</div>
        <?php endif; ?>
    </main>
</body>
</html>
