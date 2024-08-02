<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['sv_admin_username']) || !isset($_SESSION['sv_admin_email']) || !isset($_SESSION['sv_admin_id'])) {
    // Redirect to the login page
    header("Location: admin_login.php");
    exit();
}

// Get the session values
$username = $_SESSION['sv_admin_username'];
$email = $_SESSION['sv_admin_email'];
$adminId = $_SESSION['sv_admin_id'];

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form values
    $userEmail = $_POST['ap_admin_email'];
    $userPhone = $_POST['ap_admin_phone'];
    $username = $_POST['ap_admin_name']; // Added field for username
    $postDesc = $_POST['ap_desc'];
    $postDate = $_POST['ap_date'];
    $postDate = $_POST['ap_date'];
    $postType = $_POST['ap_type'];
    $currentTime = date("H:i:s");

    $mysqli = new mysqli("localhost", "root", "", "urbanlink");
    if ($mysqli->connect_errno) {
        echo "Failed to connect to MySQL: " . $mysqli->connect_error;
        exit();
    }

    $postID = 'admnp' . uniqid(); // Generate a unique post ID

    // Prepare the insert statement without the image column
    $stmt = $mysqli->prepare("INSERT INTO admin_posts (ap_id, ap_admin_id, ap_admin_name, ap_admin_email, ap_admin_phone, ap_desc, ap_date,ap_type,ap_time) VALUES (?,?,?,?,?,?,?,?,?)");
    $stmt->bind_param("sssssssss", $postID, $adminId, $username, $userEmail, $userPhone,  $postDesc, $postDate, $postType, $currentTime);

    if ($stmt->execute()) {
        $successMessage = "Post created successfully. Post ID: $postID";
    } else {
        $errorMessage = "Error creating post: " . $stmt->error;
    }

    $stmt->close();
    $mysqli->close();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Admin-Create Post</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../images/urbanlink-logo.png" type="image/icon type">

    <style>
        body {
            font-family: Arial, sans-serif;
            /* background-color: #f8f9fa; */
            background-image: url("../images/admin_post_create_bg.jpg");
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            height: 97vh;
        }

        .go-back {
            padding: 10px;
            text-align: center;
            background-color: orange;
            cursor: pointer;
            color: white;
            text-decoration: none;
        }

        .go-back:hover {
            background-color: #0088cc;
            transition: 0.2s linear;
        }

        .back-button {
            position: relative;
            top: 20px;
            margin-left: 10px;
        }

        .container {
            max-width: 800px;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            position: relative;
            backdrop-filter: blur(10px);
            background-color: rgba(255, 255, 255, 0.5);
            position: relative;
            top: 30px;
            left: 300px;
        }

        h2 {
            text-align: center;
            color: #333333;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
            justify-content: space-evenly;
        }

        .form-group.left {
            width: 49%;
            display: inline-block;
        }

        .form-group.right {
            width: 49%;
            display: inline-block;
        }

        label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
            margin-left: 10px;
        }

        input[type="text"],
        input[type="tel"],
        input[type="date"],
        select,
        textarea {
            width: 98%;
            padding: 10px;
            border-radius: 5px;
            border: 2px solid #cccccc;
            box-sizing: border-box;
            outline: none;
            margin-left: 10px;
        }

        input[type="email"] {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 2px solid #cccccc;
            box-sizing: border-box;
            outline: none;
            margin-left: 10px;
        }

        .form-group textarea {
            resize: vertical;
        }

        .note {
            font-size: 14px;
            color: #666666;
            margin-bottom: 10px;
        }

        .form-group .file-input {
            margin-bottom: 10px;
        }

        .form-group .file-input label {
            display: block;
            font-size: 14px;
            color: #333333;
            margin-bottom: 5px;
        }

        .form-group .file-input input[type="file"] {
            display: none;
        }

        .form-group .file-input .file-button {
            display: inline-block;
            padding: 8px 12px;
            background-color: #4CAF50;
            color: #ffffff;
            border: none;
            cursor: pointer;
            border-radius: 3px;
        }

        .error {
            color: #FF0000;
            margin-bottom: 10px;
        }

        .success-toast {
            position: absolute;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            padding: 10px 20px;
            background-color: rgba(0, 128, 0, 0.8);
            color: #ffffff;
            border-radius: 5px;
            text-align: center;
            visibility: hidden;
            opacity: 0;
            transition: visibility 0s, opacity 0.5s linear;
        }

        .success-toast.show {
            visibility: visible;
            opacity: 1;
        }

        .success-toast .success-message {
            margin-bottom: 5px;
        }

        .timer {
            font-size: 14px;
        }

        input[type="submit"] {
            background-color: #007bff;
            color: #ffffff;
            padding: 10px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            width: 50%;
            position: relative;
            left: 29%;
        }

        input[type="submit"]:hover {
            background-color: #4CAF50;
            transition: 0.3s ease-out;
        }

        /* Responsive Styles */

        @media screen and (min-width:801px) and (max-width: 1100px) {
            body {
                background-size: cover;
                background-position: center;
            }

            .container {
                width: 75%;
                margin: 20px auto;
                padding: 10px;
                position: relative;
                left: 0px;
                top: -2px;
            }

            h2 {
                font-size: 24px;
                margin-bottom: 15px;
            }

            .form-group {
                margin-bottom: 10px;
            }

            input[type="submit"] {
                padding: 8px;
            }

            input[type="text"],
            input[type="tel"] {
                width: 85%;
                padding: 10px;
                border-radius: 5px;
                border: 2px solid #cccccc;
                box-sizing: border-box;
                outline: none;
                margin-left: 20px;
            }

            label {
                font-weight: bold;
                display: block;
                margin-bottom: 5px;
                margin-left: 30px;
            }

            input[type="date"],
            select,
            textarea {
                width: 85%;
                padding: 10px;
                border-radius: 5px;
                border: 2px solid #cccccc;
                box-sizing: border-box;
                outline: none;
                margin-left: 45px;
            }

            input[type="email"] {
                width: 85%;
                padding: 10px;
                border-radius: 5px;
                border: 2px solid #cccccc;
                box-sizing: border-box;
                outline: none;
                margin-left: 20px;
            }

            input[type="submit"]:hover {
                background-color: #4CAF50;
                transition: 0.3s ease-out;
            }
        }


        @media screen and (min-width:601px) and (max-width: 800px) {
            body {
                background-size: cover;
                background-position: center;
            }

            .container {
                width: 75%;
                margin: 20px auto;
                padding: 10px;
                position: relative;
                left: 0px;
                top: -2px;
            }

            h2 {
                font-size: 24px;
                margin-bottom: 15px;
            }

            .form-group {
                margin-bottom: 10px;
            }

            input[type="submit"] {
                padding: 8px;
            }

            input[type="text"],
            input[type="tel"] {
                width: 85%;
                padding: 10px;
                border-radius: 5px;
                border: 2px solid #cccccc;
                box-sizing: border-box;
                outline: none;
                margin-left: 20px;
            }

            label {
                font-weight: bold;
                display: block;
                margin-bottom: 5px;
                margin-left: 30px;
            }

            input[type="date"],
            select,
            textarea {
                width: 85%;
                padding: 10px;
                border-radius: 5px;
                border: 2px solid #cccccc;
                box-sizing: border-box;
                outline: none;
                margin-left: 45px;
            }

            input[type="email"] {
                width: 85%;
                padding: 10px;
                border-radius: 5px;
                border: 2px solid #cccccc;
                box-sizing: border-box;
                outline: none;
                margin-left: 20px;
            }

            input[type="submit"]:hover {
                background-color: #4CAF50;
                transition: 0.3s ease-out;
            }

        }

        @media screen and (min-width:300px) and (max-width: 600px) {

            body {
                background-size: cover;
                background-position: center;
            }

            .container {
                width: 92%;
                margin: 20px auto;
                padding: 10px;
                position: relative;
                left: 0px;
                top: -2px;
            }

            h2 {
                font-size: 24px;
                margin-bottom: 15px;
            }

            .form-group {
                margin-bottom: 10px;
            }

            input[type="submit"] {
                padding: 8px;
            }

            input[type="text"],
            input[type="tel"] {
                width: 95%;
                padding: 10px;
                border-radius: 5px;
                border: 2px solid #cccccc;
                box-sizing: border-box;
                outline: none;
                margin-left: 10px;
            }

            input[type="date"],
            select,
            textarea {
                width: 95%;
                padding: 10px;
                border-radius: 5px;
                border: 2px solid #cccccc;
                box-sizing: border-box;
                outline: none;
                margin-left: 10px;
            }

            input[type="email"] {
                width: 95%;
                padding: 10px;
                border-radius: 5px;
                border: 2px solid #cccccc;
                box-sizing: border-box;
                outline: none;
                margin-left: 10px;
            }

            input[type="submit"]:hover {
                background-color: #4CAF50;
                transition: 0.3s ease-out;
            }

            .back-button {
                display: block;
                padding: 10px 5px;
                border-radius: 3px;
                cursor: pointer;
                z-index: 1;
                position: relative;
                top: -2px;
            }
        }
    </style>
</head>

<body>
    <div class="back-button">
        <a href="admin_landing.php" class="go-back">⬅️</a>
    </div>
    <div class="container">
        <h2>Create Post</h2>

        <?php if (isset($errorMessage)) : ?>
            <div class="error"><?php echo $errorMessage; ?></div>
        <?php endif; ?>

        <?php if (isset($successMessage)) : ?>
            <div class="success-toast">
                <div class="success-message"><?php echo $successMessage; ?></div>
                <div class="timer">Redirecting in 3 seconds...</div>
            </div>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="ap_admin_id">User ID</label>
                <input type="text" id="ap_admin_id" name="ap_admin_id" value="<?php echo $adminId; ?>" disabled>
            </div>

            <div class="form-group left">
                <label for="ap_admin_name">Username</label>
                <input type="text" id="ap_admin_name" name="ap_admin_name" placeholder="Enter your username" value="<?php echo $username; ?>" required>
            </div>

            <div class="form-group left">
                <label for="ap_admin_email">User Email</label>
                <input type="email" id="ap_admin_email" name="ap_admin_email" placeholder="Enter your email" required value="<?php echo $email; ?>">
            </div>

            <div class="form-group left">
                <label for="ap_admin_phone">User Phone</label>
                <input type="tel" id="ap_admin_phone" name="ap_admin_phone" placeholder="Enter your phone number" pattern="[0-9]{10}" oninput="validatePhoneNumber(this)" required>
            </div>
            <div class="form-group left">
                <label for="ap_type">Post Type</label>
                <select id="ap_type" name="ap_type" required>
                    <option value="Jobs">Jobs</option>
                    <option value="Social Service">Social Service</option>
                    <option value="Events">Events</option>
                    <option value="News">News</option>
                    <option value="Announcements">Announcements</option>
                    <option value="Volunteer Opportunities">Volunteer Opportunities</option>
                    <option value="Education">Education</option>
                    <option value="Health and Wellness">Health and Wellness</option>
                    <option value="Environment">Environment</option>
                    <option value="Community Development">Community Development</option>
                    <option value="Fundraising">Fundraising</option>
                    <option value="Arts and Culture">Arts and Culture</option>
                    <option value="Sports and Recreation">Sports and Recreation</option>
                    <option value="Public Safety">Public Safety</option>
                    <option value="Transportation">Transportation</option>
                    <option value="Technology and Innovation">Technology and Innovation</option>
                    <option value="Civic Engagement">Civic Engagement</option>
                    <option value="Local Initiatives">Local Initiatives</option>
                    <option value="Resources and Services">Resources and Services</option>
                    <option value="Advocacy">Advocacy</option>
                    <option value="Food and Nutrition">Food and Nutrition</option>
                    <option value="Housing">Housing</option>
                </select>
            </div>



            <div class="form-group">
                <label for="ap_desc">Post Description</label>
                <textarea id="ap_desc" name="ap_desc" rows="5" placeholder="Enter post description" required></textarea>
            </div>

            <div class="form-group">
                <label for="ap_date">Date</label>
                <input type="date" id="ap_date" name="ap_date" required>
            </div>


            <input type="submit" value="Create Post">
        </form>
    </div>

    <script>
        <?php if (isset($successMessage)) : ?>
            setTimeout(function() {
                window.location.href = "admin_landing.php";
            }, 3000);
            document.querySelector('.success-toast').classList.add('show');
        <?php endif; ?>
    </script>
    <script>
        function validatePhoneNumber(input) {
            // Remove non-numeric characters
            input.value = input.value.replace(/\D/g, '');

            // Limit the length to 10 characters
            if (input.value.length > 10) {
                input.value = input.value.slice(0, 10);
            }
        }
    </script>
</body>

</html>