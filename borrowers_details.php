<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "interest"; // Replace with your database name

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to fetch borrower details with calculations
$sql = "SELECT 
    bd.id AS borrower_id,
    bd.name AS borrower_name,
    bd.nic AS borrower_nic,
    bd.address AS borrower_address,
    COUNT(b.lone_number) AS loan_count,
    SUM(b.amount) AS total_loan,
    SUM(b.total_payments) AS loan_settled,
    SUM(b.total_arrears) AS loan_in_arrears,
    SUM(b.amount - b.total_payments) AS current_loan_balance
FROM 
    borrower_details bd
LEFT JOIN 
    borrowers b ON bd.id = b.borrower_details_id
GROUP BY 
    bd.id;
";

$result = $conn->query($sql);

// Query to calculate total loan amount for all borrowers
$totalLoansQuery = "SELECT SUM(amount) AS total_loans FROM borrowers";
$totalLoansResult = $conn->query($totalLoansQuery);
$totalLoans = $totalLoansResult->fetch_assoc()['total_loans'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borrower Details</title>
    <link rel="stylesheet" href="./css/month_summary.css">
</head>
<body>

<h1>Borrower Details</h1>
<h3>Total Loan Amount for All Borrowers: <?php echo number_format($totalLoans, 2); ?></h3>
<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Name</th>
            <th>NIC</th>
            <th>Address</th>
            <th>Loan Count</th>
            <th>Total Loan Amount</th>
            <th>Loan Settled</th>
            <th>Loan in Arrears</th>
            <th>Current Loan Balance</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result->num_rows > 0): ?>
            <?php 
             $row_number = 1;
                while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row_number; ?></td>
                    <td><?php echo htmlspecialchars($row['borrower_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['borrower_nic']); ?></td>
                    <td><?php echo htmlspecialchars($row['borrower_address']); ?></td>
                    <td><?php echo htmlspecialchars($row['loan_count']); ?></td>
                    <td><?php echo number_format($row['total_loan'], 2); ?></td>
                    <td><?php echo number_format($row['loan_settled'], 2); ?></td>
                    <td><?php echo number_format($row['loan_in_arrears'], 2); ?></td>
                    <td><?php echo number_format($row['current_loan_balance'], 2); ?></td>
                </tr>
            <?php 
                $row_number++;
                endwhile; 
            ?>
        <?php else: ?>
            <tr>
                <td colspan="8">No borrower details found.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>

<?php
$conn->close();
?>
