<?php
include 'connection.php';

// Bisa pakai GET atau POST
$id = isset($_GET['id']) ? $_GET['id'] : $_POST['id'];

// Ambil info gambar dulu untuk dihapus filenya
$query_cek = mysqli_query($koneksi, "SELECT foto FROM menus WHERE id='$id'");
$data = mysqli_fetch_assoc($query_cek);

if ($data) {
    // Hapus file fisik gambar jika ada
    if ($data['foto'] && file_exists("uploads/" . $data['foto'])) {
        unlink("uploads/" . $data['foto']);
    }

    // Hapus data di database
    $sql = "DELETE FROM menus WHERE id='$id'";
    if (mysqli_query($koneksi, $sql)) {
        echo json_encode(["status" => "success", "message" => "Menu berhasil dihapus"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Gagal hapus db"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Menu tidak ditemukan"]);
}
?>