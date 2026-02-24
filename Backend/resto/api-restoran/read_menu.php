<?php
// Menampilkan error agar mudah dilacak
ini_set('display_errors', 1);
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

include 'connection.php';

if (!isset($koneksi)) {
    die(json_encode(["status" => "error", "message" => "Koneksi database hilang."]));
}

/**
 * PERBAIKAN: 
 * Mengganti menus.kategori_id menjadi menus.id_kategori sesuai error database
 */
$sql = "SELECT menus.*, kategoris.nama_kategori 
        FROM menus 
        LEFT JOIN kategoris ON menus.id_kategori = kategoris.id 
        ORDER BY menus.id DESC";

$result = mysqli_query($koneksi, $sql);

if (!$result) {
    // Jika masih error, PHP akan memberitahu kolom mana yang sebenarnya ada di tabelmu
    die(json_encode(["status" => "error", "message" => mysqli_error($koneksi)]));
}

$data = [];
// Sesuaikan dengan nama folder project Laravel kamu
$base_url = "http://localhost/resto/public/storage/";

while ($row = mysqli_fetch_assoc($result)) {
    $row['foto_url'] = !empty($row['foto']) ? $base_url . $row['foto'] : "https://via.placeholder.com/300x200?text=No+Image";
    
    // Pastikan tipe data sesuai agar Ionic tidak bingung
    $row['harga'] = (int)$row['harga'];
    $row['stok_porsi'] = (int)$row['stok_porsi'];
    $data[] = $row;
}

echo json_encode([
    "status" => "success",
    "data" => $data
]);
?>