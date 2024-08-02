<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "urbanlink";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
<?php
// Calculate the date one month ago
$oneMonthAgo = date('Y-m-d', strtotime('-1 month'));

// SQL query to select and delete old posts
$sql = "DELETE FROM public_posts WHERE pp_date < DATE_ADD(CURDATE(), INTERVAL -1 MONTH)";
if ($conn->query($sql) === TRUE) {
    echo "Old posts deleted successfully.";
} else {
    echo "Error deleting old posts: " . $conn->error;
}
?>
