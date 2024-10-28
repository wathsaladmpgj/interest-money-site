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
    $salary = $_POST['salary'];
    $allowance = $_POST['allowance'];
    $provision = $_POST['provision'];

    // Validate the inputs (optional but recommended)
    if (empty($name) || empty($salary) || empty($allowance) || empty($provision)) {
        echo "All fields are required.";
    } else {
        // Prepare the SQL query to insert data
        $sql = "INSERT INTO employee_details (name, salary, allownce, privision) VALUES (?, ?, ?, ?)";

        // Prepare and bind the statement
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("siii", $name, $salary, $allowance, $provision);

        // Execute the statement and check for success
        if ($stmt->execute()) {
            echo "<script>";
            echo "alert('Employee details inserted successfully!')";
            echo "window.location.href = 'add_borrower.php';";
            echo "</script>";
        } else {
            echo "<script>";
            echo "alert('Error:')" . $stmt->error;
            echo "window.location.href = 'add_borrower.php';";
            echo "</script>";
        }

        // Close the statement
        $stmt->close();
    }
}

$conn->close();
?>