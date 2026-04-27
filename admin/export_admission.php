<?php
require_once 'db.php';
if (!isLoggedIn()) redirect('login.php');

// Fetch all data
$stmt = $pdo->query("SELECT full_name, email, phone, school_origin, status, created_at FROM ms_admission ORDER BY created_at DESC");
$data = $stmt->fetchAll();

// Set Headers for Download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=data_pendaftar_ppdb_' . date('Y-m-d') . '.csv');

// Create a file pointer connected to the output stream
$output = fopen('php://output', 'w');

// Output the column headings
fputcsv($output, array('Nama Lengkap', 'Email', 'No. Telp', 'Asal Sekolah', 'Status', 'Tanggal Daftar'));

// Output the data
if (count($data) > 0) {
    foreach ($data as $row) {
        fputcsv($output, $row);
    }
}

fclose($output);
exit();
?>
