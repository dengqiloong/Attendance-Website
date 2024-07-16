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

// Fetch data from database
$sql = "SELECT * FROM attendance";
$result = $conn->query($sql);

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();
$objPHPExcel->setActiveSheetIndex(0);

// Add column headers
$column = 0;
while ($fieldinfo = $result->fetch_field()) {
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($column, 1, $fieldinfo->name);
    $column++;
}

// Add data rows
$row = 2;
while ($data = $result->fetch_assoc()) {
    $column = 0;
    foreach ($data as $cell) {
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($column, $row, $cell);
        $column++;
    }
    $row++;
}

// Save Excel file
$filename = 'attendance_export.xlsx';
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save($filename);

echo "Data exported to Excel file successfully.";
?>
