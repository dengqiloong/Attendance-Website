<?php
include_once 'PHPExcel.php';

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "attendance";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Load Excel file
$filename = 'attendance_export.xlsx';
$objPHPExcel = PHPExcel_IOFactory::load($filename);

// Get worksheet dimensions
$sheet = $objPHPExcel->getSheet(0);
$highestRow = $sheet->getHighestRow();
$highestColumn = $sheet->getHighestColumn();

// Loop through each row of the worksheet in turn
for ($row = 2; $row <= $highestRow; $row++) {
    // Read a row of data into an array
    $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);

    // Prepare SQL update query
    $id = $rowData[0][0]; // Assuming first column is ID
    $attendance_status = $rowData[0][1]; // Adjust this according to your columns
    // Add other columns as needed

    $sql = "UPDATE attendance SET attendance_status='$attendance_status' WHERE id=$id";
    $conn->query($sql);
}

echo "Data updated from Excel file successfully.";
?>
