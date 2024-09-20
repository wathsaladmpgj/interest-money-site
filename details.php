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

$borrower_id = $_GET['id'];

// Fetch borrower details
$sql = "SELECT * FROM borrowers WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $borrower_id);
$stmt->execute();
$result = $stmt->get_result();
$borrower = $result->fetch_assoc();

// Calculate the date difference
$loan_date = new DateTime($borrower['lone_date']);
$due_date = new DateTime($borrower['due_date']);
$interval = $loan_date->diff($due_date);  // Get date difference

// Create an array to hold the dates from the loan date to the due date
$calendar_dates = array();
$days_difference = $interval->days;

for ($i = 1; $i <= $days_difference; $i++) {
    $loan_date_clone = clone $loan_date;  // Clone the loan date so that we don't modify the original
    $loan_date_clone->modify("+$i day");
    $calendar_dates[] = $loan_date_clone->format('Y-m-d');  // Store the date in the array
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borrower Details</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>Details for <?php echo $borrower['name']; ?></h1>
    <p><strong>Loan Amount:</strong> Rs.<?php echo $borrower['amount']; ?></p>
    <p><strong>Rental:</strong> <?php echo $borrower['rental']; ?></p>
    <p><strong>Agree Value:</strong> Rs.<?php echo $borrower['agree_value']; ?></p>
    <p><strong>Interest:</strong> Rs.<?php echo $borrower['interest']; ?></p>
    <p><strong>Interest per Day:</strong> Rs.<?php echo $borrower['interest_day']; ?></p>
    <p><strong>Loan Date:</strong> <?php echo $borrower['lone_date']; ?></p>
    <p><strong>Number of Rentals:</strong> <?php echo $borrower['no_rental']; ?></p>
    <p><strong>Due Date:</strong> <?php echo $borrower['due_date']; ?></p>
    <p><strong>Total Payment:</strong> Rs.</p>
    <p><strong>Arrears:</strong> Rs.</p>
    <p><strong>Closing Date:</strong></p>

    <h2>Calendar from Loan Date to Due Date:</h2>

    <table>
        <tr>
            <th>Date</th>
            <th>Status</th>
        </tr>
        <?php foreach ($calendar_dates as $date): ?>
        <tr>
            <td><?php echo $date; ?></td>
            <td>Not Paid</td> <!-- You can modify this based on payment status -->
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
