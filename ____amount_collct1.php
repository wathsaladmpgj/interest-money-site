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

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $nic = $_POST['nic'];
    $amount = $_POST['amount'];
    $interest_rate = $_POST['interestRate'];
    $lone_date = $_POST['date_lent'];
    $no_rental = $_POST['norental'];
    $address = $_POST['address'];

    $month_interest = $amount * $interest_rate / 100;
    $interest = $month_interest * 2;
    $rental = ($amount + $interest) / $no_rental;
    $agree_value = $rental * $no_rental;

    $date = new DateTime($lone_date);
    $date->modify("+".($no_rental - 1)." days");
    $due_date = $date->format('Y-m-d');

    $interest = $agree_value - $amount;
    $interest_day = $interest / $no_rental;

    $sql = "INSERT INTO borrowers (name, nic, address, amount, rental, agree_value, interest, interest_day, lone_date, no_rental, due_date) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssdddddsis", $name, $nic, $address, $amount, $rental, $agree_value, $interest, $interest_day, $lone_date, $no_rental, $due_date);

    if ($stmt->execute()) {
        echo "<script>";
        echo "alert('Borrower added successfully!');";
        echo "window.location.href = 'add_borrower.php';"; // Redirect after alert
        echo "</script>";
    } else {
        echo "Error: " . $conn->error;
    }

    $stmt->close();
}

$conn->close();
?>
