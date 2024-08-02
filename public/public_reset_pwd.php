<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($password === $confirmPassword) {
        $servername = "localhost";
        $dbUsername = "root";
        $dbPassword = "";
        $dbname = "urbanlink";

        try {
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $dbUsername, $dbPassword);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $userId = $_SESSION['user_id'];
            $location = $_SESSION['reset_location'];
            $passwordColumn = ($location === 'gobi') ? 'gobi_user_password' : 'sathy_user_password';
            $userid = ($location === 'gobi') ? 'gobi_user_id' : 'sathy_user_id';
            $table = ($location === 'gobi') ? 'gobi_users' : 'sathy_users';

            // $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE $table SET $passwordColumn = :password WHERE $userid = :userId");
            $stmt->bindParam(':password', $password);
            // $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':userId', $userId);
            $stmt->execute();

            $successMessage = "Password updated successfully. Redirecting to login page...";
            echo "<script>
                    setTimeout(function() {
                                window.location.href='main_login.php';
                                }, 3000);
                </script>";

            // header("refresh:3;url=main_login.php");
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        $errorMessage = "Passwords do not match. Please try again.";
    }
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Reset Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
            margin: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            max-width: 400px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            text-align: left;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            font-weight: bold;
        }

        input[type="password"] {
            width: calc(100% - 16px);
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            margin: 8px 0;
        }

        input[type="submit"] {
            background-color: #009688;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        input[type="submit"]:hover {
            background-color: #00796b;
        }

        .message {
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 10px;
        }

        .success {
            background-color: #FFD700;
        }

        .error {
            background-color: #E32636;
            color: #fff;
        }

        h1 {
            text-align: center;
            color: #333333;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <?php if (isset($successMessage)) echo "<p>$successMessage</p>"; ?>
    <?php if (isset($errorMessage)) echo "<p>$errorMessage</p>"; ?>
    <h1>Reset Password!!</h1>
    <div class="container">
    <form method="post" action="">
        <label for="password">New Password:</label>
        <input type="password" id="password" name="password" required><br><br>
        <label for="confirm_password">Confirm New Password:</label>
        <input type="password" id="confirm_password" name="confirm_password" required><br><br>
        <input type="submit" value="Reset Password">
    </form>
</div>
</body>

</html>