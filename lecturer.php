<?php
session_start();
include 'db.php';
include 'check_role.php';
checkRole('lecturer');

include_once 'Classes/PHPExcel.php'; // Ensure this path is correct
include_once 'fpdf186/fpdf.php'; // Ensure this path is correct

// Handle form submissions for approving/denying excuses
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['approve_excuse'])) {
        // Approve excuse
        $id = $_POST['id'];
        $sql = "UPDATE attendance SET status='approved' WHERE id='$id'";
        $conn->query($sql);
    } elseif (isset($_POST['deny_excuse'])) {
        // Deny excuse
        $id = $_POST['id'];
        $reason = $_POST['deny_reason'];
        $sql = "UPDATE attendance SET status='denied', deny_reason='$reason' WHERE id='$id'";
        $conn->query($sql);
    } elseif (isset($_POST['export_to_excel'])) {
        exportToExcel($conn);
    } elseif (isset($_POST['export_to_pdf'])) {
        exportToPDF($conn);
    }
}

// Fetch attendance data
$sql = "SELECT * FROM attendance";
$result = $conn->query($sql);

// Fetch messages and MCs
$sql_messages = "SELECT * FROM messages";
$messages_result = $conn->query($sql_messages);

// Function to export data to Excel
function exportToExcel($conn) {
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

    // Force download of the file
    header('Content-Description: File Transfer');
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($filename));
    readfile($filename);
    exit;
}

// Function to export data to PDF
function exportToPDF($conn) {
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 12);

    // Fetch data from the database
    $sql = "SELECT * FROM attendance";
    $result = $conn->query($sql);

    // Add column headers
    $pdf->Cell(20, 10, 'Student ID', 1);
    $pdf->Cell(40, 10, 'Student Name', 1);
    $pdf->Cell(30, 10, 'Class Code', 1);
    $pdf->Cell(30, 10, 'Date', 1);
    $pdf->Cell(20, 10, 'Status', 1);
    $pdf->Cell(50, 10, 'Remarks', 1);
    $pdf->Ln();

    // Add data rows
    while ($row = $result->fetch_assoc()) {
        $pdf->Cell(20, 10, $row['student_id'], 1);
        $pdf->Cell(40, 10, $row['student_name'], 1);
        $pdf->Cell(30, 10, $row['class_code'], 1);
        $pdf->Cell(30, 10, $row['attendance_date'], 1);
        $pdf->Cell(20, 10, $row['status'], 1);
        $pdf->Cell(50, 10, $row['remarks'], 1);
        $pdf->Ln();
    }

    // Output the PDF
    $pdf->Output('D', 'attendance_export.pdf');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Attendance System</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&amp;display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="stylesheet" href="style.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

  <div class="sidebar">
    <nav>
      <ul>
        <li><a href="#" class="logo">
            <img src="logo.png" alt="Logo">
          </a></li>
        <li><a href="#" onclick="document.getElementById('export-excel-form').submit();">
            <i class="fas fa-file-excel"></i>
            <span class="nav-item">Export to Excel</span>
          </a></li>
        <li><a href="#" onclick="document.getElementById('export-pdf-form').submit();">
            <i class="fas fa-file-pdf"></i>
            <span class="nav-item">Export to PDF</span>
          </a></li>
      </ul>
    </nav>
  </div>

  <div class="content">
    <div class="header">
      <h1>Student Attendance System</h1>
      <p>Manage and track student attendance efficiently</p>
    </div>

    <div class="container">
      <div class="info-container">
        <h2>Class Information</h2>
        <p><strong>Lecturer Name:</strong> Dr. Jane Doe</p>
        <p><strong>Classroom ID:</strong> 10A</p>
        <p><strong>Class Code:</strong> CS101</p>
        <p><strong>Date:</strong> <span id="current-date"></span></p>
        <p><strong>Time:</strong> <span id="current-time"></span></p>
      </div>

      <div class="qr-container">
        <h2>Scan QR Code for Attendance</h2>
        <img id="qr-code" src="" alt="QR Code">
      </div>
    </div>

    <div class="table-container" style="height: 400px; width: 90%; overflow: auto;">
      <table>
        <thead>
          <tr>
            <th>Student ID</th>
            <th>Student Name</th>
            <th>Check-In Date</th>
            <th>Check-In Time</th>
            <th>Attendance</th>
          </tr>
        </thead>
      </table>
    </div>

    <!-- Export to Excel form -->
    <form id="export-excel-form" method="post" action="lecturer.php">
        <input type="hidden" name="export_to_excel" value="1">
    </form>

    <!-- Export to PDF form -->
    <form id="export-pdf-form" method="post" action="lecturer.php">
        <input type="hidden" name="export_to_pdf" value="1">
    </form>

  </div>

  <script>
    function updateQRCode() {
      $.ajax({
        url: 'generate_qr.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
          if (response.qr_code_url) {
            $('#qr-code').attr('src', response.qr_code_url + '?t=' + new Date().getTime());
          } else {
            console.error('QR code generation failed:', response.error);
          }
        },
        error: function(xhr, status, error) {
          console.error('AJAX request failed:', status, error);
        }
      });
    }

    function updateDateTime() {
      const now = new Date();
      document.getElementById('current-date').innerText = now.toLocaleDateString();
      document.getElementById('current-time').innerText = now.toLocaleTimeString();
    }

    // Update QR code every 2 minutes (120000 milliseconds)
    setInterval(updateQRCode, 10000);
    // Update date and time every second
    setInterval(updateDateTime, 1000);

    // Initial QR code fetch and date-time update
    updateQRCode();
    updateDateTime();
  </script>

</body>
</html>
