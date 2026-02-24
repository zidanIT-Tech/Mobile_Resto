<?php
include 'connection.php';

$id = $_POST['id']; // ID menu yang mau diedit

// Ambil data menu lama untuk cek gambar lama
$query_cek = mysqli_query($koneksi, "SELECT foto FROM menus WHERE id='$id'");
$data_lama = mysqli_fetch_assoc($query_cek);

$foto_nama = $data_lama['foto']; // Default pakai foto lama

// Cek jika ada upload foto baru
if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $target_dir = "uploads/";
    $file_extension = pathinfo($_FILES["foto"]["name"], PATHINFO_EXTENSION);
    $foto_nama_baru = uniqid() . '.' . $file_extension;
    $target_file = $target_dir . $foto_nama_baru;

    if (move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
        // Hapus foto lama jika ada
        if ($foto_nama && file_exists("uploads/" . $foto_nama)) {
            unlink("uploads/" . $foto_nama);
        }
        $foto_nama = $foto_nama_baru; // Update variabel foto
    }
}

// Data Update
$nama_menu   = $_POST['nama_menu'];
$harga       = $_POST['harga'];
$id_kategori = $_POST['id_kategori'];
$stok_porsi  = $_POST['stok_porsi'];
$deskripsi   = $_POST['deskripsi'];
$status      = $_POST['status'];
$now         = date('Y-m-d H:i:s');

$sql = "UPDATE menus SET 
        nama_menu='$nama_menu', 
        harga='$harga', 
        id_kategori='$id_kategori', 
        stok_porsi='$stok_porsi', 
        foto='$foto_nama', 
        deskripsi='$deskripsi', 
        status='$status',
        updated_at='$now'
        WHERE id='$id'";

if (mysqli_query($koneksi, $sql)) {
    echo json_encode(["status" => "success", "message" => "Menu berhasil diupdate"]);
} else {
    echo json_encode(["status" => "error", "message" => "Gagal update: " . mysqli_error($koneksi)]);
}
?>