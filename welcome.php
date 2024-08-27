<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

// Include database connection
include('db.php');

// Fetch all users from the database
$sql = "SELECT image, id, firstname, lastname, email, mobile_number, dob FROM users";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 20px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #ddd;
        }
        .action-btn {
            padding: 5px 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            text-decoration: none;
            border-radius: 3px;
            cursor: pointer;
        }
        .action-btn.edit {
            background-color: #2196F3;
        }
        .action-btn.delete {
            background-color: #f44336;
        }
        .logout-btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .profile-link {
            color: #2196F3;
            text-decoration: none;
        }
        .profile-link:hover {
            text-decoration: underline;
        }
        .profile-img {
            max-width: 50px;
            max-height: 50px;
            border-radius: 50%;
        }
    </style>
</head>
<body>
    <h2>Welcome, <a href="profile.php" class="profile-link"><?php echo htmlspecialchars($_SESSION['email']); ?></a>!</h2>
    
    <table>
        <thead>
            <tr>
                <th>Profile Image</th>
                <th>ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Mobile Number</th>
                <th>Date of Birth</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                // Output data of each row
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";

                    $profileImage = !empty($row['image']) ? "uploads/" . htmlspecialchars($row['image']) : "uploads/default.png";
                    echo "<td><img src='" . $profileImage . "' alt='Profile Image' class='profile-img'></td>";

                    echo "<td>" . $row['id'] . "</td>";
                    echo "<td>" . $row['firstname'] . "</td>";
                    echo "<td>" . $row['lastname'] . "</td>";
                    echo "<td>" . $row['email'] . "</td>";
                    echo "<td>" . $row['mobile_number'] . "</td>";
                    echo "<td>" . $row['dob'] . "</td>";
                    echo "<td>";
                    echo "<a href='edit_user.php?id=" . $row['id'] . "' class='action-btn edit'>Edit</a> ";
                    echo "<a href='#' class='action-btn delete' onclick=\"confirmDelete(" . $row['id'] . ")\">Delete</a>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='8'>No users found</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <a href="logout.php" class="logout-btn">Logout</a>

    <script>
        function confirmDelete(userId) {
            if (confirm('Do you really want to delete this user?')) {
                window.location.href = 'delete_user.php?id=' + userId;
            }
        }
    </script>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
