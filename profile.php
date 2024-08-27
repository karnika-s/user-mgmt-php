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
        $imageFileType = strtolower(pathinfo($image["name"], PATHINFO_EXTENSION));
        $newFileName = uniqid() . '.' . $imageFileType;
        $targetFile = $targetDir . $newFileName;

        // Validate image file type
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($imageFileType, $allowedTypes)) {
            $error = "Only JPG, JPEG, PNG, and GIF files are allowed.";
        } else {
            if (move_uploaded_file($image["tmp_name"], $targetFile)) {
                // Update user data with new image path
                $updateSql = "UPDATE users SET image = ? WHERE email = ?";
                $updateStmt = $conn->prepare($updateSql);
                $updateStmt->bind_param("ss", $newFileName, $email);
                if ($updateStmt->execute()) {
                    $success = "Image uploaded successfully.";
                    $user["image"] = $newFileName;
                } else {
                    $error = "Failed to update image in the database.";
                }
            } else {
                $error = "Failed to upload image.";
            }
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
            margin: 20px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        .form-group input, .form-group img {
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .form-group img {
            max-width: 150px;
            margin-bottom: 15px;
            display: block;
            border-radius: 50%;
        }
        .form-group input[type="file"] {
            padding: 3px;
        }
        .form-group input[type="submit"] {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
            font-size: 16px;
        }
        .form-group input[type="submit"]:hover {
            background-color: #45a049;
        }
        .error, .success {
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 4px;
        }
        .error {
            color: #d8000c;
            background-color: #ffbaba;
        }
        .success {
            color: #4F8A10;
            background-color: #007bfF;
        }
        .back-btn {
            display: inline-block;
            padding: 12px 25px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
        }
        .back-btn:hover {
            background-color: #45a049;
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
                <?php if (!empty($user['image'])): ?>
                    <img src="uploads/<?php echo htmlspecialchars($user['image']); ?>" alt="Profile Image">
                <?php else: ?>
                    <p>No image uploaded</p>
                <?php endif; ?>
                <input type="file" id="image" name="image" accept="image/*">
            </div>
            <div class="form-group">
                <input type="submit" value="Upload Image">
            </div>
        </form>

        <a href="welcome.php" class="back-btn">Back to Welcome Page</a>
    </div>
</body>
</html>
