<?php
// Tampilkan error agar tidak blank
ini_set('display_errors', 1);
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

include 'connection.php';

// Ambil data JSON
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (!$data) {
    echo json_encode(["status" => "error", "message" => "JSON tidak valid"]);
    exit;
}

// Sesuaikan variabel dengan kolom di database db_resto
$nama_konsumen = mysqli_real_escape_string($koneksi, $data['nama_konsumen']);
$id_meja       = !empty($data['id_meja']) ? $data['id_meja'] : "NULL";
$total_bayar   = $data['total_bayar'];
$metode        = $data['metode_pembayaran'];
$tanggal       = date('Y-m-d H:i:s');

// 1. Simpan ke tabel orderans
$sqlOrder = "INSERT INTO orderans (nama_konsumen, id_meja, total_bayar, metode_pembayaran, status, created_at, updated_at) 
             VALUES ('$nama_konsumen', $id_meja, '$total_bayar', '$metode', 'pending', '$tanggal', '$tanggal')";

if (mysqli_query($koneksi, $sqlOrder)) {
    $id_order_baru = mysqli_insert_id($koneksi);

    // 2. Simpan rincian ke tabel detail_orderans
    foreach ($data['items'] as $item) {
        $id_menu  = $item['id_menu'];
        $jumlah   = $item['jumlah'];
        $subtotal = $item['subtotal'];
        $catatan  = mysqli_real_escape_string($koneksi, $data['catatan'] ?? ''); // Catatan global
        $metode_pesanan = $data['jenis_pesanan']; // 'dinein' atau 'takeaway'

        $sqlDetail = "INSERT INTO detail_orderans (id_orderan, id_menu, jumlah, subtotal, catatan, metode_pesanan, status, created_at, updated_at) 
                      VALUES ('$id_order_baru', '$id_menu', '$jumlah', '$subtotal', '$catatan', '$metode_pesanan', 'processing', '$tanggal', '$tanggal')";
        
        mysqli_query($koneksi, $sqlDetail);
        
        // 3. Update stok porsi
        mysqli_query($koneksi, "UPDATE menus SET stok_porsi = stok_porsi - $jumlah WHERE id = $id_menu");
    }

    echo json_encode(["status" => "success", "message" => "Pesanan terkirim!"]);
} else {
    // Jika gagal, tampilkan pesan error SQL-nya
    echo json_encode(["status" => "error", "message" => mysqli_error($koneksi)]);
}
?>