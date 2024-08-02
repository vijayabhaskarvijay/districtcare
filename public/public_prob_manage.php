<?php
session_start();

// Check if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to the login page
    header("Location: main_login.php");
    exit();
}

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

// Get the logged-in user's ID from the session
$userId = $_SESSION['user_id'];

// Fetch the number of reports done by the user
try {
    $stmt = $conn->prepare("SELECT COUNT(*) AS report_count FROM prob_details WHERE prob_user_id = :userId");
    $stmt->bindParam(':userId', $userId);
    $stmt->execute();
    $reportCount = $stmt->fetch(PDO::FETCH_ASSOC)['report_count'];
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Fetch the number of completed problems for the user
try {
    $stmt = $conn->prepare("SELECT COUNT(*) AS completed_count FROM prob_details WHERE prob_user_id = :userId AND problem_status = 'Completed'");
    $stmt->bindParam(':userId', $userId);
    $stmt->execute();
    $completedCount = $stmt->fetch(PDO::FETCH_ASSOC)['completed_count'];
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// -----------------------------
// Pagination configuration
$problemsPerPage = 4; // Change this to 5 for 5 problems per page
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $problemsPerPage; // Calculate the offset based on page number

// Fetch problems related to the logged-in user with pagination
try {
    $stmt = $conn->prepare("SELECT * FROM prob_details WHERE prob_user_id = :userId ORDER BY prob_time DESC LIMIT $offset, $problemsPerPage");
    $stmt->bindParam(':userId', $userId);
    $stmt->execute();
    $problems = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Fetch the total count of problems related to the user for pagination
try {
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM prob_details WHERE prob_user_id = :userId");
    $stmt->bindParam(':userId', $userId);
    $stmt->execute();
    $rowCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    $totalPages = ceil($rowCount / $problemsPerPage);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
// -----------------------------

?>

<!DOCTYPE html>
<html>

<head>
    <title>Public Problem Management</title>
    <link rel="icon" href="../images/urbanlink-logo.png" type="image/icon type">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://kit.fontawesome.com/4c43584236.js" crossorigin="anonymous"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Signika&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
        }

        .tag:hover,
        .description:hover {
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.8);
            transform: translateY(-5px);
            transition: 0.2s ease-in;
            cursor: pointer;

        }

        .feedback-link,
        .track-link {
            background-color: #000080;
            color: white;
            padding: 5px;
            border-radius: 2px;
            margin-right: 10px;
        }

        .feedback-link:hover,
        .track-link:hover {
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.8);
            transform: translateY(-5px);
            transition: 0.2s ease-in-out;
            cursor: pointer;
            background-color: #2ecc71;
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

        body {
            font-family: 'Signika', sans-serif;
            /* background: linear-gradient(to bottom right, #f2f2f2, #ffffff); */
            background-attachment: fixed;
            background-image: url("../images/8685784_3915274.jpg");
            background-size: cover;
            background-position: center;
            margin: 0;
            padding: 0;
        }

        .container {
            display: block;
            width: 90%;
            margin: 30px auto;
            background-color: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 1);
            overflow-y: auto;
            height: 640px;
            position: relative;
            top: 0px;
            border: 1px solid rgba(255, 255, 255, 0.8);
            left: 60px;
            scrollbar-width: thin;
            /* For Firefox */
            scrollbar-color: #a0a0a0 #f0f0f0;

        }

        /* For Firefox */
        /* Webkit (Chrome, Safari) styling */
        ::-webkit-scrollbar {
            width: 10px;
            border-radius: 50%;
            transform: translateX(-10px);
        }

        ::-webkit-scrollbar-thumb {
            background-color: #000080;
            border-radius: 5px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background-color: #808080;
        }

        ::-webkit-scrollbar-track {
            background-color: transparent;
        }

        /* PAGINATION CSS STARTS */
        .pagination {
            text-align: center;
            align-items: center;
            justify-content: center;
            border: 2px solid black;
            background-color: #000000;
            border-radius: 5px;
            position: relative;
            top: -20px;
            width: fit-content;
            left: 750px;
        }

        .page-link {
            display: inline-block;
            margin: 5px;
            padding: 5px 10px;
            color: #333;
            background-color: #f1f1f1;
            border: 1px solid #ccc;
            border-radius: 3px;
            transition: transform 0.3s, background-color 0.3s;
        }

        .page-link:hover {
            color: white;
            background-color: #000080;
            border-radius: 3px;
            transform: translateZ(10px);
        }


        /* PAGINATION CSS ENDS */



        /* CSS for the gadgets */
        .gadgets {
            display: flex;
            margin-bottom: 20px;
            /* flex-direction: column; */
        }

        .gadget {
            background-color: #ffffff;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 1);
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            width: 200px;
            margin: 20px;
            position: relative;
            top: -60px;
            /* left: 180px; */

        }

        .gadget-title {
            font-weight: bold;
            color: #333333;
        }

        .gadget-value {
            font-size: 24px;
            color: #3498db;
        }


        .endmessage {
            color: red;
            border: 2px solid #2ecc71;
            pad: 10px;
            font-weight: 700;
            background: #000000;
            position: relative;
            left: 300px;
            top: -200px;
        }

        h2 {
            text-align: center;
            color: #333333;
            margin-bottom: 20px;
            position: relative;
            top: -30px;
        }

        .problem {
            border: 2px solid #000080;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 15px;
            position: relative;
            top: -50px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.8);
        }

        .problem .tags {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }

        .filter-sort {
            display: flex;
            /* justify-content: space-between; */
            margin-bottom: 20px;
            position: relative;
            top: -10px;
            left: -20px;
            z-index: 1;
            flex-direction: row-reverse;

        }

        .filter-sort select {
            padding: 5px;
            border-radius: 3px;
        }

        .problem .tag {
            padding: 5px 10px;
            font-size: 12px;
            color: #ffffff;
            border-radius: 5px;
        }

        .problem .title {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .problem .description {
            color: white;
            margin-bottom: 10px;
            background-color: #777777;
            padding: 20px;
            border-radius: 5px;
            text-transform: capitalize;
            font-size: 20px;
        }

        .problem .actions {
            text-align: right;
            position: relative;
            display: flex;
        }

        .problem .actions button {
            padding: 5px 10px;
            background-color: #e74c3c;
            color: #ffffff;
            border: none;
            cursor: pointer;
            border-radius: 3px;
            margin: 5px;
        }

        .problem .actions button:hover {
            background-color: #000080;
            transition: 0.3s ease-in-out;
        }

        @media only screen and (min-width:901px) and (max-width: 1200px) {
            .container {
                width: 95%;
                position: relative;
                top: 20px;
                left: 0%;
            }

            .pagination {
                position: relative;
                top: 10px;
                left: 10%;
                width: 70%;
            }
        }

        @media only screen and (min-width:601px) and (max-width: 900px) {
            .container {
                width: 95%;
                position: relative;
                top: 20px;
                left: 0%;
            }

            .pagination {
                position: relative;
                top: 10px;
                left: 12%;
                width: 70%;
            }
        }

        @media only screen and (min-width:300px) and (max-width: 600px) {
            body {
                display: flex;
                flex-direction: column;
            }

            .container {
                width: 95%;
                margin: 30px auto;
                position: relative;
                top: 20px;
                left: -1%;
            }

            .pagination {
                position: relative;
                top: 10px;
                left: 10%;
                width: 70%;
            }
        }
    </style>
</head>

<body>
    <div class="back-button">
        <a href="public_user_landing.php" class="go-back">⬅️ GO BACK</a>
    </div>
    <!-- Display the gadgets at the top of the page -->
    <div class="container">
        <h2>Public Problem Management</h2>
        <div class="gadgets">
            <div class="gadget">
                <div class="gadget-title">Total Reports</div>
                <div class="gadget-value"><?php echo $reportCount; ?></div>
            </div>
            <div class="gadget">
                <div class="gadget-title">Completed Problems</div>
                <div class="gadget-value"><?php echo $completedCount; ?></div>
            </div>
        </div>

        <?php foreach ($problems as $problem) : ?>
            <div class="problem">
                <div class="tags">
                    <div class="tag" style="background-color: #3498db;"><?php echo $problem['prob_user_name']; ?></div>
                    <div class="tag" style="background-color: #2ecc71;"><?php echo $problem['prob_user_phone']; ?></div>
                    <div class="tag" style="background-color: #033e3e;"><?php echo $problem['prob_user_loc']; ?></div>
                    <div class="tag" style="background-color: #ff00ff;"><?php echo $problem['prob_user_mainarea']; ?></div>
                    <div class="tag" style="background-color: orange;"><?php echo $problem['prob_dept']; ?></div>
                    <div class="tag" style="background-color: #000000;"><?php echo $problem['prob_type']; ?></div>
                    <div class="tag" style="background-color: #66cf;"><?php echo $problem['prob_date']; ?></div>
                </div>
                <div class="description">
                    <!-- Display the problem description fetched from the prob_Desc column -->
                    <?php echo nl2br($problem['prob_desc']); ?>
                </div>
                <div class="actions">
                    <!-- Conditional display of comment icons -->
                    <?php if ($problem['processed_by'] === 'NGO') : ?>
                        <!-- Display the comment icon for NGO feedback -->
                        <?php
                        $_SESSION['ngo_feedback_problem_id'] = $problem['prob_id'];
                        $_SESSION['ngo_feedback_user_name'] = $problem['prob_user_name'];
                        $_SESSION['ngo_feedback_prob_type'] = $problem['prob_type'];
                        $_SESSION['ngo_feedback_prob_loc'] = $problem['prob_loc'];
                        $_SESSION['ngo_feedback_prob_date'] = $problem['prob_date'];
                        $_SESSION['ngo_feedback_prob_dept'] = $problem['prob_dept'];
                        $_SESSION['ngo_feedback_prob_desc'] = $problem['prob_desc'];
                        ?>
                        <a href="public_ngo_feedback_collect.php?problem_id=<?php echo $problem['prob_id']; ?>" class="feedback-link"><i class="fa-solid fa-comment"></i> NGO Feedback</a>
                        <!-- <a href="public_ngo_feedback_collect.php" class="feedback-link">
                            <i class="fa-solid fa-comment"></i> NGO Feedback
                        </a> -->
                    <?php elseif ($problem['processed_by'] === 'Government') : ?>
                        <!-- Display the comment icon for Government feedback -->
                        <?php
                        $_SESSION['feedback_problem_id'] = $problem['prob_id'];
                        $_SESSION['feedback_user_name'] = $problem['prob_user_name'];
                        $_SESSION['feedback_prob_type'] = $problem['prob_type'];
                        $_SESSION['feedback_prob_loc'] = $problem['prob_loc'];
                        $_SESSION['feedback_prob_date'] = $problem['prob_date'];
                        $_SESSION['feedback_prob_dept'] = $problem['prob_dept'];
                        $_SESSION['feedback_prob_desc'] = $problem['prob_desc'];
                        ?>
                        <a href="public_feedback.php?problem_id=<?php echo $problem['prob_id']; ?>" class="feedback-link"><i class="fa-solid fa-comment"></i> Feedback</a>
                        <!-- <a href="public_feedback.php" class="feedback-link">
                            <i class="fa-solid fa-comment"></i> Feedback
                        </a> -->
                    <?php endif; ?>

                    <!-- Set the problem ID and problem status in session variables -->
                    <?php
                    $_SESSION['tracked_problem_id'] = $problem['prob_id'];
                    $_SESSION['tracked_problem_status'] = $problem['problem_status'];
                    $_SESSION['ngo_tracked_problem_status'] = $problem['ngo_problem_status'];
                    $_SESSION['tracked_user_name'] = $problem['prob_user_name'];
                    $_SESSION['tracked_prob_type'] = $problem['prob_type'];
                    $_SESSION['tracked_prob_loc'] = $problem['prob_loc'];
                    $_SESSION['tracked_prob_date'] = $problem['prob_date'];
                    $_SESSION['tracked_prob_dept'] = $problem['prob_dept'];
                    $_SESSION['tracked_prob_usermainarea'] = $problem['prob_user_mainarea'];
                    $_SESSION['tracked_prob_userphone'] = $problem['prob_user_phone'];
                    $_SESSION['tracked_prob_userloc'] = $problem['prob_user_loc'];
                    $_SESSION['tracked_prob_desc'] = $problem['prob_desc'];
                    $_SESSION['tracked_prob_processby'] = $problem['processed_by'];
                    ?>
                    <!-- Redirect to prob_status_track.php -->
                    <!-- <a href="prob_status_track.php" class="track-link">
                        <i class="fas fa-route"></i> Track
                    </a> -->
                    <!-- Add a link to prob_status_track.php with the problem ID as a parameter -->
                    <a href="prob_status_track.php?problem_id=<?php echo $problem['prob_id']; ?>" class="track-link"><i class="fas fa-route"></i> Track</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="pagination">
        <?php
        $maxPageLinks = 5; // Number of pagination links to show

        // Calculate the range of pagination links to display
        $startPage = max(1, $page - floor($maxPageLinks / 2));
        $endPage = min($startPage + $maxPageLinks - 1, $totalPages);

        // Show "Previous" button
        if ($page > 1) {
            echo "<a href='?page=" . ($page - 1) . "' class='page-link'>&lt; Prev</a>";
        }

        // Display pagination links
        for ($i = $startPage; $i <= $endPage; $i++) {
            echo "<a href='?page=$i' class='page-link";
            if ($i === $page) {
                echo " current-page";
            }
            echo "'>$i</a>";
        }

        // Show "Next" button
        if ($page < $totalPages) {
            echo "<a href='?page=" . ($page + 1) . "' class='page-link'>Next &gt;</a>";
        }
        ?>
    </div>


    <script src="https://kit.fontawesome.com/your-font-awesome-kit.js" crossorigin="anonymous"></script>

    <script>
        document.getElementById('export-button').addEventListener('click', function() {
            // Send AJAX request to generate PDF
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'generate_pdf.php', true);
            xhr.responseType = 'blob';
            xhr.onload = function() {
                if (xhr.status === 200) {
                    var blob = xhr.response;
                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = 'problem_report.pdf';
                    link.click();
                }
            };
            xhr.send();
        });
    </script>

</body>


</html>