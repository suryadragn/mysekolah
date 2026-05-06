<?php
require_once 'admin/db.php';

$appName = $_ENV['APP_NAME'] ?? 'MySekolah';

// Fetch all news items
$news = $pdo->query("SELECT * FROM ms_news ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita & Artikel | <?php echo $appName; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <?php echo $themeCss ?? ''; ?>
    <?php if(!empty($globalSettings['site_favicon'])): ?>
    <link rel="icon" href="uploads/<?php echo $globalSettings['site_favicon']; ?>">
    <?php endif; ?>
</head>

<body>
    <div class="bg-blob blob-1"></div>
    <div class="bg-blob blob-2"></div>

    <nav class="site-nav">
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
            <li><a href="news.php">Berita</a></li>
            <li><a href="index.php#gallery">Galeri</a></li>
            <li><a href="admission.php">PPDB</a></li>
            <li><a href="index.php#contact">Kontak</a></li>
        </ul>
        <a href="admin/login.php" class="btn btn-glass" style="padding: 0.5rem 1.5rem;">Masuk</a>
    </nav>

    <main style="padding: 8rem 10% 5rem;">
        <h1 style="font-size: 3rem; text-align: center; margin-bottom: 1rem;">Berita & <span style="color: var(--secondary)">Artikel</span></h1>
        <p style="color: var(--text-muted); text-align: center; max-width: 600px; margin: 0 auto 4rem;">Ikuti perkembangan terbaru mengenai kegiatan, prestasi, dan pengumuman di lingkungan sekolah kami.</p>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 2rem;">
            <?php foreach ($news as $item): 
                $img = $item['image'] ? 'uploads/' . $item['image'] : 'https://images.unsplash.com/photo-1546410531-bb4caa6b424d?auto=format&fit=crop&q=80&w=1000';
            ?>
                <div class="stat-card" style="text-align: left; padding: 0; overflow: hidden; display: flex; flex-direction: column;">
                    <div style="height: 250px; background: url('<?php echo $img; ?>') center/cover no-repeat;"></div>
                    <div style="padding: 2.5rem; flex: 1; display: flex; flex-direction: column;">
                        <span style="color: var(--secondary); font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;"><?php echo date('d M Y', strtotime($item['created_at'])); ?></span>
                        <h4 style="margin: 1.5rem 0; font-size: 1.5rem; line-height: 1.4;"><?php echo $item['title']; ?></h4>
                        <p style="color: var(--text-muted); font-size: 0.95rem; line-height: 1.6; margin-bottom: 2rem;">
                            <?php echo substr(strip_tags($item['content']), 0, 150); ?>...
                        </p>
                        <a href="read_news.php?id=<?php echo $item['id']; ?>" style="color: var(--primary); text-decoration: none; margin-top: auto; font-weight: 700; display: flex; align-items: center; gap: 8px;">
                            Baca Selengkapnya <span style="font-size: 1.2rem;">→</span>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <?php if (empty($news)): ?>
                <div style="text-align: center; grid-column: 1 / -1; color: var(--text-muted); padding: 5rem; background: var(--glass); border-radius: 20px; border: 1px solid var(--glass-border);">
                    <p>Belum ada berita yang diterbitkan saat ini.</p>
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
