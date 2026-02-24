<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
include 'connection.php';

// Gunakan alias 'nama_meja' agar terbaca di Ionic
$sql = "SELECT id, nomor_meja AS nama_meja, status FROM mejas ORDER BY nomor_meja ASC";
$result = mysqli_query($koneksi, $sql);
$data = [];

while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

echo json_encode(["status" => "success", "data" => $data]);
?>