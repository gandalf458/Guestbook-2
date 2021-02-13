<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Sign our guestbook</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="css/guestbook.css" rel="stylesheet">
</head>
<body>
<!-- dummy page - to be replaced by user's own page -->
<h1>Guestbook script</h1>
<h2>Sign our guestbook</h2>
<?php
require 'guestbook-sign.inc.php';
?>
<p><a href="guestbook.php">View the guestbook</a></p>
<script>
(function() {
  var s = document.querySelector("#submit");
  document.querySelector("form").addEventListener("submit", function() {
    s.disabled = true;
    s.value = "Please Wait...";
  });
})();
</script>
</body>
</html>
