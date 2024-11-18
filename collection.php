<?php
// Set header to JSON
header('Content-Type: application/json');

// Database connection settings
$servername = "localhost";
$username = "root";     // replace with your database username
$password = "";     // replace with your database password
$dbname = "interest";       // replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL query to fetch the data
$sql = "SELECT month,monthly_payment_sum,interest_received FROM monthly_details";
$result = $conn->query($sql);

$data = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

// Return data as JSON
echo json_encode($data);

// Close the connection
$conn->close();
?>
