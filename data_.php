<?php
// Database connection and query logic
$conn = new mysqli('localhost', 'root', '', 'interest');
$sql = "SELECT month,stock_increase_percentage FROM monthly_savings ORDER BY month ASC LIMIT 10";
$result = $conn->query($sql);

$data = array();
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
$conn->close();
?>