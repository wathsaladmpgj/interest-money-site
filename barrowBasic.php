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
        if ($total_payments >= $agree_value) {
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
        $sql_con = "SELECT id, name, status FROM borrowers WHERE status = 'con'";
        $result_con = $conn->query($sql_con);

        if ($result_con->num_rows > 0) {
            while ($row_con = $result_con->fetch_assoc()) {
                echo "<li><a class='borrower-name' href='details.php?id=" . $row_con['id'] . "'>" . $row_con['name'] . "</a></li>";
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
        $sql_no = "SELECT id, name, status FROM borrowers WHERE status = 'no'";
        $result_no = $conn->query($sql_no);

        if ($result_no->num_rows > 0) {
            while ($row_no = $result_no->fetch_assoc()) {
                echo "<li><a class='borrower-name' href='details.php?id=" . $row_no['id'] . "'>" . $row_no['name'] . "</a></li>";
            }
        } else {
            echo "<li>No borrowers in arrears found.</li>";
        }
        ?>
    </ul>

    <hr>

    <!-- Section for settled borrowers -->
    <h2>SETTLED BORROWERS</h2>
    <ul>
        <?php
        $sql_yes = "SELECT id, name, status FROM borrowers WHERE status = 'yes'";
        $result_yes = $conn->query($sql_yes);

        if ($result_yes->num_rows > 0) {
            while ($yes_row = $result_yes->fetch_assoc()) {
                echo "<li><a class='borrower-name' href='details.php?id=" . $yes_row['id'] . "'>" . $yes_row['name'] . "</a></li>";
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
