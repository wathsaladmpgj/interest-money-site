<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'interest');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch borrowers with due_date today or later
$today = date('Y-m-d');
$sql_collect = "SELECT id, name, lone_number, due_date, rental FROM borrowers WHERE due_date >= ?";
$stmt = $conn->prepare($sql_collect);
$stmt->bind_param("s", $today);
$stmt->execute();
$result_collect = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Borrower Payments</title>
    <link rel="stylesheet" href="./css/month_summary.css">
</head>
<body>
    <h1>Borrower Payment Due</h1>
    <form method="POST" action="./collect_amount1.php">
        <table border="1">
            <tr>
                <th>Select</th>
                <th>Name</th>
                <th>Loan Number</th>
                <th>Due Date</th>
                <th>Rental</th>
            </tr>
            <?php while ($row = $result_collect->fetch_assoc()) { ?>
                <tr>
                    <td><input type="checkbox" name="borrower_ids[]" value="<?php echo $row['id']; ?>"></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['lone_number']; ?></td>
                    <td><?php echo $row['due_date']; ?></td>
                    <td><?php echo $row['rental']; ?></td>
                </tr>
            <?php } ?>
        </table>
        <br>
        <button type="submit">Enter Payments</button>
    </form>
</body>
</html>
<?php
$conn->close();
?>
