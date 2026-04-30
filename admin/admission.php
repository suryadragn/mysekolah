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

// Fetch Admissions
$admissions = $pdo->query("SELECT * FROM ms_admission ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data PPDB | <?php echo $appName; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css?v=<?php echo time(); ?>">
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
        
        .table-container { background: var(--glass); border-radius: 20px; border: 1px solid var(--glass-border); overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th, td { padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--glass-border); }
        th { background: rgba(255,255,255,0.02); color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase; }
        .badge { padding: 0.4rem 0.8rem; border-radius: 50px; font-size: 0.75rem; font-weight: 600; }
        .badge-success { background: rgba(34, 197, 94, 0.1); color: #22c55e; }
        .badge-warning { background: rgba(234, 179, 8, 0.1); color: #eab308; }
    </style>
</head>
<body>
    <?php 
    $page = 'admission';
    require 'layout/sidebar.php'; 
    ?>

    <main>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h1>Data Pendaftar PPDB</h1>
            <a href="export_admission.php" class="btn btn-primary">📥 Tarik Data ke Excel</a>
        </div>
        
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Nama Lengkap</th>
                        <th>Email</th>
                        <th>No. Telp</th>
                        <th>Asal Sekolah</th>
                        <th>Tanggal Daftar</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($admissions as $row): ?>
                    <tr>
                        <td><?php echo $row['full_name']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td><?php echo $row['phone']; ?></td>
                        <td><?php echo $row['school_origin']; ?></td>
                        <td><?php echo date('d M Y', strtotime($row['created_at'])); ?></td>
                        <td>
                            <span class="badge badge-<?php echo $row['status'] === 'verified' ? 'success' : 'warning'; ?>">
                                <?php echo ucfirst($row['status']); ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($row['status'] !== 'verified'): ?>
                                <a href="admission.php?verify=<?php echo $row['id']; ?>" style="color: var(--secondary); text-decoration: none; margin-right: 1rem;">Verifikasi</a>
                            <?php endif; ?>
                            <a href="admission.php?delete=<?php echo $row['id']; ?>" style="color: var(--accent); text-decoration: none;" onclick="return confirm('Hapus pendaftar ini?')">Hapus</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($admissions)): ?>
                        <tr><td colspan="8" style="text-align: center; color: var(--text-muted); padding: 3rem;">Belum ada data pendaftar.</td></tr>
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
