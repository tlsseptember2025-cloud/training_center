<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '//PHPMailer/src/SMTP.php';
require_once __DIR__ . '/PHPMailer/src/Exception.php';


function send_email($new_email, $subject, $body){
     
    $mail2 = new PHPMailer(true);

    try {
        // SMTP Settings
        $mail2->isSMTP();
        $mail2->Host       = 'smtp.gmail.com';  
        $mail2->SMTPAuth   = true;
        $mail2->Username   = 'ramiwahdan2023@gmail.com';
        $mail2->Password   = 'ovxf tfja ezpw ihps';  
        $mail2->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail2->Port       = 587;

        // Sender info
        $mail2->setFrom('ramiwahdan2023@gmail.com', 'Training Center');
        $mail2->addAddress($new_email, 'Admin');

        // Email content
        $mail2->isHTML(true);
        $mail2->Subject = $subject;
        $mail2->Body = $body;

        $mail2->send();
        return "SUCCESS";

    } catch (Exception $e) {
        return "Email could not be sent. Error: {$mail2->ErrorInfo}";
    }
}

function sendCertificateEmail($toEmail, $toName, $courseName, $certificatePath) {

    $mail = new PHPMailer(true);

    try {
        // SMTP Settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';  
        $mail->SMTPAuth   = true;
        $mail->Username   = 'ramiwahdan2023@gmail.com';
        $mail->Password   = 'ovxf tfja ezpw ihps';  
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Sender info
        $mail->setFrom('wahbib@gmail.com', 'Training Center');
        $mail->addAddress($toEmail, $toName);

        // Email content
        $mail->isHTML(true);
        $mail->Subject = "Your Certificate for $courseName";

        $mail->Body = "
            <h2>Congratulations, $toName! 🎉</h2>
            <p>You have successfully completed <strong>$courseName</strong>.</p>
            <p>Your certificate is attached to this email.</p>
        ";

        // Attach certificate PDF
        if (file_exists($certificatePath)) {
            $mail->addAttachment($certificatePath);
        } else {
            return "ERROR: Certificate file not found!";
        }

        $mail->send();
        return "SUCCESS";

    } catch (Exception $e) {
        return "Email could not be sent. Error: {$mail->ErrorInfo}";
    }
}