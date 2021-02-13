<?php
/**
 * PHP/SQLite Guestbook script
 *
 * Copyright (c) 2015-21, Irwin Associates and Graham R Irwin
 *
 * See license.txt for details
 */

// script to sign the guestbook

require 'constants.inc.php';
$missing = false;
// form has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // a crude trick to inhibit header injection
  if (!isset($_SERVER['HTTP_USER_AGENT'])) die();

  // get form input
  $name    = htmlspecialchars($_POST['name']);
  $email   = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
  $comment = htmlspecialchars($_POST['comment']);
  $captcha = (int)$_POST['captcha'];
  $cvalue  = (int)$_POST['cvalue'];

  // validate form input
  $noName    = ($name === '')    ? true : false;
  $noEmail   = ($email === '')   ? true : false;
  $noComment = ($comment === '') ? true : false;
  $noCaptcha = ($captcha === '') ? true : false;
  if ($captcha !== $cvalue) $noCaptcha = true;
  $noConsent = !isset($_POST['consent']) ? true : false;
  $missing = $noName || $noEmail || $noComment || $noCaptcha || $noConsent;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$missing) :
  // no missing fields, so update database
  try {
    // open db connection
    $db = new PDO('sqlite:'.ADMIN_DIR.'/guestbook.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $timedate  = date('Y-m-d h:i:s');
    $ipaddress = $_SERVER['REMOTE_ADDR'];
    $approved  = APPROVED;
    $error     = ''; // $error is not currently set

    // check if email address is in the whitelist and if so approve it
    $query = 'SELECT email FROM whitelist WHERE email = "'.$email.'";';
    $result = $db->query($query);
    $rows = $result->fetchAll();
    if (count($rows) === 1)
      $approved = 1;

    // update database
    $data = [
      'name' => $name,
      'email' => $email,
      'comment' => $comment,
      'timedate' => $timedate,
      'ipaddress' => $ipaddress,
      'approved' => $approved
    ];
    $query = 'INSERT INTO guestbook
      (name, email, comment, timedate, ipaddress, approved)
      VALUES
      (:name, :email, :comment, :timedate, :ipaddress, :approved);';
    $stmt = $db->prepare($query);
    $stmt->execute($data);

    if (!$approved && SENDMAIL) {
      $str     = $_SERVER['PHP_SELF'];
      $message = 'Hello' . PHP_EOL . PHP_EOL
        . $name . ' (' . $email . ') has posted a comment in the guestbook:' . PHP_EOL . PHP_EOL
        . $comment . PHP_EOL . PHP_EOL
        . 'Please review and approve or delete as appropriate.' . PHP_EOL . PHP_EOL
        . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'] . substr($str, 0, strrpos($str, '/')) . '/' . ADMIN_DIR . '/' . PHP_EOL;
      // To use PHPMailer instead of PHP's mail() function, comment out the following 2 statements and uncomment the one following them. You will also need to supply the PHPMailer library.
      $headers = 'From: '.FROM_EMAIL.PHP_EOL;
      @mail(TO_EMAIL, 'New guestbook entry', $message, $headers);
//      require 'mailSend.inc.php';
    }

    echo '<p>Thank you for your comment.';
    if (!$approved)
      echo ' It will need to be approved before it is published.';
    echo '</p>', "\n";
  }
  // error handling
  catch (PDOException $e) {
    echo $e->getMessage();
  }

  // close the database
  $db = null;

else :
  // missing fields OR form not submitted
  if ($missing) {
    echo '<p class="error">You have not completed the form. Please fill in all fields and press Submit when complete.</p>', "\n";
  } else {
    echo '<p>Please sign our guestbook. All fields are required. Although we require your email address this will not be published. Comments will normally need approval before being published.</p>', "\n";
  }
  $a = rand(1,7);
  $b = rand(2,8);
  $cvalue = $a + $b;
  $noName    = isset($noName)    ? $noName    : false;
  $noEmail   = isset($noEmail)   ? $noEmail   : false;
  $noCaptcha = isset($noCaptcha) ? $noCaptcha : false;
  $noComment = isset($noComment) ? $noComment : false;
  $noConsent = isset($noConsent) ? $noConsent : false;
?>
<form class="gbform" method="post">
  <div class="field">
    <label for="name"<?php if ($noName) echo ' class="error"' ?>>Name:</label>
    <input name="name" id="name" type="text" size="30" value="<?php if (isset($name)) echo $name ?>" required>
  </div>
  <div class="field">
    <label for="email"<?php if ($noEmail) echo ' class="error"' ?>>Email:</label>
    <input name="email" id="email" type="email" size="30" value="<?php if (isset($email)) echo $email ?>" required>
  </div>
  <div class="field">
    <label for="comment"<?php if ($noComment) echo ' class="error"' ?>>Comment:</label>
    <textarea name="comment" id="comment" cols="31" rows="5" required><?php if (isset($comment)) echo $comment ?></textarea>
  </div>
  <div class="field">
    <label for="captcha"<?php if ($noCaptcha) echo ' class="error"' ?>> <?= 'What is ',$a,'+',$b,'?' ?> </label>
    <input name="captcha" id="captcha" type="text" size="3" required>
  </div>
  <div class="consent<?php if ($noConsent) echo ' error' ?>">
    <input type="checkbox" name="consent" id="consent" required> 
    <label for="consent">I consent to my name and comment being posted on the website and the details supplied being stored on the website’s server.</label>
  </div>
  <input type="submit" name="submit" id="submit" value="Submit">
  <input type="hidden" id="cvalue" name="cvalue" value="<?= $cvalue ?>">
</form>
<?php
endif;
