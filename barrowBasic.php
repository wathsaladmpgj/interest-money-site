<?php
// Database connection
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

// Fetch borrowers and update status
$sql = "SELECT id, name, due_date, total_payments, agree_value FROM borrowers";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $id = $row['id'];
        $due_date = $row['due_date'];
        $total_payments = $row['total_payments'];
        $agree_value = $row['agree_value'];

        // Determine status
        if ($total_payments == $agree_value) {
            $status = 'yes'; // Settled
        } elseif ($due_date < date('Y-m-d') && $total_payments < $agree_value) {
            $status = 'no'; // Arrears
        } elseif ($due_date >= date('Y-m-d') && $total_payments < $agree_value) {
            $status = 'con'; // Currently paying
        }

        // Update status in the database
        $update_sql = "UPDATE borrowers SET status = '$status' WHERE id = $id";
        $conn->query($update_sql);
    }
}

// Fetch updated borrowers to display
$sql = "SELECT id, name, status FROM borrowers";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borrowers List</title>
    <link rel="stylesheet" href="./css/borrowBasic.css">
</head>
<body>
    <h1>Borrowers</h1>
    <!-- Section for new borrowers -->
    <h2>NEW BORROWERS</h2>
    <ul>
        <?php
        $sql = "SELECT id, name, status FROM borrowers WHERE status = 'con'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<li><a class='borrower-name' href='details.php?id=" . $row['id'] . "'>" . $row['name'] . "</li>";
            }
        } else {
            echo "<li>No new borrowers found.</li>";
        }
        ?>
    </ul>

    <hr>
    <!-- Section for borrowers in arrears -->
    <h2>ARREARS BORROWERS</h2>
    <ul>
        <?php
        $sql = "SELECT id, name, status FROM borrowers WHERE status = 'no'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<li><a class='borrower-name' href='details.php?id=" . $row['id'] . "'>" . $row['name'] . "</li>";
            }
        } else {
            echo "<li>No borrowers in arrears found.</li>";
        }
        ?>
    </ul>

    <!-- Section for settled borrowers -->
     <hr>
    <h2>SETTLED BORROWERS</h2>
    <ul>
        <?php
        $sql = "SELECT id, name, status FROM borrowers WHERE status = 'yes'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<li><a class='borrower-name' href='details.php?id=" . $row['id'] . "'>" . $row['name'] .  "</li>";
            }
        } else {
            echo "<li>No settled borrowers found.</li>";
        }
        ?>
    </ul>

</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
