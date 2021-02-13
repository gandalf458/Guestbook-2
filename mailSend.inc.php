<?php
/**
 * PHP/SQLite Guestbook script
 *
 * Copyright (c) 2015-21, Irwin Associates and Graham R Irwin
 *
 * See license.txt for details
 */

// Mail sender script using PHPMailer. Not required if using PHP's send() function.
// If used, mail host, user, password etc will need to be defined in constants.inc.php,
// and the PHPMailer library included. The port may also need changing below.

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

$mail = new PHPMailer(true);
try {
  #$mail->SMTPDebug = 2;   // Enable verbose debug output
  $mail->isSMTP();
  $mail->Host = MAILER_HOST;
  $mail->SMTPAuth = true;
  $mail->Username = MAILER_USER;
  $mail->Password = MAILER_PASS;
  $mail->SMTPSecure = 'ssl';
  $mail->Port = 465;
  if ($_SERVER['HTTP_HOST'] === 'localhost') {
    $mail->SMTPSecure = '';
    $mail->Port = 2525;
  }

  $mail->setFrom(FROM_EMAIL, FROM_NAME);
  $mail->addAddress(TO_EMAIL);
  $mail->addReplyTo($email, $name);

  $mail->isHTML(false);
  $mail->Subject = 'New guestbook entry';
  $mail->Body    = $message;

  if ($error === '')  // $error is not currently used
    $mail->send();
} catch (Exception $e) {
  echo 'Mailer Error: ', $mail->ErrorInfo;
}
