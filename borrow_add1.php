<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "interest"; // Ensure this is your actual database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate form inputs
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $nic = isset($_POST['nic']) ? trim($_POST['nic']) : '';
    $address = isset($_POST['address']) ? trim($_POST['address']) : '';

    if (!empty($name) && !empty($nic) && !empty($address)) {
        $sql = "INSERT INTO borrower_details (name, nic, address) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("sss", $name, $nic, $address);

            if ($stmt->execute()) {
                echo "<script>";
                echo "alert('Borrower added successfully!');";
                echo "window.location.href = 'borrow_add.php';"; // Redirect after alert
                echo "</script>";
            } else {
                echo "Error: " . $stmt->error;
            }

            $stmt->close();
        } else {
            echo "Error preparing statement: " . $conn->error;
        }
    } else {
        echo "Please fill in all required fields.";
    }
}

$conn->close();
?>
