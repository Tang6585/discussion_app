<?php
require __DIR__ . '/config/mailer.php';

$result = sendMail("your_email@example.com", "Test Mail", "Hello! This is a test email from your forum.");

if ($result === true) {
    echo "✅ Email sent successfully!";
} else {
    echo "❌ Error: " . $result;
}
