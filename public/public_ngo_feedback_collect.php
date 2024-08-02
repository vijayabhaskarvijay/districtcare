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


// Check if the problem_id parameter is set in the URL
if (isset($_GET['problem_id'])) {
    $problemId = $_GET['problem_id'];
    try {
        $stmt = $conn->prepare("SELECT * FROM prob_details WHERE prob_id = :problemId");
        $stmt->bindParam(':problemId', $problemId);
        $stmt->execute();
        $problemDetails = $stmt->fetch(PDO::FETCH_ASSOC);
        // Store the problem details in session variables
        $_SESSION['ngo_feedback_problem_id'] = $problemDetails['prob_id'];
        $_SESSION['ngo_feedback_user_name'] = $problemDetails['prob_user_name'];
        $_SESSION['ngo_tracked_problem_status'] = $problemDetails['ngo_problem_status'];
        $_SESSION['ngo_tracked_problem_dept'] = $problemDetails['prob_dept'];
        $_SESSION['ngo_feedback_prob_loc'] = $problemDetails['prob_loc'];
        $_SESSION['ngo_feedback_prob_date'] = $problemDetails['prob_date'];
        $_SESSION['ngo_feedback_prob_desc'] = $problemDetails['prob_desc'];
        // $_SESSION['tracked_problem_status'] = $problemDetails['problem_status'];
        // $_SESSION['tracked_prob_processby'] = $problemDetails['processed_by'];
        // $_SESSION['tracked_prob_type'] = $problemDetails['prob_type'];

    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
} else {
    // Redirect to an appropriate page if problem_id is not provided
    header("Location: some_error_page.php");
    exit();
}


// Fetch NGO names from ngo_details table
try {
    $stmt = $conn->prepare("SELECT ngo_org_name FROM ngo_details");
    $stmt->execute();
    $ngoNames = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

function generateUniqueID()
{
    $prefix = "PNGOFB";
    $uniquePart = uniqid(); // Generates a unique identifier based on the current time
    return $prefix . $uniquePart;
}
// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $probId = $_POST['prob_id'];
    $username = $_POST['username'];
    $orgName = $_POST['org_name'];
    $probDesc = $_POST['prob_desc'];
    $probLoc = $_POST['prob_loc'];
    $probDate = $_POST['prob_date'];
    $fbDesc = $_POST['fb_desc'];
    $fbRate = $_POST['fb_rate'];

    // Calculate and update the average star rating in ngo_details table
    $updateRatingQuery = "UPDATE ngo_details SET ngo_star_rating = ((ngo_star_rating * ngo_feedback_count) + :fbRate) / (ngo_feedback_count + 1), ngo_feedback_count = ngo_feedback_count + 1 WHERE ngo_org_name = :orgName";
    try {
        $stmt = $conn->prepare($updateRatingQuery);
        $stmt->bindParam(':fbRate', $fbRate);
        $stmt->bindParam(':orgName', $orgName);
        $stmt->execute();
    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }

    // Insert feedback details into ngo_feedback_details table
    $insertFeedbackQuery = "INSERT INTO ngo_feedback_details (ngo_fb_id,ngo_fb_prob_id, ngo_fb_username, ngo_fb_orgname, ngo_fb_probdesc, ngo_fb_probloc, ngo_fb_probdate, ngo_fb_fbdesc, ngo_fb_rate) VALUES (:ngo_fb_id,:probId, :username, :orgName, :probDesc, :probLoc, :probDate, :fbDesc, :fbRate)";
    try {
        $stmt = $conn->prepare($insertFeedbackQuery);
        $generatedID = generateUniqueID();
        $stmt->bindParam(':ngo_fb_id', $generatedID);
        $stmt->bindParam(':probId', $probId);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':orgName', $orgName);
        $stmt->bindParam(':probDesc', $probDesc);
        $stmt->bindParam(':probLoc', $probLoc);
        $stmt->bindParam(':probDate', $probDate);
        $stmt->bindParam(':fbDesc', $fbDesc);
        $stmt->bindParam(':fbRate', $fbRate);
        $stmt->execute();
        // Display success message with NGO feedback ID
        echo '<div class="success-message">Feedback submitted successfully. Feedback ID: ' . $generatedID . '</div>';

        // Redirect to public_prob_manage.php after 3 seconds
        echo '<script>
                setTimeout(function(){
                    window.location.href = "public_prob_manage.php";
                }, 3000);
            </script>';
    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
    // Redirect to a success page or perform other actions
    // header("Location: success.php");
    // exit();
}
?>

<!DOCTYPE html>
<html>

<head>

    <title>Public NGO Feedback Collection</title>
    <link rel="icon" href="../images/urbanlink-logo.png" type="image/icon type">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <style>
        /* Reset some default styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* New color scheme */
        :root {
            --primary-color: #3498db;
            --secondary-color: #2ecc71;
            --background-color: #f2f2f2;
            --text-color: #333;
        }

        body {
            font-family: Arial, sans-serif;
            background-image: url("../images/climpek.png"), linear-gradient(to right top, rgba(255, 255, 255, 0.8), rgba(255, 255, 255, 0), rgba(255, 255, 255, 0.8));
            color: var(--text-color);
        }

        .toast {
            position: fixed;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            background-color: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            visibility: hidden;
            opacity: 0;
            transition: visibility 0s, opacity 0.3s linear;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #685f5f;
            border-radius: 10px;
            box-shadow: 0px 2px 6px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: var(--primary-color);
        }

        form {
            display: grid;
            gap: 10px;
        }

        label {
            font-weight: bold;
        }

        input[type="text"],
        input[type="date"],
        select,
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        button {
            background-color: var(--primary-color);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
            position: relative;
            /* top: 250px;
            z-index: 1;
            left: 250px; */
        }

        button:hover {
            background-color: var(--secondary-color);
        }

        .rating-section {
            margin-top: 20px;
            display: flex;
            flex-direction: column;
        }

        .rating-stars {
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            color: gray;
        }

        .rating-stars input[type="radio"] {
            display: none;
        }

        .rating-stars label {
            cursor: pointer;
            margin: 0;
            padding: 0;
            transition: color 0.3s;
        }

        /* Change the color of hovered and selected stars */
        .rating-stars label:hover,
        .rating-stars input[type="radio"]:checked~label {
            color: #f39c12;
        }

        /* Revert the color of unselected stars on hover */
        .rating-stars input[type="radio"]:not(:checked)~label:hover {
            color: #f1c40f;
        }

        /* Add this to your existing styles */
        .success-message {
            text-align: center;
            color: #2ecc71;
            /* Green color or your preferred color */
            font-size: 18px;
            margin-top: 10px;
        }
    </style>


</head>

<body>
    <div class="container">
        <div id="toast" class="toast"></div>
        <h2>Public NGO Feedback Collection</h2>
        <form method="POST">

            <label for="prob_id">Problem ID:</label>
            <input type="text" id="prob_id" name="prob_id" value="<?php echo $_SESSION['ngo_feedback_problem_id'] ?>" readonly><br>

            <label for="username">Username:</label>
            <input class="username" type="text" id="username" name="username" value="<?php echo $_SESSION['ngo_feedback_user_name'] ?>" readonly><br>

            <label for="org_name">Organization Name:</label>
            <select id="org_name" name="org_name" required>
                <?php foreach ($ngoNames as $ngoName) : ?>
                    <option value="<?php echo $ngoName; ?>"><?php echo $ngoName; ?></option>
                <?php endforeach; ?>
            </select><br>

            <label for="prob_desc">Problem Description:</label>
            <textarea class="desc" id="prob_desc" name="prob_desc" readonly><?php echo $_SESSION['ngo_feedback_prob_desc'] ?></textarea><br>

            <label for="prob_loc">Problem Location:</label>
            <input type="text" id="prob_loc" name="prob_loc" value="<?php echo $_SESSION['ngo_feedback_prob_loc'] ?>" readonly><br>

            <label for="prob_date">Problem Date:</label>
            <input class="date" type="date" id="prob_date" name="prob_date" value="<?php echo $_SESSION['ngo_feedback_prob_date'] ?>" readonly><br>

            <label for="fb_desc">Feedback Description:</label>
            <textarea id="fb_desc" name="fb_desc" required></textarea><br>

            <!-- <label for="fb_rate">Feedback Rating:</label>
            <input class="rate" type="number" id="fb_rate" name="fb_rate" min="1" max="10" required><br> -->

            <div class="rating-section">
                <label for="fb_rate">Feedback Rating:</label>
                <div class="rating-stars">
                    <input type="radio" name="fb_rate" id="star1" value="10">
                    <label for="star1">&#9733;10 </label>
                    <input type="radio" name="fb_rate" id="star2" value="9">
                    <label for="star2">&#9733;9</label>
                    <input type="radio" name="fb_rate" id="star3" value="8">
                    <label for="star3">&#9733;8</label>
                    <input type="radio" name="fb_rate" id="star4" value="7">
                    <label for="star4">&#9733;7</label>
                    <input type="radio" name="fb_rate" id="star5" value="6">
                    <label for="star5">&#9733;6</label>
                    <input type="radio" name="fb_rate" id="star6" value="5">
                    <label for="star6">&#9733;5</label>
                    <input type="radio" name="fb_rate" id="star7" value="4">
                    <label for="star7">&#9733;4</label>
                    <input type="radio" name="fb_rate" id="star8" value="3">
                    <label for="star8">&#9733;3</label>
                    <input type="radio" name="fb_rate" id="star9" value="2">
                    <label for="star9">&#9733;2</label>
                    <input type="radio" name="fb_rate" id="star10" value="1">
                    <label for="star10">&#9733;1</label>
                </div>
            </div>

            <button type="submit">Submit Feedback</button>
        </form>

    </div>

    <script>
        // Function to show a toast message
        function showToast(message) {
            var toast = document.getElementById("toast");
            toast.innerHTML = message;
            toast.style.visibility = "visible";
            setTimeout(function() {
                toast.style.visibility = "hidden";
                // Redirect to the specified page
                window.location.href = "public_prob_manage.php";
            }, 3000); // Wait for 3 seconds
        }
    </script>
</body>

</html>