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
    <link rel="stylesheet" href="../assets/css/style.css?v=<?php echo time(); ?>">
    <?php echo $themeCss ?? ''; ?>
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
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 2rem;">
            <h1>Pengaturan Umum</h1>
        </div>
        
        <?php if (isset($success)): ?>
            <div style="background: rgba(34, 197, 94, 0.1); color: #22c55e; padding: 1.2rem; border-radius: 12px; margin-bottom: 2rem; border: 1px solid rgba(34, 197, 94, 0.2); display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 1.2rem;">✅</span> <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 2rem;">
                <!-- Academic & Statistics -->
                <div style="display: flex; flex-direction: column; gap: 2rem;">
                    <div class="form-card">
                        <h3 style="margin-bottom: 2rem; color: var(--secondary); display: flex; align-items: center; gap: 10px;">
                            <span>🎓</span> Informasi Akademik
                        </h3>
                        <div class="form-group">
                            <label>Tahun Ajaran Aktif</label>
                            <input type="text" name="settings[academic_year]" value="<?php echo htmlspecialchars($settings['academic_year'] ?? ''); ?>" placeholder="Contoh: 2026/2027">
                            <small style="color: var(--text-muted); margin-top: 0.5rem; display: block;">Tahun ajaran ini akan otomatis tercatat saat siswa mendaftar PPDB.</small>
                        </div>
                    </div>

                    <div class="form-card">
                        <h3 style="margin-bottom: 2rem; color: var(--secondary); display: flex; align-items: center; gap: 10px;">
                            <span>📊</span> Statistik Sekolah (Counter)
                        </h3>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                            <div class="form-group">
                                <label>Siswa Aktif</label>
                                <input type="text" name="settings[stat_students]" value="<?php echo htmlspecialchars($settings['stat_students'] ?? ''); ?>" placeholder="1.2k+">
                            </div>
                            <div class="form-group">
                                <label>Tenaga Pendidik</label>
                                <input type="text" name="settings[stat_teachers]" value="<?php echo htmlspecialchars($settings['stat_teachers'] ?? ''); ?>" placeholder="85+">
                            </div>
                            <div class="form-group">
                                <label>Prestasi</label>
                                <input type="text" name="settings[stat_achievements]" value="<?php echo htmlspecialchars($settings['stat_achievements'] ?? ''); ?>" placeholder="42">
                            </div>
                            <div class="form-group">
                                <label>Ekstrakurikuler</label>
                                <input type="text" name="settings[stat_extracurriculars]" value="<?php echo htmlspecialchars($settings['stat_extracurriculars'] ?? ''); ?>" placeholder="15">
                            </div>
                        </div>
                    </div>

                    <div class="form-card">
                        <h3 style="margin-bottom: 2rem; color: var(--secondary); display: flex; align-items: center; gap: 10px;">
                            <span>🎛️</span> Warna Tema
                        </h3>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                            <div class="form-group" style="margin-bottom: 0;">
                                <label>Primary</label>
                                <input type="color" name="settings[theme_primary]" value="<?php echo htmlspecialchars($settings['theme_primary'] ?? '#6366f1'); ?>" style="height: 52px; padding: 0.6rem;">
                            </div>
                            <div class="form-group" style="margin-bottom: 0;">
                                <label>Primary Dark</label>
                                <input type="color" name="settings[theme_primary_dark]" value="<?php echo htmlspecialchars($settings['theme_primary_dark'] ?? '#4f46e5'); ?>" style="height: 52px; padding: 0.6rem;">
                            </div>
                            <div class="form-group" style="margin-bottom: 0;">
                                <label>Secondary</label>
                                <input type="color" name="settings[theme_secondary]" value="<?php echo htmlspecialchars($settings['theme_secondary'] ?? '#06b6d4'); ?>" style="height: 52px; padding: 0.6rem;">
                            </div>
                            <div class="form-group" style="margin-bottom: 0;">
                                <label>Accent</label>
                                <input type="color" name="settings[theme_accent]" value="<?php echo htmlspecialchars($settings['theme_accent'] ?? '#f43f5e'); ?>" style="height: 52px; padding: 0.6rem;">
                            </div>
                            <div class="form-group" style="grid-column: 1 / -1; margin-bottom: 0;">
                                <label>Background</label>
                                <input type="color" name="settings[theme_bg_dark]" value="<?php echo htmlspecialchars($settings['theme_bg_dark'] ?? '#0f172a'); ?>" style="height: 52px; padding: 0.6rem;">
                            </div>
                        </div>
                        <small style="color: var(--text-muted); display: block; margin-top: 1rem;">Perubahan berlaku setelah disimpan dan refresh halaman.</small>
                    </div>
                </div>

                <!-- Visual Assets -->
                <div class="form-card">
                    <h3 style="margin-bottom: 2rem; color: var(--secondary); display: flex; align-items: center; gap: 10px;">
                        <span>🎨</span> Identitas Visual & Aset
                    </h3>
                    
                    <div class="form-group" style="margin-bottom: 2rem;">
                        <label>Favicon (Ikon Tab)</label>
                        <div style="display: flex; align-items: center; gap: 1.5rem; background: rgba(255,255,255,0.03); padding: 1rem; border-radius: 12px; border: 1px dashed var(--glass-border);">
                            <?php if(!empty($settings['site_favicon'])): ?>
                                <img src="../uploads/<?php echo $settings['site_favicon']; ?>" alt="Favicon" style="height: 32px; width: 32px; object-fit: contain;">
                            <?php else: ?>
                                <div style="height: 32px; width: 32px; background: var(--glass); border-radius: 4px;"></div>
                            <?php endif; ?>
                            <input type="file" name="site_favicon" accept="image/*" style="font-size: 0.8rem;">
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 2rem;">
                        <label>Logo Website</label>
                        <div style="display: flex; align-items: center; gap: 1.5rem; background: rgba(255,255,255,0.03); padding: 1rem; border-radius: 12px; border: 1px dashed var(--glass-border);">
                            <?php if(!empty($settings['site_logo'])): ?>
                                <img src="../uploads/<?php echo $settings['site_logo']; ?>" alt="Logo" style="height: 40px; border-radius: 4px;">
                            <?php else: ?>
                                <div style="height: 40px; width: 100px; background: var(--glass); border-radius: 4px;"></div>
                            <?php endif; ?>
                            <input type="file" name="site_logo" accept="image/*" style="font-size: 0.8rem;">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Hero Background (Beranda)</label>
                        <div style="background: rgba(255,255,255,0.03); padding: 1rem; border-radius: 12px; border: 1px dashed var(--glass-border);">
                            <?php if(!empty($settings['hero_bg'])): ?>
                                <img src="../uploads/<?php echo $settings['hero_bg']; ?>" alt="Hero BG" style="width: 100%; height: 120px; border-radius: 8px; object-fit: cover; margin-bottom: 1rem;">
                            <?php endif; ?>
                            <input type="file" name="hero_bg" accept="image/*" style="width: 100%; font-size: 0.8rem;">
                            <small style="color: var(--text-muted); display: block; margin-top: 0.5rem;">Rasio 16:9 direkomendasikan.</small>
                        </div>
                    </div>
                </div>
            </div>

            <div style="margin-top: 3rem; position: sticky; bottom: 2rem; z-index: 10;">
                <button type="submit" name="save_settings" class="btn btn-primary" style="width: auto; padding: 1.2rem 4rem; box-shadow: 0 10px 30px rgba(0, 209, 255, 0.3);">
                    Simpan Semua Perubahan
                </button>
            </div>
        </form>
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
