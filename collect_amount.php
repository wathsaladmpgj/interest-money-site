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
                $conn = new mysqli('localhost', 'root', '', 'interest');
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // Fetch borrower names
                $sql = "SELECT id, name FROM borrowers";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='".htmlspecialchars($row['id'], ENT_QUOTES)."'>".htmlspecialchars($row['name'], ENT_QUOTES)."</option>";
                    }
                } else {
                    echo "<option>No Name Available</option>";
                }

                $stmt->close();
                $conn->close();
            ?>
        </select>
        <p id="noResults" style="display:none; color:red;">No matching names found</p><br><br>

        <script>
            const input = document.getElementById('nameInput');
            const select = document.getElementById('showName');
            const noResults = document.getElementById('noResults');

            input.addEventListener('input', function() {
                const filter = input.value.toLowerCase();
                const options = select.options;
                let matchFound = false;

                for (let i = 0; i < options.length; i++) {
                    const name = options[i].text.toLowerCase();
                    if (name.includes(filter)) {
                        options[i].style.display = '';
                        matchFound = true;
                    } else {
                        options[i].style.display = 'none';
                    }
                }

                noResults.style.display = matchFound ? 'none' : 'block';

                // Automatically select the first visible option
                for (let i = 0; i < options.length; i++) {
                    if (options[i].style.display !== 'none') {
                        select.selectedIndex = i;
                        break;
                    }
                }
            });
        </script>

        <label for="du_date">Due Date</label><br>
        <input type="date" id="du_date" name="du_date" required autocomplete="off"><br><br>

        <label for="payment_date">Payment Date</label><br>
        <input type="date" id="payment_date" name="payment_date" required autocomplete="off"><br><br>

        <label for="payment">Payment Amount</label><br>
        <input type="number" id="payment" name="payment" step="0.01" required autocomplete="off"><br><br>

        <input type="submit" value="Add Payment">
        <button type="button" onclick="window.location.href='home.php'">Back</button>
    </form>
</body>
</html>
