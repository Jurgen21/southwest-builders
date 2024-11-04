<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Përfshini autoloader-in
require_once __DIR__ . '/vendor/autoload.php';

// Importoni klasat e PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Kontrolloni nëse forma është submit-uar
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    die("Direct access not allowed");
}

try {
    // Merrni të dhënat e formës
    $name = isset($_POST["name"]) ? trim($_POST["name"]) : '';
    $surname = isset($_POST["surname"]) ? trim($_POST["surname"]) : '';
    $email = isset($_POST["email"]) ? trim($_POST["email"]) : '';
    $mobile = isset($_POST["mobile"]) ? trim($_POST["mobile"]) : '';
    $postcode = isset($_POST["postcode"]) ? trim($_POST["postcode"]) : '';
    $country = isset($_POST["country"]) ? trim($_POST["country"]) : '';
    $position = isset($_POST["position"]) ? trim($_POST["position"]) : '';
    $years = isset($_POST["years"]) ? trim($_POST["years"]) : '';
    $permit = isset($_POST["permit"]) ? trim($_POST["permit"]) : '';
    $description = isset($_POST["description"]) ? trim($_POST["description"]) : '';

    // Validoni të dhënat bazike
    if (empty($name) || empty($surname) || empty($email) || empty($mobile)) {
        throw new Exception("Please fill all required fields");
    }

    // Validoni emailin
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Invalid email format");
    }

    // Kontrolloni nëse është ngarkuar një file
    if (!isset($_FILES['cv']) || $_FILES['cv']['error'] === UPLOAD_ERR_NO_FILE) {
        throw new Exception("Please upload your CV");
    }

    // Përpunoni file-in
    $cvFileName = $_FILES['cv']['name'];
    $cvFileTmp = $_FILES['cv']['tmp_name'];
    $cvFilePath = 'uploads/' . time() . '_' . basename($cvFileName);

    // Krijoni dosjen uploads nëse nuk ekziston
    if (!file_exists('uploads')) {
        mkdir('uploads', 0777, true);
    }

    // Validoni tipin e file-it
    $validFileTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
    $fileType = mime_content_type($cvFileTmp);

    if (!in_array($fileType, $validFileTypes)) {
        throw new Exception("Invalid file type. Only PDF, DOC, and DOCX are allowed.");
    }

    // Kontrolloni madhësinë e file-it (max 5MB)
    if ($_FILES['cv']['size'] > 5 * 1024 * 1024) {
        throw new Exception("File is too large. Maximum size is 5MB.");
    }

    // Zhvendosni file-in e uploaduar
    if (!move_uploaded_file($cvFileTmp, $cvFilePath)) {
        throw new Exception("File upload failed.");
    }

    // Krijoni instancën e PHPMailer
    $mail = new PHPMailer(true);

    // Konfigurimet e serverit
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'jurgentanushi7@gmail.com'; // Your Gmail address
    $mail->Password = 'iqck wnzp pubv bwca.'; // Your Gmail App Password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    $mail->CharSet = 'UTF-8';

    // Marrësit
    $mail->setFrom($email, "$name $surname");
    $mail->addAddress('jurgentanushi7@gmail.com', 'Jurgen Tanushi');
    $mail->addReplyTo($email, "$name $surname");

    // Bashkëngjitni CV-në
    $mail->addAttachment($cvFilePath, $cvFileName);

    // Përmbajtja e email-it
    $mail->isHTML(true);
    $mail->Subject = "New Job Application from $name $surname";

    $mailBody = "<h1>Job Application Details</h1>";
    $mailBody .= "<p><strong>Name:</strong> $name $surname</p>";
    $mailBody .= "<p><strong>Email:</strong> $email</p>";
    $mailBody .= "<p><strong>Mobile:</strong> $mobile</p>";
    $mailBody .= "<p><strong>Postcode:</strong> $postcode</p>";
    $mailBody .= "<p><strong>Country:</strong> $country</p>";
    $mailBody .= "<p><strong>Position Applied For:</strong> $position</p>";
    $mailBody .= "<p><strong>Years of Experience:</strong> $years</p>";
    $mailBody .= "<p><strong>Work Permit:</strong> $permit</p>";
    $mailBody .= "<p><strong>Description:</strong><br>" . nl2br(htmlspecialchars($description)) . "</p>";

    $mail->Body = $mailBody;
    $mail->AltBody = strip_tags(str_replace('<br>', "\n", $mailBody));

    // Dërgoni email-in
    if (!$mail->send()) {
        throw new Exception("Email could not be sent. Mailer Error: " . $mail->ErrorInfo);
    }

    // Ridrejtoni te faqja e suksesit
    header("Location: cv.html");
    exit();

} catch (Exception $e) {
    // Shfaqni mesazhin e errorit
    die("An error occurred: " . $e->getMessage());
}
?>