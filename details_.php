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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borrower Details</title>
</head>
<body>
    <h1>Details for <?php echo $borrower['name']; ?></h1>
    <p><strong>Lone Amount:</strong> Rs.<?php echo $borrower['amount']; ?></p>
    <p><strong>Rental:</strong> <?php echo $borrower['rental']; ?></p>
    <p><strong>Agree Value:</strong> Rs.<?php echo $borrower['agree_value']; ?></p>
    <p><strong>Interest</strong> Rs.<?php echo $borrower['interest']; ?></p>
    <p><strong>Interest for Day</strong> Rs.<?php echo $borrower['interest_day']; ?></p>
    <p><strong>Lone Date:</strong> <?php echo $borrower['lone_date']; ?></p>
    <p><strong>NO of Rental:</strong> <?php echo $borrower['no_rental']; ?></p>
    <p><strong>Due Date:</strong> <?php echo $borrower['due_date']; ?></p>
    <p><strong>Total Payment:</strong> Rs.</p>
    <p><strong>Arrears:</strong> Rs.</p>
    <p><strong>Closing Date:</strong></p>

    <form action="">
        <label for=""></label>
    </form>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
