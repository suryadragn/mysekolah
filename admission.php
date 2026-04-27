<?php
require_once 'admin/db.php';

$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $school = $_POST['school_origin'];
    
    $stmt = $pdo->prepare("INSERT INTO ms_admission (full_name, email, phone, school_origin) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $email, $phone, $school]);
    $success = "Pendaftaran Anda berhasil! Silakan tunggu konfirmasi melalui email.";
}

$appName = $_ENV['APP_NAME'] ?? 'MySekolah';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran PPDB Online | <?php echo $appName; ?></title>
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
        <a href="index.php" class="btn btn-glass">Kembali</a>
    </nav>

    <main style="padding: 5rem 10%; display: flex; align-items: center; justify-content: center; min-height: 80vh;">
        <div class="stat-card" style="max-width: 600px; width: 100%; text-align: left;">
            <h1 style="font-size: 2.5rem; margin-bottom: 1rem;">Formulir PPDB Online</h1>
            <p style="color: var(--text-muted); margin-bottom: 2.5rem;">Silakan isi data calon siswa dengan lengkap dan benar.</p>

            <?php if ($success): ?>
                <div style="background: rgba(34, 197, 94, 0.1); color: #22c55e; padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem; border: 1px solid rgba(34, 197, 94, 0.2);">
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST" style="display: flex; flex-direction: column; gap: 1.5rem;">
                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; color: var(--text-muted);">Nama Lengkap Siswa</label>
                    <input type="text" name="full_name" required style="width: 100%; padding: 1rem; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: 10px; color: white;">
                </div>
                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; color: var(--text-muted);">Email Orang Tua / Siswa</label>
                    <input type="email" name="email" required style="width: 100%; padding: 1rem; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: 10px; color: white;">
                </div>
                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; color: var(--text-muted);">Nomor Telepon / WhatsApp</label>
                    <input type="tel" name="phone" required style="width: 100%; padding: 1rem; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: 10px; color: white;">
                </div>
                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; color: var(--text-muted);">Sekolah Asal</label>
                    <input type="text" name="school_origin" required style="width: 100%; padding: 1rem; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: 10px; color: white;">
                </div>
                <button type="submit" class="btn btn-primary" style="margin-top: 1rem;">Kirim Pendaftaran</button>
            </form>
        </div>
    </main>
</body>
</html>
