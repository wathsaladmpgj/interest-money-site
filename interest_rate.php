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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $interest1 = $_POST['interestRate1'];
    $interest2 = $_POST['interestRate2'];
    $interest3 = $_POST['interestRate3'];
    $interest4 = $_POST['interestRate4'];

    $sql = "UPDATE interestrate SET interest1 = ?, interest2 = ?, interest3 = ?, interest4 = ? WHERE id = 1";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("dddd", $interest1, $interest2, $interest3, $interest4);

    if ($stmt->execute()) {
        echo "<script>alert('Interest rates updated successfully!');</script>";
        // Optionally redirect to avoid resubmission
        // header("Location: /path/to/your/success/page.php");
    } else {
        echo "Error: " . $conn->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Interest Rates</title>
    <link rel="stylesheet" href="./css/interest_rate.css">
</head>
<body>
    <h2>INTEREST RATE</h2>
    <form action="" method="POST">
        <label for="interestRate1">Interest 1 (%)</label>
        <input type="number" id="interestRate1" name="interestRate1" min="0" max="100" step="0.01" required><br><br>

        <label for="interestRate2">Interest 2 (%)</label>
        <input type="number" id="interestRate2" name="interestRate2" min="0" max="100" step="0.01" required><br><br>

        <label for="interestRate3">Interest 3 (%)</label>
        <input type="number" id="interestRate3" name="interestRate3" min="0" max="100" step="0.01" required><br><br>

        <label for="interestRate4">Interest 4 (%)</label>
        <input type="number" id="interestRate4" name="interestRate4" min="0" max="100" step="0.01" required><br><br>

        <input type="submit" value="Submit">
    </form>
</body>
</html>
