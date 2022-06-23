<?php
require_once "../util/pdo.php";
require_once "../util/db.php";
require_once "../util/html_maker_form.php";
session_start();
$form_maker = new HtmlMakerForm();
$db         = new Db();
$artikl = $db -> get_artikl_data($pdo, $_GET['artikl_id']);
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <?php require_once "../util/head.php" ?>
</head>
<body>
  <?php require_once "../util/navbar.php" ?>
  <?php
    $form_maker -> create_artikl_form("Pregled Artikla", $artikl, "readonly");
  ?>

  <script src="../js/jquery-3.6.0.min.js"></script>
  <script src="../bootstrap/js/bootstrap.min.js"></script>
</body>
</html>
