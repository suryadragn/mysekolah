<?php
require_once 'db.php';
if (!isLoggedIn()) redirect('login.php');

$appName = $_ENV['APP_NAME'] ?? 'MySekolah';

// Handle Add
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_social'])) {
    $platform = $_POST['platform'];
    $url = $_POST['url'];
    $icon_text = $_POST['icon_text'];
    
    $stmt = $pdo->prepare("INSERT INTO ms_socials (platform, url, icon_text) VALUES (?, ?, ?)");
    $stmt->execute([$platform, $url, $icon_text]);
    $success = "Sosial media berhasil ditambahkan!";
}

// Handle Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_social'])) {
    $id = $_POST['id'];
    $platform = $_POST['platform'];
    $url = $_POST['url'];
    $icon_text = $_POST['icon_text'];
    
    $stmt = $pdo->prepare("UPDATE ms_socials SET platform = ?, url = ?, icon_text = ? WHERE id = ?");
    $stmt->execute([$platform, $url, $icon_text, $id]);
    $success = "Sosial media berhasil diperbarui!";
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM ms_socials WHERE id = ?");
    $stmt->execute([$id]);
    redirect('socials.php');
}

// Check Edit Mode
$editData = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM ms_socials WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $editData = $stmt->fetch();
}

// Fetch Socials
$socials = $pdo->query("SELECT * FROM ms_socials ORDER BY id ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Sosial Media | <?php echo $appName; ?></title>
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
        
        .form-card { background: var(--glass); padding: 2rem; border-radius: 20px; border: 1px solid var(--glass-border); margin-bottom: 3rem; }
        .form-group { margin-bottom: 1.5rem; }
        label { display: block; margin-bottom: 0.5rem; color: var(--text-muted); font-size: 0.9rem; }
        input { width: 100%; padding: 1rem; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: 10px; color: white; }
        
        .table-container { background: var(--glass); border-radius: 20px; border: 1px solid var(--glass-border); overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th, td { padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--glass-border); }
        th { background: rgba(255,255,255,0.02); color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase; }
        .icon-preview { width: 40px; height: 40px; background: var(--glass); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; border: 1px solid var(--glass-border); }
    </style>
</head>
<body>
    <?php 
    $page = 'socials';
    require 'layout/sidebar.php'; 
    ?>

    <main>
        <h1 style="margin-bottom: 2rem;">Kelola Link Sosial Media</h1>

        <?php if (isset($success)): ?>
            <div style="background: rgba(34, 197, 94, 0.1); color: #22c55e; padding: 1.2rem; border-radius: 12px; margin-bottom: 2rem; border: 1px solid rgba(34, 197, 94, 0.2);">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <div class="form-card" style="max-width: 800px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h3 style="color: var(--secondary); margin: 0;"><?php echo $editData ? 'Edit Sosial Media' : 'Tambah Sosial Media Baru'; ?></h3>
                <?php if ($editData): ?>
                    <a href="socials.php" style="color: var(--text-muted); text-decoration: none; font-size: 0.9rem;">Batal Edit</a>
                <?php endif; ?>
            </div>
            <form action="socials.php" method="POST" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <?php if ($editData): ?>
                    <input type="hidden" name="id" value="<?php echo $editData['id']; ?>">
                <?php endif; ?>
                <div class="form-group" style="margin-bottom: 0;">
                    <label>Nama Platform</label>
                    <input type="text" name="platform" placeholder="Contoh: YouTube" value="<?php echo $editData ? htmlspecialchars($editData['platform']) : ''; ?>" required>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label>Ikon Singkat (Maks 3 Huruf)</label>
                    <input type="text" name="icon_text" placeholder="Contoh: YT" maxlength="3" value="<?php echo $editData ? htmlspecialchars($editData['icon_text']) : ''; ?>" required>
                </div>
                <div class="form-group" style="grid-column: 1 / -1; margin-bottom: 0;">
                    <label>URL / Link Profil</label>
                    <input type="url" name="url" placeholder="https://" value="<?php echo $editData ? htmlspecialchars($editData['url']) : ''; ?>" required>
                </div>
                <div style="grid-column: 1 / -1; margin-top: 1rem;">
                    <?php if ($editData): ?>
                        <button type="submit" name="update_social" class="btn btn-primary" style="padding: 1rem 2rem;">Simpan Perubahan</button>
                    <?php else: ?>
                        <button type="submit" name="add_social" class="btn btn-primary" style="padding: 1rem 2rem;">Tambah Sosial Media</button>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <h3 style="margin-top: 3rem; margin-bottom: 1.5rem;">Daftar Tautan Sosial Media</h3>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th style="width: 80px;">Ikon</th>
                        <th>Platform</th>
                        <th>Link URL</th>
                        <th style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($socials as $row): ?>
                    <tr>
                        <td>
                            <div class="icon-preview"><?php echo $row['icon_text']; ?></div>
                        </td>
                        <td style="font-weight: 600;"><?php echo $row['platform']; ?></td>
                        <td><a href="<?php echo $row['url']; ?>" target="_blank" style="color: var(--text-muted); text-decoration: none;"><?php echo $row['url']; ?></a></td>
                        <td>
                            <a href="socials.php?edit=<?php echo $row['id']; ?>" style="color: var(--secondary); text-decoration: none; font-weight: 600; margin-right: 1rem;">Edit</a>
                            <a href="socials.php?delete=<?php echo $row['id']; ?>" style="color: var(--accent); text-decoration: none; font-weight: 600;" onclick="return confirm('Hapus sosial media ini?')">Hapus</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($socials)): ?>
                        <tr><td colspan="4" style="text-align: center; color: var(--text-muted); padding: 3rem;">Belum ada tautan sosial media.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
