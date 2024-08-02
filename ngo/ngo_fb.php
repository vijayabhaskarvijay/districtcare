<!DOCTYPE html>
<html>

<head>
    <script src="https://kit.fontawesome.com/4c43584236.js" crossorigin="anonymous"></script>
    <link rel="icon" href="../images/urbanlink-logo.png" type="image/icon type">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
        }

        .container {
            background-color: #fff;
            box-shadow: 10px 0 10px rgba(0, 0, 0, 2);
            margin: 20px auto;
            max-width: 600px;
            padding: 20px;
            border-radius: 10px;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        /* Apply a unique background color to the fb-in div */
        .fb-in {
            background-color: #009688;
            /* Teal, you can change this to your desired color */
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            transition: background-color 0.3s ease-in-out;
            cursor: pointer;
        }

        /* Change the label and paragraph styles within the fb-in div */
        .fb-in label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color: #fff;
            /* Text color for labels */
        }

        .fb-in p {
            margin: 0;
            padding: 5px;
            color: #fff;
            /* Text color for paragraphs */
        }

        /* Apply a hover effect to the fb-in div */
        .fb-in:hover {
            background-color: #FFD700;
            box-shadow: 0 0 20px rgba(255, 215, 0, 1);
        }


        hr {
            margin: 10px 0;
            /* Margin above and below the horizontal line */
            border: none;
            border-top: 1px solid #ccc;
            /* Style and color of the horizontal line */
        }

        .highlight-hover:hover {
            background-color: #f5f5f5;
            transition: background-color 0.3s ease-in-out;
        }

        .no-feedback {
            text-align: center;
            margin-top: 50px;
        }

        .emoji {
            font-size: 60px;
        }

        /* Custom Graphics and Icons */
        .no-feedback .emoji {
            font-size: 120px;
            color: #888;
            /* Adjust color as needed */
        }

        /* Hover Effects for Label and Paragraph */

        /* Subtle Fade-in Animation */
        .container {
            animation: fadeIn 1s ease-in-out;
        }

        @keyframes fadeIn {
            0% {
                opacity: 0;
            }

            100% {
                opacity: 1;
            }
        }
    </style>
</head>

<body>
    <?php
    // Check if prob_id is present in the URL
    if (isset($_GET['prob_id'])) {
        $prob_id = $_GET['prob_id'];

        // Create a connection to the database
        $conn = new mysqli("localhost", "root", "", "urbanlink");

        // Check the connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Query to fetch feedback details based on prob_id
        $feedback_sql = "SELECT * FROM ngo_feedback_details WHERE ngo_fb_prob_id = ?";
        $feedback_stmt = $conn->prepare($feedback_sql);
        $feedback_stmt->bind_param("s", $prob_id);
        $feedback_stmt->execute();
        $feedback_result = $feedback_stmt->get_result();

        // Fetch and store feedback details in session variables
        if ($feedback_result->num_rows > 0) {
            $feedback_data = $feedback_result->fetch_assoc();
            $_SESSION['fb_id'] = $feedback_data['ngo_fb_id'];
            $_SESSION['fb_prob_id'] = $feedback_data['ngo_fb_prob_id'];
            $_SESSION['fb_user_name'] = $feedback_data['ngo_fb_username'];
            $_SESSION['fb_org_name'] = $feedback_data['ngo_fb_orgname'];
            $_SESSION['fb_prob_desc'] = $feedback_data['ngo_fb_probdesc'];
            $_SESSION['fb_prob_loc'] = $feedback_data['ngo_fb_probloc'];
            $_SESSION['fb_prob_date'] = $feedback_data['ngo_fb_probdate'];
            $_SESSION['fb_prob_fbdesc'] = $feedback_data['ngo_fb_fbdesc'];
            // $_SESSION['fb_prob_type'] = $feedback_data['fb_prob_type'];
            $_SESSION['fb_rate'] = $feedback_data['ngo_fb_rate'];

            // Close the database connection
            $conn->close();
        } else {
            // Handle the case where no feedback is found
            echo '<div class="no-feedback"><div class="emoji">😶‍🌫️🫣</div><div style="background-color: #fff; padding: 10px;">No feedback found for this problem.</div></div>';
            exit();
        }
    } else {
        // Handle the case where prob_id is not present in the URL
        echo '<div class="no-feedback"><div class="emoji">😞</div><div style="background-color: #fff; padding: 10px;">Problem ID not provided.</div></div>';
        exit();
    }
    ?>

    <!-- Display feedback details using session variables -->
    <div class="container">

        <h1>Feedback Details</h1>
        <div class="fb-in">
            <label>Feedback ID:</label>
            <p><?php echo $_SESSION['fb_id']; ?></p>
        </div>
        <div class="fb-in">
            <label>Problem ID:</label>
            <p><?php echo $_SESSION['fb_prob_id']; ?></p>
        </div>

        <div class="fb-in">
            <label>Problem Description:</label>
            <p><?php echo $_SESSION['fb_prob_desc']; ?></p>
        </div>

        <div class="fb-in">
            <label>Problem Location:</label>
            <p><?php echo $_SESSION['fb_prob_loc']; ?></p>
        </div>

        <div class="fb-in">
            <label>Problem Date:</label>
            <p><?php echo $_SESSION['fb_prob_date']; ?></p>
        </div>

        <div class="fb-in">
            <label>Org User Name:</label>
            <p><?php echo $_SESSION['fb_user_name']; ?></p>
        </div>
        <div class="fb-in">
            <label>Organization Name:</label>
            <p><?php echo $_SESSION['fb_org_name']; ?></p>
        </div>
        <div class="fb-in">
            <label>Public Feedback:</label>
            <p><?php echo $_SESSION['fb_prob_fbdesc']; ?></p>
        </div>

        <!-- <div class="fb-in">
            <label>Problem Type:</label>
            <p><?php echo $_SESSION['fb_prob_type']; ?></p>
        </div> -->

        <div class="fb-in">
            <label>Feedback Rate:</label>
            <p><?php echo $_SESSION['fb_rate']; ?></p>
        </div>
    </div>
</body>

</html>