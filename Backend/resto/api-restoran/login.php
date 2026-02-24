<?php
// Supaya kalau ada error PHP, tidak merusak format JSON
error_reporting(0); 
header('Content-Type: application/json');
include 'connection.php';

// Cek method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'fail', 'msg' => 'Method not allowed']);
    exit;
}

// Ambil JSON body
$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

// Validasi input
if (!$data || !isset($data['username']) || !isset($data['password'])) {
    echo json_encode(['status' => 'fail', 'msg' => 'Input tidak lengkap']);
    exit;
}

$username = trim($data['username']);
$password = $data['password'];

// Gunakan variabel $koneksi (sesuai file koneksi.php)
// Dan pastikan nama tabel benar (misal: users)
$stmt = $koneksi->prepare("SELECT id, username, email, password FROM users WHERE username = ?");

if (!$stmt) {
    // Jika query gagal disiapkan (misal salah nama tabel)
    echo json_encode(['status' => 'fail', 'msg' => 'Query error']);
    exit;
}

$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['status' => 'fail', 'msg' => 'Username tidak ditemukan']);
    exit;
}

$user = $result->fetch_assoc();

if (password_verify($password, $user['password'])) {
    // Password Benar
    unset($user['password']); // Hapus password biar gak dikirim ke HP
    echo json_encode([
        'status' => 'success',
        'msg' => 'Login berhasil',
        'data' => $user
    ]);
} else {
    // Password Salah
    echo json_encode([
        'status' => 'fail', 
        'msg' => 'Password salah'
    ]);
}
?>