<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "interest";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
date_default_timezone_set('Asia/Colombo');
$today = new DateTime();
$yesterday = (new DateTime())->modify('-1 day');
$query_due_rental = "SELECT id, name, amount, total_arrears, lone_date, due_date FROM borrowers ORDER BY id ASC";
$result_due_rental = mysqli_query($conn, $query_due_rental);

while ($row = mysqli_fetch_assoc($result_due_rental)) {
    $loan_date = new DateTime($row['lone_date']);
    $due_date = new DateTime($row['due_date']);
    $days_passed = $due_date >= $today ? $loan_date->diff($yesterday)->days : $loan_date->diff($due_date)->days;

    $id = $row['id'];
    $update_query = "UPDATE borrowers SET days_passed = ? WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("ii", $days_passed, $id);
    $stmt->execute();
}

mysqli_close($conn);
?>
