<?php
require_once 'db.php';
if (!isLoggedIn()) redirect('login.php');

// Fetch all data
$stmt = $pdo->query("
    SELECT
        academic_year,
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
        IF(has_pip = 1, 'Ya', 'Tidak') AS has_pip,
        status,
        created_at
    FROM ms_admission
    ORDER BY created_at DESC
");
$data = $stmt->fetchAll();

// Set Headers for Download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=data_pendaftar_ppdb_' . date('Y-m-d') . '.csv');

// Create a file pointer connected to the output stream
$output = fopen('php://output', 'w');

// Output the column headings
fputcsv($output, array(
    'Tahun Ajaran',
    'Nama Lengkap',
    'Jenis Kelamin',
    'Asal Sekolah (SD/MI)',
    'NISN',
    'Tempat Lahir',
    'Tanggal Lahir',
    'Agama',
    'Alamat Lengkap',
    'Nama Orang Tua/Wali',
    'Nomor HP Siswa',
    'Nomor WA Orang Tua/Wali',
    'Email',
    'PIP',
    'Status',
    'Tanggal Daftar'
));

// Output the data
if (count($data) > 0) {
    foreach ($data as $row) {
        fputcsv($output, $row);
    }
}

fclose($output);
exit();
?>
