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
            echo "Employee details inserted successfully!";
        } else {
            echo "Error: " . $stmt->error;
        }

        // Close the statement
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enter Employee Details</title>
    <link rel="stylesheet" href="./css/enter_employee.css">
</head>
<body>
    <nav>
        <li><a href="./employee.php">UPDATE EMPLOYEE</a></li>
        <li><a href="./employee_details.php">EMPLOYEE DETAILS</a></li>
    </nav>
    <h2>Enter New Employee Details</h2>
    <form action="" method="POST">
        <label for="name">Employee Name</label>
        <input type="text" id="name" name="name" required><br><br>

        <label for="salary">Salary</label>
        <input type="number" id="salary" name="salary" required><br><br>

        <label for="allowance">Allowance</label>
        <input type="number" id="allowance" name="allowance" required><br><br>

        <label for="provision">Provision</label>
        <input type="number" id="provision" name="provision" required><br><br>

        <input type="submit" value="Submit">
    </form>
</body>
</html>
