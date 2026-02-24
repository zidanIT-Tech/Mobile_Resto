<?php
include 'connection.php';

// Cek apakah ada file foto yang dikirim
$foto_nama = null;
if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $target_dir = "uploads/";
    $file_extension = pathinfo($_FILES["foto"]["name"], PATHINFO_EXTENSION);
    // Generate nama file unik agar tidak bentrok
    $foto_nama = uniqid() . '.' . $file_extension;
    $target_file = $target_dir . $foto_nama;

    if (!move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
        echo json_encode(["status" => "error", "message" => "Gagal upload gambar"]);
        exit;
    }
}

// Ambil data text dari POST
$nama_menu   = $_POST['nama_menu'];
$harga       = $_POST['harga'];
$id_kategori = $_POST['id_kategori']; // Pastikan ID ini ada di tabel kategoris
$stok_porsi  = $_POST['stok_porsi'];
$deskripsi   = $_POST['deskripsi'];
$status      = $_POST['status']; // 'available' atau 'sold out' (sesuai Enum)
$now         = date('Y-m-d H:i:s');

// Query Insert
$sql = "INSERT INTO menus (nama_menu, harga, id_kategori, stok_porsi, foto, deskripsi, status, created_at, updated_at) 
        VALUES ('$nama_menu', '$harga', '$id_kategori', '$stok_porsi', '$foto_nama', '$deskripsi', '$status', '$now', '$now')";

if (mysqli_query($koneksi, $sql)) {
    echo json_encode(["status" => "success", "message" => "Menu berhasil ditambahkan"]);
} else {
    echo json_encode(["status" => "error", "message" => "Gagal: " . mysqli_error($koneksi)]);
}
?>