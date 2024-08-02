<?php
session_start();

// Check if the user is already logged in
if (isset($_SESSION['user_id'])) {
    // Redirect to the landing page
    header("Location: public_user_landing.php");
    exit();
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form values
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $location = $_POST['location'];

    // Connect to the database
    $servername = "localhost";
    $dbUsername = "root";
    $dbPassword = "";
    $dbname = "urbanlink";

    try {
        // Create a PDO instance
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $dbUsername, $dbPassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Set table and column names based on the selected location
        $table = ($location === 'gobi') ? 'gobi_users' : 'sathy_users';
        $userColumn = ($location === 'gobi') ? 'gobi_user_name' : 'sathy_user_name';
        $idColumn = ($location === 'gobi') ? 'gobi_user_id' : 'sathy_user_id';
        $emailColumn = ($location === 'gobi') ? 'gobi_user_email' : 'sathy_user_email';
        $passwordColumn = ($location === 'gobi') ? 'gobi_user_password' : 'sathy_user_password';
        $phoneColumn = ($location === 'gobi') ? 'gobi_user_phone_number' : 'sathy_user_phone_number';
        $accStatusColumn = ($location === 'gobi') ? 'gobi_user_acc_status' : 'sathy_user_acc_status';

        // Prepare and execute the query
        $stmt = $conn->prepare("SELECT $idColumn, $phoneColumn, $accStatusColumn FROM $table WHERE $userColumn = :username AND $emailColumn = :email AND $passwordColumn = :password");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->execute();

        // Check if a matching record is found
        if ($stmt->rowCount() > 0) {
            // Fetch the user ID, phone number, and account status
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $userId = $row[$idColumn];
            $userPhone = $row[$phoneColumn];
            $accStatus = $row[$accStatusColumn];

            if ($accStatus === 'UNBLOCKED') {
                // Store user ID and other details in session variables
                $_SESSION['user_id'] = $userId;
                $_SESSION['username'] = $username;
                $_SESSION['email'] = $email;
                $_SESSION['location'] = $location;
                $_SESSION['user_phone'] = $userPhone; // Add user phone to the session

                // Redirect to the landing page
                header("Location: public_user_landing.php");
                exit();
            } else {

                // $errorMessage = "YOUR ACCOUNT IS BLOCKED DUE TO AN ISSUE. PLEASE CONTACT ADMIN [admin001@gmail.com].";
                $errorMessage = "YOUR ACCOUNT IS BLOCKED DUE TO AN ISSUE. PLEASE CONTACT ADMIN [admin@gmail.com]";
                echo '<script>
                    setTimeout(function() {
                        window.location.href = "../index.php";
                    }, 3000);
                </script>';
            }
        } else {
            $errorMessage = "Invalid credentials. Please try again.";
        }
    } catch (PDOException $e) {
        // Handle database connection or query errors
        $errorMessage = "Database error: " . $e->getMessage();
    }
}
?>

<!-- Rest of the HTML code remains unchanged -->

<!DOCTYPE html>
<html>

<head>
    <title>Public User Login</title>
    <link rel="icon" href="../images/urbanlink-logo.png" type="image/icon type">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@300&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto Slab', sans-serif;
            margin: 0;
            padding: 0;
            background-image: url("../images/checkered-pattern.png"), linear-gradient(to right top, white, white);
            /* background-image: url("./images/diagonal-striped-brick.png"), linear-gradient(to right top, white, white); */
            /* background: #f0f0f0; */
            height: 87vh;
            overflow: hidden;
            align-items: center;
            justify-content: center;
        }

        body::-webkit-scrollbar {
            display: none;
        }

        .forgot:hover {
            color: #cc0000;
            transition: 0.2s linear;
        }


        .go-back {
            padding: 10px;
            text-align: center;
            background-color: orange;
            cursor: pointer;
            color: white;
            text-decoration: none;
            border: 2px solid white;
            border-radius: 5px;
        }

        .go-back:hover {
            background-color: #0088cc;
            transition: 0.2s linear;
        }

        .back-button {
            position: relative;
            top: 50px;
            margin-left: 10px;
        }

        .toggle-password {
            position: absolute;
            top: 53%;
            right: 35px;
            transform: translateY(-50%);
            color: white;
            background-color: pink;
            cursor: pointer;
            padding: 5px;
            border-radius: 10px;
        }

        .toggle-password:hover {
            color: #555;
        }

        .container {
            position: absolute;
            background-color: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            left: 50%;
            top: 35%;
            display: grid;
            grid-template-columns: repeat(2, 50%);
            transform: translate(-50%, -50%);
            width: 850px;
            margin: 100px auto;
            padding: 20px;
            border-radius: 20px;
            height: 550px;
            box-shadow: 10px 10px 10px rgba(0, 0, 0, 1);

        }

        img {
            float: right;
        }

        h2 {
            text-align: center;
            color: black;
            margin-bottom: 20px;
            position: relative;
            left: 200px;
            top: 10px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        select {
            width: 370px;
            padding: 10px;
            border-radius: 3px;
            border: none;
            border-bottom: 2px solid #ccc;
            outline: none;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus,
        select:focus {
            border-bottom: 2px solid #0088cc;
        }



        input[type="submit"] {
            width: 370px;
            padding: 10px;
            background-color: lightseagreen;
            color: #ffffff;
            border: none;
            cursor: pointer;
            border-radius: 3px;
            margin-top: 10px;
        }

        input[type="submit"]:hover {
            background-color: #4CAF50;
            transition: 0.2s ease-in;
        }

        .loginpic {
            position: relative;
            height: 300px;
            width: 300px;
            left: 50px;
            top: 20px;
            border-radius: 10px;
        }

        .posi {
            position: relative;
            top: -10px;
            left: 50px;
        }

        .error {
            color: #FF0000;
            margin-bottom: 10px;
            position: absolute;
            margin-top: 10px;
            margin-left: 30px;
            text-align: center;
        }

        .success {
            color: #008000;
            margin-bottom: 10px;
        }

        @media only screen and (min-width:801px) and (max-width:1100px) {


            .container {
                margin: 0 auto;
                width: 60%;
                position: absolute;
                height: 550px;
                left: 50%;
                padding: 0 auto;
                top: 300px;
                align-items: center;
                justify-content: center;

            }

            h2 {
                position: absolute;
                top: -10px;
                left: 35%;
            }

            .posi {
                top: 160px;
                position: absolute;
                left: 20%;

            }

            .form-group input,
            .form-group select {
                box-sizing: border-box;
                position: relative;
                width: 150%;

            }

            .loginpic {
                width: 35%;
                position: absolute;
                left: 30%;
                top: 8%;
                height: 110px;
            }

            .btn {
                box-sizing: border-box;
                position: relative;
                width: 80%;
            }

            .back-button {
                position: relative;
                top: 10px;
                left: -5px;

            }

            .go-back {
                padding: 1px;
                text-align: center;
                background-color: orange;
                cursor: pointer;
                color: white;
                text-decoration: none;
                border: 2px solid white;
                border-radius: 5px;
                height: 50px;
                width: 50px;
            }
        }


        /* 700 to 800 */
        @media only screen and (min-width:701px) and (max-width:800px) {

            .container {
                margin: 0 auto;
                width: 65%;
                position: absolute;
                height: 550px;
                left: 50%;
                padding: 0 auto;
                top: 300px;
                align-items: center;
                justify-content: center;

            }

            h2 {
                position: absolute;
                top: -10px;
                left: 35%;
            }

            .posi {
                top: 160px;
                position: absolute;
                left: 20%;

            }

            .form-group input,
            .form-group select {
                box-sizing: border-box;
                position: relative;
                width: 150%;

            }

            .loginpic {
                width: 35%;
                position: absolute;
                left: 30%;
                top: 8%;
                height: 110px;
            }

            .btn {
                box-sizing: border-box;
                position: relative;
                width: 80%;
            }

            .back-button {
                position: relative;
                top: 10px;
                left: -5px;

            }

            .go-back {
                padding: 1px;
                text-align: center;
                background-color: orange;
                cursor: pointer;
                color: white;
                text-decoration: none;
                border: 2px solid white;
                border-radius: 5px;
                height: 50px;
                width: 50px;
            }

        }

        /* 600 to 700 */
        @media only screen and (min-width:601px) and (max-width:700px) {
            .container {
                margin: 0 auto;
                width: 70%;
                position: absolute;
                height: 550px;
                left: 50%;
                padding: 0 auto;
                top: 300px;

            }

            h2 {
                position: absolute;
                top: -10px;
                left: 30%;
            }

            .posi {
                top: 160px;
                position: absolute;
                left: 20%;

            }

            .form-group input,
            .form-group select {
                box-sizing: border-box;
                position: relative;
                width: 150%;

            }

            .loginpic {
                width: 40%;
                position: absolute;
                left: 30%;
                top: 10%;
                height: 100px;
            }

            .btn {
                box-sizing: border-box;
                position: relative;
                width: 80%;
            }

            .back-button {
                position: relative;
                top: 10px;
                left: -5px;

            }

            .go-back {
                padding: 1px;
                text-align: center;
                background-color: orange;
                cursor: pointer;
                color: white;
                text-decoration: none;
                border: 2px solid white;
                border-radius: 5px;
                height: 50px;
                width: 50px;
            }
        }

        /* 500 to 600 */
        @media only screen and (min-width:300px) and (max-width:600px) {

            .container {
                margin: 0 auto;
                width: 65%;
                position: absolute;
                height: 550px;
                left: 50%;
                padding: 0 auto;
                top: 300px;

            }

            h2 {
                position: absolute;
                top: -10px;
                left: 30%;
            }

            .posi {
                top: 160px;
                position: absolute;
                left: 10%;

            }

            .form-group input,
            .form-group select {
                box-sizing: border-box;
                position: relative;
                width: 150%;

            }

            .loginpic {
                width: 40%;
                position: absolute;
                left: 30%;
                top: 10%;
                height: 100px;
            }

            .btn {
                box-sizing: border-box;
                position: relative;
                width: 80%;
            }

            .back-button {
                position: relative;
                top: 10px;
                left: -5px;

            }

            .go-back {
                padding: 1px;
                text-align: center;
                background-color: orange;
                cursor: pointer;
                color: white;
                text-decoration: none;
                border: 2px solid white;
                border-radius: 5px;
                height: 50px;
                width: 50px;
            }
        }

        /* 300 to 500 BELOW */

        @media only screen and (min-width:300px) and (max-width:500px) {

            .container {
                margin: 0 auto;
                width: 80%;
                position: absolute;
                height: 550px;
                left: 50%;
                padding: 0 auto;
                top: 300px;
                box-shadow: 10px 10px 10px rgba(0, 0, 0, 0.8);
            }

            h2 {
                position: absolute;
                top: -10px;
                left: 30%;
            }

            .posi {
                top: 160px;
                position: absolute;
                left: 13%;

            }

            .form-group input,
            .form-group select {
                box-sizing: border-box;
                position: relative;
                width: 130%;

            }

            .loginpic {
                width: 40%;
                position: absolute;
                left: 30%;
                top: 9%;
                height: 100px;
                box-shadow: 0px 0px 10px rgba(0, 0, 0, 1);
            }

            .btn {
                box-sizing: border-box;
                position: relative;
                width: 80%;
            }

            .back-button {
                position: relative;
                top: 10px;
                left: -5px;

            }

            .go-back {
                padding: 1px;
                text-align: center;
                background-color: orange;
                cursor: pointer;
                color: white;
                text-decoration: none;
                border: 2px solid white;
                border-radius: 5px;
                height: 50px;
                width: 50px;
            }
        }
    </style>
</head>

<body>
    <div class="back-button">
        <a href="../index.php" class="go-back">⬅️</a>
    </div>
    <div class="container">
        <h2>Main Login</h2>

        <?php if (isset($errorMessage)) : ?>
            <div class="error"><?php echo $errorMessage; ?></div>
        <?php endif; ?>
        <div class="left"></div>
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="posi">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required autocomplete="off">
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required autocomplete="off">
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required autocomplete="off">
                    <i class="fas fa-eye toggle-password"></i>
                </div>

                <div class="form-group">
                    <label for="location">Location</label>
                    <select id="location" name="location" required>
                        <option value="--Select Option--">--Select Option--</option>
                        <option value="gobi">Gobichettipalayam</option>
                        <option value="sathy">Sathyamangalam</option>
                    </select>
                </div>
                <div class="for-pwd">
                    <a href="public_forgot_pwd.php" class="forgot">Forgot Password?</a>
                </div>

                <div class="form-group subbtn">
                    <input type="submit" value="Login">
                </div>

            </div>
        </form>
        <img class="loginpic" src="../images/main_login_pic.jpg">
    </div>
    <script>
        const passwordInput = document.getElementById('password');
        const togglePassword = document.querySelector('.toggle-password');

        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</body>

</html>