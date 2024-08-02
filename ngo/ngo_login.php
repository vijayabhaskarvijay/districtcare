<?php
// Start the session
session_start();

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $username = $_POST['username'];
    $userphone = $_POST['userphone'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $location = $_POST['location'];
    $organization = $_POST['organization'];
    $organizationemail = $_POST['organizationemail'];

    // Database connection
    $servername = "localhost";
    $dbUsername = "root";
    $dbPassword = "";
    $dbname = "urbanlink";

    $conn = new mysqli($servername, $dbUsername, $dbPassword, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare SQL query
    $stmt = $conn->prepare("SELECT * FROM ngo_details WHERE ngo_user_name = ? AND ngo_user_email = ? AND ngo_user_pwd = ? AND ngo_org_place = ? AND ngo_org_mail= ? AND ngo_user_phone = ?");
    $stmt->bind_param("ssssss", $username, $email, $password, $location, $organizationemail, $userphone);

    // Execute the query
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Fetch the user data
        $row = $result->fetch_assoc();
        $ngoId = $row['ngo_id'];
        $accReqStatus = $row['ngo_acc_req_status'];

        // Check account request status
        if ($accReqStatus == 'Pending' || $accReqStatus == 'Rejected') {
            echo '<script>';
            echo 'alert("Your account request is pending or rejected. Please wait for Approval!! You will be redirected to Home page after 5 seconds.");';
            echo 'setTimeout(function() { window.location.href = "../index.php"; }, 5000);'; // Redirect after 5 seconds
            echo '</script>';
        } elseif ($accReqStatus == 'Approve') {
            // Additional validation and session setup here

            // Save values in session variables
            $_SESSION['ngo_username'] = $username;
            $_SESSION['ngo_useremail'] = $email;
            $_SESSION['ngo_userphone'] = $userphone;
            $_SESSION['ngo_organization'] = $organization;
            $_SESSION['ngo_organizationemail'] = $organizationemail;
            $_SESSION['ngo_location'] = $location;
            $_SESSION['ngo_id'] = $ngoId;

            // Redirect to ngo_landing.php
            header("Location: ngo_landing.php");
            exit();
        }
    } else {
        // Invalid credentials, display error message
        $errorMessage = "Invalid username, email, password, or location";
    }

    // Close database connection
    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<!-- Rest of the HTML code remains unchanged -->


<!DOCTYPE html>
<html>

<head>
    <title>NGO Login</title>
    <link rel="icon" href="../images/urbanlink-logo.png" type="image/icon type">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@300&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, minimum-scale=0.1">
    <style>
        body {
            font-family: 'Roboto Slab', sans-serif;
            margin: 0;
            padding: 0;
            background-image: url("../images/checkered-pattern.png"), linear-gradient(to right top, white, white);
            height: 87vh;
            overflow: hidden;
        }

        .toggle-password {
            position: relative;
            left: -5%;
            /* transform: translateY(-50%); */
            color: white;
            background-color: pink;
            cursor: pointer;
            padding: 5px;
            border-radius: 10px;
            z-index: 1;
        }

        .toggle-password:hover {
            color: #555;
        }

        .container {
            width: 900px;
            margin: 0 auto;
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border: 1px solid #ccc;
            border-radius: 20px;
            height: 650px;
            position: relative;
            top: 30px;
            left: 300px;
            display: inline-block;
            box-shadow: 10px 10px 10px rgba(0, 0, 0, 1);
        }

        .container h2 {
            text-align: center;
            /* margin-bottom: 20px; */
            position: relative;
            top: -30px;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
            top: -40px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            margin-left: 50px;
        }

        .form-group input {
            width: 350px;
            padding: 10px;
            border: none;
            border-bottom: 2px solid #ccc;
            outline: none;
            border-radius: 5px;
            margin-left: 50px;
        }

        .form-group input:focus {
            border-bottom: 2px solid #0088cc;
        }

        .form-group select {
            width: 370px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-left: 50px;
            /* background-color: red; */
        }


        .btn {
            display: block;
            width: 370px;
            padding: 10px;
            text-align: center;
            border-radius: 5px;
            background-color: lightseagreen;
            font-weight: 700;
            color: #fff;
            text-decoration: none;
            border: none;
            position: relative;
            left: 50px;
            top: -20px;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #4CAF50;
            transition: 0.2s ease-in;
        }

        .error-message {
            color: red;
            margin-top: 10px;
        }

        .posi {
            position: relative;
            top: -20px;
            left: -30px;
        }

        .login-pic {
            width: 440px;
            height: 340px;
            position: relative;
            top: -550px;
            left: -20px;
            border-radius: 10px;
            /* border: 10px solid black; */
        }

        .login-pic:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        img {
            float: right;
        }

        @media only screen and (min-width:800px) and (max-width:1100px) {

            .container {
                margin: 0 auto;
                width: 55%;
                position: relative;
                height: 890px;
                left: 180px;
                padding: 0 auto;
                top: 10px;

            }

            .posi {
                top: 230px;
                position: relative;
                left: 3px;

            }

            .form-group input,
            .form-group select {
                box-sizing: border-box;
                width: 80%;
                position: relative;

            }

            .login-pic {
                width: 85%;
                position: relative;
                left: -35px;
                top: -650px;
                height: 200px;
            }

            .btn {
                box-sizing: border-box;
                position: relative;
                width: 80%;
            }
        }

        @media only screen and (min-width:600px) and (max-width:800px) {

            .container {
                margin: 0 auto;
                width: 65%;
                position: relative;
                height: 890px;
                left: 130px;
                padding: 0 auto;
                top: 10px;

            }

            .posi {
                top: 230px;
                position: relative;
                left: 3px;

            }

            .form-group input,
            .form-group select {
                box-sizing: border-box;
                width: 80%;
                position: relative;

            }

            .login-pic {
                width: 85%;
                position: relative;
                left: -35px;
                top: -650px;
                height: 200px;
            }

            .btn {
                box-sizing: border-box;
                position: relative;
                width: 80%;
            }
        }

        @media only screen and (min-width:300px) and (max-width:600px) {

            .container {
                margin: 0;
                margin-left: -15px;
                width: 80%;
                position: relative;
                height: 890px;
                left: 30px;
                padding: 0 auto;
                top: 5px;
                bottom: 10px;

            }

            .posi {
                top: 230px;
                position: relative;
                left: -25px;

            }

            .form-group input,
            .form-group select {
                box-sizing: border-box;
                width: 85%;
                position: relative;

            }

            .login-pic {
                width: 85%;
                position: relative;
                left: -20px;
                top: -650px;
                height: 200px;
            }

            .btn {
                box-sizing: border-box;
                position: relative;
                width: 85%;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>NGO Login</h2>
        <form method="POST" action="ngo_login.php">
            <div class="posi">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" placeholder="Enter your User Name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" placeholder="Enter your UserEmail" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" placeholder="Enter your Password" required>
                    <i class="fas fa-eye toggle-password"></i>
                </div>
                <div class="form-group">
                    <label for="userphone">User Phone Number:</label>
                    <input type="tel" id="userphone" name="userphone" placeholder="Enter your User Phone Number" required>
                </div>
                <div class="form-group">
                    <label for="organization">Organization:</label>
                    <input type="text" id="organization" name="organization" placeholder="Enter your Org-Name" required>
                </div>
                <div class="form-group">
                    <label for="organizationemail">Organization Email:</label>
                    <input type="email" id="organizationemail" name="organizationemail" placeholder="Enter your Organization Email" required>
                </div>
                <div class="form-group ">
                    <label for="location">Location:</label>
                    <select id="location" name="location" required>
                        <option value="-- Select Option --">-- Select Option --</option>
                        <option value="Gobichettipalayam">Gobichettipalayam</option>
                        <option value="Sathyamangalam">Sathyamangalam</option>
                    </select>
                </div>
                <button type="submit" class="btn">Login</button>
            </div>
        </form>

        <?php if (isset($errorMessage)) : ?>
            <p class="error-message"><?php echo $errorMessage; ?></p>
        <?php endif; ?>

        <img class="login-pic" src="../images/8640544.jpg" alt="Login Image">
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