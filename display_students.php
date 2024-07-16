<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Project";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL query to retrieve student data
$sql = "SELECT id, name, ic, email, course_id, course_name FROM users";
$result = $conn->query($sql);

// Check if the query returned any results
if ($result->num_rows > 0) {
    // Start building the HTML table
    echo "<table>
            <tr>
                <th>Student ID</th>
                <th>Student Name</th>
                <th>IC</th>
                <th>Email</th>
                <th>Course ID</th>
                <th>Course Name</th>
            </tr>";

    // Output data of each row
    while($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . $row["id"]. "</td>
                <td>" . $row["name"]. "</td>
                <td>" . $row["ic"]. "</td>
                <td>" . $row["email"]. "</td>
                <td>" . $row["course_id"]. "</td>
                <td>" . $row["course_name"]. "</td>
              </tr>";
    }

    // Close the HTML table
    echo "</table>";
} else {
    echo "0 results";
}

// Close the database connection
$conn->close();
?>
