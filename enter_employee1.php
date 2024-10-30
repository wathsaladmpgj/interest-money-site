<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "interest"; // Change this to your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the form data
    $name = $_POST['name'];
    $nic = $_POST['nic'];
    
    // Validate the inputs (optional but recommended)
    if (empty($name) || empty($nic)) {
        echo "All fields are required.";
    } else {
        // Prepare the SQL query to insert data
        $sql = "INSERT INTO employee_details (name, nic) VALUES (?, ?)";

        // Prepare and bind the statement
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $name, $nic);

        // Execute the statement and check for success
        if ($stmt->execute()) {
            echo "<script>";
            echo "alert('Employee details inserted successfully!');"; // Add semicolon here
            echo "window.location.href = 'enter_employee.php';"; // Add semicolon here
            echo "</script>";
        } else {
            echo "<script>";
            echo "alert('Error: " . $stmt->error . "');"; // Add semicolon here and fix error display
            echo "window.location.href = 'enter_employee.php';"; // Add semicolon here
            echo "</script>";
        }

        // Close the statement
        $stmt->close();
    }
}

$conn->close();
?>
