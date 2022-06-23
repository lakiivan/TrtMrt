<?php
require_once "../util/pdo.php";
require_once "../util/db.php";
require_once "../util/html_maker_form.php";

session_start();
$form_maker = new HtmlMakerForm();
$db         = new Db();
$artikl = $db -> get_artikl_data($pdo, $_GET['artikl_id']);
$artikl_id = $_GET['artikl_id'];

//************ POST METOD ******************************
if (isset($_POST['part_number'])) {
  echo"POST STARTED";
  $sql = "UPDATE `artikli` SET `part_number`=:part_number,`opis`=:opis,
  `link`=:link,`cena_net`=:cena_net,`cena_list`=:cena_list,`komentar`=:komentar
  WHERE artikl_id = :artikl_id";
  $stmt = $pdo -> prepare($sql);
  $stmt -> execute(array(
    ':part_number'  => $_POST['part_number'],
    ':opis'         => $_POST['opis'],
    ':link'         => $_POST['link'],
    ':cena_net'     => $_POST['cena_net'],
    ':cena_list'    => $_POST['cena_list'],
    ':komentar'     => $_POST['komentar'],
    ':artikl_id'    => $artikl_id
  ));
  $_SESSION['success'] = 'Artikl '.$_POST['part_number'].' je uspešno dodat u bazu artikala';
  header('Location: artikli.php');
  return;
}

?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <?php require_once "../util/head.php" ?>
</head>
<body>
  <?php
    require_once "../util/navbar.php";
    $form_maker -> create_artikl_form("Ažuriranje artikla", $artikl, "");
  ?>

  <script src="../js/jquery-3.6.0.min.js"></script>
  <script src="../bootstrap/js/bootstrap.min.js"></script>
</body>
</html>
