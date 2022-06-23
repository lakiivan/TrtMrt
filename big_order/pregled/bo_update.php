<?php
  require_once "../../util/pdo.php";
  require_once "../bo_util/upit.php";

  $upit = new Upit();

  if(isset($_GET['big_order_id']) && isset($_GET['status'])) {
    $upit -> update_bo_status($pdo, $_GET['big_order_id'], $_GET['status']);

    $_SESSION['success'] = 'Big order '.$_GET['big_order_id'].' status '.$_GET['status'].' je uspešno dodat u bazu';
    header('Location: http://localhost/trt_mrt/big_order/big_order_interface.php?big_order_id='.$_GET['big_order_id']);
    return;
  }
?>
