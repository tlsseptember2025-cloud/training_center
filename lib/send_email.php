<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/SMTP.php';
require_once __DIR__ . '/PHPMailer/Exception.php';

function sendCertificateEmail($toEmail, $toName, $courseName, $certificatePath) {

    $mail = new PHPMailer(true);

    try {
        // SMTP Settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';  
        $mail->SMTPAuth   = true;
        $mail->Username   = 'ramiwahdan2023@gmail.com';
        $mail->Password   = 'alem bevx vcee jvir';  
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