<?php
// Prevent caching of page
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['ngo_username'])) {
    header("Location: ngo_login.php");
    exit();
}

// Get the session data
$orgLocation = $_SESSION['ngo_location'];
// Create a connection to the database
$conn = new mysqli("localhost", "root", "", "urbanlink");

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve the problems based on department and place
// Fetch problems based on organization location
$orgLocation = $_SESSION['ngo_location'];
$sql = "SELECT * FROM prob_details WHERE prob_user_loc = '$orgLocation'";
// $sql ="SELECT * FROM prob_details WHERE processed_by = 'NGO'";

$result = $conn->query($sql);



// Update the status of read problems
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["prob_id"]) && isset($_POST["new_status"])) {
        $prob_id = $_POST["prob_id"];
        $new_status = $_POST["new_status"];
        $update_sql = "UPDATE prob_details SET ngo_problem_status = ? WHERE prob_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ss", $new_status, $prob_id);
        if ($update_stmt->execute()) {
            // Close the update statement
            $update_stmt->close();
            // Redirect back to the same page to refresh
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            // Handle update error if needed
        }
    }
}

// Update the processby of read problems
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["prob_id"]) && isset($_POST["new_processby"])) {
        $prob_id = $_POST["prob_id"];
        $new_processby = $_POST["new_processby"];
        $update_sql = "UPDATE prob_details SET processed_by = ? WHERE prob_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ss", $new_processby, $prob_id);
        if ($update_stmt->execute()) {
            // Close the update statement
            $update_stmt->close();
            // Redirect back to the same page to refresh
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            // Handle update error if needed
        }
    }
}
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Inside the loop that displays problem data
    if (isset($_POST["update_ngo_org"])) {
        $prob_id = $_POST["prob_id"];
        $new_ngo_org_name = $_POST["ngo_org_name"];

        $update_sql = "UPDATE prob_details SET prob_ngo_orgname = ? WHERE prob_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ss", $new_ngo_org_name, $prob_id);

        if ($update_stmt->execute()) {
            // Close the update statement
            $update_stmt->close();
            // Redirect back to the same page to refresh
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            // Handle update error if needed
        }
    }
}

// ---------------------------------------------------------COUNT STARTS --------------------------------------------------------
// Fetch the total number of problems reported
try {
    $totalReportStmt = $conn->prepare("SELECT COUNT(*) AS total_report_count FROM prob_details");
    $totalReportStmt->execute();
    $totalReportResult = $totalReportStmt->get_result();
    $totalReportCount = $totalReportResult->fetch_assoc()['total_report_count'];
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// ---------------------------------------------------------COUNT ENDS --------------------------------------------------------
// Number of records to display per page
$recordsPerPage = 4;
// Get the current page number from the URL query parameter
$current_page = isset($_GET['page']) ? intval($_GET['page']) : 1;
// Create a connection to the database
$conn = new mysqli("localhost", "root", "", "urbanlink");
// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Calculate the offset for the LIMIT clause
$offset = ($current_page - 1) * $recordsPerPage;
// Retrieve the problems based on department and place with pagination
$sql = "SELECT * FROM prob_details WHERE prob_user_loc = '$orgLocation' ORDER BY prob_time DESC LIMIT $offset, $recordsPerPage";
$result = $conn->query($sql);
// Calculate total pages
$totalPages = ceil($totalReportCount / $recordsPerPage);
// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html>

<head>
    <title>NGO Problem Page</title>
    <script src="https://kit.fontawesome.com/4c43584236.js" crossorigin="anonymous"></script>
    <link rel="icon" href="../images/urbanlink-logo.png" type="image/icon type">
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@300&display=swap" rel="stylesheet">
    <style>
        body {
            background-image: url("../images/diamond-upholstery.png"), linear-gradient(to right top, #435F75, #BCC6CC, #435F75);
            /* background-color: #DADBDD; */
            /* font-family: Arial, sans-serif; */
            font-family: 'Roboto Slab', sans-serif;
        }

        .new-problem {
            background-image: linear-gradient(45deg, #673AB7, #00BCD4, #00BCD4, #673AB7);
            background-size: 200% 100%;
            animation: gradientAnimation 4s linear infinite;
            color: white;
            font-weight: 700;
        }

        @keyframes gradientAnimation {
            0% {
                background-position: 100% 0;
            }

            100% {
                background-position: -100% 0;
            }
        }

        .pagination {
            margin-top: 20px;
            text-align: center;
        }

        .pagination a,
        .pagination span {
            display: inline-block;
            cursor: pointer;
            padding: 6px 12px;
            margin: 2px;
            border: 1px solid #ddd;
            background-color: #435f75;
            border-radius: 4px;
            color: white;
            text-decoration: none;
            transition: background-color 0.3s, color 0.3s;
        }

        .pagination a:hover {
            background-color: red;
            color: white;
        }

        .pagination span.current {
            background-color: green;
            color: #fff;
        }

        .pagination a:first-child,
        .pagination a:last-child {
            margin: 0;
        }

        .pagination a.disabled {
            pointer-events: none;
            color: #bbb;
        }

        .pagination a.disabled:hover {
            background-color: transparent;
        }

        .workby {
            background: #4A90E2;
            color: white;
            padding: 5px;
            font-weight: 700;
            border-radius: 10px;
        }

        .processby {
            background: #FF5722;
            font-weight: 700;
            color: white;
        }

        .container {
            width: 95%;
            margin: 0 auto;
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.5);
            backdrop-filter: blur(10px);
            border-radius: 10px;
            box-shadow: 0 0 50px rgba(0, 0, 0, 0.3);
            position: relative;
            overflow-x: auto;
            overflow-y: auto;
            height: 500px;
            border: 2px solid black;
            scrollbar-width: thin;
            scrollbar-color: #a0a0a0 #f0f0f0;
        }

        /* For Firefox */
        /* Webkit (Chrome, Safari) styling */
        ::-webkit-scrollbar {
            width: 10px;
            /* border-radius: 50%; */
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
            display: none;
        }


        .gadgets {
            display: flex;
            flex-direction: row;
            justify-content: flex-end;
        }

        .search-container {
            position: relative;
            top: 30px;
            left: 1%;
            margin-right: 50px;
        }

        #search-input {
            padding: 10px 30px;
            border-radius: 20px;
            margin-right: 5px;
        }

        #search-button {
            padding: 5px 10px;
        }

        /* COUNT SECTION CARD DESIGN CSS STARTS */
        .count-container {
            float: right;
            position: relative;
            top: -20px;
        }

        .problem-count {
            width: 200px;
            height: 80px;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s, box-shadow 0.3s;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            font-family: Arial, sans-serif;
            background: linear-gradient(45deg,
                    rgba(255, 0, 0, 0.3),
                    rgba(0, 255, 0, 0.3),
                    rgba(0, 0, 255, 0.3));
        }

        .problem-count:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        .problem-count p {
            display: block;
            margin-top: 10px;
            font-size: 1.3rem;
            color: #435f75;
        }

        .problem-count span {
            display: block;
            font-size: 2rem;
            font-weight: bold;
            color: orange;
            position: relative;
            top: -15%;
        }

        /* COUNT SECTION CARD DESIGN CSS ENDS */

        /* Add styles for filter elements and clear button */
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

        table {
            width: 100%;
            border-collapse: collapse;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
            margin-top: 20px;
            margin-right: 10px;
            font-weight: 700;
        }

        td {
            padding: 5px;
            width: 50%;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        th {
            padding: 5px;
            width: 50%;
            text-align: left;
            cursor: grab;
            border-bottom: 1px solid #ddd;
        }

        tr:nth-child(even) {
            background-color: #BCC6CC;
        }

        .heading {
            background-color: #435f75;
        }

        /* Default table row style */
        tr {
            background-color: white;
            transition: background-color 0.3s ease;
        }

        /* Hover animation */
        tr:hover {
            background-color: rgba(0, 255, 98, 0.5);
            color: rgba(255, 255, 255, 1);
        }

        td:hover {
            cursor: pointer;
            color: #F75D59;
        }

        select {
            width: 100%;
            padding: 5px;
        }

        button {
            background-color: #F75D59;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            margin-top: 10px;
            border-radius: 10px;
        }

        button:hover {
            background-color: #45a049;
            transition: 0.2s ease;
        }

        /* ---------------MEDIA QUERIES STARTS----------------------*/
        /* Media Query for screen width 501px - 700px */
        @media (min-width: 501px) and (max-width: 700px) {
            .container {
                width: 100%;
                height: auto;
            }


            .gadgets {
                flex-direction: column;
                align-items: center;
            }

            .count-container {
                flex-direction: column;
                align-items: center;
                gap: 10px;
            }

            .problem-count {
                width: 90%;
                height: auto;
                padding: 10px;
                font-size: 0.8rem;
            }

            table {
                width: 100%;
                font-size: 0.8rem;
            }
        }

        /* Media Query for screen width 701px - 900px */
        @media (min-width: 701px) and (max-width: 900px) {
            .container {
                width: 100%;
                height: auto;
            }


            .gadgets {
                flex-direction: column;
                align-items: center;
            }

            .count-container {
                flex-direction: column;
                align-items: center;
                gap: 10px;
            }

            .problem-count {
                width: 90%;
                height: auto;
                padding: 10px;
                font-size: 0.8rem;
            }

            table {
                width: 100%;
                font-size: 0.8rem;
            }
        }

        /* Media Query for screen width 300px - 500px */
        @media (min-width: 300px) and (max-width: 500px) {
            .container {
                width: 100%;
                height: auto;
            }


            .gadgets {
                flex-direction: column;
                align-items: center;
            }

            .count-container {
                flex-direction: column;
                align-items: center;
                gap: 10px;
            }

            .problem-count {
                width: 90%;
                height: auto;
                padding: 10px;
                font-size: 0.8rem;
            }

            table {
                width: 100%;
                font-size: 0.8rem;
            }
        }

        /* ---------------MEDIA QUERIES ENDS ----------------------*/
    </style>

    <script>
        function updateProblemStatus(problemId) {
            var newStatus = document.getElementById("status-" + problemId).value;
            // Send an AJAX request to update the problem status
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "govn_probpage.php", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    // Parse the AJAX response
                    var response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        // Display success message
                        var successMessage = document.getElementById("success-message");
                        successMessage.style.display = "block";
                        // Update dropdown selection
                        document.getElementById("status-" + problemId).value = response.newStatus;
                    }
                }
            };
            xhr.send("prob_id=" + problemId + "&new_status=" + newStatus);
        }
    </script>
    <script>
        function updateProcessby(problemId) {
            var newProcessby = document.getElementById("processby-" + problemId).value;
            // Send an AJAX request to update the problem processby
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "govn_probpage.php", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    // Parse the AJAX response
                    var response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        // Display success message
                        var successMessage = document.getElementById("success-message");
                        successMessage.style.display = "block";
                        // Update dropdown selection
                        document.getElementById("processby-" + problemId).value = response.newProcessby;
                    }
                }
            };
            xhr.send("prob_id=" + problemId + "&new_processby=" + newProcessby);
        }
    </script>
</head>

<body>
    <div class="back-button">
        <a href="ngo_landing.php" class="go-back">⬅️ GO BACK</a>
    </div>
    <div class="gadgets">
        <div class="search-container">
            <input type="text" id="search-input" placeholder="Search...">
            <button id="search-button">Search</button>
            <button id="reset-button">Reset</button>
        </div>
        <div class="count-container">
            <div class="problem-count" id="total-count">
                <p> Total Reports: </p><span> <?php echo $totalReportCount; ?> </span>
            </div>
        </div>
    </div>

    <div class="container">
        <h2>NGO Problem Page</h2>
        <table>
            <!-- Table header -->
            <tr class="heading">
                <th>Problem ID</th>
                <th>User Name</th>
                <th>User Phone</th>
                <th>User Location</th>
                <th>User Main Area</th>
                <th>Problem Dept</th>
                <th>Problem Type</th>
                <th>Description</th>
                <th>Problem Location</th>
                <th>Problem Status</th>
                <th>Processing By</th>
                <th>Update Processing By</th>
                <th>Update Status</th>
                <th>Organization</th>
                <th>Track Process</th>
                <th>Request Fund to Govn</th>
                <th>Review Feedback</th>
            </tr>
            <?php
            // $ngoOrgName = "";
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $problemId = $row["prob_id"];
                    $userid = $row["prob_user_id"];
                    $username = $row["prob_user_name"];
                    $userphone = $row["prob_user_phone"];
                    $userloc = $row["prob_user_loc"];
                    $usermainarea = $row["prob_user_mainarea"];
                    $problemdept = $row["prob_dept"];
                    $problemtype = $row["prob_type"];
                    $description = $row["prob_desc"];
                    $problemloc = $row["prob_loc"];
                    $problemdate = $row["prob_date"];
                    $status = $row["problem_status"];
                    $ngo_status = $row["ngo_problem_status"];
                    $processby = $row["processed_by"];
                    $isNew = $status === 'NEW';
                    // Create a new connection to the database
                    $innerConn = new mysqli("localhost", "root", "", "urbanlink");

                    if ($innerConn->connect_error) {
                        die("Inner Connection failed: " . $innerConn->connect_error);
                    }

                    $problemId = $innerConn->real_escape_string($problemId); // Escape the value for safety

                    $ngoOrgName = ""; // Initialize $ngoOrgName

                    $ngoOrgNameQuery = "SELECT prob_ngo_orgname FROM prob_details WHERE prob_id = '$problemId'";
                    $ngoOrgNameResult = $innerConn->query($ngoOrgNameQuery);

                    if ($ngoOrgNameResult->num_rows > 0) {
                        $row = $ngoOrgNameResult->fetch_assoc();
                        $ngoOrgName = $row["prob_ngo_orgname"];
                    }

                    // Close the inner connection
                    $innerConn->close();
            ?>

                    <tr <?php if ($isNew) echo 'class="new-problem"'; ?>>
                        <td><?php echo $problemId; ?></td>
                        <td><?php echo $username; ?></td>
                        <td><?php echo $userphone ?></td>
                        <td><?php echo $userloc ?></td>
                        <td><?php echo $usermainarea ?></td>
                        <td><?php echo $problemdept ?></td>
                        <td><?php echo $problemtype ?></td>
                        <td><?php echo $description ?></td>
                        <td><?php echo $problemloc ?></td>
                        <td><?php echo $ngo_status ?></td>
                        <td class="processby"><?php echo $processby ?></td>
                        <td>
                            <?php
                            if ($processby === 'Government') {
                                echo '<span class="workby">--x--</span>';
                            } else {
                                echo '
                            <form method="post">
                                            <input type="hidden" name="prob_id" value="' . $problemId . '">
                                            <select name="new_processby">
                                                <option value="" ' . ($processby === '' ? 'selected' : '') . '>Select</option>
                                                <option value="NGO" ' . ($processby === 'NGO' ? 'selected' : '') . '>NGO</option>
                                            </select>
                                            <button type="submit" name="update">Update</button>
                            </form>';
                            }
                            ?>
                        </td>
                        <td>
                            <?php
                            if ($processby === 'Government') {
                                echo '<span class="workby">--x--</span>';
                                // echo '<span class="workby">Working by Governmenst</span>';
                            } else {
                                if ($processby === 'NGO') { ?>
                                    <form method="post">
                                        <input type="hidden" name="prob_id" value="<?php echo $problemId; ?>">
                                        <select name="new_status">
                                            <option value="" <?php if ($status === '') echo 'selected'; ?>>Select</option>
                                            <option value="Read" <?php if ($status === 'Read') echo 'selected'; ?>>Read</option>
                                            <option value="Preparing" <?php if ($status === 'Preparing') echo 'selected'; ?>>Preparing</option>
                                            <option value="Working" <?php if ($status === 'Working') echo 'selected'; ?>>Working</option>
                                            <option value="Completed" <?php if ($status === 'Completed') echo 'selected'; ?>>Completed</option>
                                        </select>
                                        <button type="submit" name="update">Update</button>
                                    </form>
                            <?php }
                            }
                            ?>
                        </td>
                        <td>
                            <?php
                            if ($processby === 'Government') {
                                echo '<span class="workby">--x--</span>';
                            } else {
                                if ($processby === 'NGO') { ?>
                                    <!-- <span id="ngo-org-input-<?php echo $problemId; ?>">
                                        <?php if (!empty($ngoOrgName)) echo $ngoOrgName; ?>
                                    </span> -->
                                    <form method="post">
                                        <input type="hidden" name="prob_id" value="<?php echo $problemId; ?>">
                                        <?php
                                        // Assuming $ngoOrgName contains the value of 'prob_ngo_orgname' for the current row
                                        if (!empty($ngoOrgName)) {
                                            echo '<span>' . $ngoOrgName . '</span>';
                                        } else {
                                            echo '<input type="text" name="ngo_org_name" placeholder="Enter NGO Org Name">';
                                            echo '<button type="submit" name="update_ngo_org">Save</button>';
                                        }
                                        ?>
                                    </form>
                            <?php }
                            }
                            ?>
                        </td>

                        <td>
                            <a href="ngo_prob_status_track.php?prob_id=<?php echo $problemId; ?>">
                                <button> <i class="fas fa-route"></i> Track </button>
                            </a>
                        </td>

                        <td>
                            <?php
                            if ($processby === 'Government') {
                                echo '<span class="workby">--x--</span>';
                            } else {
                                echo '
                            <a href="ngo_fund_request.php?prob_id=' . $problemId . '">
                                <button><i class="fa fa-money" aria-hidden="true"></i>Request</button>
                            </a>';
                            }
                            ?>
                        </td>
                        <td>
                            <?php
                            if ($processby === 'Government') {
                                echo '<span class="workby">--x--</span>';
                            } else {
                                // Assuming $processedBy contains the value of 'processed_by' for the current row
                                if ($processby === "Government") {
                                    echo '<a href="ngo_govn_fb.php?prob_id=' . $problemId . '"><button>Feedback</button></a>';
                                } elseif ($processby === "NGO") {
                                    echo '<a href="ngo_fb.php?prob_id=' . $problemId . '"><button>NGO Feedback</button></a>';
                                }
                            }
                            ?>
                        </td>
                    </tr>
            <?php
                }
            } else {
                echo '<tr><td colspan="13">No problems found.</td></tr>';
            }
            ?>

        </table>
        <div id="problem-popup-overlay" class="problem-popup-overlay"></div>
        <div id="success-message" style="display: none; color: green;">Status updated successfully.</div>
    </div>

    <div class="pagination">
        <?php
        $maxVisiblePages = 5; // Number of pagination links to show
        $halfVisible = floor($maxVisiblePages / 2);
        $startPage = max(1, $current_page - $halfVisible);
        $endPage = min($totalPages, $current_page + $halfVisible);

        if ($totalPages > $maxVisiblePages) {
            if ($current_page > 1) {
                echo '<a href="?page=1">First</a> ';
                echo '<a href="?page=' . ($current_page - 1) . '">Previous</a> ';
            }
        }

        for ($i = $startPage; $i <= $endPage; $i++) {
            if ($i == $current_page) {
                echo '<span class="current">' . $i . '</span> ';
            } else {
                echo '<a href="?page=' . $i . '">' . $i . '</a> ';
            }
        }

        if ($totalPages > $maxVisiblePages) {
            if ($current_page < $totalPages) {
                echo '<a href="?page=' . ($current_page + 1) . '">Next</a> ';
                echo '<a href="?page=' . $totalPages . '">Last</a> ';
            }
        }
        ?>
    </div>


    <!-- FILTER SCRIPTS -->

    <script>
        function toggleOrgInput() {
            var processbySelect = document.getElementById("processby-<?php echo $problemId; ?>");
            var orgInput = document.getElementById("ngo-org-input-<?php echo $problemId; ?>");

            if (processbySelect.value === 'NGO') {
                orgInput.style.display = 'inline-block';
            } else {
                orgInput.style.display = 'none';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            toggleOrgInput(); // Call the function on page load
            var processbySelect = document.getElementById("processby-<?php echo $problemId; ?>");
            processbySelect.addEventListener('change', toggleOrgInput);
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var statusElements = document.querySelectorAll('.new-problem td:nth-child(10)');

            statusElements.forEach(function(element) {
                var status = element.innerText.trim();
                if (status !== 'NEW') {
                    element.parentElement.classList.remove('new-problem');
                }
            });
        });
    </script>
    <!-- SEARCH BAR JS BELOW -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var searchButton = document.getElementById('search-button');
            var searchInput = document.getElementById('search-input');
            var resetButton = document.getElementById('reset-button');
            var tableRows = document.querySelectorAll('.container table tr:not(.heading)');

            searchButton.addEventListener('click', function() {
                var filterValue = searchInput.value.toLowerCase();
                var tableRows = document.querySelectorAll('.container table tr:not(.heading)');

                tableRows.forEach(function(row) {
                    var cells = row.getElementsByTagName('td');
                    var matchFound = false;

                    for (var i = 0; i < cells.length; i++) {
                        var cellText = cells[i].textContent.toLowerCase();
                        if (cellText.indexOf(filterValue) > -1) {
                            matchFound = true;
                            break;
                        }
                    }

                    if (matchFound) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });

            resetButton.addEventListener('click', function() {
                searchInput.value = '';
                tableRows.forEach(function(row) {
                    row.style.display = '';
                });
            });
        });
    </script>
</body>

</html>