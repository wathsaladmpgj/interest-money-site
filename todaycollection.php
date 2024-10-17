<?php
// Connect to the database
$servername = "localhost";
$username = "root";
$password = ""; // Your MySQL password
$dbname = "interest"; // Your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the date from user input (via GET request, for example)
$selected_date = $_GET['date'];

// SQL query to fetch names and amounts for the selected payment date
$sql = "SELECT borrowers.name, payments.rental_amount 
        FROM payments 
        JOIN borrowers ON payments.borrower_id = borrowers.id 
        WHERE payments.payment_date = '$selected_date'";

$result = $conn->query($sql);

// Prepare data for JSON encoding
$payments = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $payments[] = $row;
    }
}

// Send the data as JSON
echo json_encode($payments);

$conn->close();
?>
