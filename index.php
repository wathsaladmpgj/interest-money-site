<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<script>
    function updatePayments() {
        const xhr = new XMLHttpRequest();
        xhr.open("GET", "no_of_rental.php", true);
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                console.log(xhr.responseText); // You can remove this line in production
            }
        };
        xhr.send();
    }

    // Call updatePayments() every 5 minutes (300000 milliseconds)
    setInterval(updatePayments, 300000);
</script>

</body>
</html>