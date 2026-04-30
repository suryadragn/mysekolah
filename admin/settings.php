<?php
require_once 'db.php';
if (!isLoggedIn()) redirect('login.php');

$appName = $_ENV['APP_NAME'] ?? 'MySekolah';

// Handle Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_settings'])) {
    foreach ($_POST['settings'] as $key => $value) {
        $stmt = $pdo->prepare("UPDATE ms_settings SET s_value = ? WHERE s_key = ?");
        // if key doesn't exist, insert it
        if ($stmt->execute([$value, $key]) && $stmt->rowCount() == 0) {
            $stmt = $pdo->prepare("INSERT IGNORE INTO ms_settings (s_key, s_value) VALUES (?, ?)");
            $stmt->execute([$key, $value]);
        }
    }
    
    // Handle File Uploads
    $uploadDir = '../uploads/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
    
    $files = ['site_favicon', 'site_logo', 'hero_bg'];
    foreach ($files as $fileKey) {
        if (isset($_FILES[$fileKey]) && $_FILES[$fileKey]['error'] == 0) {
            $filename = time() . '_' . basename($_FILES[$fileKey]['name']);
            if (move_uploaded_file($_FILES[$fileKey]['tmp_name'], $uploadDir . $filename)) {
                $stmt = $pdo->prepare("UPDATE ms_settings SET s_value = ? WHERE s_key = ?");
                if ($stmt->execute([$filename, $fileKey]) && $stmt->rowCount() == 0) {
                    $stmt = $pdo->prepare("INSERT INTO ms_settings (s_key, s_value) VALUES (?, ?)");
                    $stmt->execute([$fileKey, $filename]);
                }
            }
        }
    }
    $success = "Pengaturan berhasil diperbarui!";
    
    // Refresh settings
    $settings_raw = $pdo->query("SELECT * FROM ms_settings")->fetchAll();
    foreach ($settings_raw as $s) {
        $settings[$s['s_key']] = $s['s_value'];
    }
}

// Fetch Settings
$settings_raw = $pdo->query("SELECT * FROM ms_settings")->fetchAll();
$settings = [];
foreach ($settings_raw as $s) {
    $settings[$s['s_key']] = $s['s_value'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Umum | <?php echo $appName; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="admin-body">
    <div class="mobile-admin-header">
        <button id="openSidebar" style="background: none; border: none; color: white; font-size: 1.5rem; cursor: pointer;">☰</button>
        <div style="font-weight: 800; font-size: 1.1rem;"><?php echo strtoupper($appName); ?></div>
    </div>

    <?php 
    $page = 'settings';
    require 'layout/sidebar.php'; 
    ?>

    <main class="admin-main">
        <h1 style="margin-bottom: 2rem;">Pengaturan Umum</h1>
        
        <?php if (isset($success)): ?>
            <div style="background: rgba(34, 197, 94, 0.1); color: #22c55e; padding: 1.2rem; border-radius: 12px; margin-bottom: 2rem; border: 1px solid rgba(34, 197, 94, 0.2);">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <div class="form-card">
            <form action="" method="POST" enctype="multipart/form-data">
                <h3 style="margin-bottom: 2rem; color: var(--secondary);">Informasi Akademik</h3>
                <div class="form-group">
                    <label>Tahun Ajaran Aktif</label>
                    <input type="text" name="settings[academic_year]" value="<?php echo htmlspecialchars($settings['academic_year'] ?? ''); ?>" placeholder="Contoh: 2026/2027">
                </div>
                
                <h3 style="margin-top: 3rem; margin-bottom: 2rem; color: var(--secondary);">Identitas Visual</h3>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                    <div class="form-group">
                        <label>Favicon (Ikon Tab Browser)</label>
                        <?php if(!empty($settings['site_favicon'])): ?>
                            <div style="margin-bottom: 1rem;">
                                <img src="../uploads/<?php echo $settings['site_favicon']; ?>" alt="Favicon" style="height: 40px; border-radius: 8px;">
                            </div>
                        <?php endif; ?>
                        <input type="file" name="site_favicon" accept="image/*">
                    </div>
                    <div class="form-group">
                        <label>Logo Website (Tampil di Sebelah Nama Aplikasi)</label>
                        <?php if(!empty($settings['site_logo'])): ?>
                            <div style="margin-bottom: 1rem;">
                                <img src="../uploads/<?php echo $settings['site_logo']; ?>" alt="Logo" style="height: 40px; border-radius: 8px;">
                            </div>
                        <?php endif; ?>
                        <input type="file" name="site_logo" accept="image/*">
                    </div>
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label>Gambar Latar Belakang (Hero Section Depan)</label>
                        <?php if(!empty($settings['hero_bg'])): ?>
                            <div style="margin-bottom: 1rem;">
                                <img src="../uploads/<?php echo $settings['hero_bg']; ?>" alt="Hero BG" style="height: 100px; border-radius: 8px; object-fit: cover;">
                            </div>
                        <?php endif; ?>
                        <input type="file" name="hero_bg" accept="image/*">
                        <small style="color: var(--text-muted); display: block; margin-top: 0.5rem;">Direkomendasikan rasio lanskap resolusi tinggi (misal: 1920x1080).</small>
                    </div>
                </div>

                <h3 style="margin-top: 3rem; margin-bottom: 2rem; color: var(--secondary);">Statistik Sekolah (Counter)</h3>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                    <div class="form-group">
                        <label>Siswa Aktif</label>
                        <input type="text" name="settings[stat_students]" value="<?php echo htmlspecialchars($settings['stat_students'] ?? ''); ?>" placeholder="Contoh: 1.2k+">
                    </div>
                    <div class="form-group">
                        <label>Tenaga Pendidik</label>
                        <input type="text" name="settings[stat_teachers]" value="<?php echo htmlspecialchars($settings['stat_teachers'] ?? ''); ?>" placeholder="Contoh: 85+">
                    </div>
                    <div class="form-group">
                        <label>Prestasi Nasional</label>
                        <input type="text" name="settings[stat_achievements]" value="<?php echo htmlspecialchars($settings['stat_achievements'] ?? ''); ?>" placeholder="Contoh: 42">
                    </div>
                    <div class="form-group">
                        <label>Ekstrakurikuler</label>
                        <input type="text" name="settings[stat_extracurriculars]" value="<?php echo htmlspecialchars($settings['stat_extracurriculars'] ?? ''); ?>" placeholder="Contoh: 15">
                    </div>
                </div>

                <div style="margin-top: 3rem; border-top: 1px solid var(--glass-border); padding-top: 2rem;">
                    <button type="submit" name="save_settings" class="btn btn-primary" style="width: auto; padding: 1rem 3rem;">Simpan Perubahan</button>
                </div>
            </form>
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
