<?php
// Prevent caching of page
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

// Start the session
session_start();

// Check if session variables are set
if (!isset($_SESSION['sv_gvn_staffloc']) || !isset($_SESSION['sv_gvn_staffdept'])) {
    // Redirect to the login page
    header("Location: govn_login.php");
    exit();
}

// Get the session data
$govn_staff_dept = $_SESSION['sv_gvn_staffdept'];
$govn_staff_loc = $_SESSION['sv_gvn_staffloc'];
// Create a connection to the database
$conn = new mysqli("localhost", "root", "", "urbanlink");

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve the problems based on department and place
// $sql = "SELECT * FROM prob_details WHERE prob_dept = ? AND prob_user_loc = ?";
$sql = "SELECT * FROM prob_details WHERE (prob_dept = ? OR prob_dept = 'Other Department') AND prob_user_loc = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $govn_staff_dept, $govn_staff_loc);
$stmt->execute();
$result = $stmt->get_result();

// Update the status of read problems
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["prob_id"]) && isset($_POST["new_status"])) {
        $prob_id = $_POST["prob_id"];
        $new_status = $_POST["new_status"];
        $update_sql = "UPDATE prob_details SET problem_status = ? WHERE prob_id = ?";
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

// Fetch the number of problems reported by the staff's department
try {
    $departmentReportStmt = $conn->prepare("SELECT COUNT(*) AS department_report_count FROM prob_details WHERE prob_dept = ?");
    $departmentReportStmt->bind_param("s", $govn_staff_dept); // Bind the parameter
    $departmentReportStmt->execute();
    $departmentReportResult = $departmentReportStmt->get_result();
    $departmentReportCount = $departmentReportResult->fetch_assoc()['department_report_count'];
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
// ---------------------------------------------------------COUNT ENDS --------------------------------------------------------
// Pagination configuration
$problemsPerPage = 6; // Change this to the desired number of problems per page
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $problemsPerPage;
// Retrieve the problems based on department and place with pagination
// $sql = "SELECT * FROM prob_details WHERE (prob_dept = ? OR prob_dept = 'Other Department') AND prob_user_loc = ? ORDER BY prob_time DESC LIMIT ?, ?";
$sql = "SELECT * FROM prob_details WHERE (prob_dept = ? OR prob_dept = 'Other Department') AND prob_user_loc = ? ORDER BY prob_time LIMIT ?, ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssii", $govn_staff_dept, $govn_staff_loc, $offset, $problemsPerPage);
$stmt->execute();
$result = $stmt->get_result();

// Close the database connection
$conn->close();
?>



<!DOCTYPE html>
<html>

<head>
    <title>Government Staff Problem Page</title>
    <script src="https://kit.fontawesome.com/4c43584236.js" crossorigin="anonymous"></script>
    <link rel="icon" href="../images/urbanlink-logo.png" type="image/icon type">
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@300&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            background-image: url("../images/diamond-upholstery.png"), linear-gradient(to right top, #435F75, #BCC6CC, #435F75);
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

        /* Add this CSS to style the search container */
        .search-container {
            position: relative;
            top: 10px;
        }

        #search-input {
            padding: 10px 30px;
            border-radius: 20px;
            margin-right: 5px;
        }

        #search-button {
            padding: 5px 10px;
        }



        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 10px;
        }

        .page-link {
            display: inline-block;
            padding: 8px 12px;
            margin: 0 5px;
            background-color: #f1f1f1;
            border: 1px solid #ddd;
            border-radius: 3px;
            text-decoration: none;
            color: #333;
            transition: background-color 0.3s, color 0.3s;
        }

        .page-link:hover {
            background-color: #ddd;
        }

        .current-page {
            background-color: #007bff;
            color: #fff;
            border-color: #007bff;
        }

        .current-page:hover {
            background-color: #007bff;
            cursor: default;
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
            width: 5px;
            /* border-radius: 50%; */
            transform: translateX(-10px);
        }

        /* ::-webkit-scrollbar-thumb {
            background-color: #000080;
            border-radius: 2px;
        } */

        ::-webkit-scrollbar-thumb:hover {
            background-color: #808080;
        }

        ::-webkit-scrollbar-track {
            background-color: transparent;
            display: none;
        }


        .gadgets {
            display: flex;
            justify-content: space-between;
        }

        /* COUNT SECTION CARD DESIGN CSS STARTS */
        .count-container {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
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
            font-size: 1.2rem;
            color: #435f75;
        }

        .problem-count span {
            display: block;
            /* margin-top: 10px; */
            font-size: 2rem;
            font-weight: bold;
            color: orange;
            position: relative;
            top: -15%;
        }

        /* COUNT SECTION CARD DESIGN CSS ENDS */

        /* Add styles for filter elements and clear button */
        .filter-section {
            position: relative;
            left: 10px;
            top: 10px;
        }

        #filter-status,
        #filter-user-main-area,
        #filter-problem-type {
            margin: 10px;
            width: 30%;
            border-radius: 5px;
            padding: 10px;
        }

        #clear-filters {
            margin: 10px;
            width: 100px;
            padding: 10px;
            border-radius: 5px;
            position: relative;
            /* top: -10px; */
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

        td:hover {
            cursor: pointer;
            color: #F75D59;
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

        tr {
            background-color: white;
            transition: background-color 0.3s ease;
        }

        /* Hover animation */
        tr:hover {
            background-color: rgba(0, 255, 98, 0.5);
            /* Orange */
            color: rgba(255, 255, 255, 1);
            /* White */
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

        /* Media Query for screen width 701px - 900px */
        @media (min-width: 901px) and (max-width:1150px) {
            body {
                overflow-y: scroll;
                width: 100%;
                margin: 0;
                padding: 0;
            }

            .container {
                width: 150%;
                height: 500px;
                position: relative;
                top: 50px;
                margin: 20px;
            }

            .gadgets {
                flex-direction: row;
                align-items: center;
                position: relative;
                min-width: 100%;
                top: 20px;
            }

            .filter-section {
                display: flex;
                flex-direction: column;
                position: relative;
                align-items: flex-start;
            }

            .filter-option {
                width: 200px !important;
            }


            #clear-filters {
                width: 200px;
                margin: 10px 10px;
            }

            .count-container {
                flex-direction: row;
                align-items: center;
                gap: 30px;
                position: relative;
                left: 50px;
            }

            .problem-count {
                width: 100%;
                height: 100px;
                padding: 10px;
                font-size: 0.8rem;

            }

            table {
                width: 100%;
                font-weight: 600;
                font-size: 0.8rem;
            }

            .go-back {
                position: relative;
                top: -20px;
            }
        }

        @media (min-width: 701px) and (max-width: 900px) {
            body {
                overflow-y: scroll;
                width: 100%;
                margin: 0;
                padding: 0;
            }

            .container {
                width: 150%;
                height: 500px;
                position: relative;
                top: 50px;
                margin: 20px;
            }

            .gadgets {
                flex-direction: row;
                align-items: center;
                position: relative;
                min-width: 100%;
                top: 20px;
            }

            .filter-section {
                display: flex;
                flex-direction: column;
                position: relative;
                align-items: flex-start;
            }

            .filter-option {
                width: 200px !important;
            }


            #clear-filters {
                width: 200px;
                margin: 10px 10px;
            }

            .count-container {
                flex-direction: row;
                align-items: center;
                gap: 30px;
                position: relative;
                left: 50px;
            }

            .problem-count {
                width: 100%;
                height: 100px;
                padding: 10px;
                font-size: 0.8rem;

            }

            table {
                width: 100%;
                font-weight: 600;
                font-size: 0.8rem;
            }

            .go-back {
                position: relative;
                top: -20px;
            }
        }

        /* Media Query for screen width 501px - 700px */
        @media (min-width: 501px) and (max-width: 700px) {

            body {
                overflow-y: scroll;
                width: 100%;
                margin: 0;
                padding: 0;
            }

            .container {
                width: 150%;
                height: 500px;
                position: relative;
                top: 50px;
                margin: 20px;
            }

            .workby {
                background: #4A90E2;
                color: white;
                padding: 5px;
                font-weight: 700;
                border-radius: 10px;
            }

            .processby {
                background-color: #FF5722;
                font-weight: 700;
                color: white;
            }

            .gadgets {
                flex-direction: row;
                align-items: center;
                position: relative;
                min-width: 100%;
                top: 20px;
            }

            .filter-section {
                display: flex;
                flex-direction: column;
                position: relative;
                align-items: flex-start;
            }

            .filter-option {
                width: 200px !important;
            }


            #clear-filters {
                width: 200px;
                margin: 10px 10px;
            }

            .count-container {
                flex-direction: row;
                align-items: center;
                gap: 30px;
                position: relative;
                left: 50px;
            }

            .problem-count {
                width: 100px;
                height: 100px;
                padding: 10px;
                font-size: 0.8rem;

            }

            table {
                width: 100%;
                font-weight: 600;
                font-size: 0.8rem;
            }

            .go-back {
                position: relative;
                top: -20px;
            }
        }

        /* Media Query for screen width 300px - 500px */
        @media (min-width: 300px) and (max-width: 500px) {

            body {
                overflow-y: scroll;
                width: 100%;
                margin: 0;
                padding: 0;
            }

            .container {
                width: 150%;
                height: 500px;
                position: relative;
                top: 50px;
                margin: 10px;
            }

            .gadgets {
                flex-direction: row;
                align-items: center;
                position: relative;
                top: 20px;
                padding: 20px;
            }

            .filter-option {
                width: 200px !important;
            }

            .filter-section {
                display: flex;
                flex-direction: column;
            }

            #clear-filters {
                width: 200px;
                margin: 10px 10px;
            }

            .count-container {
                flex-direction: row;
                align-items: center;
                gap: 30px;
                position: relative;
                left: 20px;
            }

            .problem-count {
                width: 100px;
                height: 100px;
                padding: 10px;
                font-size: 0.8rem;

            }

            table {
                width: 100%;
                font-weight: 600;
                font-size: 0.8rem;
            }

            .go-back {
                position: relative;
                top: -20px;
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
        <a href="govn_landing.php" class="go-back">⬅️ GO BACK</a>
    </div>
    <div class="gadgets">
        <div class="filter-section">
            <select id="filter-status" class="filter-option">
                <option value="">All Statuses</option>
                <option value="Read">Read</option>
                <option value="Preparing">Preparing</option>
                <option value="Working">Working</option>
                <option value="Completed">Completed</option>
                <option value="Working by NGO">Working by NGO</option>
            </select>
            <select id="filter-user-main-area" class="filter-option">
                <option value="">All Locations</option>
                <option value="Alingiam(gobi)">Alingiam(gobi)</option>
                <option value="Basuvanapuram">Basuvanapuram</option>
                <option value="Elathur Chettipalayam">Elathur Chettipalayam</option>
                <option value="Erangattur">Erangattur</option>
                <option value="Getticheyur">Getticheyur</option>
                <option value="Gobichettipalayam East">Gobichettipalayam East</option>
                <option value="Gobichettipalayam South">Gobichettipalayam South</option>
                <option value="Kallipatti">Kallipatti</option>
                <option value="Karattadipalayam">Karattadipalayam</option>
                <option value="Kasipalayam (erode)">Kasipalayam (erode)</option>
                <option value="Kidarai">Kidarai</option>
                <option value="Kodiveri">Kodiveri</option>
                <option value="Kolappalur (erode)">Kolappalur (erode)</option>
                <option value="Kummakalipalayam">Kummakalipalayam</option>
                <option value="Nambiyur">Nambiyur</option>
                <option value="Nanjagoundenpalayam">Nanjagoundenpalayam</option>
                <option value="Pariyur Vellalapalayam">Pariyur Vellalapalayam</option>
                <option value="Pattimaniakaranpalayam">Pattimaniakaranpalayam</option>
                <option value="Perumugaipudur">Perumugaipudur</option>
                <option value="Pudukkaraipudur">Pudukkaraipudur</option>
                <option value="Pudupalayam (erode)">Pudupalayam (erode)</option>
                <option value="Sakthinagar">Sakthinagar</option>
                <option value="Sokkumaripalayam">Sokkumaripalayam</option>
                <option value="Suriappampalayam">Suriappampalayam</option>
                <option value="Theethampalayam">Theethampalayam</option>
                <option value="Thuckanaickenpalayam">Thuckanaickenpalayam</option>
            </select>
            <button id="clear-filters">Clear Filters</button>
        </div>
        <div class="search-container">
            <input type="text" id="search-input" placeholder="Search...">
            <button id="search-button">Search</button>
            <button id="reset-button">Reset</button>
        </div>
        <div class="count-container">
            <div class="problem-count" id="total-count">
                <p> Total Reports: </p><span> <?php echo $totalReportCount; ?> </span>
            </div>
            <div class="problem-count" id="department-count">
                <p> Department Reports: </p> <span><?php echo $departmentReportCount; ?></span>
            </div>
        </div>

    </div>

    <div class="container">
        <h2>Government Staff Problem Page</h2>
        <table>
            <!-- Table header -->
            <tr class="heading">
                <th>Problem ID</th>
                <th>User ID</th>
                <th>User Name</th>
                <th>User Phone</th>
                <th>User Location</th>
                <th>User Main Area</th>
                <th>Problem Dept</th>
                <th>Problem Type</th>
                <th>Description</th>
                <th>Date</th>
                <th>Problem Location</th>
                <th>Work Status From Govn Side</th>
                <th>Processing By</th>
                <th>Update Status</th>
                <th>Update Processing By</th>
                <th>Track Process</th>
                <th>See Feedbacks</th>
            </tr>
            <?php
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
            ?>
                    <tr <?php if ($isNew) echo 'class="new-problem"'; ?>>
                        <td><?php echo $problemId; ?></td>
                        <td><?php echo $userid; ?></td>
                        <td><?php echo $username; ?></td>
                        <td><?php echo $userphone ?></td>
                        <td><?php echo $userloc ?></td>
                        <td><?php echo $usermainarea ?></td>
                        <td><?php echo $problemdept ?></td>
                        <td><?php echo $problemtype ?></td>
                        <td><?php echo $description ?></td>
                        <td><?php echo $problemdate ?></td>
                        <td><?php echo $problemloc ?></td>
                        <td><?php echo $status ?></td>
                        <td class="processby"><?php echo $processby ?></td>
                        <td>
                            <form method="post">
                                <input type="hidden" name="prob_id" value="<?php echo $problemId; ?>">
                                <select name="new_status">
                                    <option value="" <?php if ($status === '') echo 'selected'; ?>>Select</option>
                                    <option value="Read" <?php if ($status === 'Read') echo 'selected'; ?>>Read</option>
                                    <option value="Preparing" <?php if ($status === 'Preparing') echo 'selected'; ?>>Preparing</option>
                                    <option value="Working" <?php if ($status === 'Working') echo 'selected'; ?>>Working</option>
                                    <option value="Completed" <?php if ($status === 'Completed') echo 'selected'; ?>>Completed</option>
                                    <option value="Working by NGO" <?php if ($status === 'Working by NGO') echo 'selected'; ?>>Working by NGO</option>
                                </select>
                                <button type="submit" name="update">Update</button>
                            </form>
                        </td>
                        <td>
                            <?php
                            if ($processby === 'NGO') {
                                echo '<span class="workby">--x--</span>';
                            } else {
                                echo '
                            <form method="post">
                                            <input type="hidden" name="prob_id" value="' . $problemId . '">
                                            <select name="new_processby">
                                                <option value="" ' . ($processby === '' ? 'selected' : '') . '>Select</option>
                                                <option value="Government" ' . ($processby === 'Government' ? 'selected' : '') . '>Government</option>
                                            </select>
                                            <button type="submit" name="update">Update</button>
                            </form>';
                            }
                            ?>
                        </td>

                        <td>
                            <!-- Redirect to prob_status_track.php -->
                            <!-- <a href="govn_prob_status_track.php?prob_id=' . $problemId . '" class="track-link"> -->
                            <a href="govn_prob_status_track.php?prob_id=<?php echo $problemId; ?>">
                                <button> <i class="fas fa-route"></i> Track </button>
                            </a>

                        </td>
                        <td>
                            <?php
                            // Assuming $processedBy contains the value of 'processed_by' for the current row
                            if ($processby === "Government") {
                                echo '<a href="govn_fb.php?prob_id=' . $problemId . '"><button>Feedback</button></a>';
                            } elseif ($processby === "NGO") {
                                echo '<a href="govn_ngo_fb.php?prob_id=' . $problemId . '"><button>NGO Feedback</button></a>';
                            }
                            ?>
                            <!-- <a href="govn_fb.php?prob_id=<?php echo $problemId; ?>">Feedback</a> -->
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
        $totalPages = ceil($departmentReportCount / $problemsPerPage);

        // Show "Previous" button
        if ($page > 1) {
            echo "<a href='?page=" . ($page - 1) . "' class='page-link'>&lt; Prev</a>";
        }

        // Display pagination links
        for ($i = 1; $i <= $totalPages; $i++) {
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


    <!-- FILTER SCRIPTS -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var filterStatus = document.getElementById("filter-status");
            var filterUserMainArea = document.getElementById("filter-user-main-area");
            var clearFiltersButton = document.getElementById("clear-filters");

            var tableRows = document.querySelectorAll(".container table tr:not(.heading)");
            var originalRowDisplays = Array.from(tableRows).map(function(row) {
                return row.style.display;
            });

            function applyFilters() {
                var selectedStatus = filterStatus.value;
                var selectedUserMainArea = filterUserMainArea.value;

                tableRows.forEach(function(row, index) {
                    var statusCell = row.children[10].textContent;
                    var userLocationCell = row.children[5].textContent;

                    var statusFilterPassed = selectedStatus === "" || statusCell === selectedStatus;
                    var userLocationFilterPassed = selectedUserMainArea === "" || userLocationCell === selectedUserMainArea;

                    if (statusFilterPassed && userLocationFilterPassed) {
                        row.style.display = originalRowDisplays[index];
                    } else {
                        row.style.display = "none";
                    }
                });
            }

            filterStatus.addEventListener("change", applyFilters);
            filterUserMainArea.addEventListener("change", applyFilters);

            clearFiltersButton.addEventListener("click", function() {
                filterStatus.value = "";
                filterUserMainArea.value = "";
                applyFilters();
            });
        });
    </script>
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