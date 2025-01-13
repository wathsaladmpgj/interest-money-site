<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate email
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format.");
    }

    // Hash the password securely
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Generate a unique verification code
    $verification_code = bin2hex(random_bytes(16));

    // Database connection
    $conn = new mysqli('localhost', 'root', '', 'interest');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if email already exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        die("Email is already registered. Please use a different email.");
    }
    $stmt->close();

    // Insert new user
    $stmt = $conn->prepare("INSERT INTO users (email, password, verification_code) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $email, $password, $verification_code);

    if ($stmt->execute()) {
        // Send verification email
        require 'vendor/autoload.php'; // Ensure you have installed PHPMailer using Composer

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'janijaniwathsala@gmail.com'; // Your Gmail email
            $mail->Password = 'eojy grxh koja shni';       // Your Gmail App Password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Email content
            $mail->setFrom('janijaniwathsala@gmail.com', 'MASTER PC');
            $mail->addAddress($email);

            $verification_url = "https://chatgpt.com/?code=$verification_code";
            $mail->isHTML(true);
            $mail->Subject = 'Email Verification';
            $mail->Body = "Click <a href='$verification_url'>here</a> to verify your email.";

            $mail->send();
            echo "Registration successful! A verification email has been sent to your email.";
        } catch (Exception $e) {
            echo "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        echo "Error: Could not register the user. " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
