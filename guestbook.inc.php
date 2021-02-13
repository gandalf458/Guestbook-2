<?php
/**
 * PHP/SQLite Guestbook script
 *
 * Copyright (c) 2015-21, Irwin Associates and Graham R Irwin
 *
 * See license.txt for details
 */

// script to display guestbook entries (paginated version)

require 'constants.inc.php';
try {
  // open db connection and create db and tables if first time here
  $db = new PDO('sqlite:'.ADMIN_DIR.'/guestbook.sqlite');
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $db->exec('CREATE TABLE IF NOT EXISTS guestbook (
    id INTEGER PRIMARY KEY,
    name TEXT,
    email TEXT,
    comment TEXT,
    timedate TEXT,
    ipaddress TEXT,
    approved INTEGER
  );');
  $db->exec('CREATE TABLE IF NOT EXISTS whitelist (
    email TEXT PRIMARY KEY
  );');

  $perpage = PERPAGE; // number of comments per page
  $p = isset($_GET['p']) ? (int)$_GET['p'] : 1;
  if ($p < 1) $p = 1;
  $start = ($p-1) * $perpage;
  $query = 'SELECT count(id) FROM guestbook WHERE approved = 1;';
  $result = $db->query($query);
  $rows = $result->fetch();
  $totPages = ceil((int)$rows['count(id)'] / $perpage);

  $query = 'SELECT name, comment, timedate
    FROM guestbook
    WHERE approved=1
    ORDER BY id DESC
    LIMIT ?, ?;';
  $stmt = $db->prepare($query);
  $stmt->execute([$start, $perpage]);
  $rows = $stmt->fetchAll();

  foreach ($rows as $row) {
    $date = date("j M Y", strtotime($row['timedate']));
    $comment = trim($row['comment']);
    $comment = str_replace("\n", '<br>', $comment);
    echo <<<EOT

    <div class="gbentry">
      <div class="gbcomment">{$comment}</div>
      <div class="gbauthor">{$row['name']} <span>{$date}</span></div>
    </div>

EOT;
  }

  // pagination links ********
  if ($totPages > 1) {
    echo '<p class="more">';
    // previous page
    if ($p > 1) {
      echo ' <a href="', htmlspecialchars($_SERVER['PHP_SELF']);
      if ($p > 2)
        echo '?p=', $p-1;
      echo '" title="Previous page">&lsaquo;</a> ';
    }
    // all pages by number
    for ($pp = 1; $pp <= $totPages; $pp++) {
      if ($pp === $p) {
        // current page
        echo ' <span>', $pp, '</span> ';
      } else {
        echo ' <a href="', htmlspecialchars($_SERVER['PHP_SELF']);
        if ($pp !== 1)
          echo '?p=', $pp;
        echo '">', $pp, '</a> ';
      }
    }
    // next page
    if ($p < $totPages)
      echo ' <a href="', htmlspecialchars($_SERVER['PHP_SELF']), '?p=', $p+1, '" title="Next page">&rsaquo;</a> ';
    //
    echo "</p>\n";
  }

}
// error handling
catch (PDOException $e) {
  echo $e->getMessage();
}

$db = null;
