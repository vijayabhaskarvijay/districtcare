<?php
// Replace with your actual database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "urbanlink";

// Create a database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize a variable for serial number
$serialNumber = 1;

// Function to fetch all GOVN details from the database
function fetchAllGovn()
{
    global $conn, $serialNumber;

    $query = "SELECT * FROM govn_staff_details";
    $result = $conn->query($query);

    $govnstaffData = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $row['serial_number'] = $serialNumber++;
            $govnstaffData[] = $row;
        }
    }
    return $govnstaffData;
}

// Check if the "delete" parameter is present in the URL
if (isset($_GET['delete'])) {
    $deleteID = $_GET['delete'];
    $deleteResult = deletegovnstaff($deleteID);
    if ($deleteResult) {
        // Refresh the page to update the GOVN list after deletion
        echo "<script>window.location.href = 'admin_govn_manage.php';</script>";
    }
}

// Function to delete an GOVN based on its ID
function deletegovnstaff($govnstaffID)
{
    global $conn;

    $query = "DELETE FROM govn_staff_details WHERE govn_staff_id = '$govnstaffID'";
    $result = $conn->query($query);
    return $result;
}
?>

<?php
// Pagination configuration
$govnstaffsPerPage = 15;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $govnstaffsPerPage;

// Retrieve GOVNs based on pagination
$sql = "SELECT * FROM govn_staff_details LIMIT $offset, $govnstaffsPerPage";
$result = $conn->query($sql);

// Count total GOVNs for pagination
$sqlCount = "SELECT COUNT(*) AS total FROM govn_staff_details";
$countResult = $conn->query($sqlCount);
$rowCount = $countResult->fetch_assoc()['total'];
$totalPages = ceil($rowCount / $govnstaffsPerPage);
?>

<!-- PAGINATION PHP ENDS -->

<!DOCTYPE html>
<html>

<head>
    <title>Admin Manage Govn Staff</title>
    <link rel="icon" href="../images/urbanlink-logo.png" type="image/icon type">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/4c43584236.js" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <style>
        /* styles.css */

        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            background-image: url("../images/admin_govn_manage_bg.jpg");
            background-size: cover;
            background-position: 0 -90px;
            margin: 0;
            padding: 0;
        }

        .btn {
            padding: 10px;
            background-color: #007bff;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.2s ease;
            color: white;
        }

        .btn:hover {
            background-color: #ad1e23;
        }

        .container {
            width: 95%;
            /* max-width: 1200px; */
            margin: 20px auto;
            background-color: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(5px);
            padding: 20px;
            border-radius: 10px;
            border-left: 2px solid #333;
            border-bottom: 2px solid #333;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            position: relative;
            top: 20px;
            left: -2px;
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }


        .btn {
            padding: 5px;
            background-color: #007bff;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.2s ease;
            color: white;
            margin: 7px;
            display: flex;
            flex-direction: column;
        }

        .btn:hover {
            background-color: rgb(132, 0, 255);
            color: #ffff;
        }

        .btn-2 {
            padding: 5px;
            background-color: #007bff;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.2s ease;
            color: white;
            margin: 7px;
            position: relative;
            top: 5px;
        }

        .btn-2:hover {
            background-color: rgb(132, 0, 255);
            color: #ffff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
            margin-top: 20px;
        }

        th,
        td {
            padding: 12px 15px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        tr:hover {
            background-color: #989191;
        }

        td {
            font-weight: 600;
        }

        td:hover {
            cursor: pointer;
            color: white;
        }

        a {
            text-decoration: none;
            color: #007bff;
        }

        a:hover {
            text-decoration: underline;
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
            text-decoration: none;
        }

        .back-button {
            position: relative;
            top: 20px;
            margin-left: 10px;
        }

        /* Pagination Styles */
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
            gap: 10px;
            position: relative;
            top: 200px !important;
        }

        .pagination a {
            padding: 8px 12px;
            border: 1px solid #007bff;
            border-radius: 4px;
            color: #007bff;
            text-decoration: none;
        }

        .pagination a.active {
            background-color: #007bff;
            color: #fff;
        }

        /* Responsive design using media queries */
        @media screen and (max-width: 600px) {
            table {
                font-size: 14px;
            }
        }

        @media screen and (max-width: 400px) {
            table {
                font-size: 12px;
            }
        }
    </style>
</head>

<body>
    <div class="back-button">
        <a href="admin_landing.php" class="go-back">⬅️</a>
    </div>
    <div class="container">
        <h1>Admin Manage Govn Staff</h1>
        <table>
            <tr>
                <th>Serial Number</th>
                <th>Govn Staff ID</th>
                <th>Name</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Location</th>
                <th>Password</th>
                <th>Staff Department</th>
                <th>Staff MPIN</th>
                <th>Actions</th>
            </tr>
            <?php
            // Fetch all GOVNs from the database
            $govnstaffData = fetchAllGovn();

            foreach ($govnstaffData as $govnstaff) {
                echo '<tr>';
                echo '<td>' . $govnstaff['serial_number'] . '</td>';
                echo '<td>' . $govnstaff['govn_staff_id'] . '</td>';
                echo '<td>' . $govnstaff['govn_staff_name'] . '</td>';
                echo '<td>' . $govnstaff['govn_staff_phone'] . '</td>';
                echo '<td>' . $govnstaff['govn_staff_email'] . '</td>';
                echo '<td>' . $govnstaff['govn_staff_location'] . '</td>';
                echo '<td>' . $govnstaff['govn_staff_password'] . '</td>';
                echo '<td>' . $govnstaff['govn_staff_work_dept'] . '</td>';
                echo '<td>' . $govnstaff['govn_staff_mpin'] . '</td>';
                echo '<td>';
                echo '<a href="admin_govn_update_sep.php?id=' . $govnstaff['govn_staff_id'] . '" class="btn">Update</a> | ';
                echo '<a href="?delete=' . $govnstaff['govn_staff_id'] . '" onclick="return confirmDelete();" class="btn">Delete</a>';
                echo '</td>';
                echo '</tr>';
            }
            ?>
        </table>
    </div>

    <!-- PAGINATION SECTION STARTS -->

    <div class="pagination">
        <?php
        $numLinks = min($totalPages, 10); // Display a maximum of 10 links
        $startPage = max($page - floor($numLinks / 2), 1);
        $endPage = $startPage + $numLinks - 1;

        if ($endPage > $totalPages) {
            $startPage = max($totalPages - $numLinks + 1, 1);
            $endPage = $totalPages;
        }

        if ($page > 1) {
            echo '<a href="?page=' . ($page - 1) . '">Previous</a>';
        }

        for ($i = $startPage; $i <= $endPage; $i++) {
            echo '<a href="?page=' . $i . '"';
            if ($i == $page) {
                echo ' class="active"';
            }
            echo '>' . $i . '</a>';
        }

        if ($page < $totalPages) {
            echo '<a href="?page=' . ($page + 1) . '">Next</a>';
        }
        ?>
    </div>


    <!-- PAGINATION SECTION ENDS-->

    <script>
        function confirmDelete() {
            return confirm("Are you sure you want to delete this Govn Staff?");
        }
    </script>
</body>

</html>

<?php
// Close database connection
$conn->close();
?>