<?php
require_once "../../util/pdo.php";

if(isset($_POST['nap_eura']) && isset($_POST['kontakt_id']) && isset($_POST['big_order_id'])) {
  $sql = "UPDATE bo_naplate
  SET ukupno_eura =:ukupno_eura
  WHERE big_order_id =:big_order_id AND kontakt_id = :kontakt_id";
  $stmt = $pdo -> prepare($sql);
  $stmt -> execute(array(
    ':big_order_id' => $_POST['big_order_id'],
    ':kontakt_id' => $_POST['kontakt_id'],
    ':ukupno_eura' => $_POST['nap_eura']
  ));
}

$_SESSION['success'] = 'Big order id - '.$_POST['big_order_id'].' Kontakt id '.$_POST['kontakt_id'].' iznos naplate je uspesno updateovan u tabeli bo_naplate';
header('Location: bo_naplata_interface.php?big_order_id='.$_POST['big_order_id']);
return;
?>
