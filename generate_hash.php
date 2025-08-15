<?php
// Script untuk menghasilkan password hash untuk admin123

$password = 'admin123';
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "Password: " . $password . "\n";
echo "Hash: " . $hash . "\n";
echo "Panjang Hash: " . strlen($hash) . " karakter\n";

// Simpan ke file
file_put_contents('password_hash.txt', $hash);
echo "Hash juga telah disimpan ke file password_hash.txt\n";
?>