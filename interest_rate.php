
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Interest Rates</title>
    <link rel="stylesheet" href="./css/interest_rate.css">
</head>
<body>
    <h2>INTEREST RATE</h2>
    <form action="./interest_rate1.php" method="POST">
        <label for="interestRate1">Interest 1 (%)</label>
        <input type="number" id="interestRate1" name="interestRate1" min="0" max="100" step="0.01" required><br><br>

        <label for="interestRate2">Interest 2 (%)</label>
        <input type="number" id="interestRate2" name="interestRate2" min="0" max="100" step="0.01" required><br><br>

        <label for="interestRate3">Interest 3 (%)</label>
        <input type="number" id="interestRate3" name="interestRate3" min="0" max="100" step="0.01" required><br><br>

        <label for="interestRate4">Interest 4 (%)</label>
        <input type="number" id="interestRate4" name="interestRate4" min="0" max="100" step="0.01" required><br><br>

        <input type="submit" value="Submit">
    </form>
</body>
</html>
