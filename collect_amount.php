<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Collect Amount</title>
    <link rel="stylesheet" href="./css/collectAmount.css">
</head>
<body>
    <h2>Add Payment Details</h2>
    
    <form action="" method="post">
        <label for="showName">Select Name</label><br>
        <select name="showName" id="showName" required autocomplete="off">
        <?php 
        // Connect to the database
        $conn = new mysqli('localhost', 'root', '', 'interest');
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Fetch names of borrowers whose due_date is today or later
        $sql = "SELECT id, name FROM borrowers WHERE due_date >= CURDATE()";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // Loop through each row and create an option element
            while ($row = $result->fetch_assoc()) {
                echo "<option value='".$row['id']."'>".$row['name']."</option>";
            }
        } else {
            echo "<option>No Name available</option>";
        }

        $conn->close();
        ?>
        </select><br><br>

        <label for="du_date">Due Date</label><br>
        <input type="date" id="du_date" name="du_date" required autocomplete="off"><br><br>

        <label for="payment_date">Payment Date</label><br>
        <input type="date" id="payment_date" name="payment_date" required autocomplete="off"><br><br>

        <label for="payment">Payment Amount</label><br>
        <input type="number" id="payment" name="payment" step="0.01" required autocomplete="off"><br><br>

        <input type="submit" value="Add Payment">
        <button type="button" onclick="goBack()">Back</button> <!-- Back button -->
    </form>

    <script>
        function goBack() {
            window.history.back(); // Go back to the previous page
        }
    </script>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $borrower_id = $_POST['showName'];
    $du_date = $_POST['du_date'];
    $payment_date = $_POST['payment_date'];
    $payment_amount = $_POST['payment'];

    // Reconnect to the database
    $conn = new mysqli('localhost', 'root', '', 'interest');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Insert payment details into payments table
    $sql = "INSERT INTO payments (borrower_id, du_date, rental_amount, payment_date) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isss", $borrower_id, $du_date, $payment_amount, $payment_date);

    if ($stmt->execute()) {
        // Redirect to the previous page after successful insertion
        echo "<script>alert('Payment added successfully!');</script>"; // Adjust this line if needed
        exit(); // Ensure no further code is executed after redirection
    } else {
        // Use alert box for errors
        echo "<script>alert('Error: " . addslashes($conn->error) . "');</script>"; // Escape quotes for JavaScript
    }

    // Close the connection
    $stmt->close();
    $conn->close();
}
?>

</body>
</html>
