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

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $nic = $_POST['nic'];
    $amount = $_POST['amount'];
    $interest_rate = $_POST['interestRate'];
    $lone_date = $_POST['date_lent'];
    $no_rental = $_POST['norental'];


    $month_interest = $amount * $interest_rate/100;
    $interest = $month_interest*2;
    $rental = ($amount+$interest)/$no_rental;
    // Calculate the agreed value
    $agree_value = $rental * $no_rental;

    // Calculate the due date by adding the number of rental periods to the lone date
    $date = new DateTime($lone_date); // Create DateTime object from loan date
    $date->modify("+$no_rental days"); // Add the number of rental months to the date
    $due_date = $date->format('Y-m-d'); // Convert back to string

    $interest = $agree_value - $amount;
    $interest_day = $interest/$no_rental;

    // Insert data into the borrowers table
    $sql = "INSERT INTO borrowers (name,nic, amount, rental, agree_value,interest,interest_day, lone_date, no_rental, due_date) 
            VALUES (?,?, ?, ?, ?,?, ?,?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdsssssss", $name,$nic, $amount, $rental, $agree_value,$interest,$interest_day, $lone_date, $no_rental, $due_date);

    if ($stmt->execute()) {
        echo "<script>";
        echo "alert('Borrower added successfully!')";
        echo "</script>";
    } else {
        echo "Error: " . $conn->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Borrower</title>
    <link rel="stylesheet" href="./add_borrow.css">
</head>
<body>
    <h1>Add New Borrower</h1>

    <form action="add_borrower.php" method="post">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required><br><br>

        <label for="nic">Enter NIC Number</label>
        <input type="text" id="nic" name="nic" required><br><br>

        <label for="amount">Loan Amount:</label>
        <input type="number" id="amount" name="amount" step="0.01" required><br><br>

        <label for="interestRate">Interest Rate (%):</label>
        <input type="number" id="interestRate" name="interestRate" min="0" max="100" step="0.01" required><br><br>


        <label for="date_lent">Loan Date:</label>
        <input type="date" id="date_lent" name="date_lent" required><br><br>

        <label for="norental">Number of Rentals:</label>
        <input type="number" id="norental" name="norental" required><br><br>

        <input type="submit" value="Submit">
    </form>
</body>
</html>

<?php
$conn->close();
?>
