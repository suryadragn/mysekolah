<?php
require_once 'db.php';
if (!isLoggedIn()) redirect('login.php');

$appName = $_ENV['APP_NAME'] ?? 'MySekolah';

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM ms_news WHERE id = ?");
    $stmt->execute([$id]);
    redirect('news.php');
}

// Fetch all news
$news = $pdo->query("SELECT * FROM ms_news ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Berita | <?php echo $appName; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css?v=<?php echo time(); ?>">
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
        .table-container { background: var(--glass); border-radius: 20px; border: 1px solid var(--glass-border); overflow-x: auto; margin-top: 2rem; }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th, td { padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--glass-border); }
        th { background: rgba(255,255,255,0.02); color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase; }
        .btn-sm { padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.8rem; text-decoration: none; }
        .btn-danger { background: rgba(244, 63, 94, 0.1); color: var(--accent); }
    </style>
</head>
<body class="admin-body">
    <div class="mobile-admin-header">
        <button id="openSidebar" style="background: none; border: none; color: white; font-size: 1.5rem; cursor: pointer;">☰</button>
        <div style="font-weight: 800; font-size: 1.1rem;"><?php echo strtoupper($appName); ?></div>
    </div>

    <?php 
    $page = 'news';
    require 'layout/sidebar.php'; 
    ?>

    <main class="admin-main">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h1 style="font-size: 2rem;">Kelola Berita</h1>
            <a href="news_add.php" class="btn btn-primary">+ Tambah Berita</a>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Judul Berita</th>
                        <th>Tanggal Posting</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($news as $row): ?>
                    <tr>
                        <td><?php echo $row['title']; ?></td>
                        <td><?php echo date('d M Y', strtotime($row['created_at'])); ?></td>
                        <td>
                            <a href="news_edit.php?id=<?php echo $row['id']; ?>" style="color: var(--secondary); margin-right: 1rem;">Edit</a>
                            <a href="news.php?delete=<?php echo $row['id']; ?>" class="btn-sm btn-danger" onclick="return confirm('Hapus berita ini?')">Hapus</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($news)): ?>
                        <tr><td colspan="3" style="text-align: center; color: var(--text-muted);">Belum ada berita.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
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
