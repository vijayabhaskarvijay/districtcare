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

// Function to fetch all NGO details from the database
function fetchAllNGO()
{
    global $conn;

    $query = "SELECT * FROM public_contact_help";
    $result = $conn->query($query);

    $pchData = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $pchData[] = $row;
        }
    }
    return $pchData;
}

// Check if the "delete" parameter is present in the URL
if (isset($_GET['delete'])) {
    $deleteID = $_GET['delete'];
    $deleteResult = deleteNGO($deleteID);
    if ($deleteResult) {
        // Refresh the page to update the NGO list after deletion
        echo "<script>window.location.href = 'admin_public_help.php';</script>";
    }
}

// Function to delete an NGO based on its ID
function deleteNGO($pchID)
{
    global $conn;

    $query = "DELETE FROM public_contact_help WHERE pch_id = '$pchID'";
    $result = $conn->query($query);
    return $result;
}
?>

<!-- PAGINATION PHP STARTS-->
<!-- Your HTML content here -->

<?php
// Pagination configuration
$pchsPerPage = 15;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $pchsPerPage;

// Retrieve NGOs based on pagination
$sql = "SELECT * FROM public_contact_help LIMIT $offset, $pchsPerPage";
$result = $conn->query($sql);

// Count total NGOs for pagination
$sqlCount = "SELECT COUNT(*) AS total FROM public_contact_help";
$countResult = $conn->query($sqlCount);
$rowCount = $countResult->fetch_assoc()['total'];
$totalPages = ceil($rowCount / $pchsPerPage);
?>

<!-- PAGINATION PHP ENDS -->

<!DOCTYPE html>
<html>

<head>
    <title>Review - Public Question </title>
    <link rel="icon" href="../images/urbanlink-logo.png" type="image/icon type">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/4c43584236.js" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <style>
        /* styles.css */

        body {
            font-family: Arial, sans-serif;
            background-color: grey;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 95%;
            /* max-width: 1200px; */
            margin: 20px auto;
            background-color: rgba(255, 255, 255, 0.50);
            backdrop-filter: blur(5px);
            padding: 20px;
            border-radius: 10px;
            border-left: 2px solid #333;
            border-bottom: 2px solid #333;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
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
            top: 440px;
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
        <h1>Public Questions Review</h1>
        <table>
            <tr>
                <!-- <th>Question ID</th> -->
                <th>User ID</th>
                <th>Name</th>
                <th>Phone</th>
                <th>Email</th>
                <th>User Place</th>
                <th>User Question</th>
                <th>Date Questioned</th>
                <th>Answer Replied</th>
                <th>Actions</th>
            </tr>
            <?php
            // Fetch all NGOs from the database
            $pchData = fetchAllNGO();

            foreach ($pchData as $pch) {
                echo '<tr>';
                echo '<td>' . $pch['pch_user_id'] . '</td>';
                echo '<td>' . $pch['pch_user_name'] . '</td>';
                echo '<td>' . $pch['pch_user_phone'] . '</td>';
                echo '<td>' . $pch['pch_user_email'] . '</td>';
                echo '<td>' . $pch['pch_user_location'] . '</td>';
                echo '<td>' . $pch['pch_desc'] . '</td>';
                echo '<td>' . $pch['pch_date'] . '</td>';
                echo '<td>' . $pch['pch_admin_reply'] . '</td>';
                echo '<td class="two-btn">';
                echo '<div><a href="admin_pch_update_sep.php?id=' . $pch['pch_id'] . '">Update</a></div> | ';
                echo '<div><a href="?delete=' . $pch['pch_id'] . '" onclick="return confirmDelete();">Delete</a></div>';
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
            return confirm("Are you sure you want to delete this Question From Public?");
        }
    </script>
</body>

</html>

<?php
// Close database connection
$conn->close();
?>