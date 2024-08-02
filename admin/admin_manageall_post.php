<?php
// Logout PHP code
if (isset($_POST['logout'])) {
    // Destroy session
    session_name("admin_session");
    session_start();
    session_unset();
    session_destroy();
    // Redirect to index.php
    header("Location: ../index.php");
    exit();
}
// Start session
session_start();
// Check if logged in
if (!isset($_SESSION['sv_admin_username'])) {
    // Redirect to login page
    header("Location:admin_login.php");
    exit();
}
if (isset($_SESSION['sv_admin_phone'])) {
    $userPhone = $_SESSION['sv_admin_phone'];
} else {
    // Handle case when user_phone is not set in the session
    $userPhone = "N/A";
}
//STORE THE SESSION VARIABLES
$adminID = $_SESSION['sv_admin_id'];
$adminUsername = $_SESSION['sv_admin_username'];
$adminEmail = $_SESSION['sv_admin_email'];
$adminPhone = $_SESSION['sv_admin_phone'];
// Set session cookie with expiration time
$expire = time() + (60 * 60); // 1 hour
setcookie(session_name(), session_id(), $expire);
// Replace the database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "urbanlink";
// Retrieve posts based on the selected option
$selectedOption = isset($_GET['option']) ? $_GET['option'] : '';
// Set the default value for selectedOption
$selectedOption = 'public_posts';
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Check if selectedOption exists in the $_GET array
if (isset($_GET['option'])) {
    // Assign the value of $_GET['option'] to selectedOption
    $selectedOption = $_GET['option'];
}
// Function to retrieve and display posts
function displayPostsPagination($tableName, $title, $postIDColumn, $postColumns, $creationTimeColumn, $currentPage, $postsPerPage)
{
    global $conn, $selectedOption;
    // Calculate the starting row for the current page
    $startRow = ($currentPage - 1) * $postsPerPage;

    // Display user ID based on the selected option
    $userIdColumn = '';
    // Display user ID based on the selected option
    switch ($selectedOption) {
        case 'public_posts':
            $userIdColumn = 'pp_user_id';
            break;
        case 'ngo_post_details':
            $userIdColumn = 'ngo_user_id';
            break;
        case 'admin_posts':
            $userIdColumn = 'ap_admin_id';
            break;
        case 'govn_posts':
            $userIdColumn = 'gp_staff_id';
            break;
        default:
            // Set a default value for $userIdColumn in case none of the options match
            $userIdColumn = '';
    }
    $query = "SELECT * FROM $tableName ORDER BY $creationTimeColumn DESC LIMIT $startRow, $postsPerPage";
    $result = $conn->query($query);
    echo '<div id="' . $tableName . '" class="post-container">';
    echo '<h2>' . $title . '</h2>';
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $postID = $row[$postIDColumn];
            echo '<form method="post" action="">
                <input type="hidden" name="table" value="' . $tableName . '">
                <input type="hidden" name="id_column" value="' . $postIDColumn . '">
                <input type="hidden" name="id" value="' . $postID . '">
                <div class="post">';
            echo '<div class="details">';
            echo '<span class="tag-username">' . $row[$postColumns[0]] . '</span>';
            echo '<span class="tag-phone">' . $row[$postColumns[1]] . '</span>';
            echo '<span class="tag-location">' . $row[$postColumns[2]] . '</span>';
            echo '<span class="tag-date">' . $row[$postColumns[3]] . '</span>';
            echo '<span class="tag-type">' . $row[$postColumns[4]] . '</span>';
            echo '<span class="tag-userid">' . $row[$userIdColumn] . '</span>'; // Display user ID based on the selected option
            echo '</div>';
            echo '<div class="description">' . $row[$postColumns[5]] . '</div>';
            echo '<button type="submit" name="delete" class="delete-button" onclick="return confirmDelete()">Delete</button>';

            echo '</div></form>';
        }
    } else {
        echo '<p>No posts to Check👍.</p>';
    }
    echo '</div>';
}
// Array of post options and their details
$postOptions = [
    [
        'tableName' => 'public_posts',
        'title' => 'Public Posts',
        'postIDColumn' => 'pp_id',
        'postColumns' => ['pp_username', 'pp_userphone', 'pp_userloc', 'pp_date', 'pp_type', 'pp_userpost_description'],
        'creationTimeColumn' => 'pp_time'
    ],
    [
        'tableName' => 'ngo_post_details',
        'title' => 'NGO Posts',
        'postIDColumn' => 'ngo_post_id',
        'postColumns' => ['ngo_user_name', 'ngo_user_email', 'ngo_user_phone', 'ngo_post_date', 'ngo_post_type', 'ngo_post_desc'],
        'creationTimeColumn' => 'ngo_post_time'
    ],
    [
        'tableName' => 'admin_posts',
        'title' => 'Admin Posts',
        'postIDColumn' => 'ap_id',
        'postColumns' => ['ap_admin_name', 'ap_admin_phone', 'ap_place', 'ap_date', 'ap_type', 'ap_desc'],
        'creationTimeColumn' => 'ap_time'
    ],
    [
        'tableName' => 'govn_posts',
        'title' => 'Government Announcements and Updates',
        'postIDColumn' => 'gp_id',
        'postColumns' => ['gp_staff_name', 'gp_staff_phone', 'gp_loc', 'gp_date', 'gp_type', 'gp_desc'],
        'creationTimeColumn' => 'gp_time'
    ]
];
// Function to get the total number of posts for a given table
function getTotalPostsCount($tableName)
{
    global $conn;
    $query = "SELECT COUNT(*) AS total FROM $tableName";
    $result = $conn->query($query);
    if ($result) {
        $row = $result->fetch_assoc();
        return $row['total'];
    }
    return 0;
}

// CODE TO HANDLE DELETION OPERATION IN DB SIDE
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
    $table = $_POST['table'];
    $id_column = $_POST['id_column'];  // Change from $id_column to $id
    $id = $_POST['id'];  // Change from $id_column to $id

    // Assuming that each table has an ID column named 'post_id'
    $sql = "DELETE FROM $table WHERE $id_column = '$id'"; // Add single quotes around $id_column

    if ($conn->query($sql) === TRUE) {
        // echo 'Post deleted successfully';
        echo '<div id="popup" class="popup">
                    <p>Post Deleted Successfully!!!</p>
                </div>';
        echo '<script>
                    setTimeout(function() {
                        window.location.href = "admin_manageall_post.php";
                    }, 3000);
                </script>';
    } else {
        echo 'Error deleting post';
    }
}


?>

<!DOCTYPE html>
<html>

<head>
    <script>
        function confirmDelete() {
            return confirm("Are you sure you want to delete this post?");
        }
    </script>
    <script>
        // Display the popup
        document.addEventListener("DOMContentLoaded", function() {
            var popup = document.getElementById("popup");
            popup.style.display = "block";
        });
    </script>
    <title>Admin Landing</title>
    <link rel="icon" href="../images/urbanlink-logo.png" type="image/icon type">
    <meta http-equiv="refresh" content="60">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/4c43584236.js" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url("../images/manageposts-bg.jpg");
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            margin: 0;
            padding: 0;
            display: flex;
            height: 900px;
            width: 100%;
        }

        .post-title {
            color: #6a0dad;
            position: relative;
            top: 0px;
        }

        .popup {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: rgba(0, 0, 0, 0.8);
            color: #fff;
            padding: 20px;
            border-radius: 5px;
            z-index: 9999;
            display: none;
        }

        .popup h3 {
            margin-top: 0;
        }

        .log {
            padding: 20px;
            border: 2px solid white;
            background-color: #3090C7;
            cursor: pointer;
            color: white;
            position: relative;
            left: 15px;
            top: 50px;
            width: 85%;
        }

        .log:hover {
            background-color: #e57373;
            transition: 0.2s ease-out;
        }

        :root {
            --nav-color: #435f75;
            --dark-grey: #333;
        }

        .main-container {
            display: flex;
            flex-wrap: wrap;
            width: 80%;
            margin: 20px auto;
            background-color: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            padding: 20px;
            height: 770px;
            position: absolute;
            overflow-y: auto;
            left: 8%;
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.8);
            top: 50px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.8);
            opacity: 0.9;
        }

        .main-container::-webkit-scrollbar {
            width: 10px;
        }

        .main-container::-webkit-scrollbar-thumb {
            background-color: #435f75;
            border-radius: 5px;
            position: relative;
            left: -10px;
        }

        .main-container::-webkit-scrollbar-thumb:hover {
            background-color: #3090C7;
        }

        #clear-filter {
            padding: 5px 10px;
            background-color: red;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            width: 80px;
            margin: 10px;
        }

        #clear-filter:hover {
            background-color: green;
            color: white;
            transition: background-color 0.3s ease;
        }

        .filter-bar {
            width: 50px;
            display: flex;
            align-items: center;
            position: relative;
            top: -420px;
            right: -800px;
            z-index: 1;
        }

        .filter-bar label {
            width: 500px;
            font-weight: bold;
            color: #333;
        }

        #filter-select {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.4);
        }

        #filter-select option {
            font-size: 15px;
            padding: 8px;
        }

        .bgoption {
            background-color: white;
        }

        .options-container {
            width: 100%;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 10px;
            border-radius: 5px;
            background-color: #05386B;
            height: 80px;
        }

        .options-container a {
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            border: 2px solid white;
            background-color: #3796A3;
            backdrop-filter: blur(5px);
            z-index: 1;
        }

        .options-container a:hover {
            background-color: #5CDB95;
            color: #000000;
            transform: translateY(-8px);
            box-shadow: 0 0 15px #5CDB95;
            transition: 0.1s ease-in-out;
        }

        .options-container a.active {
            background: linear-gradient(90deg, rgba(153, 30, 84, 1) 26%, rgba(17, 63, 119, 1) 100%);

        }

        .options-container a.active:hover {
            color: #fff;
            box-shadow: 0 0 15px #fff;
            ;
        }

        .hr-1 {
            border: 0;
            height: 1px;
            background-image: linear-gradient(to right, #6a0dad, #ffd700, #6a0dad);
        }

        /* REPORT BUTTON STYLE CODE STARTS */
        .delete-button {
            /* Add your styles here */
            padding: 10px 20px;
            background-color: #FF6347;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            margin-top: 1%;
        }

        .delete-button:hover {
            background-color: #FF4500;
            box-shadow: 0 0 15px rgba(255, 0, 0, 0.9);
            transform: translateY(-5px);
            transition: 0.2s ease-in-out;
        }

        .delete-button:focus {
            outline: none;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.3);
        }

        /* REPORT BUTTON STYLE CODE ENDS */
        .post-container::-webkit-scrollbar {
            width: 10px;
        }

        .post-container::-webkit-scrollbar-thumb {
            background-color: #435f75;
            border-radius: 5px;
        }

        .post-container::-webkit-scrollbar-thumb:hover {
            background-color: #3090C7;
        }

        .post-container {
            height: 500px;
            width: 100%;
            position: relative;
            top: -60px;
            border-radius: 10px;
            padding: 10px;
            box-sizing: border-box;
            margin: 10px auto;
        }

        .post {
            margin-bottom: 20px;
            border-bottom: 1px solid #ccc;
            padding: 5px;
            background-color: white;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.8);
            border-radius: 5px;
        }

        .post .details {
            margin-bottom: 10px;
            display: flex;
        }

        .post .details span {
            margin-right: 10px;
            display: inline-block;
            padding: 5px 10px;
            border-radius: 5px;
        }

        .post .tags {
            margin-top: 10px;
        }

        .post .tags span {
            display: inline-block;
            padding: 3px 6px;
            margin-right: 5px;
            border-radius: 3px;
        }

        .tag-username {
            background-color: #ffcc80;
            color: white;
            font-size: 13px;
            font-weight: bold;
            text-transform: capitalize;
        }

        .tag-username:hover {
            transform: translateY(-5px);
            transition: 0.2s ease-in-out;
            cursor: pointer;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.8);
        }

        .tag-date:hover {
            transform: translateY(-5px);
            transition: 0.2s ease-in-out;
            cursor: pointer;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.8);
        }

        .tag-type:hover {
            transform: translateY(-5px);
            transition: 0.2s ease-in-out;
            cursor: pointer;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.8);
        }

        .tag-location:hover {
            transform: translateY(-5px);
            transition: 0.2s ease-in-out;
            cursor: pointer;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.8);
        }

        .tag-phone:hover {
            transform: translateY(-5px);
            transition: 0.2s ease-in-out;
            cursor: pointer;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.8);
        }

        .tag-userid:hover {
            transform: translateY(-5px);
            transition: 0.2s ease-in-out;
            cursor: pointer;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.8);
        }

        .tag-location {
            background-color: #64b5f6;
            font-size: 13px;
            color: white;
            font-weight: bold;
        }

        .tag-phone {
            background-color: #81c784;
            font-size: 13px;
            color: white;
            font-weight: bold;
        }

        .tag-date {
            background-color: #e57373;
            font-size: 13px;
            color: white;
            font-weight: bold;
        }

        .tag-type {
            background-color: purple;
            font-size: 13px;
            color: white;
            font-weight: bold;
        }

        .tag-userid {
            background-color: coral;
            font-size: 13px;
            color: white;
            font-weight: bold;
        }

        .post .description {
            background-color: #DADBDD;
            /* background-color: #f9f9f9; */
            padding: 10px;
            border-radius: 5px;
            /* width: 70%; */
            text-transform: capitalize;
        }

        .post .description:hover {
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.8);
            transform: translateY(-5px);
            transition: 0.2s ease-in-out;
            cursor: pointer;
        }

        .search-bar {
            position: relative;
            top: -425px;
            z-index: 1;
            right: -1100px;
            margin-top: 10px;
            display: flex;
            align-items: center;
            /* justify-content: center; */
        }

        #search-input {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-right: 10px;
            width: 250px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.4);
        }

        #search-button {
            padding: 10px;
            background-color: #3090C7;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        #search-button:hover {
            background-color: #e57373;
            transition: 0.2s ease-out;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.4);
        }

        /* PAGINATION SECTION STARTS */
        .pagination {
            position: absolute;
            left: 750px;
            top: 850px;
            z-index: 1;
        }

        .pagination a {
            display: inline-block;
            padding: 8px 12px;
            margin: 0 5px;
            color: #333;
            text-decoration: none;
            border: 1px solid #ccc;
            border-radius: 4px;
            transition: background-color 0.3s, color 0.3s;

        }

        .pagination a.active {
            background-color: #007bff;
            color: #fff;
            border-color: #007bff;
        }

        .pagination a:hover {
            background-color: #6a0dad;
            border-color: #6a0dad;
        }

        .pagination a.disabled {
            pointer-events: none;
            color: #ccc;
            border-color: #ccc;
        }

        /* PAGINATION SECTION ENDS */
        /* ------------------------------------------------------------------------------------------------------------------------- */


        @media only screen and (min-width:300px) and (max-width:600px) {
            body {
                font-family: Arial, sans-serif;
                background: #ffffff;
                margin: 0;
                padding: 0;
                display: flex;
                height: 900px;
                width: 100%;
            }

            .main-container {
                display: flex;
                flex-wrap: wrap;
                width: 300%;
                margin: 20px auto;
                background-color: rgba(255, 255, 255, 0.8);
                backdrop-filter: blur(10px);
                padding: 50px;
                height: 500px;
                position: absolute;
                overflow-y: auto;
                left: 8%;
                border-radius: 10px;
                border: 1px solid rgba(255, 255, 255, 0.8);
                top: 150px;
                box-shadow: 0 0 20px rgba(0, 0, 0, 0.8);
                opacity: 0.9;
            }

            .post-container {
                height: 500px;
                width: 100%;
                position: relative;
                top: -30px;
                border-radius: 10px;
                padding: 10px;
                box-sizing: border-box;
                margin: 10px auto;
            }
        }
    </style>
    <script src="https://kit.fontawesome.com/4c43584236.js" crossorigin="anonymous"></script>
</head>

<body>
    <!-- FILTER DROPDOWN MENU STARTS -->
    <div class="filter-bar">
        <label for="filter-select">Filter Type:</label>
        <select id="filter-select" name="filter">
            <option value="Select Options">Select Options</option>
            <option value="Jobs">Jobs</option>
            <option value="Social Service">Social Service</option>
            <option value="Events">Events</option>
            <option value="News">News</option>
            <option value="Announcements">Announcements</option>
            <option value="Volunteer Opportunities">Volunteer Opportunities</option>
            <option value="Education">Education</option>
            <option value="Health and Wellness">Health and Wellness</option>
            <option value="Environment">Environment</option>
            <option value="Community Development">Community Development</option>
            <option value="Fundraising">Fundraising</option>
            <option value="Arts and Culture">Arts and Culture</option>
            <option value="Sports and Recreation">Sports and Recreation</option>
            <option value="Public Safety">Public Safety</option>
            <option value="Transportation">Transportation</option>
            <option value="Technology and Innovation">Technology and Innovation</option>
            <option value="Civic Engagement">Civic Engagement</option>
            <option value="Local Initiatives">Local Initiatives</option>
            <option value="Resources and Services">Resources and Services</option>
            <option value="Advocacy">Advocacy</option>
            <option value="Food and Nutrition">Food and Nutrition</option>
            <option value="Housing">Housing</option>
        </select>
        <button id="clear-filter">Clear Filter</button>
    </div>
    <!-- FILTER DROPDOWN MENU ENDS -->
    <!-- SEARCH BAR COMPLETE CODE STARTS -->
    <div class="search-bar">
        <input type="text" id="search-input" placeholder="Search...">
        <button id="search-button">Search</button>
    </div>
    <script>
        $(document).ready(function() {
            // Filter posts based on problem type
            $('#filter-select').change(function() {
                var selectedType = $(this).val().toLowerCase();
                if (selectedType === '') {
                    $('.post').show();
                } else {
                    $('.post').each(function() {
                        var type = $(this).find('.tag-type').text().toLowerCase();
                        if (type !== selectedType) {
                            $(this).hide();
                        } else {
                            $(this).show();
                        }
                    });
                }
            });
            // Clear Filter button click event
            $('#clear-filter').click(function() {
                // Reset the filter dropdown to the default option
                $('#filter-select').prop('selectedIndex', 0);
                // Show all posts
                $('.post').show();
            });
            // Function to filter and display posts based on search input
            function filterPosts(searchTerm) {
                $('.post').each(function() {
                    var description = $(this).find('.description').text().toLowerCase();
                    if (description.indexOf(searchTerm) === -1) {
                        $(this).hide();
                    } else {
                        $(this).show();
                    }
                });
            }
            // Search button click event
            $('#search-button').click(function() {
                var searchTerm = $('#search-input').val().toLowerCase();
                filterPosts(searchTerm);
            });
            // Clear search input and show all posts
            $('#search-input').on('input', function() {
                var searchTerm = $(this).val().toLowerCase();
                if (searchTerm === '') {
                    $('.post').show();
                }
            });
        });
    </script>
    <!-- SEARCH BAR COMPLETE CODE ENDS -->
    <!-- Main Container -->
    <div class="main-container">
        <div class="options-container">
            <?php
            // Display options
            foreach ($postOptions as $option) {
                $tableName = $option['tableName'];
                $title = $option['title'];
                $activeClass = ($selectedOption === $tableName) ? 'active' : '';
                echo '<a href="?option=' . $tableName . '" class="' . $activeClass . '">' . $title . '</a>';
            }
            ?>
        </div>


        <div class="post-container">
            <?php
            $selectedOption = isset($_GET['option']) ? $_GET['option'] : 'public_posts';
            // Calculate the total number of posts
            $totalPosts = getTotalPostsCount($selectedOption);

            // Define the number of posts to display per page
            $postsPerPage = 20;

            // Calculate the total number of pages
            $totalPages = ceil($totalPosts / $postsPerPage);

            // Get the current page number
            $currentPage = isset($_GET['page']) ? intval($_GET['page']) : 1;

            // Calculate the offset for the SQL query
            $offset = ($currentPage - 1) * $postsPerPage;

            // Display posts based on the selected option and current page
            foreach ($postOptions as $option) {
                $tableName = $option['tableName'];
                $title = '<h2 class="post-title">' . $option['title'] . '</h2>';
                $postIDColumn = $option['postIDColumn'];
                $postColumns = $option['postColumns'];
                $creationTimeColumn = $option['creationTimeColumn'];
                if ($selectedOption === $tableName) {
                    // Get the current page from the query parameter, default to 1
                    $currentPage = isset($_GET['page']) ? intval($_GET['page']) : 1;
                    // Number of posts to display per page
                    $postsPerPage = 4;

                    // Call the displayPostsPagination function
                    displayPostsPagination($tableName, $title, $postIDColumn, $postColumns, $creationTimeColumn, $currentPage, $postsPerPage);

                    // Add pagination links
                    $totalPosts = getTotalPostsCount($tableName);
                    $totalPages = ceil($totalPosts / $postsPerPage);
                }
            }
            ?>
        </div>
    </div>
    <div class="pagination">
        <?php
        for ($page = 1; $page <= $totalPages; $page++) {
            echo '<a href="?option=' . $selectedOption . '&page=' . $page . '"';
            if ($page === $currentPage) {
                echo ' class="active"';
            }
            echo '>' . $page . '</a>';
        }
        ?>
    </div>
</body>

</html>
<?php
// Close database connection
$conn->close();
?>