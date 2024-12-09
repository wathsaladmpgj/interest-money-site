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

// Fetch borrower details for the dropdown
$borrowerDetailsQuery = "SELECT id, name FROM borrower_details";
$borrowerDetailsResult = $conn->query($borrowerDetailsQuery);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $borrower_id = $_POST['borrower_id']; // Selected borrower ID
    $amount = $_POST['amount'];
    $interest_rate = $_POST['interestRate'];
    $lone_date = $_POST['date_lent'];
    $no_rental = $_POST['norental'];

    // Fetch borrower details (name, nic, address) based on the selected borrower ID
    $borrowerQuery = "SELECT name, nic, address FROM borrower_details WHERE id = ?";
    $stmt = $conn->prepare($borrowerQuery);
    $stmt->bind_param("i", $borrower_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $borrower = $result->fetch_assoc();

    if ($borrower) {
        $name = $borrower['name'];
        $nic = $borrower['nic'];
        $address = $borrower['address'];

        // Calculate loan number (lone_number)
        $loanNumberQuery = "SELECT MAX(lone_number) AS max_lone_number FROM borrowers WHERE borrower_details_id = ?";
        $stmt = $conn->prepare($loanNumberQuery);
        $stmt->bind_param("i", $borrower_id);
        $stmt->execute();
        $loanResult = $stmt->get_result();
        $loanData = $loanResult->fetch_assoc();

        // If this is the first loan, set lone_number to 1. Otherwise, increment the lone_number.
        $lone_number = $loanData['max_lone_number'] ? $loanData['max_lone_number'] + 1 : 1;

        // Loan calculations
        $month_interest = $amount * $interest_rate / 100;
        $interest = $month_interest * 2;
        $rental = ($amount + $interest) / $no_rental;
        $agree_value = $rental * $no_rental;

        $date = new DateTime($lone_date);
        $date->modify("+".($no_rental - 1)." days");
        $due_date = $date->format('Y-m-d');

        $interest = $agree_value - $amount;
        $interest_day = $interest / $no_rental;

        // Insert loan details into borrowers table
        $sql = "INSERT INTO borrowers (borrower_details_id, lone_number, name, nic, address, amount, rental, agree_value, interest, interest_day, lone_date, no_rental, due_date) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "iisssdddddsis",
            $borrower_id,
            $lone_number,
            $name,
            $nic,
            $address,
            $amount,
            $rental,
            $agree_value,
            $interest,
            $interest_day,
            $lone_date,
            $no_rental,
            $due_date
        );

        if ($stmt->execute()) {
            echo "<script>";
            echo "alert('Loan added successfully for borrower: $name!');";
            echo "window.location.href = 'borrow_add.php';"; // Redirect after alert
            echo "</script>";
        } else {
            echo "Error: " . $conn->error;
        }

        $stmt->close();
    } else {
        echo "Error: Borrower details not found!";
    }
}

$conn->close();
?>
