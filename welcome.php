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
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7fa;
            margin: 0;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        h2 {
            margin-bottom: 20px;
            font-size: 2rem;
            color: #333;
        }

        table {
            width: 100%;
            max-width: 1000px;
            border-collapse: collapse;
            margin-bottom: 30px;
            background-color: #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #e6f7ff;
        }

        .action-btn {
            padding: 8px 15px;
            margin: 2px;
            color: white;
            border: none;
            text-decoration: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .action-btn.edit {
            background-color: #28a745;
        }

        .action-btn.delete {
            background-color: #dc3545;
        }

        .action-btn:hover {
            transform: translateY(-2px);
        }

        .logout-btn {
            padding: 10px 20px;
            background-color: #ff6b6b;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-size: 1rem;
            transition: background-color 0.3s ease;
        }

        .logout-btn:hover {
            background-color: #e63946;
        }

        .profile-link {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }

        .profile-link:hover {
            text-decoration: underline;
        }

        .profile-img {
            max-width: 50px;
            max-height: 50px;
            border-radius: 50%;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .message {
    padding: 10px;
    margin-bottom: 20px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.message.success {
    background-color: #d4edda;
    border-color: #c3e6cb;
    color: #155724;
}

.message.error {
    background-color: #f8d7da;
    border-color: #f5c6cb;
    color: #721c24;
}

        @media (max-width: 600px) {
            body {
                padding: 10px;
            }

            table {
                width: 100%;
                overflow-x: auto;
                display: block;
            }

            th, td {
                padding: 10px;
                font-size: 0.9rem;
            }

            .action-btn {
                font-size: 0.8rem;
            }

            .logout-btn {
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <h2>Welcome, <a href="profile.php" class="profile-link"><?php echo htmlspecialchars($_SESSION['email']); ?></a>!</h2>
    <?php if (isset($_SESSION['message'])): ?>
        <div class="message"><?php echo htmlspecialchars($_SESSION['message']); ?></div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>
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
                    echo "<br>";
                    echo "<br>"; 
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
