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
    $id = $_POST['id'];

    // Delete borrower from database
    $sql = "DELETE FROM borrowers WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "Are you sure you want to delete this borrower";
    } else {
        echo "Error: " . $conn->error;
    }

    

    $stmt->close();
}

$conn->close();
?>
