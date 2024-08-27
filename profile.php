<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

// Include database connection
include('db.php');

// Fetch user details from the database
$email = $_SESSION['email'];
$sql = "SELECT id, firstname, lastname, email, dob, image FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Handle image upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["image"])) {
    $image = $_FILES["image"];
    if ($image["error"] == UPLOAD_ERR_OK) {
        $targetDir = "uploads/";
        $targetFile = $targetDir . basename($image["name"]);
        if (move_uploaded_file($image["tmp_name"], $targetFile)) {
            $updateSql = "UPDATE users SET image = ? WHERE email = ?";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bind_param("ss", $image["name"], $email);
            if ($updateStmt->execute()) {
                $success = "Image uploaded successfully.";
                $user["image"] = $image["name"];
            } else {
                $error = "Failed to update image in the database.";
            }
        } else {
            $error = "Failed to upload image.";
        }
    } else {
        $error = "Error in file upload.";
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 20px;
        }
        .container {
            width: 50%;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input, .form-group img {
            width: 100%;
            box-sizing: border-box;
        }
        .form-group img {
            max-width: 200px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .form-group input[type="file"] {
            padding: 0;
        }
        .form-group input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        .form-group input[type="submit"]:hover {
            background-color: #45a049;
        }
        .error, .success {
            color: red;
            margin-bottom: 10px;
        }
        .success {
            color: green;
        }
        .back-btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>User Profile</h2>
        
        <?php if (isset($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if (isset($success)): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="firstname">First Name</label>
                <input type="text" id="firstname" value="<?php echo htmlspecialchars($user['firstname']); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="dob">Date of Birth</label>
                <input type="text" id="dob" value="<?php echo htmlspecialchars($user['dob']); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="image">Profile Image</label>
                <?php if ($user['image']): ?>
                    <img src="uploads/<?php echo htmlspecialchars($user['image']); ?>" alt="Profile Image">
                <?php else: ?>
                    <p>No image uploaded</p>
                <?php endif; ?>
                <input type="file" id="image" name="image">
            </div>
            <div class="form-group">
                <input type="submit" value="Upload Image">
            </div>
        </form>

        <a href="welcome.php" class="back-btn">Back to Welcome Page</a>
    </div>
</body>
</html>
