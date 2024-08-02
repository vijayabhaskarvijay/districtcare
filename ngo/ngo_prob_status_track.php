<?php
session_start();

if (!isset($_SESSION['ngo_username'])) {
header("Location: ngo_login.php");
exit();
}

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
    $statustrack_sql = "SELECT * FROM prob_details WHERE prob_id = ?";
    $statustrack_stmt = $conn->prepare($statustrack_sql);
    $statustrack_stmt->bind_param("s", $prob_id);
    $statustrack_stmt->execute();
    $statustrack_result = $statustrack_stmt->get_result();

    // Fetch and store feedback details in session variables
    if ($statustrack_result->num_rows > 0) {
        $statustrack_data = $statustrack_result->fetch_assoc();
        // $_SESSION['govn_tracked_fb_id'] = $statustrack_data['fb_id'];
        $_SESSION['govn_tracked_problem_id'] = $statustrack_data['prob_id'];
        $_SESSION['govn_tracked_user_name'] = $statustrack_data['prob_user_name'];
        $_SESSION['govn_tracked_prob_dept'] = $statustrack_data['prob_dept'];
        $_SESSION['govn_tracked_prob_type'] = $statustrack_data['prob_type'];
        $_SESSION['govn_tracked_prob_desc'] = $statustrack_data['prob_desc'];
        $_SESSION['govn_tracked_prob_loc'] = $statustrack_data['prob_loc'];
        $_SESSION['govn_tracked_prob_date'] = $statustrack_data['prob_date'];
        $_SESSION['govn_tracked_problem_status'] = $statustrack_data['problem_status'];
        $_SESSION['govn_ngo_tracked_problem_status'] = $statustrack_data['ngo_problem_status'];
        $_SESSION['govn_tracked_prob_usermainarea'] = $statustrack_data['prob_user_mainarea'];
        $_SESSION['govn_tracked_prob_userphone'] = $statustrack_data['prob_user_phone'];
        $_SESSION['govn_tracked_prob_userloc'] = $statustrack_data['prob_user_loc'];
        $_SESSION['govn_tracked_prob_processby'] = $statustrack_data['processed_by'];

        // Close the database connection
        $conn->close();
    } else {
        // Handle the case where no feedback is found
        echo '<div class="no-feedback"><div class="emoji">😶‍🌫️🫣</div><div style="background-color: #fff; padding: 10px;">No Status found for this problem.</div></div>';
        exit();
    }
} else {
    // Handle the case where prob_id is not present in the URL
    echo '<div class="no-feedback"><div class="emoji">😞</div><div style="background-color: #fff; padding: 10px;">Problem ID not provided.</div></div>';
    exit();
}

// Get the tracked problem details from session variables
$trackedProblemStatus = $_SESSION['govn_tracked_problem_status'];
$ngo_trackedProblemStatus = $_SESSION['govn_ngo_tracked_problem_status'];
$trackedProblemId = $_SESSION['govn_tracked_problem_id'];
$trackedProblemDept = $_SESSION['govn_tracked_prob_dept'];

// Define an array to map problem statuses to their corresponding step
$problemStatusSteps = [
    'NEW' => 0,
    'Read' => 1,
    'Preparing' => 2,
    'Working' => 3,
    'Completed' => 4,
];
// Define an array to map problem statuses to their corresponding step
$ngo_problemStatusSteps = [
    'NEW' => 0,
    'Read' => 1,
    'Preparing' => 2,
    'Working' => 3,
    'Completed' => 4,
];

$statusDescriptions = [
    'NEW' => 'New problem report received and awaiting review.',
    'Read' => 'Problem report has been read and is under consideration.',
    'Preparing' => 'Preparations are being made to address the reported problem.',
    'Working' => 'Efforts are underway to resolve the reported problem.',
    'Completed' => 'The reported problem has been successfully resolved.',
];

// Mock user details, replace with actual user details
$userDetails = [
    'name' => $_SESSION['govn_tracked_user_name'],
    'phone' => $_SESSION['govn_tracked_prob_userphone'], // Replace with actual phone number
    'location' => $_SESSION['govn_tracked_prob_userloc'], // Replace with actual location
];

?>
<!DOCTYPE html>
<html>

<head>
    <title>Problem Status Tracking</title>
    <link rel="icon" href="../images/urbanlink-logo.png" type="image/icon type">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Arial', 'sans-serif';
            font-weight: 400;
            background: linear-gradient(to bottom, #3498db, #1abc9c);
            /* background-color: #ffffff; */
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
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

        /* GO BACK  START*/

        .go-back {
            padding: 10px;
            text-align: center;
            background-color: orange;
            cursor: pointer;
            color: white;
            text-decoration: none;
            position: relative;
            top: -10px;
            left: -650px;
            border-radius: 10px;
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


        /* BO BACK ENDS */
        /* PROBLEM STATUS TRACKER PHP CODE STARTS */
        <?php foreach ($problemStatusSteps as $status => $step) { ?><?php
                                                                    $stepColor = ($step <= $problemStatusSteps[$trackedProblemStatus]) ? '#228B22' : '#ccc';
                                                                    $barColor = ($step <= $problemStatusSteps[$trackedProblemStatus]) ? '#E2F516' : '#ccc';
                                                                    ?>.step.step-<?php echo $step; ?> {
            background-color: <?php echo $stepColor; ?>;
        }

        .bar.bar-<?php echo $step; ?> {
            background-color: <?php echo $barColor; ?>;
        }

        <?php } ?><?php foreach ($ngo_problemStatusSteps as $ngo_status => $ngo_step) { ?><?php
                                                                                            $ngo_stepColor = ($ngo_step <= $ngo_problemStatusSteps[$ngo_trackedProblemStatus]) ? '#95db' : '#ccc';
                                                                                            $ngo_barColor = ($ngo_step <= $ngo_problemStatusSteps[$ngo_trackedProblemStatus]) ? '#95db' : '#ccc';
                                                                                            ?>.ngo_step.ngo_step-<?php echo $ngo_step; ?> {
            background-color: <?php echo $ngo_stepColor; ?>;
        }

        .ngo_bar.ngo_bar-<?php echo $ngo_step; ?> {
            background-color: <?php echo $ngo_barColor; ?>;
        }

        <?php } ?>
        /* PROBLEM STATUS TRACKER PHP CODE ENDS */

        .status-container {
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 20px;
        }

        .details-box {
            background-color: #f2f2f2;
            border: 2px solid #3498db;
            border-radius: 20px;
            padding: 10px;
            margin-bottom: 30px;
            position: relative;
            left: 250px;
            width: 60%;
            text-align: center;
            box-shadow: rgba(0, 0, 0, 0.8) 0px 0px 28px, rgba(0, 0, 0, 0.22) 0px 10px 10px;
            /* width: 500px; */
        }

        .step {
            width: 100px;
            height: 100px;
            background-color: #ffffff;
            border: 2px solid #228B22;
            border-radius: 10%;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            text-align: center;
        }

        .govn_track {
            position: relative;
            left: -50px;
        }

        .ngo_track {
            position: relative;
            left: 0px;
        }

        .govn_track_p,
        .ngo_track_p {
            color: #800000;
            font-style: italic;
            text-transform: uppercase;
            font-size: 20px;
            margin-right: 10px;
        }

        .govn_track_p:hover,
        .ngo_track_p:hover {
            transform: scale(1.1) rotateX(360deg);
            box-shadow: 0 0 40px rgba(0, 0, 0, 0.7);
            color: white;
            cursor: pointer;
        }

        .ngo_step {
            width: 100px;
            height: 100px;
            background-color: #ffffff;
            border: 2px solid #95db;
            border-radius: 10%;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            text-align: center;
            margin-top: 20px;
        }

        .step:hover {
            transform: scale(1.1) rotateX(360deg);
            box-shadow: 0 0 40px rgba(0, 0, 0, 0.7);
            color: red;
        }

        .ngo_step:hover {
            transform: scale(1.1) rotateX(360deg);
            box-shadow: 0 0 40px rgba(0, 0, 0, 0.7);
            color: red;
        }

        .bar {
            width: 120px;
            height: 5px;
            background-color: #3498db;
        }

        .ngo_bar {
            width: 120px;
            height: 7px;
            position: relative;
            top: 8px;
            background-color: #3498db;
        }

        .step-title {
            text-align: center;
            margin-top: 8px;
            position: relative;
            top: -5px;
        }

        .ngo_step-title {
            text-align: center;
            margin-top: 8px;
            position: relative;
            top: -5px;
        }

        /* Tooltip container */
        .tooltip {
            position: relative;
            display: inline-block;
            cursor: pointer;
        }

        /* Tooltip text */
        .tooltiptext {
            visibility: hidden;
            width: 200px;
            background-color: #333;
            color: #fff;
            text-align: center;
            border-radius: 5px;
            padding: 5px;
            position: absolute;
            z-index: 1;
            bottom: 125%;
            left: 50%;
            transform: translateX(-50%);
            opacity: 0;
            transition: opacity 0.3s;
        }

        .tooltip:hover .tooltiptext {
            visibility: visible;
            opacity: 1;
        }

        @media only screen and (min-width:801px) and (max-width:1100px) {
            .details-box {
                width: 700%;
                top: 50;
                margin-left: -345px;
                margin-right: -100px;
            }

            .status-container {
                display: inline-block;
                margin-left: 38%;
            }

            .bar {
                width: 5px;
                height: 120px;
                margin-left: 48%;
            }

            .ngo_bar {
                width: 5px;
                height: 120px;
                margin-left: 48%;
            }
        }

        @media only screen and (min-width:701px) and (max-width:800px) {

            .details-box {
                width: 250%;
                top: 50;
                margin-left: -150px;
            }

            .status-container {
                display: inline-block;
                margin-left: 38%;
            }

            .bar {
                width: 5px;
                height: 120px;
                margin-left: 48%;
            }

            .ngo_bar {
                width: 5px;
                height: 120px;
                margin-left: 48%;
            }
        }

        @media only screen and (min-width:601px) and (max-width:700px) {
            .details-box {
                width: 100%;
            }

            .status-container {
                display: inline-block;
                margin-left: 38%;
            }

            .bar {
                width: 5px;
                height: 120px;
                margin-left: 48%;
            }

            .ngo_bar {
                width: 5px;
                height: 120px;
                margin-left: 48%;
            }
        }

        @media only screen and (min-width:300px) and (max-width:600px) {
            .details-box {
                width: 85%;
                margin-left: auto;
                margin-right: 5%;
                margin-top: 10%
            }

            .status-container {
                display: inline-block;
                margin-left: 38%;
            }

            .bar {
                width: 5px;
                height: 120px;
                margin-left: 48%;
            }

            .ngo_bar {
                width: 5px;
                height: 120px;
                margin-left: 48%;
            }

            .go-back {
                padding: 10px;
                text-align: center;
                background-color: orange;
                cursor: pointer;
                color: white;
                text-decoration: none;
                position: relative;
                top: -20px;
                right: -450px;
            }


            .back-button {
                position: relative;
                bottom: 180px;
                margin-left: 180px;
            }


        }
    </style>
</head>

<body>
    <div class="back-button">
        <a href="ngo_probpage.php" class="go-back">⬅️ GO BACK</a>
    </div>
    <div class="container">
        <div class="details-box">
            <div class="details1">
                <h3>Problem Details</h3>
                <p><strong>Problem ID:</strong> <?php echo $trackedProblemId; ?></p>
                <p><strong>Government Updated Problem Status:</strong> <?php echo $trackedProblemStatus; ?></p>
                <p><strong>NGO Updated Problem Status:</strong> <?php echo $ngo_trackedProblemStatus; ?></p>
                <p><strong>Related Department:</strong> <?php echo $trackedProblemDept ?></p>
                <p><strong>Problem Type:</strong> <?php echo $_SESSION['govn_tracked_prob_type']; ?></p>
                <p><strong>Problem Location:</strong> <?php echo $_SESSION['govn_tracked_prob_loc']; ?></p>
                <p><strong>Problem Date:</strong> <?php echo $_SESSION['govn_tracked_prob_date']; ?></p>
                <p><strong>Problem Desc:</strong> <?php echo $_SESSION['govn_tracked_prob_desc']; ?></p>
                <p><strong>Processing By:</strong> <?php echo $_SESSION['govn_tracked_prob_processby']; ?></p>
            </div>
        </div>

        <div class="status-container govn_track">
            <p class="govn_track_p">Government Track:</p>
            <?php foreach ($problemStatusSteps as $status => $step) : ?>
                <div class="tooltip">
                    <div class="step step-<?php echo $step; ?>">
                        <div class="step-title"><?php echo $status; ?></div>
                    </div>
                    <div class="tooltiptext">
                        <?php echo $statusDescriptions[$status]; ?>
                    </div>
                </div>
                <?php if ($step < 4) : ?>
                    <div class="bar bar-<?php echo $step; ?>"></div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <div class="status-container ngo_track">
            <p class="ngo_track_p">NGO Track:</p>
            <?php foreach ($ngo_problemStatusSteps as $ngo_status => $ngo_step) : ?>
                <div class="tooltip">
                    <div class="ngo_step ngo_step-<?php echo $ngo_step; ?>">
                        <div class="ngo_step-title"><?php echo $ngo_status; ?></div>
                    </div>
                    <div class="tooltiptext">
                        <?php echo $statusDescriptions[$ngo_status]; ?>
                    </div>
                </div>
                <?php if ($ngo_step < 4) : ?>
                    <div class="ngo_bar ngo_bar-<?php echo $ngo_step; ?>"></div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <div class="details-box">
            <div class="details2">
                <h3>User Details</h3>
                <p><strong>Name:</strong> <?php echo $userDetails['name']; ?></p>
                <p><strong>Phone:</strong> <?php echo $userDetails['phone']; ?></p>
                <p><strong>Location:</strong> <?php echo $userDetails['location']; ?></p>
            </div>
        </div>
    </div>
</body>

</html>