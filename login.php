<?php
session_start(); // Start the session
include 'db.php'; // Include the database connection file

if (isset($_POST["login"])) {
    $email = $_POST["email"];
    $password = $_POST["password"];
    $sql = "SELECT `id`, `password`, `role` FROM `users` WHERE `email`='$email'";
    $result = $conn->query($sql);
    if ($result->num_rows < 1) {
        echo "<span style='color:red'>USERNAME OR EMAIL NOT FOUND</span>";
    } else {
        $row = $result->fetch_assoc();
        if ($row["password"] == $password) {
            $_SESSION["email"] = $email;
            $_SESSION["role"] = $row["role"];
            $_SESSION["student_id"] = $row["id"]; // Store student ID in session
            echo "<span style='color:green;'>LOGIN!</span>";

            // Redirect based on user role
            if ($row["role"] == "admin") {
                header("Location: admin.php");
            } elseif ($row["role"] == "lecturer") {
                header("Location: lecturer.php");
            } elseif ($row["role"] == "student") {
                header("Location: student.php");
            } else {
                echo "<span style='color:red;'>INVALID USER ROLE</span>";
            }
            exit(); // Make sure to exit after the redirect
        } else {
            echo "<span style='color:red;'>INVALID USERNAME OR PASSWORD</span>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Login</title>
</head>

<body>
    <div class="form-container">
        <h2>Login</h2>
        <form action="" method="POST">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" placeholder="Email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" placeholder="Password" required>
            </div>
            <button type="submit" name="login">Login</button>
        </form>
    </div>
</body>

</html>
