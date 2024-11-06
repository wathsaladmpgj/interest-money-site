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
        echo "<script>";
        echo "alert('Interest rates updated successfully!');";
        echo "window.location.href = 'monthly_details.php';";
        echo "</script>";
        // Optionally redirect to avoid resubmission
        // header("Location: /path/to/your/success/page.php");
    } else {
        echo "<script>";
        echo "alert('Error:');";
        echo "window.location.href = 'monthly_details.php';";
        echo "</script>";
    }

    $stmt->close();
}

$conn->close();
?>