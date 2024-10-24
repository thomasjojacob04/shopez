<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $phone = $_POST['phone'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    // Process the form data here, such as saving it to a database or sending an email
    // For example, you can use the mail function to send the message via email

    $to = "thomasjo494@gmail.com";  // Replace with your email address
    $headers = "From: contact_form@example.com";

    if (mail($to, $subject, $message . "\nPhone: " . $phone, $headers)) {
        echo "Message sent successfully!";
    } else {
        echo "Failed to send message.";
    }
}
?>

