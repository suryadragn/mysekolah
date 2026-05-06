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
    <link rel="stylesheet" href="../assets/css/style.css?v=<?php echo time(); ?>">
    <?php echo $themeCss ?? ''; ?>
</head>
<body class="admin-body">
    <div class="mobile-admin-header">
        <button id="openSidebar" style="background: none; border: none; color: white; font-size: 1.5rem; cursor: pointer;">☰</button>
        <div style="font-weight: 800; font-size: 1.1rem;"><?php echo strtoupper($appName); ?></div>
    </div>

    <?php 
    $page = 'messages';
    require 'layout/sidebar.php'; 
    ?>

    <main class="admin-main">
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

    <script>
        const sidebar = document.getElementById('sidebar');
        const openSidebar = document.getElementById('openSidebar');
        const closeSidebar = document.getElementById('closeSidebar');

        if (openSidebar) {
            openSidebar.addEventListener('click', () => sidebar.classList.add('active'));
        }
        if (closeSidebar) {
            closeSidebar.addEventListener('click', () => sidebar.classList.remove('active'));
        }

        window.addEventListener('resize', () => {
            if (window.innerWidth > 992) {
                sidebar.classList.remove('active');
                if(closeSidebar) closeSidebar.style.display = 'none';
            } else {
                if(closeSidebar) closeSidebar.style.display = 'block';
            }
        });
        
        if (window.innerWidth <= 992 && closeSidebar) closeSidebar.style.display = 'block';
    </script>
</body>
</html>
