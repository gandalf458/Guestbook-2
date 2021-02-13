# Guestbook-2

This is a simple website guestbook script in PHP and SQLite, with a little Javascript. It has been substantially updated from the original version.

The script comprises the following files:

* guestbook.php          - dummy page to display the guestbook entries
* guestbook.inc.php      - include file to display the guestbook entries
* guestbook-sign.php     - dummy page to make a new entry in the guestbook
* guestbook-sign.inc.php - include file to make a new entry in the guestbook
* constants.inc.php      - include file to set several constants
* mailSend.inc.php       - include file for sending mail using PHPMailer
* css/guestbook.css      - basic CSS for the guestbook pages
* admin/index.php        - admin menu
* admin/admin.css        - CSS for the admin routines
* admin/guestbook-edit.php - admin page to maintain guestbook entries
* admin/mtx-whitelist.php - admin page to maintain an email address whitelist

For security the guestbook SQLite database and the admin routines should reside in a subdirectory which is password protected (most likely using .htaccess and .htpasswd). The admin directory does not need to be named 'admin'; in fact it is advised that the directory name is changed. If it is changed, the ADMIN_DIR constant will need to be amended in constants.inc.php.

The guestbook SQLite database and tables are created the first time the guestbook(.inc).php script is run.

The guestbook does not require users to log in, so anyone visiting the site is able to leave a comment. A simple CAPTCHA is included to help inhibit bots. When a new comment is made in the guestbook, an email is sent to the adminstrator; this can be disabled if not required. By default a new entry needs approval before it is shown on the public page; again, this can be changed if required. There is also an email whitelist to permit comments by anyone whose email address is in the whitelist not to require approval. 

The script does not provide threaded responses, just a chronological list of comments.

The script is ready to use except that the 'to' and 'from' email addresses need to be set in constants.inc.php. If using PHPMailer, the PHPMailer library will need to be included and the mail server credentials supplied. In addition, you will probably want to provide your own guestbook.php and guestbook-sign.php pages, as well as make changes to the CSS.

Updated: 2021-02-13 09:12
