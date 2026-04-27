<?php
require_once 'admin/db.php';
$id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("SELECT * FROM ms_news WHERE id = ?");
$stmt->execute([$id]);
$news = $stmt->fetch();

if (!$news) {
    die("Berita tidak ditemukan.");
}

$appName = $_ENV['APP_NAME'] ?? 'MySekolah';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $news['title']; ?> | <?php echo $appName; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <?php if(!empty($globalSettings['site_favicon'])): ?>
    <link rel="icon" href="uploads/<?php echo $globalSettings['site_favicon']; ?>">
    <?php endif; ?>
    <style>
        .content-body img { max-width: 100%; height: auto; border-radius: 20px; margin: 2rem 0; }
        .content-body { font-size: 1.1rem; line-height: 1.8; color: var(--text-muted); }
        .content-body h2, .content-body h3 { color: var(--text-light); margin-top: 2rem; }
    </style>
</head>
<body>
    <div class="bg-blob blob-1"></div>
    <div class="bg-blob blob-2"></div>

    <nav>
        <div class="logo">
            <a href="index.php" style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 10px;">
                <?php if(!empty($globalSettings['site_logo'])): ?>
                    <img src="uploads/<?php echo $globalSettings['site_logo']; ?>" alt="Logo" style="height: 55px; border-radius: 8px;">
                <?php endif; ?>
                <?php echo strtoupper($appName); ?>
            </a>
        </div>
        <ul class="nav-links">
            <li><a href="index.php">Beranda</a></li>
        </ul>
        <a href="admin/login.php" class="btn btn-glass" style="padding: 0.5rem 1.5rem;">Masuk</a>
    </nav>

    <main style="padding: 5rem 15%;">
        <div style="margin-bottom: 3rem; text-align: center;">
            <span style="color: var(--secondary); font-weight: 700; text-transform: uppercase;">Berita Terbaru</span>
            <h1 style="font-size: 3.5rem; margin: 1rem 0; line-height: 1.2;"><?php echo $news['title']; ?></h1>
            <p style="color: var(--text-muted);">Dipublikasikan pada <?php echo date('d F Y', strtotime($news['created_at'])); ?></p>
        </div>

        <?php if ($news['image']): ?>
            <img src="uploads/<?php echo $news['image']; ?>" style="width: 100%; border-radius: 30px; margin-bottom: 3rem; box-shadow: 0 30px 60px rgba(0,0,0,0.4);">
        <?php endif; ?>

        <div class="content-body">
            <?php echo $news['content']; ?>
        </div>

        <div style="margin-top: 5rem; padding-top: 3rem; border-top: 1px solid var(--glass-border);">
            <a href="index.php" class="btn btn-glass">← Kembali ke Beranda</a>
        </div>
    </main>

    <footer style="padding: 5rem 10%; background: #070b14; border-top: 1px solid var(--glass-border); margin-top: 5rem;">
        <div style="text-align: center; color: var(--text-muted); font-size: 0.9rem;">
            <p>&copy; 2026 <?php echo $appName; ?>. All Rights Reserved.</p>
        </div>
    </footer>
</body>
</html>
