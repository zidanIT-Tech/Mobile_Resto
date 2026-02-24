<?php
include 'connection.php';

$sql = "SELECT * FROM kategoris ORDER BY nama_kategori ASC";
$result = mysqli_query($koneksi, $sql);

$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

echo json_encode([
    "status" => "success",
    "data" => $data
]);
?>