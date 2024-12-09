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
    $employee_id = $_POST['employee_id']; // Get employee_id from the dropdown
    $payment_date = $_POST['payment_date'];
    $payment_month = $_POST['payment_month'];

    // Validate the inputs (optional but recommended)
    if (empty($employee_id) || empty($payment_date) || empty($payment_month)) {
        echo "All fields are required.";
    } else {
        // Prepare the SQL query to insert data into payment_details
        $sql = "INSERT INTO employee_payment_details (employee_id, salary, allownce, privision, payment_date, payment_month) 
                VALUES (?, ?, ?, ?, ?, ?)";

        // Prepare and bind the statement
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiiiss", $employee_id, $salary, $allownce, $privision, $payment_date, $payment_month);

        // Execute the statement and check for success
        if ($stmt->execute()) {
            echo "<script>alert('Payment details added successfully!')</script>";
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
    <h2>Update Employee Payment Details</h2>
    <form action="" method="POST">
        <div class="form-group">
            <label for="employee_id">Select Employee</label>
            <select id="employee_id" name="employee_id" required>
                <option value="">Select an employee</option>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
                    }
                } else {
                    echo '<option value="">No employees found</option>';
                }
                ?>
            </select>
        </div>

        <div class="form-group">
            <label for="salary">Salary</label>
            <input type="number" id="salary" name="salary">
        </div>

        <div class="form-group">
            <label for="allownce">Allowance</label>
            <input type="number" id="allownce" name="allownce">
        </div>

        <div class="form-group">
            <label for="privision">Provision</label>
            <input type="number" id="privision" name="privision">
        </div>

        <div class="form-group">
            <label for="payment_date">Payment Date</label>
            <input type="date" id="payment_date" name="payment_date" required>
        </div>

        <br>
        <div class="form-group">
            <label for="payment_month">Payment Month</label>
            <select id="payment_month" name="payment_month" required>
                <option value="">Select a month</option>
                <?php
                $currentYear = date("Y");
                for ($m = 1; $m <= 12; $m++) {
                    $monthNumber = str_pad($m, 2, "0", STR_PAD_LEFT);
                    $monthName = date("F", mktime(0, 0, 0, $m, 1));
                    echo '<option value="' . $currentYear . '-' . $monthNumber . '">' . $currentYear . '-' . $monthNumber . ' (' . $monthName . ')</option>';
                }
                ?>
            </select>
        </div><br>

        <input type="submit" value="Add Payment Details">
    </form>
</body>
</html>
