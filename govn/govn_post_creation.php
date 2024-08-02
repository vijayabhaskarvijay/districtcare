<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['sv_gvn_staffname']) || !isset($_SESSION['sv_gvn_staffemail']) || !isset($_SESSION['sv_gvn_staffdept']) || !isset($_SESSION['sv_gvn_staffloc']) || !isset($_SESSION['sv_gvn_staffid'])) {
    // Redirect to the login page
    header("Location: ngo_login.php");
    exit();
}

// Get the session values
$username = $_SESSION['sv_gvn_staffname'];
$email = $_SESSION['sv_gvn_staffemail'];
$organization = $_SESSION['sv_gvn_staffdept'];
$location = $_SESSION['sv_gvn_staffloc'];
$govnId = $_SESSION['sv_gvn_staffid'];
$govn_dept = $_SESSION['sv_gvn_staffdept'];
$govn_phone = $_SESSION['sv_gvn_staffphone'];

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form values
    $userEmail = $_POST['gp_staff_email'];
    $userPhone = $_POST['gp_staff_phone'];
    $username = $_POST['gp_staff_name']; // Added field for username
    $postDesc = $_POST['gp_desc'];
    $staffDept = $_POST['gp_staff_dept'];
    $postDate = $_POST['gp_date'];
    $postLocation = $_POST['gp_loc'];
    $postType = $_POST['gp_type']; // Get the selected post type
    $currentTime = date("H:i:s");

    $mysqli = new mysqli("localhost", "root", "", "urbanlink");
    if ($mysqli->connect_errno) {
        echo "Failed to connect to MySQL: " . $mysqli->connect_error;
        exit();
    }

    $postID = 'govnp' . uniqid(); // Generate a unique post ID

    // Prepare the insert statement without the image column
    $stmt = $mysqli->prepare("INSERT INTO govn_posts (gp_id, gp_staff_id, gp_staff_name, gp_staff_email, gp_staff_phone, gp_staff_dept, gp_loc, gp_desc, gp_date, gp_type,gp_time) VALUES (?, ?, ?,?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssssss", $postID, $govnId, $username, $userEmail, $userPhone, $staffDept, $postLocation, $postDesc, $postDate, $postType, $currentTime);

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
    <title>GOVN-Create Post</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../images/urbanlink-logo.png" type="image/icon type">

    <style>
        body {
            font-family: Arial, sans-serif;
            /* background-color: #f8f9fa; */
            background-image: url("../images/ngo_post_creation_bg.jpeg");
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
            margin: 20px;
            position: relative;
            top: -25px;
            left: -8px;
            border-radius: 5px;
        }

        .go-back:hover {
            background-color: #0088cc;
            transition: 0.2s linear;
        }

        .back-button {
            position: relative;
            top: 30px;
            margin-left: 10px;
        }

        /* MARQUEE CODE CSS SECTION STARTS */

        .marquee-container {
            width: 60%;
            overflow: hidden;
            white-space: nowrap;
            /* background-color: #f7f7f7; */
            padding: 10px 0;
            position: relative;
            top: 20px;
            border: 1px solid black;
            border-radius: 25px;
            left: 18%;
            background: linear-gradient(45deg, #ffcc00, #ff6666, #ff66b2, #cc66ff, #6699ff, #66ccff);
            background-size: 600% 600%;
            animation: gradientAnimation 25s linear infinite;
            color: #fff;
            text-shadow: 4px 4px 4px rgba(0, 0, 0, 0.4);
        }

        /* Style for the marquee content */
        .marquee-content {
            display: inline-block;
            margin-right: 100%;
            animation: marquee 15s linear infinite;
        }

        /* Keyframes for the marquee animation */
        @keyframes marquee {
            0% {
                transform: translateX(100%);
            }

            100% {
                transform: translateX(-100%);
            }
        }

        @keyframes gradientAnimation {
            0% {
                background-position: 0% 0%;
            }

            100% {
                background-position: 600% 600%;
            }
        }

        /* MARQUEE CODE CSS SECTION ENDS */


        .container {
            max-width: 800px;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            position: relative;
            backdrop-filter: blur(10px);
            background-color: rgba(255, 255, 255, 0.5);
            position: relative;
            /* top: 30px; */
            left: 300px;
            height: 600px;
        }

        h2 {
            text-align: center;
            color: #333333;
            margin-bottom: 20px;
            position: relative;
            top: -30px;
        }

        .form-group {
            margin-bottom: 10px;
            justify-content: space-evenly;
            position: relative;
            top: -30px;
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
            z-index: 1;
            transition: visibility 0s, opacity 0.5s linear;
        }

        .success-toast.show {
            z-index: 1;
            visibility: visible;
            opacity: 1;
        }

        .success-toast .success-message {
            margin-bottom: 5px;
            z-index: 1;
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
            top: -30px;
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
        }
    </style>
</head>

<body>
    <div class="back-button">
        <a href="govn_landing.php" class="go-back">⬅️ GO BACK</a>
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
            <div class="form-group left">
                <label for="gp_staff_email">User Email</label>
                <input type="email" id="gp_staff_email" name="gp_staff_email" placeholder="Enter your email" required value="<?php echo $email; ?>">
            </div>
            <div class="form-group left">
                <label for="gp_staff_name">Username</label>
                <input type="text" id="gp_staff_name" name="gp_staff_name" placeholder="Enter your username" value="<?php echo $username; ?>" required>
            </div>
            <div class=" form-group left">
                <label for="gp_staff_dept">Department</label>
                <select id="gp_staff_dept" name="gp_staff_dept" class="custom-select">
                    <option value="Revenue Department" <?php if ($_SESSION['sv_gvn_staffdept'] === 'Revenue Department') echo 'selected'; ?>>Revenue Department</option>
                    <option value="Public Works Department (PWD)" <?php if ($_SESSION['sv_gvn_staffdept'] === 'Public Works Department (PWD)') echo 'selected'; ?>>Public Works Department (PWD)</option>
                    <option value="Municipal Corporation" <?php if ($_SESSION['sv_gvn_staffdept'] === 'Municipal Corporation') echo 'selected'; ?>>Municipal Corporation</option>
                    <option value="Health Department" <?php if ($_SESSION['sv_gvn_staffdept'] === 'Health Department') echo 'selected'; ?>>Health Department</option>
                    <option value="Education Department" <?php if ($_SESSION['sv_gvn_staffdept'] === 'Education Department') echo 'selected'; ?>>Education Department</option>
                    <option value="Agriculture Department" <?php if ($_SESSION['sv_gvn_staffdept'] === 'Agriculture Department') echo 'selected'; ?>>Agriculture Department</option>
                    <option value="Police Department" <?php if ($_SESSION['sv_gvn_staffdept'] === 'Police Department') echo 'selected'; ?>>Police Department</option>
                    <option value="Fire and Rescue Services Department" <?php if ($_SESSION['sv_gvn_staffdept'] === 'Fire and Rescue Services Department') echo 'selected'; ?>>Fire and Rescue Services Department</option>
                    <option value="Social Welfare Department" <?php if ($_SESSION['sv_gvn_staffdept'] === 'Social Welfare Department') echo 'selected'; ?>>Social Welfare Department</option>
                    <option value="Rural Development Department" <?php if ($_SESSION['sv_gvn_staffdept'] === 'Rural Development Department') echo 'selected'; ?>>Rural Development Department</option>
                    <option value="Transport Department" <?php if ($_SESSION['sv_gvn_staffdept'] === 'Transport Department') echo 'selected'; ?>>Transport Department</option>
                    <option value="Forest Department" <?php if ($_SESSION['sv_gvn_staffdept'] === 'Forest Department') echo 'selected'; ?>>Forest Department</option>
                    <option value="Animal Husbandry Department" <?php if ($_SESSION['sv_gvn_staffdept'] === 'Animal Husbandry Department') echo 'selected'; ?>>Animal Husbandry Department</option>
                    <option value="Town Planning Department" <?php if ($_SESSION['sv_gvn_staffdept'] === 'Town Planning Department') echo 'selected'; ?>>Town Planning Department</option>
                    <option value="Electricity Department" <?php if ($_SESSION['sv_gvn_staffdept'] === 'Electricity Department') echo 'selected'; ?>>Electricity Department</option>
                    <option value="Water Supply and Sanitation Department" <?php if ($_SESSION['sv_gvn_staffdept'] === 'Water Supply and Sanitation Department') echo 'selected'; ?>>Water Supply and Sanitation Department</option>
                    <option value="Public Distribution System (PDS) Department" <?php if ($_SESSION['sv_gvn_staffdept'] === 'Public Distribution System (PDS) Department') echo 'selected'; ?>>Public Distribution System (PDS) Department</option>
                    <option value="Information and Public Relations Department" <?php if ($_SESSION['sv_gvn_staffdept'] === 'Information and Public Relations Department') echo 'selected'; ?>>Information and Public Relations Department</option>
                </select>
            </div>
            <div class="form-group left">
                <label for="gp_staff_phone">User Phone</label>
                <input type="tel" id="gp_staff_phone" name="gp_staff_phone" placeholder="Enter your phone number" value="<?php echo $govn_phone; ?>" pattern="[0-9]{10}" oninput="validatePhoneNumber(this)" required>
            </div>
            <div class="form-group">
                <label for="gp_type">Post Type</label>
                <select id="gp_type" name="gp_type" required>
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
                    <option value="Others">Others</option>
                </select>
            </div>


            <div class="form-group">
                <label for="gp_desc">Post Description</label>
                <textarea id="gp_desc" name="gp_desc" rows="5" placeholder="Enter post description" required></textarea>
            </div>
            <div class="form-group">
                <label for="gp_date">Date</label>
                <input type="date" id="gp_date" name="gp_date" required>
            </div>
            <div class="form-group">
                <label for="gp_loc">Location</label>
                <select id="gp_loc" name="gp_loc" required>
                    <option value="Gobichettipalayam">Gobichettipalayam</option>
                    <option value="Sathyamangalam">Sathyamangalam</option>
                </select>
            </div>

            <input type="submit" value="Create Post">
        </form>
    </div>
    <!-- MARQUEE STARTS -->
    <div class="marquee-container">
        <div class="marquee-content">
            Let's create a supportive and respectful environment. Please refrain from sharing inappropriate content, engaging in spam, or promoting any prohibited activities. Together, we can build a positive community.
        </div>
    </div>
    <!-- MARQUEE ENDS -->
    <script>
        <?php if (isset($successMessage)) : ?>
            setTimeout(function() {
                window.location.href = "govn_landing.php";
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