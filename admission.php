<?php
require_once 'admin/db.php';

$success = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $consent = trim($_POST['consent'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $name = trim($_POST['full_name'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $school = trim($_POST['school_origin'] ?? '');
    $nisn = trim($_POST['nisn'] ?? '');
    $birthPlace = trim($_POST['birth_place'] ?? '');
    $birthDate = trim($_POST['birth_date'] ?? '');
    $religion = trim($_POST['religion'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $parentName = trim($_POST['parent_name'] ?? '');
    $studentPhone = trim($_POST['student_phone'] ?? '');
    $parentWa = trim($_POST['parent_wa'] ?? '');
    $hasPipRaw = trim($_POST['has_pip'] ?? '');
    $hasPip = ($hasPipRaw === 'Ya') ? 1 : 0;

    if ($consent !== 'Ya') {
        $error = 'Anda harus menyetujui pernyataan pendaftaran.';
    } elseif ($email === '' || $name === '' || $gender === '' || $school === '' || $birthPlace === '' || $birthDate === '' || $religion === '' || $address === '' || $parentName === '' || $parentWa === '' || $hasPipRaw === '') {
        $error = 'Mohon lengkapi semua field yang wajib diisi.';
    } else {
        $academicYear = $globalSettings['academic_year'] ?? date('Y') . '/' . (date('Y') + 1);

        $stmt = $pdo->prepare("
            INSERT INTO ms_admission (
                full_name,
                gender,
                school_origin,
                nisn,
                birth_place,
                birth_date,
                religion,
                address,
                parent_name,
                student_phone,
                phone,
                email,
                academic_year,
                consent,
                has_pip,
                status
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $name,
            $gender,
            $school,
            ($nisn === '' ? null : $nisn),
            $birthPlace,
            $birthDate,
            $religion,
            $address,
            $parentName,
            ($studentPhone === '' ? null : $studentPhone),
            $parentWa,
            $email,
            $academicYear,
            1,
            $hasPip,
            'pending',
        ]);

        $success = "Pendaftaran Anda berhasil! Data sudah masuk ke sistem.";
    }
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
        <a href="index.php" class="btn btn-glass">Kembali</a>
    </nav>

    <main style="padding: 5rem 10%; display: flex; align-items: center; justify-content: center; min-height: 80vh;">
        <div class="stat-card" style="max-width: 600px; width: 100%; text-align: left;">
            <h1 style="font-size: 2.5rem; margin-bottom: 1rem;">Formulir PPDB Online</h1>
            <p style="color: var(--text-muted); margin-bottom: 2.5rem;">Silakan isi data calon siswa sesuai formulir PPDB.</p>

            <?php if ($success): ?>
                <div style="background: rgba(34, 197, 94, 0.1); color: #22c55e; padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem; border: 1px solid rgba(34, 197, 94, 0.2);">
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div style="background: rgba(239, 68, 68, 0.1); color: #f87171; padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem; border: 1px solid rgba(239, 68, 68, 0.2);">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST" style="display: flex; flex-direction: column; gap: 1.5rem;">
                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; color: var(--text-muted);">Email *</label>
                    <input type="email" name="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" style="width: 100%; padding: 1rem; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: 10px; color: white;">
                </div>
                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; color: var(--text-muted);">Pernyataan Pendaftaran *</label>
                    <div class="radio-group">
                        <label class="radio-pill">
                            <input type="radio" name="consent" value="Ya" required <?php echo (($_POST['consent'] ?? '') === 'Ya') ? 'checked' : ''; ?>>
                            Ya, saya menyatakan mendaftar sebagai peserta didik baru.
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; color: var(--text-muted);">Nama Lengkap *</label>
                    <input type="text" name="full_name" required value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>" style="width: 100%; padding: 1rem; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: 10px; color: white;">
                </div>
                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; color: var(--text-muted);">Jenis Kelamin *</label>
                    <select name="gender" required style="width: 100%; padding: 1rem; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: 10px; color: white;">
                        <option value="" style="background: #0f172a;">Pilih</option>
                        <option value="Laki-laki" style="background: #0f172a;" <?php echo (($_POST['gender'] ?? '') === 'Laki-laki') ? 'selected' : ''; ?>>Laki-laki</option>
                        <option value="Perempuan" style="background: #0f172a;" <?php echo (($_POST['gender'] ?? '') === 'Perempuan') ? 'selected' : ''; ?>>Perempuan</option>
                    </select>
                </div>
                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; color: var(--text-muted);">Asal Sekolah (SD/MI) *</label>
                    <input type="text" name="school_origin" required value="<?php echo htmlspecialchars($_POST['school_origin'] ?? ''); ?>" style="width: 100%; padding: 1rem; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: 10px; color: white;">
                </div>
                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; color: var(--text-muted);">NISN (kosongi jika belum tahu)</label>
                    <input type="text" name="nisn" value="<?php echo htmlspecialchars($_POST['nisn'] ?? ''); ?>" style="width: 100%; padding: 1rem; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: 10px; color: white;">
                </div>
                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; color: var(--text-muted);">Tempat Lahir *</label>
                    <input type="text" name="birth_place" required value="<?php echo htmlspecialchars($_POST['birth_place'] ?? ''); ?>" style="width: 100%; padding: 1rem; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: 10px; color: white;">
                </div>
                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; color: var(--text-muted);">Tanggal Lahir *</label>
                    <input type="date" name="birth_date" required value="<?php echo htmlspecialchars($_POST['birth_date'] ?? ''); ?>" style="width: 100%; padding: 1rem; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: 10px; color: white;">
                </div>
                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; color: var(--text-muted);">Agama *</label>
                    <select name="religion" required style="width: 100%; padding: 1rem; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: 10px; color: white;">
                        <option value="" style="background: #0f172a;">Pilih</option>
                        <?php
                            $religions = ['Islam','Kristen','Katolik','Hindu','Buddha','Konghucu','Lainnya'];
                            foreach ($religions as $r) {
                                $sel = (($_POST['religion'] ?? '') === $r) ? 'selected' : '';
                                echo '<option value="' . htmlspecialchars($r) . '" style="background: #0f172a;" ' . $sel . '>' . htmlspecialchars($r) . '</option>';
                            }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; color: var(--text-muted);">Alamat Rumah Lengkap *</label>
                    <textarea name="address" required rows="4" style="width: 100%; padding: 1rem; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: 10px; color: white; resize: vertical;"><?php echo htmlspecialchars($_POST['address'] ?? ''); ?></textarea>
                </div>
                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; color: var(--text-muted);">Nama Orang Tua / Wali *</label>
                    <input type="text" name="parent_name" required value="<?php echo htmlspecialchars($_POST['parent_name'] ?? ''); ?>" style="width: 100%; padding: 1rem; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: 10px; color: white;">
                </div>
                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; color: var(--text-muted);">Nomor HP Siswa (jika ada)</label>
                    <input type="tel" name="student_phone" value="<?php echo htmlspecialchars($_POST['student_phone'] ?? ''); ?>" style="width: 100%; padding: 1rem; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: 10px; color: white;">
                </div>
                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; color: var(--text-muted);">Nomor WA Orang Tua / Wali *</label>
                    <input type="tel" name="parent_wa" required value="<?php echo htmlspecialchars($_POST['parent_wa'] ?? ''); ?>" style="width: 100%; padding: 1rem; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: 10px; color: white;">
                </div>
                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; color: var(--text-muted);">Memiliki kartu dan rekening PIP *</label>
                    <div class="radio-group">
                        <label class="radio-pill">
                            <input type="radio" name="has_pip" value="Ya" required <?php echo (($_POST['has_pip'] ?? '') === 'Ya') ? 'checked' : ''; ?>>
                            Ya
                        </label>
                        <label class="radio-pill">
                            <input type="radio" name="has_pip" value="Tidak" required <?php echo (($_POST['has_pip'] ?? '') === 'Tidak') ? 'checked' : ''; ?>>
                            Tidak
                        </label>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary" style="margin-top: 1rem;">Kirim Pendaftaran</button>
            </form>
        </div>
    </main>
</body>
</html>
