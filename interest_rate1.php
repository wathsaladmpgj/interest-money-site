<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "interest";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $interest1 = $_POST['interest1'];
    $interest2 = $_POST['interest2'];
    $interest3 = $_POST['interest3'];
    $interest4 = $_POST['interest4'];
    $updated_month = $_POST['updated_month'];

    // Find the latest record and set its end_month to one month before the new updated_month
    $previous_record_query = "SELECT id, updated_month FROM interestrate ORDER BY updated_month DESC LIMIT 1";
    $result = $conn->query($previous_record_query);

    if ($result->num_rows > 0) {
        $previous_record = $result->fetch_assoc();
        $previous_id = $previous_record['id'];
        $previous_updated_month = $previous_record['updated_month'];

        // Calculate end_month as one month before updated_month
        $end_month_date = DateTime::createFromFormat('Y/m', $updated_month);
        $end_month_date->modify('-1 month');
        $end_month = $end_month_date->format('Y/m');

        // Update the previous record's end_month
        $update_end_month_query = "UPDATE interestrate SET end_month = '$end_month' WHERE id = $previous_id";
        $conn->query($update_end_month_query);
    }

    // Insert the new interest rate record
    $insert_query = "INSERT INTO interestrate (interest1, interest2, interest3, interest4, updated_month) 
                     VALUES ($interest1, $interest2, $interest3, $interest4, '$updated_month')";

    if ($conn->query($insert_query)) {
        echo "New interest rates added successfully!";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>