<?php
require_once 'admin/db.php';

$appName = $_ENV['APP_NAME'] ?? 'MySekolah';

// Fetch all gallery items
$gallery = $pdo->query("SELECT * FROM ms_gallery ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galeri Sekolah | <?php echo $appName; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <?php if(!empty($globalSettings['site_favicon'])): ?>
    <link rel="icon" href="uploads/<?php echo $globalSettings['site_favicon']; ?>">
    <?php endif; ?>
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
            <li><a href="index.php#home">Beranda</a></li>
            <li><a href="index.php#about">Tentang</a></li>
            <li><a href="index.php#news">Berita</a></li>
            <li><a href="index.php#gallery">Galeri</a></li>
            <li><a href="admission.php">PPDB</a></li>
            <li><a href="index.php#contact">Kontak</a></li>
        </ul>
        <a href="admin/login.php" class="btn btn-glass" style="padding: 0.5rem 1.5rem;">Masuk</a>
    </nav>

    <main style="padding: 8rem 10% 5rem;">
        <h1 style="font-size: 3rem; text-align: center; margin-bottom: 1rem;">Galeri <span style="color: var(--secondary)">Sekolah</span></h1>
        <p style="color: var(--text-muted); text-align: center; max-width: 600px; margin: 0 auto 4rem;">Dokumentasi kegiatan dan fasilitas sekolah untuk memberikan gambaran lingkungan belajar kami.</p>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem;">
            <?php foreach ($gallery as $item): ?>
                <div style="height: 300px;background: url('uploads/<?php echo $item['image']; ?>') center/cover no-repeat; border-radius: 24px; border: 1px solid var(--glass-border); position: relative; overflow: hidden;" class="stat-card">
                    <div style="position: absolute; bottom: 0; left: 0; right: 0; padding: 2.5rem; background: linear-gradient(transparent, rgba(0,0,0,0.9));">
                        <h4 style="color: white; font-size: 1.25rem;"><?php echo $item['title']; ?></h4>
                        <p style="color: var(--secondary); font-size: 0.8rem; margin-top: 0.5rem;"><?php echo date('d M Y', strtotime($item['created_at'])); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php if (empty($gallery)): ?>
                <div style="text-align: center; grid-column: 1 / -1; color: var(--text-muted); padding: 5rem; background: var(--glass); border-radius: 20px; border: 1px solid var(--glass-border);">
                    <p>Belum ada foto yang diunggah ke galeri.</p>
                    <a href="index.php" class="btn btn-primary" style="margin-top: 2rem; display: inline-block;">Kembali ke Beranda</a>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <footer style="padding: 5rem 10%; background: #070b14; border-top: 1px solid var(--glass-border);">
        <div style="text-align: center; color: var(--text-muted); font-size: 0.9rem;">
            <p>&copy; 2026 <?php echo $appName; ?>. All Rights Reserved.</p>
        </div>
    </footer>
</body>

</html>