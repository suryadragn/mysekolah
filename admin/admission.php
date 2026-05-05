<?php
require_once 'db.php';
if (!isLoggedIn()) redirect('login.php');

$appName = $_ENV['APP_NAME'] ?? 'MySekolah';

// Handle Update Status
if (isset($_GET['verify'])) {
    $id = $_GET['verify'];
    $stmt = $pdo->prepare("UPDATE ms_admission SET status = 'verified' WHERE id = ?");
    $stmt->execute([$id]);
    redirect('admission.php');
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM ms_admission WHERE id = ?");
    $stmt->execute([$id]);
    redirect('admission.php');
}

// Fetch unique academic years for filter
$years = $pdo->query("SELECT DISTINCT academic_year FROM ms_admission WHERE academic_year IS NOT NULL ORDER BY academic_year DESC")->fetchAll(PDO::FETCH_COLUMN);

// Handle Filter
$filterYear = $_GET['year'] ?? '';
$query = "SELECT * FROM ms_admission";
$params = [];

if ($filterYear) {
    $query .= " WHERE academic_year = ?";
    $params[] = $filterYear;
}

$query .= " ORDER BY created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$admissions = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data PPDB | <?php echo $appName; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css?v=<?php echo time(); ?>">
    <style>
        .filter-card {
            background: var(--glass);
            border: 1px solid var(--glass-border);
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 1.5rem;
            flex-wrap: wrap;
        }
        .filter-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .filter-group select {
            background: rgba(255,255,255,0.05);
            border: 1px solid var(--glass-border);
            padding: 0.6rem 1rem;
            border-radius: 8px;
            color: white;
            outline: none;
            min-width: 150px;
        }
        .filter-group select option {
            background: #0f172a;
        }
        
        .table-container { background: var(--glass); border-radius: 20px; border: 1px solid var(--glass-border); overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th, td { padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--glass-border); }
        th { background: rgba(255,255,255,0.02); color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase; }
        .badge { padding: 0.4rem 0.8rem; border-radius: 50px; font-size: 0.75rem; font-weight: 600; }
        .badge-success { background: rgba(34, 197, 94, 0.1); color: #22c55e; }
        .badge-warning { background: rgba(234, 179, 8, 0.1); color: #eab308; }
    </style>
</head>
<body class="admin-body">
    <div class="mobile-admin-header">
        <button id="openSidebar" style="background: none; border: none; color: white; font-size: 1.5rem; cursor: pointer;">☰</button>
        <div style="font-weight: 800; font-size: 1.1rem;"><?php echo strtoupper($appName); ?></div>
    </div>

    <?php 
    $page = 'admission';
    require 'layout/sidebar.php'; 
    ?>

    <main class="admin-main">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h1>Data Pendaftar PPDB</h1>
            <a href="export_admission.php" class="btn btn-primary" style="width: auto; padding: 0.8rem 1.5rem;">📥 Tarik Data ke Excel</a>
        </div>

        <div class="filter-card">
            <div class="filter-group">
                <span>🔍 Filter Tahun Ajaran:</span>
                <form action="" method="GET" id="filterForm">
                    <select name="year" onchange="document.getElementById('filterForm').submit()">
                        <option value="">Semua Tahun</option>
                        <?php foreach ($years as $y): ?>
                            <option value="<?php echo $y; ?>" <?php echo $filterYear === $y ? 'selected' : ''; ?>>
                                <?php echo $y; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>
            <?php if ($filterYear): ?>
                <a href="admission.php" style="color: var(--accent); text-decoration: none; font-size: 0.9rem;">Reset Filter</a>
            <?php endif; ?>
        </div>
        
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Tahun Ajaran</th>
                        <th>Nama</th>
                        <th>JK</th>
                        <th>Asal Sekolah</th>
                        <th>Tempat/Tgl Lahir</th>
                        <th>Orang Tua/Wali</th>
                        <th>Kontak</th>
                        <th>PIP</th>
                        <th>Tanggal Daftar</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($admissions as $row): ?>
                    <tr>
                        <td>
                            <div style="font-weight: 600; color: var(--secondary);"><?php echo $row['academic_year'] ?? '-'; ?></div>
                        </td>
                        <td>
                            <div style="font-weight: 600;"><?php echo $row['full_name']; ?></div>
                            <?php if (!empty($row['nisn'])): ?>
                                <div style="font-size: 0.8rem; color: var(--text-muted);">NISN: <?php echo $row['nisn']; ?></div>
                            <?php endif; ?>
                        </td>
                        <td><?php echo $row['gender'] ?? '-'; ?></td>
                        <td>
                            <?php echo $row['school_origin']; ?>
                        </td>
                        <td>
                            <div style="font-size: 0.9rem;"><?php echo $row['birth_place'] ?? '-'; ?></div>
                            <div style="font-size: 0.8rem; color: var(--text-muted);">
                                <?php echo !empty($row['birth_date']) ? date('d/m/Y', strtotime($row['birth_date'])) : '-'; ?>
                            </div>
                        </td>
                        <td>
                            <div style="font-size: 0.9rem; font-weight: 600;"><?php echo $row['parent_name'] ?? '-'; ?></div>
                        </td>
                        <td>
                            <div style="font-size: 0.9rem;"><?php echo $row['phone'] ?? '-'; ?></div>
                            <?php if (!empty($row['student_phone'])): ?>
                                <div style="font-size: 0.8rem; color: var(--text-muted);">Siswa: <?php echo $row['student_phone']; ?></div>
                            <?php endif; ?>
                            <div style="font-size: 0.8rem; color: var(--text-muted);"><?php echo $row['email'] ?? '-'; ?></div>
                        </td>
                        <td>
                            <span class="badge badge-<?php echo !empty($row['has_pip']) ? 'success' : 'warning'; ?>">
                                <?php echo !empty($row['has_pip']) ? 'Ya' : 'Tidak'; ?>
                            </span>
                        </td>
                        <td><?php echo date('d/m/y', strtotime($row['created_at'])); ?></td>
                        <td>
                            <span class="badge badge-<?php echo $row['status'] === 'verified' ? 'success' : 'warning'; ?>">
                                <?php echo ucfirst($row['status']); ?>
                            </span>
                        </td>
                        <td>
                            <div style="display: flex; gap: 10px;">
                                <?php if ($row['status'] !== 'verified'): ?>
                                    <a href="admission.php?verify=<?php echo $row['id']; ?>" class="btn" style="padding: 0.4rem 0.8rem; font-size: 0.75rem; background: rgba(0, 209, 255, 0.1); color: var(--secondary); border: 1px solid var(--secondary);">Verifikasi</a>
                                <?php endif; ?>
                                <a href="admission.php?delete=<?php echo $row['id']; ?>" class="btn" style="padding: 0.4rem 0.8rem; font-size: 0.75rem; background: rgba(255, 62, 116, 0.1); color: var(--accent); border: 1px solid var(--accent);" onclick="return confirm('Hapus pendaftar ini?')">Hapus</a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($admissions)): ?>
                        <tr><td colspan="11" style="text-align: center; color: var(--text-muted); padding: 3rem;">Belum ada data pendaftar.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
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
