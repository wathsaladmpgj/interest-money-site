<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "interest";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$today = new DateTime();
$yesterday = (new DateTime())->modify('-1 day');
$query = "SELECT id, name, amount, total_arrears, lone_date, due_date FROM borrowers ORDER BY id ASC";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

while ($row = mysqli_fetch_assoc($result)) {
    $loan_taken_date = new DateTime($row['lone_date']);
    $due_date = new DateTime($row['due_date']);
    $days_passed = $due_date >= $today ? $loan_taken_date->diff($yesterday)->days : $loan_taken_date->diff($due_date)->days;

    $id = $row['id'];
    $update_query = "UPDATE borrowers SET days_passed = ? WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("ii", $days_passed, $id);
    $stmt->execute();
}

mysqli_close($conn);
?>
