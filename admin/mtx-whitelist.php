<?php
/**
 * PHP/SQLite Guestbook script
 *
 * Copyright (c) 2015-21, Irwin Associates and Graham R Irwin
 *
 * See license.txt for details
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Maintain whitelist</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="admin.css" rel="stylesheet">
</head>
<body>
<main>
  <h1>Guestbook admin</h1>
  <h2>Maintain whitelist</h2>
  <p>You may add or delete entries in the whitelist.</p>
  <p class="right"><a href="#add">Add an entry</a></p>
  <?php
  $dba = 'guestbook.sqlite';

  $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

  try {
    $db = new PDO('sqlite:'.$dba);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // add an entry
    if ($action === 'add') {
      $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
      $query = 'INSERT OR IGNORE INTO whitelist (email) VALUES (:email);';
      $stmt = $db->prepare($query);
      $stmt->bindParam(':email', $email);
      $stmt->execute();
      echo '<p class="done">Email address added if unique</p>', "\n";
    }

    // delete an entry
    elseif ($action == 'del') {
      $email = filter_var($_GET['email'], FILTER_VALIDATE_EMAIL);
      $query = 'DELETE FROM whitelist WHERE email = :email;';
      $stmt = $db->prepare($query);
      $stmt->bindParam(':email', $email);
      $stmt->execute();
      echo '<p class="done">Email address deleted</p>', "\n";
    }

    // in all cases list all entries
    echo '<h3>Current entries</h3>', "\n";
    $query = 'SELECT email FROM whitelist ORDER BY email;';
    $result = $db->query($query);
    foreach ($result as $row) {
      echo '<p>', $row['email'],
        ' : <a href="?action=del&amp;email=', $row['email'],
        '" class="delete">delete</a></p>', "\n";
    }

    $db = null;

  } catch (PDOException $e) {
    echo $e->getMessage();
  }

  $temp = parse_url($_SERVER['PHP_SELF']);
  // display add form
  ?>
  <div id="add">
    <h3>Add an entry</h3>
    <form id="form" method="post" action="<?=$temp['path']?>">
      <div>
        <label for="email">Email address:</label>
        <input type="email" id="email" name="email" required>
      </div>
      <input type="hidden" name="action" value="add">
      <input type="submit" name="submit" id="submit" value="Add email">
    </form>
  </div>
  <p>&nbsp;</p>
  <p><a href=".">Admin home page</a></p>
  <hr>
  <p>Admin scripts &copy; Irwin Associates Web Design, 2015-21</p>
</main>
<script>
(function() {
  const dis = document.querySelectorAll(".delete");
  for (let c = 0; c < dis.length; c++) {
    dis[c].addEventListener("click", function(e) {
      if (!confirm('Delete entry? Are you sure?'))
        e.preventDefault();
    });
  }

  var s = document.querySelector("#submit");
  document.querySelector("form").addEventListener("submit", function() {
    s.disabled = true;
    s.value = "Please Wait...";
  });
})();
</script>
</body>
</html>
