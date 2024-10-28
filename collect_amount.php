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
    
    <form action="./collect_amount1.php" method="post">
    <label for="showName">Select Name</label><br>
<input type="text" id="nameInput" placeholder="Type to search..."><br><br>
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

<script>
    const input = document.getElementById('nameInput');
    const select = document.getElementById('showName');

    input.addEventListener('input', function() {
        const filter = input.value.toLowerCase();
        const options = select.options;

        // Loop through the options and hide those that don't match
        for (let i = 0; i < options.length; i++) {
            const name = options[i].text.toLowerCase();
            options[i].style.display = name.includes(filter) ? '' : 'none';
        }

        // If there's a match, select the first matching option
        for (let i = 0; i < options.length; i++) {
            if (options[i].style.display !== 'none') {
                select.selectedIndex = i; // Select the matching option
                break;
            }
        }
    });
</script>

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



</body>
</html>
