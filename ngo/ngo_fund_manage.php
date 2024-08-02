<?php
// Start the session
session_start();

// Database connection credentials
$servername = "localhost";
$username = "root";
$password = "";
$database = "urbanlink";

// Create a new database connection
$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Replace with the logged-in NGO user's ID
$loggedInUserId = $_SESSION['ngo_id'];

// ---------------------------------------------------------COUNT STARTS --------------------------------------------------------
// Fetch the total number of problems reported
try {
    $totalReportStmt = $conn->prepare("SELECT COUNT(*) AS total FROM funding_details WHERE fund_user_id = '$loggedInUserId'");
    $totalReportStmt->execute();
    $totalReportResult = $totalReportStmt->get_result();
    $totalReportCount = $totalReportResult->fetch_assoc()['total'];
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// ---------------------------------------------------------COUNT ENDS --------------------------------------------------------
// Number of records to display per page
$recordsPerPage = 7;
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
$sql = "SELECT * FROM funding_details WHERE fund_user_id = '$loggedInUserId' LIMIT $offset, $recordsPerPage";
$result = $conn->query($sql);
// Calculate total pages
$totalPages = ceil($totalReportCount / $recordsPerPage);


$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NGO Fund Management</title>
    <link rel="icon" href="../images/urbanlink-logo.png" type="image/icon type">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 0;
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
            height: 550px;
            border: 2px solid black;
            scrollbar-width: thin;
            scrollbar-color: #a0a0a0 #f0f0f0;
            top: 50px;
        }


        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            overflow-y: auto;
            border-bottom: 1px solid #ccc;
            border-radius: 00px;
            cursor: pointer;
        }

        td {
            padding: 5px;
            width: 50%;
            text-align: left;
            border: 1px solid black;
        }

        th {
            padding: 5px;
            width: 50%;
            text-align: left;
            cursor: grab;
            border: 1px solid black;
            background-color: #357EC7;
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
            background-color: rgba(255, 165, 0, 0.8);
            /* Orange */
            color: rgba(255, 255, 255, 1);
            /* White */
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
            top: -15px;
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

        /* Existing styles... */

        /* Add these styles for fund status, govn staff phone, fund collect place, and fund notice */
        .spl-highlight[data-status="Approved"] {
            color: green;
        }

        .spl-highlight[data-status="Discussing"] {
            color: gold;
        }

        .spl-highlight[data-status="Rejected"] {
            color: red;
        }

        .pagination {
            margin-top: 20px;
            text-align: center;
            position: relative;
            top:50px;
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

        /* Adjust this media query for responsiveness */
        @media (max-width: 768px) {
            .container {
                margin: 10px;
                padding: 10px;
            }
        }
    </style>
</head>

<body>
    <div class="back-button">
        <a href="ngo_landing.php" class="go-back">⬅️ GO BACK</a>
    </div>
    <div class="container">
        <h2>NGO Fund Management</h2>
        <table>
            <tr>
                <th>Fund ID</th>
                <th>Problem ID</th>
                <th>User Name</th>
                <th>User Phone</th>
                <th>Organization Name</th>
                <th>Organization Location</th>
                <th>Organization Phone</th>
                <th>Fund Amount</th>
                <th>Problem Description</th>
                <th>Problem Type</th>
                <th>Problem Department</th>
                <th>Fund Request Description</th>
                <th>Fund Request Date</th>
                <th>Fund Status</th>
                <th>Government Staff Phone</th>
                <th>Fund Collection Place</th>
                <th>Notice</th>
            </tr>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['fund_id'] . "</td>";
                    echo "<td>" . $row['fund_prob_id'] . "</td>";
                    echo "<td>" . $row['fund_user_name'] . "</td>";
                    echo "<td>" . $row['fund_user_phone'] . "</td>";
                    echo "<td>" . $row['fund_org_name'] . "</td>";
                    echo "<td>" . $row['fund_org_loc'] . "</td>";
                    echo "<td>" . $row['fund_org_phone'] . "</td>";
                    echo "<td>" . $row['fund_amount'] . "</td>";
                    echo "<td>" . $row['fund_prob_desc'] . "</td>";
                    echo "<td>" . $row['fund_prob_type'] . "</td>";
                    echo "<td>" . $row['fund_prob_dept'] . "</td>";
                    echo "<td>" . $row['fund_req_desc'] . "</td>";
                    echo "<td>" . $row['fund_req_date'] . "</td>";
                    // echo "<td class='spl-highlight'>" . $row['fund_status'] . "</td>";
                    // echo "<td class='spl-highlight'>" . $row['fund_govnstaff_phone'] . "</td>";
                    // echo "<td class='spl-highlight'>" . $row['fund_collect_place'] . "</td>";
                    // echo "<td class='spl-highlight'>" . $row['fund_notice'] . "</td>";
                    // Inside your while loop, where you echo table cells
                    echo "<td class='spl-highlight' data-status='" . $row['fund_status'] . "'>" . $row['fund_status'] . "</td>";
                    echo "<td class='spl-highlight' data-govnstaffphone='" . $row['fund_govnstaff_phone'] . "'>" . $row['fund_govnstaff_phone'] . "</td>";
                    echo "<td class='spl-highlight' data-collectplace='" . $row['fund_collect_place'] . "'>" . $row['fund_collect_place'] . "</td>";
                    echo "<td class='spl-highlight' data-notice='" . $row['fund_notice'] . "'>" . $row['fund_notice'] . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='17'>No fund requests found.</td></tr>";
            }
            ?>
        </table>
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

</body>

</html>