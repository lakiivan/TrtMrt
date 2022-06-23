<?php
  require_once "../../util/pdo.php";

  if(isset($_POST['artikl_order_id'])){
    $sql = "DELETE FROM artikl_orderi WHERE artikl_order_id = :aoid";
    $stmt = $pdo -> prepare($sql);
    $stmt -> execute (array(':aoid' => $_POST['artikl_order_id']));

    $_SESSION['success']  = "Artikl order id = ".$_POST['artikl_order_id']. " je uspešno izbrisan iz baze";
    header('Location: http://localhost/trt_mrt/big_order/porucivanje/single_order_interface.php?kontakt_id='.$_POST['kontakt_id'].'&big_order_id='.$_POST['big_order_id']);
    return;
  }
?>
