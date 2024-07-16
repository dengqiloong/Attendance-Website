<?php
session_start();
include 'db.php';
include 'check_role.php';
checkRole('admin');

// Initialize message variable
$message = "";

// Handle form submissions for adding, editing, and deleting users
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_user'])) {
        // Add new user
        $id = $_POST['id'];
        $name = $_POST['name'];
        $ic = $_POST['ic'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $role = $_POST['role'];
        $course_id = $_POST['course_id'];
        $course_name = $_POST['course_name'];
        $sql = "INSERT INTO users (id, name, ic, email, password, role, course_id, course_name) VALUES ('$id', '$name', '$ic', '$email', '$password', '$role', '$course_id', '$course_name')";
        if ($conn->query($sql) === TRUE) {
            $_SESSION['message'] = "New user added successfully.";
        } else {
            $_SESSION['message'] = "Error: " . $sql . "<br>" . $conn->error;
        }
    } elseif (isset($_POST['edit_user'])) {
        // Edit user
        $id = $_POST['id'];
        $name = $_POST['name'];
        $ic = $_POST['ic'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $role = $_POST['role'];
        $course_id = $_POST['course_id'];
        $course_name = $_POST['course_name'];
        $sql = "UPDATE users SET name='$name', ic='$ic', email='$email', password='$password', role='$role', course_id='$course_id', course_name='$course_name' WHERE id='$id'";
        if ($conn->query($sql) === TRUE) {
            $_SESSION['message'] = "User updated successfully.";
        } else {
            $_SESSION['message'] = "Error: " . $sql . "<br>" . $conn->error;
        }
    } elseif (isset($_POST['delete_user'])) {
        // Delete user
        $id = $_POST['id'];
        $sql = "DELETE FROM users WHERE id='$id'";
        if ($conn->query($sql) === TRUE) {
            $_SESSION['message'] = "User deleted successfully.";
        } else {
            $_SESSION['message'] = "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

// Fetch users from the database
$sql = "SELECT * FROM users";
$result = $conn->query($sql);

// Retrieve and clear message from session
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Page</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="stylesheet" href="style.css">
</head>
<body>

  <div class="sidebar">
    <nav>
      <ul>
        <li><a href="#" class="logo">
            <img src="logo.png" alt="Logo">
            <span class="nav-item"><br>Admin</span>
          </a></li>
        <li><a href="logout.php" class="logout">
            <i class="fas fa-sign-out-alt"></i>
            <span class="nav-item">Log out</span>
          </a></li>
      </ul>
    </nav>
  </div>

  <div class="content">
    <div class="header">
      <h1>Admin Page</h1>
      <p>Manage and track users efficiently</p>
    </div>

    <div class="container form-section">
        <?php if ($message != ""): ?>
            <div class="message">
                <p><?php echo $message; ?></p>
            </div>
        <?php endif; ?>
        
        <div class="info-container">
          <h2>Add New User</h2>
          <form method="POST" action="">
              <div>
                  <label for="id">ID:</label>
                  <input type="text" id="id" name="id" required>
              </div>
              <div>
                  <label for="name">Name:</label>
                  <input type="text" id="name" name="name" required>
              </div>
              <div>
                  <label for="ic">IC:</label>
                  <input type="text" id="ic" name="ic" required>
              </div>
              <div>
                  <label for="email">Email:</label>
                  <input type="email" id="email" name="email" required>
              </div>
              <div>
                  <label for="password">Password:</label>
                  <input type="password" id="password" name="password" required>
              </div>
              <div>
                  <label for="role">Role:</label>
                  <select id="role" name="role" required>
                      <option value="lecturer">Lecturer</option>
                      <option value="student">Student</option>
                  </select>
              </div>
              <div>
                  <label for="course_id">Course ID:</label>
                  <input type="text" id="course_id" name="course_id">
              </div>
              <div>
                  <label for="course_name">Course Name:</label>
                  <input type="text" id="course_name" name="course_name">
              </div>
              <button type="submit" name="add_user" class="button">Add User</button>
          </form>
        </div>

        <!-- Form for editing a user -->
        <div class="info-container">
          <h2>Edit User</h2>
          <form method="POST" action="">
              <div>
                  <label for="editID">ID:</label>
                  <input type="text" id="editID" name="id" required>
              </div>
              <div>
                  <label for="editName">Name:</label>
                  <input type="text" id="editName" name="name" required>
              </div>
              <div>
                  <label for="editIC">IC:</label>
                  <input type="text" id="editIC" name="ic" required>
              </div>
              <div>
                  <label for="editEmail">Email:</label>
                  <input type="email" id="editEmail" name="email" required>
              </div>
              <div>
                  <label for="editPassword">Password:</label>
                  <input type="password" id="editPassword" name="password" required>
              </div>
              <div>
                  <label for="editRole">Role:</label>
                  <select id="editRole" name="role" required>
                      <option value="lecturer">Lecturer</option>
                      <option value="student">Student</option>
                  </select>
              </div>
              <div>
                  <label for="editCourseId">Course ID:</label>
                  <input type="text" id="editCourseId" name="course_id">
              </div>
              <div>
                  <label for="editCourseName">Course Name:</label>
                  <input type="text" id="editCourseName" name="course_name">
              </div>
              <button type="submit" name="edit_user" class="button">Save Changes</button>
          </form>
        </div>
    </div>

    <div class="table-container" style="height: 400px; width: 90%; overflow: auto;">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>IC</th>
            <th>Email</th>
            <th>Role</th>
            <th>Course ID</th>
            <th>Course Name</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['name']; ?></td>
            <td><?php echo $row['ic']; ?></td>
            <td><?php echo $row['email']; ?></td>
            <td><?php echo $row['role']; ?></td>
            <td><?php echo $row['course_id']; ?></td>
            <td><?php echo $row['course_name']; ?></td>
            <td>
              <form method="POST" action="" style="display:inline;">
                  <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                  <button class="button delete" type="submit" name="delete_user">Delete</button>
              </form>
              <button class="button" onclick="populateEditForm(<?php echo htmlspecialchars(json_encode($row)); ?>)">Edit</button>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>

  <script>
    function populateEditForm(user) {
        document.getElementById('editID').value = user.id;
        document.getElementById('editName').value = user.name;
        document.getElementById('editIC').value = user.ic;
        document.getElementById('editEmail').value = user.email;
        document.getElementById('editPassword').value = user.password;
        document.getElementById('editRole').value = user.role;
        document.getElementById('editCourseId').value = user.course_id;
        document.getElementById('editCourseName').value = user.course_name;
    }
  </script>

</body>
</html>



