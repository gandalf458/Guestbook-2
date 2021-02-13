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
<title>Maintain guestbook entries</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="admin.css" rel="stylesheet">
</head>
<body>
<main>
  <h1>Guestbook admin</h1>
  <h2>Maintain guestbook entries</h2>
  <p>You may approve or delete entries in the guestbook.</p>
  <?php
  $uo = isset($_GET['uo']) ? $_GET['uo'] : '';   // unapproved only
  $id = isset($_GET['id']) ? $_GET['id'] : 0;    // item id
  $action = isset($_GET['action']) ? $_GET['action'] : '';  // action (app/del)
  try {
    $db = new PDO('sqlite:guestbook.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // delete comment
    if (($action === 'del') && ($id > 0)) {
      $query = 'DELETE FROM guestbook WHERE id = :id;';
      $stmt = $db->prepare($query);
      $stmt->bindParam(':id', $id);
      $stmt->execute();
      echo '<p class="done">Comment deleted</p>', "\n";
    }

    // approve comment
    if (($action === 'app' || $action === 'wli') && ($id > 0)) {
      $query = 'UPDATE guestbook SET approved = 1 WHERE id = :id;';
      $stmt = $db->prepare($query);
      $stmt->bindParam(':id', $id);
      $stmt->execute();
      echo '<p class="done">Comment approved</p>', "\n";
    }

    // add email to whitelist
    if (($action === 'wli') && ($id > 0)) {
      $query = 'SELECT email FROM guestbook WHERE id = :id;';
      $stmt = $db->prepare($query);
      $stmt->bindParam('id', $id);
      $stmt->execute();
      $result = $stmt->fetch();
      $query = 'INSERT OR IGNORE INTO whitelist (email) VALUES (:email);';
      $stmt = $db->prepare($query);
      $stmt->bindParam(':email', $result['email']);
      $stmt->execute();
      echo '<p class="done">Email address whitelisted</p>', "\n";
    }

    // display entries
    $query = 'SELECT
      id, name, email, comment, timedate, ipaddress, approved
      FROM guestbook ';
    if ($uo === 'unapp')
      $query .= 'WHERE approved = 0 ';
    $query .= 'ORDER BY id DESC;';
    $result = $db->query($query);
    $rows = $result->fetchAll();
    echo '<p>There are ', count($rows), ' entries</p>', "\n";
    foreach ($rows as $row) {
    ?>
      <table>
        <tr>
          <td class="tdlabel">Id:</td>
          <td><?=$row['id']?></td>
        </tr>
        <tr>
          <td>Name:</td>
          <td><?=$row['name']?></td>
        </tr>
        <tr>
          <td>Email:</td>
          <td><?=$row['email']?></td>
        </tr>
        <tr>
          <td>Comment:</td>
          <td><?=$row['comment']?></td>
        </tr>
        <tr>
          <td>Date/time:</td>
          <td><?=$row['timedate']?></td>
        </tr>
        <tr>
          <td>IP Address:</td>
          <td><?=$row['ipaddress']?></td>
        </tr>
        <?php
        $phpself = htmlspecialchars($_SERVER['PHP_SELF']);
        ?>
        <tr>
          <td>Delete entry:</td>
          <td><a class="delete" href="<?= '?id=', $row['id'], '&amp;action=del&amp;uo=', $uo ?>">delete</a></td>
        </tr>
        <tr>
          <td><?php echo $row['approved'] == 1 ? 'Approved:' : 'Not approved:'; ?></td>
          <td><?php if ($row['approved'] != 1) echo '<a class="approve" href="?id=', $row['id'], '&amp;action=app&amp;uo=', $uo, '">approve</a>'; ?></td>
        </tr>
        <tr>
          <td></td>
          <td><?php if ($row['approved'] != 1) echo '<a class="whlist" href="?id=', $row['id'], '&amp;action=wli&amp;uo=', $uo, '">approve and whitelist email</a>'; ?></td>
        </tr>
      </table>
    <?php
    }
  }
  catch (PDOException $e) {
    echo $e->getMessage();
  }
  $db = null;
  ?>
  <p>&nbsp;</p>
  <p><a href=".">Admin home page</a></p>
  <hr>
  <p>Admin scripts &copy; Irwin Associates Web Design, 2015-21</p>
</main>
<script>
(function() {
  const dis = document.querySelectorAll(".delete");
  for (let i = 0; i < dis.length; i++) {
    dis[i].addEventListener("click", function(e) {
      if (!confirm('Delete entry? Are you sure?'))
        e.preventDefault();
    });
  }

  const aps = document.querySelectorAll(".approve");
  for (let j = 0; j < aps.length; j++) {
    aps[j].addEventListener("click", function(e) {
      if (!confirm('Approve entry? Are you sure?'))
        e.preventDefault();
    });
  }

  const whs = document.querySelectorAll(".whlist");
  for (let k = 0; k < aps.length; k++) {
    whs[k].addEventListener("click", function(e) {
      if (!confirm('Approve entry? Are you sure?'))
        e.preventDefault();
    });
  }
}());
</script>
</body>
</html>
