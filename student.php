<?php
session_start();
include 'db.php'; // Include the database connection file
include 'check_role.php';
checkRole('student'); // Ensure the user is a student

$record_attendance = false;

// Check if student ID is provided via GET (from QR code scan)
if (isset($_GET['student_id'])) {
    $student_id = $_GET['student_id'];
    $record_attendance = true;
} else {
    // Check if student ID is set in session
    if (!isset($_SESSION['student_id'])) {
        die("Invalid access. No student ID provided.");
    }
    $student_id = $_SESSION['student_id'];
}

// Fetch student information
$sql = "SELECT * FROM users WHERE id='$student_id' AND role='student'";
$result = $conn->query($sql);

if ($result->num_rows < 1) {
    die("Student not found or invalid role.");
}

$student = $result->fetch_assoc();
$student_name = $student['name'];

$message = "";

if ($record_attendance) {
    // Get current date and time
    $attendance_date = date('Y-m-d');
    $attendance_time = date('H:i:s');

    // Check if the student is already marked as present today
    $sql = "SELECT * FROM attendance WHERE student_id='$student_id' AND attendance_date='$attendance_date'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Already marked as present
        $message = "You are already marked as present for today.";
    } else {
        // Mark the student as present
        $sql = "INSERT INTO attendance (student_id, student_name, class_code, attendance_date, status, remarks, attendance_percentage, absent_count)
                VALUES ('$student_id', '$student_name', 'CS101', '$attendance_date', 'present', '', 0, 0)";

        if ($conn->query($sql) === TRUE) {
            $message = "Attendance recorded successfully.";
        } else {
            $message = "Error recording attendance: " . $conn->error;
        }
    }
}

// Fetch attendance records for the semester
$sql = "SELECT class_code, COUNT(*) as total_classes, SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as total_present FROM attendance WHERE student_id='$student_id' GROUP BY class_code";
$attendance_records = $conn->query($sql);

// Fetch overall attendance percentage for each subject
$attendance_summary = [];
while ($row = $attendance_records->fetch_assoc()) {
    $total_classes = $row['total_classes'];
    $total_present = $row['total_present'];
    $attendance_percentage = ($total_present / $total_classes) * 100;
    $attendance_summary[] = [
        'class_code' => $row['class_code'],
        'total_classes' => $total_classes,
        'total_present' => $total_present,
        'attendance_percentage' => $attendance_percentage
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Confirmation</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav>
        <ul>
            <li><a href="#" class="logo"><img src="logo.png" alt="Logo"></a></li>
            <li><a href="dashboard.php"><i class="fas fa-home"></i><span class="nav-item">Dashboard</span></a></li>
            <li><a href="attendance.php"><i class="fas fa-calendar-check"></i><span class="nav-item">Attendance</span></a></li>
            <li><a href="profile.php"><i class="fas fa-user"></i><span class="nav-item">Profile</span></a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i><span class="nav-item">Logout</span></a></li>
        </ul>
    </nav>
    <div class="content">
        <div class="header">
            <h1>Attendance Confirmation</h1>
            <p><?php echo $message; ?></p>
        </div>
        <div class="container">
            <?php if ($record_attendance && empty($message)): ?>
                <button class="button" onclick="confirmAttendance()">Click to Confirm Attendance</button>
            <?php endif; ?>
        </div>

        <div class="table-container">
            <h2>Attendance Summary</h2>
            <table>
                <thead>
                    <tr>
                        <th>Subject</th>
                        <th>Total Classes</th>
                        <th>Total Present</th>
                        <th>Attendance Percentage</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($attendance_summary as $summary): ?>
                    <tr>
                        <td><?php echo $summary['class_code']; ?></td>
                        <td><?php echo $summary['total_classes']; ?></td>
                        <td><?php echo $summary['total_present']; ?></td>
                        <td><?php echo number_format($summary['attendance_percentage'], 2); ?>%</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script>
        function confirmAttendance() {
            // Perform the attendance confirmation logic here
        }
    </script>
</body>
</html>


