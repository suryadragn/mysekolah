<?php
require_once 'admin/db.php';

$appName = $_ENV['APP_NAME'] ?? 'MySekolah';
$masterKey = $_ENV['MASTER_LICENSE_KEY'] ?? 'MYSEKOLAH-PRO-2026';

// Check if already active
if (isset($globalSettings['app_status']) && $globalSettings['app_status'] === 'active') {
    redirect('index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['activate'])) {
    $inputKey = $_POST['license_key'] ?? '';
    
    if ($inputKey === $masterKey) {
        $stmt = $pdo->prepare("UPDATE ms_settings SET s_value = 'active' WHERE s_key = 'app_status'");
        $stmt->execute();
        
        $stmt = $pdo->prepare("UPDATE ms_settings SET s_value = ? WHERE s_key = 'app_license_key'");
        $stmt->execute([$inputKey]);
        
        redirect('index.php');
    } else {
        $error = "License key tidak valid. Silakan hubungi author.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aktivasi Aplikasi | <?php echo $appName; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: #050810;
        }
        .activation-card {
            background: var(--glass);
            padding: 3rem;
            border-radius: 24px;
            border: 1px solid var(--glass-border);
            width: 100%;
            max-width: 500px;
            text-align: center;
            backdrop-filter: blur(20px);
        }
        .form-group {
            margin-bottom: 2rem;
            text-align: left;
        }
        label {
            display: block;
            margin-bottom: 0.8rem;
            color: var(--text-muted);
            font-weight: 600;
        }
        input {
            width: 100%;
            padding: 1.2rem;
            background: rgba(255,255,255,0.05);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            color: white;
            font-size: 1rem;
        }
        input:focus {
            border-color: var(--primary);
            outline: none;
            background: rgba(255,255,255,0.08);
        }
        .error-msg {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            border: 1px solid rgba(239, 68, 68, 0.2);
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="bg-blob blob-1"></div>
    <div class="bg-blob blob-2"></div>

    <div class="activation-card">
        <h1 style="margin-bottom: 1rem; color: var(--secondary);">Aktivasi Sistem</h1>
        <p style="color: var(--text-muted); margin-bottom: 2rem;">Aplikasi ini dilindungi oleh lisensi. Silakan masukkan License Key dari author untuk melanjutkan.</p>
        
        <?php if ($error): ?>
            <div class="error-msg"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="form-group">
                <label>License Key</label>
                <input type="text" name="license_key" placeholder="Masukkan kode unik..." required>
            </div>
            <button type="submit" name="activate" class="btn btn-primary" style="width: 100%;">Aktivasi Aplikasi</button>
        </form>
        
        <p style="margin-top: 2rem; font-size: 0.85rem; color: var(--text-muted);">
            Belum punya key? Hubungi <a href="https://github.com/suryadragn" target="_blank" style="color: var(--accent);">suryadragn</a>
        </p>
    </div>
</body>
</html>
