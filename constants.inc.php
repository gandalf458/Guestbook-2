<?php
/**
 * PHP/SQLite Guestbook script
 *
 * Copyright (c) 2015-21, Irwin Associates and Graham R Irwin
 *
 * See license.txt for details
 */

// This script provides a single place to change certain parameters.
// The script is included in guestbook.inc.php and guestbook-edit.inc.php

// Change the values of the following constants as required

// to and from email addresses for notification email
define('FROM_NAME',  'Guestbook');
define('FROM_EMAIL', 'me@domain.com');
define('TO_EMAIL',   'to@domain.com');

// SMTP credentials (if used)
define('MAILER_HOST', 'mail.domain.com');
define('MAILER_USER', 'me@domain.com');
define('MAILER_PASS', 'password');

// tells the script where to find the database and admin scripts
define('ADMIN_DIR', 'admin');

// 1 - automatically approve new comments (not recommended)
// 0 - do not automatically approve new comments (unless in whitelist)
define('APPROVED', 0);

// 1 - send email when new comment has been posted (unless in whitelist)
// 0 - do not send email when new comment has been posted
define('SENDMAIL', 1);

// number of comments per page
define('PERPAGE', 12);
