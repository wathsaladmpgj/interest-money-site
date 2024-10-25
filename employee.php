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
    $salary = $_POST['salary'];
    $allownce = $_POST['allownce'];
    $privision = $_POST['privision'];
    $employee_id = $_POST['employee_id'];  // Get employee_id from the dropdown

    // Validate the inputs (optional but recommended)
    if (empty($salary) || empty($allownce) || empty($privision) || empty($employee_id)) {
        echo "All fields are required.";
    } else {
        // Prepare the SQL query to update data
        $sql = "UPDATE employee_details SET salary = ?, allownce = ?, privision = ? WHERE id = ?";

        // Prepare and bind the statement
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiii",$salary, $allownce, $privision,$employee_id);

        // Execute the statement and check for success
        if ($stmt->execute()) {
            echo "<script>";
            echo "alert('Employee details updated successfully!')";
            echo "</script>";
            
        } else {
            echo "Error: " . $stmt->error;
        }

        // Close the statement
        $stmt->close();
    }
}

// Fetch all employee names for the dropdown
$sql = "SELECT id, name FROM employee_details";
$result = $conn->query($sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Employee Details</title>
    <link rel="stylesheet" href="./css/employee.css">
</head>
<body>
    <nav>
        <li><a href="./enter_employee.php">ENTER EMPLOYEE</a></li>
        <li><a href="./employee_details.php">EMPLOYEE DETAILS</a></li>
    </nav>
    <h2>Update Employee Details</h2>
    <form action="" method="POST">
        <div class="form-group">
        <label for="employee_id">Select Employee</label>
        <select id="employee_id" name="employee_id" required>
            <option value="">Select an employee</option>
            <?php
            if ($result->num_rows > 0) {
                // Output data for each employee
                while ($row = $result->fetch_assoc()) {
                    echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
                }
            } else {
                echo '<option value="">No employees found</option>';
            }
            ?>
        </select><br><br>
        </div>
        

        <div class="form-group">
        <label for="salary">Salary</label>
        <input type="number" id="salary" name="salary" required>
        </div>
        

        <div class="form-group">
        <label for="allownce">Allowance</label>
        <input type="number" id="allownce" name="allownce" required>
        </div>
       
        <div class="form-group">
        <label for="privision">Provision</label>
        <input type="number" id="privision" name="privision" required>
        </div>
        

        <input type="submit" value="Update">
    </form>
</body>
</html>
