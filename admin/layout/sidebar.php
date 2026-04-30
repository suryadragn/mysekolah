<?php
$currentPage = $page ?? '';
$appName = $_ENV['APP_NAME'] ?? 'MySekolah';
?>
<aside id="sidebar">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div class="logo"><?php echo strtoupper($appName); ?> <span style="font-size: 0.8rem; color: var(--text-muted)">ADMIN</span></div>
        <button id="closeSidebar" style="background: none; border: none; color: white; font-size: 1.5rem; cursor: pointer; display: none;">✕</button>
    </div>
    <nav class="admin-nav">
        <a href="index.php" class="admin-nav-item <?php echo $currentPage === 'dashboard' ? 'active' : ''; ?>"><span>📊</span> Dashboard</a>
        <a href="news.php" class="admin-nav-item <?php echo $currentPage === 'news' ? 'active' : ''; ?>"><span>📰</span> Kelola Berita</a>
        <a href="gallery.php" class="admin-nav-item <?php echo $currentPage === 'gallery' ? 'active' : ''; ?>"><span>🖼️</span> Galeri Foto</a>
        <a href="admission.php" class="admin-nav-item <?php echo $currentPage === 'admission' ? 'active' : ''; ?>"><span>📝</span> Data PPDB</a>
        <a href="messages.php" class="admin-nav-item <?php echo $currentPage === 'messages' ? 'active' : ''; ?>"><span>📩</span> Pesan Masuk</a>
        <a href="socials.php" class="admin-nav-item <?php echo $currentPage === 'socials' ? 'active' : ''; ?>"><span>🌐</span> Sosial Media</a>
        <a href="settings.php" class="admin-nav-item <?php echo $currentPage === 'settings' ? 'active' : ''; ?>"><span>⚙️</span> Pengaturan Umum</a>
        <a href="logout.php" class="admin-nav-item" style="color: var(--accent); margin-top: 2rem;"><span>🚪</span> Logout</a>
    </nav>
    <!-- <a href="../" class="btn btn-glass" style="display: block; text-align: center; margin-top: 2rem; margin-bottom: 1rem;">Lihat Situs</a> -->
</aside>