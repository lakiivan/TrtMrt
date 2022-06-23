<?php
//OVA SKRIPTA SRACUNAVA UKUPNU NET I PRODAJNU CENU, UKUPAN BROJ ARTIKALA I ZARADU

// da bi ova skripta mogla da se koristi i u index gde big order id nije $_GET[]
//i u big order interface gde se dobija iz $_GET[]
//provera da li je big order id setovan, odnosno da li je pozvan iz index.php
//ili je pozvana stranica big order interface gde se nig order dobija iz $_GET[]
if (isset($big_order_id)){
  //echo 'big order id je '.$big_order_id;
} else {
  $big_order_id = $_GET['big_order_id'];
}

//sam query kojim se dobijaju ukupne vrednsoti za net, prodaju artikle i zaradu
// iz tabele artikl_orderi
$stmt = $pdo -> prepare("SELECT SUM(kolicina) as sum_kolicna,
SUM(ukupna_net) as sum_ukupna_net, SUM(prodajna_cena) as sum_ukupna_list,
(SUM(prodajna_cena) - SUM(ukupna_net)) as sum_zarada
FROM artikl_orderi
WHERE big_order_id = :boid
GROUP BY big_order_id");
$stmt->execute(array(":boid" => $big_order_id));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row === false){
  $_SESSION['error']  = "Bad value for big_order id = ". $big_order_id;
  header('Location: http://localhost/trt_mrt/index.php');
  return;
} else {
  $bos_ukupna_kolicina = htmlentities($row['sum_kolicna']);
  $bos_ukupna_net = htmlentities($row['sum_ukupna_net']);
  $bos_ukupna_prodajna = htmlentities($row['sum_ukupna_list']);
  $bos_ukupna_zarada = htmlentities($row['sum_zarada']);
}
?>
