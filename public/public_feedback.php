<?php
session_start();

// Check if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to the login page
    header("Location: main_login.php");
    exit();
}

// Initialize variables
$successMessage = "";

// Get the feedback form data if the form is submitted
if (isset($_POST['submit'])) {
    // Handle the submission and save the feedback

    // Database connection credentials
    $servername = "localhost";
    $dbUsername = "root";
    $dbPassword = "";
    $dbname = "urbanlink";

    // Create a PDO instance
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $dbUsername, $dbPassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }

    

    // Get the form data
    $probId = $_SESSION['feedback_problem_id'];
    $userName = $_POST['user_name'];
    $probType = $_POST['prob_type'];
    $probDept = $_SESSION['feedback_prob_dept'];
    $probDesc = $_POST['prob_desc'];
    $probLoc = $_POST['prob_loc'];
    $probDate = $_POST['prob_date'];
    $feedbackDesc = $_POST['feedback_desc'];
    $feedbackRate = $_POST['feedback_rate'];

    // Generate unique feedback ID
    $feedbackId = "PFB" . uniqid();

    // Save the feedback to the feedback_details table
    try {
        $stmt = $conn->prepare("INSERT INTO feedback_details (fb_id, fb_prob_id, fb_user_name,fb_prob_dept, fb_prob_type, fb_prob_desc, fb_prob_loc, fb_prob_date, fb_desc, fb_rate) 
                                VALUES (:feedbackId, :probId, :userName, :probDept,:probType, :probDesc, :probLoc, :probDate, :feedbackDesc, :feedbackRate)");
        $stmt->bindParam(':feedbackId', $feedbackId);
        $stmt->bindParam(':probId', $probId);
        $stmt->bindParam(':userName', $userName);
        $stmt->bindParam(':probDept', $probDept);
        $stmt->bindParam(':probType', $probType);
        $stmt->bindParam(':probDesc', $probDesc);
        $stmt->bindParam(':probLoc', $probLoc);
        $stmt->bindParam(':probDate', $probDate);
        $stmt->bindParam(':feedbackDesc', $feedbackDesc);
        $stmt->bindParam(':feedbackRate', $feedbackRate);
        $stmt->execute();

        // Set the success message
        $successMessage = "Feedback submitted successfully! Your Feedback ID is: $feedbackId";

        // Redirect to the problem management page after 3 seconds
        echo "<script>
            setTimeout(function() {
                window.location.href = 'public_prob_manage.php';
            }, 3000);
        </script>";
    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
} else {
    // Get the form data from the session variables to pre-fill the fields
    $userName = $_SESSION['feedback_user_name'];
    $probType = $_SESSION['feedback_prob_type'];
    $probDept = $_SESSION['feedback_prob_dept'];
    $probDesc = $_SESSION['feedback_prob_desc'];
    $probLoc = $_SESSION['feedback_prob_loc'];
    $probDate = $_SESSION['feedback_prob_date'];
}

?>


<!DOCTYPE html>
<html>

<head>
    <title>Public Feedback Form</title>
    <link rel="icon" href="../images/urbanlink-logo.png" type="image/icon type">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            /* background: linear-gradient(to bottom right, #f2f2f2, #ffffff); */
            background-attachment: fixed;
            background-image: url("../images/8685784_3915274.jpg");
            background-size: cover;
            background-position: center;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 90%;
            max-width: 600px;
            margin: 30px auto;
            background-color: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #333333;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .form-group input[type="text"],
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
            transition: border-color 0.3s ease-in-out;
            box-sizing: border-box;
        }

        .form-group textarea {
            resize: vertical;
        }

        .readonly {
            background-color: #B6B6B4;
            color: white;
        }

        .rating {
            display: flex;
            justify-content: space-evenly;
            margin-top: 20px;
            margin-right: 10px;
        }

        .rating input[type="radio"] {
            display: none;
        }

        .rating label {
            font-size: 24px;
            color: #ccc;
            cursor: pointer;
            transition: color 0.3s ease-in-out;
        }

        .rating label:before {
            content: '\2605';
        }

        .rating input[type="radio"]:checked~label,
        .rating input[type="radio"]:hover~label {
            color: gold;
        }

        .rating input[type="radio"]:checked~label,
        .rating input[type="radio"]:checked~label~label {
            color: #f39c12;
        }

        .btn {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #3498db;
            color: #ffffff;
            font-size: 16px;
            text-align: center;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease-in-out;
        }

        .btn:hover {
            background-color: #2980b9;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Public Feedback Form</h2>
        <?php
        if ($successMessage !== "") {
            echo "<div class='success-message'>$successMessage</div>";
        }
        ?>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <!-- Display the problem details in the form -->
            <input type="hidden" name="prob_id" value="<?php echo htmlspecialchars($_SESSION['feedback_problem_id']); ?>">
            <div class="form-group">
                <label for="user_name">User Name:</label>
                <input class="readonly" type="text" id="user_name" name="user_name" value="<?php echo htmlspecialchars($userName); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="prob_type">Problem Department:</label>
                <input class="readonly" type="text" id="prob_dept" name="prob_dept" value="<?php echo htmlspecialchars($probDept); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="prob_type">Problem Type:</label>
                <input class="readonly" type="text" id="prob_type" name="prob_type" value="<?php echo htmlspecialchars($probType); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="prob_desc">Problem Description:</label>
                <textarea class="readonly" id="prob_desc" name="prob_desc" rows="4" readonly><?php echo htmlspecialchars($probDesc); ?></textarea>
            </div>
            <div class="form-group">
                <label for="prob_loc">Problem Location:</label>
                <input class="readonly" type="text" id="prob_loc" name="prob_loc" value="<?php echo htmlspecialchars($probLoc); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="prob_date">Problem Date:</label>
                <input class="readonly" type="text" id="prob_date" name="prob_date" value="<?php echo htmlspecialchars($probDate); ?>" readonly>
            </div>
            <!-- Feedback form fields -->
            <div class="form-group">
                <label for="feedback_desc">Feedback Description:</label>
                <textarea id="feedback_desc" name="feedback_desc" rows="4" required></textarea>
            </div>
            <div class="form-group">
                <label for="feedback_rate">Feedback Rating:</label>
                <div class="rating">
                    <input type="radio" id="star5" name="feedback_rate" value="5" required>
                    <label for="star5">5</label>
                    <input type="radio" id="star4" name="feedback_rate" value="4" required>
                    <label for="star4">4</label>
                    <input type="radio" id="star3" name="feedback_rate" value="3" required>
                    <label for="star3">3</label>
                    <input type="radio" id="star2" name="feedback_rate" value="2" required>
                    <label for="star2">2</label>
                    <input type="radio" id="star1" name="feedback_rate" value="1" required>
                    <label for="star1">1</label>
                </div>
            </div>
            <button type="submit" name="submit">Submit Feedback</button>
        </form>
    </div>

    <script>
        <?php if ($successMessage !== "") { ?>
            // Redirect to the public_prob_manage.php page after 3 seconds
            setTimeout(function() {
                window.location.href = "public_prob_manage.php";
            }, 3000);
        <?php } ?>
    </script>
</body>

</html>

