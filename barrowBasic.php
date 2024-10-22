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

// Fetch borrowers
$sql = "SELECT id, name FROM borrowers";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borrowers List</title>
    <link rel="stylesheet" href="./borrowBasic.css">
</head>
<body>
    <h1>Borrowers</h1>
    <h2>NEW BORROWERS</h2>
    <ul>
    <?php
// Connect to the database
$conn = new mysqli('localhost', 'root', '', 'interest');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch borrowers along with their due_date
$sql = "SELECT id, name, due_date FROM borrowers";
$result = $conn->query($sql);

$new_borrowers_exist = false;
$old_borrowers_exist = false;

if ($result->num_rows > 0) {
    // Display new borrowers (with due_date today or later)
    while ($row = $result->fetch_assoc()) {
        $due_date = $row['due_date'];

        // Check if due_date is today or later
        if ($due_date >= date('Y-m-d')) {
            if (!$new_borrowers_exist) {
                $new_borrowers_exist = true;
            }
            echo "<li><a class='borrower-name' href='details.php?id=" . $row['id'] . "' data-id='" . $row['id'] . "'>" . $row['name'] . "</a></li>";
        }
    }
?></ul>
<h2>OLD BORROWERS</h2>
<ul>
<?php
    // Display old borrowers (with due_date before today)
    $result->data_seek(0); // Reset result pointer to fetch the rows again
    while ($row = $result->fetch_assoc()) {
        $due_date = $row['due_date'];

        // Check if due_date is before today
        if ($due_date < date('Y-m-d')) {
            if (!$old_borrowers_exist) {
                $old_borrowers_exist = true;
            }
            echo "<li><a class='borrower-name' href='details.php?id=" . $row['id'] . "' data-id='" . $row['id'] . "'>" . $row['name'] . "</a></li>";;
        }
    }

    echo "</ul>"; // Close the list
} else {
    echo "No borrowers found.";
}

?>



    </ul>

</body>
</html>

<?php
$conn->close();
?>
