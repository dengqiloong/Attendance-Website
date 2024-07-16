<?php
session_start();
require 'phpqrcode/qrlib.php'; // Ensure this path is correct

// Ensure the session is secure
session_regenerate_id(true);

// Generate a unique token for the QR code
$token = bin2hex(random_bytes(16));
$_SESSION['qr_token'] = $token;

// Replace with the actual URL of your attendance page
$attendanceUrl = 'https://allenfoong.github.io/SAS-Student-Attendance-System/' . $token;

// Generate QR code image
$qrCodeFile = 'qr_code.png';
QRcode::png($attendanceUrl, $qrCodeFile);

// Check if QR code file is created
if (!file_exists($qrCodeFile)) {
    echo json_encode(['error' => 'QR code generation failed']);
    exit;
}

// Return the QR code image URL
echo json_encode(['qr_code_url' => $qrCodeFile]);
?>