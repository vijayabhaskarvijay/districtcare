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

// Function to fetch all NGO details from the database
function fetchAllNGO()
{
    global $conn, $serialNumber;

    $query = "SELECT * FROM ngo_details";
    $result = $conn->query($query);

    $ngoData = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $row['serial_number'] = $serialNumber++;
            $ngoData[] = $row;
        }
    }
    return $ngoData;
}

// Check if the "delete" parameter is present in the URL
if (isset($_GET['delete'])) {
    $deleteID = $_GET['delete'];
    $deleteResult = deleteNGO($deleteID);
    if ($deleteResult) {
        // Refresh the page to update the NGO list after deletion
        echo "<script>window.location.href = 'admin_ngo_manage.php';</script>";
    }
}

// Function to delete an NGO based on its ID
function deleteNGO($ngoID)
{
    global $conn;

    $query = "DELETE FROM ngo_details WHERE ngo_id = '$ngoID'";
    $result = $conn->query($query);
    return $result;
}
?>

<!-- PAGINATION PHP STARTS-->
<!-- Your HTML content here -->

<?php
// Pagination configuration
$ngosPerPage = 2;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $ngosPerPage;

// Retrieve NGOs based on pagination
$sql = "SELECT * FROM ngo_details LIMIT $offset, $ngosPerPage";
$result = $conn->query($sql);

// Count total NGOs for pagination
$sqlCount = "SELECT COUNT(*) AS total FROM ngo_details";
$countResult = $conn->query($sqlCount);
$rowCount = $countResult->fetch_assoc()['total'];
$totalPages = ceil($rowCount / $ngosPerPage);
?>

<!-- PAGINATION PHP ENDS -->

<!DOCTYPE html>
<html>

<head>
    <title>Admin Manage NGOs</title>
    <link rel="icon" href="../images/urbanlink-logo.png" type="image/icon type">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/4c43584236.js" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <style>
        /* styles.css */

        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            background-image: url("../images/ngo_manage_bg.jpg");
            background-size: cover;
            background-position: 0 -90px;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 95%;
            margin: 20px auto;
            background-color: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(5px);
            padding: 20px;
            border-radius: 10px;
            border-left: 2px solid #333;
            border-bottom: 2px solid #333;
            box-shadow: 0 0 20px rgba(0, 0, 0, 1);
            position: relative;
            top: 20px;
            left: -2px;
            overflow-x: scroll;
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

        th {
            background-image: url("../images/diamond-upholstery.png"), linear-gradient(to right top, #007bff, #007bff);
            text-align: center;
            /* background-color: #007bff; */
            color: white;
        }

        th,
        td {
            padding: 12px 15px;
            text-align: center;
            border-bottom: 1px solid #ddd;
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
            top: 40px;
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

            body {
                background-image: none;
                background-color: white;
            }

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
        <h1>Admin Manage NGOs</h1>
        <table>
            <tr>
                <th>S.NO</th>
                <th>NGO ID</th>
                <th>Name</th>
                <th>Position</th>
                <th>Phone</th>
                <th>Email</th>
                <th>User Password</th>
                <th>Organization Name</th>
                <th>Organization Place</th>
                <th>Organization Phone</th>
                <th>Organization Email</th>
                <th>Actions</th>
            </tr>
            <?php
            // Fetch all NGOs from the database
            $ngoData = fetchAllNGO();

            foreach ($ngoData as $ngo) {
                echo '<tr>';
                echo '<td>' . $ngo['serial_number'] . '</td>';
                echo '<td>' . $ngo['ngo_id'] . '</td>';
                echo '<td>' . $ngo['ngo_user_name'] . '</td>';
                echo '<td>' . $ngo['ngo_user_position'] . '</td>';
                echo '<td>' . $ngo['ngo_user_phone'] . '</td>';
                echo '<td>' . $ngo['ngo_user_email'] . '</td>';
                echo '<td>' . $ngo['ngo_user_pwd'] . '</td>';
                echo '<td>' . $ngo['ngo_org_name'] . '</td>';
                echo '<td>' . $ngo['ngo_org_place'] . '</td>';
                echo '<td>' . $ngo['ngo_org_phone'] . '</td>';
                echo '<td>' . $ngo['ngo_org_mail'] . '</td>';
                echo '<td class="two-btn">';
                echo '<div><a href="admin_ngo_update_sep.php?id=' . $ngo['ngo_id'] . '" class="btn">Update</a> </div> --- ';
                echo '<div><a href="?delete=' . $ngo['ngo_id'] . '" onclick="return confirmDelete();" class="btn-2">Delete</a></div>';
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
            return confirm("Are you sure you want to delete this NGO?");
        }
    </script>
</body>

</html>

<?php
// Close database connection
$conn->close();
?>