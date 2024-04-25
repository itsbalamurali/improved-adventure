<?php

//https://github.com/PHPMailer/PHPMailer/issues/469
require_once ('assets/libraries/class.phpmailer.php');
$mail = new PHPMailer;

//$mail->SMTPDebug = 3;                               // Enable verbose debug output
$mail->SMTPDebug = 1;                               // Enable verbose debug output

$mail->isSMTP();                                      // Set mailer to use SMTP
$mail->Host = 'smtp.mailgun.org';              // Specify main and backup SMTP servers
$mail->SMTPAuth = true;                               // Enable SMTP authentication
$mail->Username = 'postmaster@mg.localservicespro.com';                 // SMTP username
$mail->Password = 'a4e2c67365ccb771b50e7c82ac4f62d8-db137ccd-75c91478';                           // SMTP password
//$mail->Host = 'localservicespro.com';              // Specify main and backup SMTP servers
//$mail->SMTPAuth = true;                               // Enable SMTP authentication
//$mail->Username = 'info@localservicespro.com';                 // SMTP username
//$mail->Password = 'Z1tSy)%#LH.P';                           // SMTP password
//$mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
//$mail->Port = 465;                                    // TCP port to connect to
$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
$mail->Port = 2525;                                    // TCP port to connect to



$mail->From = 'no-reply@localservicespro.com';
$mail->FromName = 'Localservicespro';
//$mail->addAddress('joe@example.net', 'Joe User');     // Add a recipient
$mail->addAddress('mrunal.esw@gmail.com');               // Name is optional

//$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
$mail->isHTML(true);                                  // Set email format to HTML

$mail->Subject = 'Email From Localservicespro';
/*$mail->Subject = $subject;
if ($language == "FR" || $language == "ES" || $language == "PT" ) {
   $mail->Subject = '=?UTF-8?B?'.base64_encode($subject).'?=';
}*/
$mail->Body    = 'Email From Localservicespro <b>Localservicespro</b>';
$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

if(!$mail->send()) {
    echo 'Message could not be sent.';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
    echo 'Message has been sent';
}
?>