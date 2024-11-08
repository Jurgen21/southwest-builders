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
    $description = isset($_POST["description"]) ? trim($_POST["description"]) : '';
    $mobile = isset($_POST["mobile"]) ? trim($_POST["mobile"]) : '';

    // Validoni të dhënat bazike
    if (empty($name) || empty($surname) || empty($email) || empty($mobile)) {
        throw new Exception("Please fill all required fields");
    }

    // Validoni emailin
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Invalid email format");
    }

    // Përpunoni file-in
    $cvFileName = $_FILES['cv']['name'];
    $cvFileTmp = $_FILES['cv']['tmp_name'];
    $cvFilePath = 'uploads/' . time() . '_' . basename($cvFileName);

    // Krijoni dosjen uploads nëse nuk ekziston
    if (!file_exists('uploads')) {
        mkdir('uploads', 0777, true);
    }

    // Krijoni instancën e PHPMailer
    $mail = new PHPMailer(true);

    // Konfigurimet e serverit
    $mail = new PHPMailer;
$mail->isSMTP();
$mail->SMTPDebug = 0;
$mail->Debugoutput = 'html';
$mail->Mailer = "smtp";
$mail->Host = "smtp.gmail.com";
$mail->Port = 587;
$mail->SMTPAuth = true;
$mail->SMTPSecure = 'tls';
$mail->Username = 'jurgentanushi7@gmail.com';
$mail->Password = 'cnru jwvt jybp ptop';
$mail->SetFrom('jurgentanushi7@gmail.com','Tpp form');

    // Marrësit
    $mail->FromName = 'SOUTHWEST BUILDERS LONDON';
    $mail->addAddress("jurgentanushi7@gmail.com");
    $mail->isHTML(true); 
    
    foreach($_REQUEST as $key => $vl) {
        if (is_array($vl)) $vl=json_encode($vl);
        $message .= $key . "<br />" . $vl . "<br/> <br/>";
    }
    
    
    $message .= "<br />";
    $message .= "<br />";
    $message .= date("Y/m/d H:n:s")."<br />";
    
    
    
    $mail->Subject = "TTP Registration Form";
    $mail->Body    = $message;
    
    if(!$mail->send()) {
    echo "Error!";
    } else {
    echo "Success!";
    }

    // Përmbajtja e email-it
    $mail->isHTML(true);
    $mail->Subject = "New Job Application from $name $surname";

    $mailBody = "<h1>Contact Information</h1>";
    $mailBody .= "<p><strong>Name:</strong> $name $surname</p>";
    $mailBody .= "<p><strong>Email:</strong> $email</p>";
    $mailBody .= "<p><strong>Description:</strong><br>" . nl2br(htmlspecialchars($description)) . "</p>";

    $mail->Body = $mailBody;
    $mail->AltBody = strip_tags(str_replace('<br>', "\n", $mailBody));

    // Dërgoni email-in
    if (!$mail->send()) {
        throw new Exception("Email could not be sent. Mailer Error: " . $mail->ErrorInfo);
    }

    // Ridrejtoni te faqja e suksesit
    header("Location: contactus.html");
    exit();

} catch (Exception $e) {
    // Shfaqni mesazhin e errorit
    die("An error occurred: " . $e->getMessage());
}
?>