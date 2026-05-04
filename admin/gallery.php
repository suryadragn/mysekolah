<?php
require_once 'db.php';
if (!isLoggedIn()) redirect('login.php');

$appName = $_ENV['APP_NAME'] ?? 'MySekolah';

// Handle Add
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_photo'])) {
    $title = $_POST['title'];
    $image = $_FILES['image'];
    
    if ($image['name']) {
        $ext = pathinfo($image['name'], PATHINFO_EXTENSION);
        $filename = time() . '_' . uniqid() . '.' . $ext;
        move_uploaded_file($image['tmp_name'], '../uploads/' . $filename);
        
        $stmt = $pdo->prepare("INSERT INTO ms_gallery (title, image) VALUES (?, ?)");
        $stmt->execute([$title, $filename]);
        $success = "Foto berhasil ditambahkan!";
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    // Delete file
    $stmt = $pdo->prepare("SELECT image FROM ms_gallery WHERE id = ?");
    $stmt->execute([$id]);
    $img = $stmt->fetchColumn();
    if ($img && file_exists('../uploads/' . $img)) {
        unlink('../uploads/' . $img);
    }
    
    $stmt = $pdo->prepare("DELETE FROM ms_gallery WHERE id = ?");
    $stmt->execute([$id]);
    redirect('gallery.php');
}

// Fetch Gallery
$gallery = $pdo->query("SELECT * FROM ms_gallery ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Galeri | <?php echo $appName; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css?v=<?php echo time(); ?>">
    <style>
        .gallery-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); 
            gap: 2rem; 
            margin-top: 2rem; 
        }
        .gallery-item { 
            background: var(--glass); 
            border-radius: 20px; 
            border: 1px solid var(--glass-border); 
            overflow: hidden; 
            position: relative; 
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }
        .gallery-item:hover {
            transform: translateY(-5px);
            border-color: var(--primary);
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        .gallery-item img { 
            width: 100%; 
            aspect-ratio: 16/10; 
            object-fit: cover; 
            border-bottom: 1px solid var(--glass-border);
        }
        .gallery-content {
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        .gallery-title {
            font-weight: 600;
            font-size: 1.1rem;
            color: var(--text-light);
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        @media (max-width: 768px) {
            .form-card form {
                flex-direction: column;
            }
            .form-card button {
                width: 100% !important;
            }
        }
    </style>
</head>
<body class="admin-body">
    <div class="mobile-admin-header">
        <button id="openSidebar" style="background: none; border: none; color: white; font-size: 1.5rem; cursor: pointer;">☰</button>
        <div style="font-weight: 800; font-size: 1.1rem;"><?php echo strtoupper($appName); ?></div>
    </div>

    <?php 
    $page = 'gallery';
    require 'layout/sidebar.php'; 
    ?>

    <main class="admin-main">
        <h1 style="margin-bottom: 2rem;">Kelola Galeri Foto</h1>
        
        <?php if (isset($success)): ?>
            <div style="background: rgba(34, 197, 94, 0.1); color: #22c55e; padding: 1rem; border-radius: 10px; margin-bottom: 2rem; border: 1px solid rgba(34, 197, 94, 0.2);">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <div class="form-card">
            <h3 style="margin-bottom: 1.5rem;">Tambah Foto Baru</h3>
            <form action="" method="POST" enctype="multipart/form-data" style="display: flex; gap: 1rem; align-items: flex-start;">
                <div style="flex: 1;">
                    <input type="text" name="title" placeholder="Judul Foto" required style="padding: 1rem; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); color: white; border-radius: 10px; width: 100%;">
                </div>
                <div style="flex: 1;">
                    <input type="file" name="image" required style="padding: 0.8rem; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); color: white; border-radius: 10px; width: 100%;">
                </div>
                <button type="submit" name="add_photo" class="btn btn-primary" style="width: auto; padding: 1rem 2rem;">Upload</button>
            </form>
        </div>

        <div class="gallery-grid">
            <?php foreach ($gallery as $item): ?>
            <div class="gallery-item">
                <img src="../uploads/<?php echo $item['image']; ?>" alt="<?php echo $item['title']; ?>">
                <div class="gallery-content">
                    <h4 class="gallery-title"><?php echo $item['title']; ?></h4>
                    <a href="gallery.php?delete=<?php echo $item['id']; ?>" class="btn btn-glass" style="color: var(--accent); font-size: 0.8rem; width: 100%;" onclick="return confirm('Hapus foto ini?')">Hapus Foto</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <?php if (empty($gallery)): ?>
            <div style="text-align: center; color: var(--text-muted); padding: 5rem;">Belum ada koleksi foto.</div>
        <?php endif; ?>
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
