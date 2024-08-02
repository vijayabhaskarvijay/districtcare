<?php
session_start();

// Replace with your database connection code
$conn = new mysqli("localhost", "root", "", "urbanlink");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// CODE FOR UPDATING ACCOUNT STATUS
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $status = $_POST["status"];
    $ngo_id = $_POST["ngo_id"];

    // Update ngo_acc_req_status
    $update_sql = "UPDATE ngo_details SET ngo_acc_req_status = '$status' WHERE ngo_id = '$ngo_id'";
    if ($conn->query($update_sql) === TRUE) {
        echo '<p class="success-message">Status updated successfully!</p>';
        header("Location: " . $_SERVER['PHP_SELF']);
    } else {
        echo '<p class="error-message">Error updating status: ' . $conn->error . '</p>';
    }
}

// Pagination configuration
$perPage = 10;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $perPage;

$queryCount = "SELECT COUNT(*) as total FROM ngo_details WHERE ngo_acc_req_status = 'Pending'";
$resultCount = $conn->query($queryCount);
$totalRows = $resultCount->fetch_assoc()['total'];

$query = "SELECT * FROM ngo_details WHERE ngo_acc_req_status = 'Pending' LIMIT $offset, $perPage";
$result = $conn->query($query);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NGO Account Requests</title>
    <link rel="icon" href="../images/urbanlink-logo.png" type="image/icon type">
    <style>
        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 0;
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
            top: 40px;
            margin-left: 10px;
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


        .success-message {
            color: green;
            font-weight: bold;
        }

        .error-message {
            color: red;
            font-weight: bold;
        }

        .container {
            width: 95%;
            margin: 0 auto;
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.5);
            backdrop-filter: blur(10px);
            border-radius: 10px;
            box-shadow: 0 0 50px rgba(07, 04, 20, 1);
            position: relative;
            overflow-x: auto;
            overflow-y: auto;
            height: 500px;
            border: 2px solid black;
            scrollbar-width: thin;
            scrollbar-color: #a0a0a0 #f0f0f0;
            top: 50px;
        }

        /* Add this CSS to your existing styles */

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
            /* background-color: #007bff; */
            color: white;
        }

        .table-head {
                background-image: url("../images/diamond-upholstery.png"), linear-gradient(to right top, #007bff, #007bff);
                text-align: center;
            }

        tr:hover {
            background-color: #d5cece;
        }

        td:hover {
            cursor: pointer;
            color: white;
        }

        td {
            font-weight: 600;
        }

        .status-select {
            padding: 8px 12px;
            border: 2px solid #007bff;
            border-radius: 5px;
            background-color: white;
            font-size: 14px;
            font-weight: 600;
            color: #007bff;
            margin: 5px;
            cursor: pointer;
        }

        .status-select option {
            font-weight: normal;
            cursor: pointer;
        }

        .status-select:focus {
            outline: none;
        }

        .button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 8px 15px;
            cursor: pointer;
            border-radius: 5px;
            font-weight: 600;
        }

        .button:hover {
            background-color: #18b300;
        }

        .image-popup {
            cursor: pointer;
            transition: transform 0.2s ease-in-out;
            border-radius: 5px;
            border: 2px solid #ddd;
        }

        .image-popup:hover {
            transform: scale(1.05);
            border-color: #007bff;
        }


        /* CSS for highlighting rows */
        .highlight {
            background-color: #ffcccc;
            /* Light red background */
            border: 2px solid #ff6666;
            /* Dark red border */
        }

        /* CSS for pagination */
        .pagination {
            margin-top: 20px;
            display: flex;
            justify-content: center;
            position: relative;
            top: 50%;
        }

        .pagination a {
            display: inline-block;
            padding: 5px 10px;
            margin: 0 5px;
            text-decoration: none;
            background-color: #f2f2f2;
            border: 1px solid #ddd;
            border-radius: 5px;
            color: #333;
        }

        .pagination a.active {
            background-color: #007bff;
            /* Blue background for active page */
            border-color: #007bff;
            color: #fff;
        }

        .pagination a:hover {
            background-color: red;
            border-color: red;
        }


        /* ---------------MEDIA QUERIES STARTS----------------------*/


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

            .gadgets {
                display: flex;
                flex-direction: column;
                align-items: flex-start;
                position: relative;
                top: 30px;
                left: -210px;
                padding: 20px;
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
                position: relative;
                top: 50px;
                margin: 20px;
            }

            .gadgets {
                display: flex;
                flex-direction: column;
                align-items: flex-start;
                position: relative;
                top: 30px;
                left: -110px;
                padding: 20px;
            }

            #clear-filters {
                width: 200px;
                margin: 10px 10px;
            }


            table {
                width: 100%;
                font-weight: 600;
                font-size: 0.8rem;
                border-radius: 10px;
                border: 2px solid #aaa;
            }

            th,
            tr,
            td {
                border: 2px solid #aaa;
                border-radius: 10px;
            }

            .go-back {
                position: relative;
                top: -20px;
            }
        }

        /* ---------------MEDIA QUERIES ENDS ----------------------*/
    </style>
    <script>
        function confirmUpdate() {
            return confirm("Are you sure you want to update?");
        }
    </script>
</head>

<body>
    <div class="back-button">
        <a href="admin_landing.php" class="go-back">⬅️</a>
    </div>

    <div class="container">
        <h2>NGO Account Requests</h2>
        <table>
            <tr class="table-head">
                <th>ID</th>
                <th>Name</th>
                <th>Position</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Organization</th>
                <th>Location</th>
                <th>Approve/Reject</th>
                <th>Image</th>
            </tr>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<tr>';
                    echo '<td>' . $row['ngo_id'] . '</td>';
                    echo '<td>' . $row['ngo_user_name'] . '</td>';
                    echo '<td>' . $row['ngo_user_position'] . '</td>';
                    echo '<td>' . $row['ngo_user_phone'] . '</td>';
                    echo '<td>' . $row['ngo_user_email'] . '</td>';
                    echo '<td>' . $row['ngo_org_name'] . '</td>';
                    echo '<td>' . $row['ngo_org_place'] . '</td>';
                    echo '<td>';
                    echo '<form method="POST" onsubmit="return confirmUpdate()">';
                    echo '<select class="status-select" name="status">
                        <option value="--Select Option--">--Select Option--</option>
                        <option value="Approve">Approve</option>
                        <option value="Reject">Reject</option>
                    </select>';
                    echo '<input type="hidden" name="ngo_id" value="' . $row['ngo_id'] . '">';
                    echo '<input type="submit" class="button" value="Update">';
                    echo '</form>';
                    echo '</td>';
                    echo '<td><a href="data:image/jpeg;base64,' . base64_encode($row['ngo_id_image']) . '" target="_blank"><img class="image-popup" src="data:image/jpeg;base64,' . base64_encode($row['ngo_id_image']) . '" alt="Image" width="80" height="60"></a></td>';
                    // echo '<td><img class="image-popup" src="data:image/jpeg;base64,' . base64_encode($row['ngo_id_image']) . '" alt="Image" width="80" height="60"></td>';
                    echo '</tr>';
                }
            } else {
                echo "<tr><td colspan='16'>No  Requests found.</td></tr>";
            }
            ?>
        </table>
        <?php
        // Pagination links
        $totalPages = ceil($totalRows / $perPage);
        echo "<div class='pagination'>";
        for ($i = 1; $i <= $totalPages; $i++) {
            $activeClass = $i === $page ? 'active' : '';
            echo "<a class='$activeClass' href='ngo_acc_req.php?page=$i'>$i</a>";
        }
        echo "</div>";
        ?>
    </div>

</body>

</html>