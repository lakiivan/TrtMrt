<?php


//queri koji pronalazi ukupan broj artikala i net cenu po kupcu
// iz tabele artikl_orderi
$stmt = $pdo -> prepare("SELECT k.kontakt_id as kid, k.ime as ime, sum(kolicina) as sum_artikala, sum(ukupna_net) as sum_ukupna_net,
sum(prodajna_cena) as sum_prodajna_cena, (sum(prodajna_cena) - sum(ukupna_net)) as sum_zarada
FROM artikl_orderi
INNER JOIN kontakti as k ON k.kontakt_id = artikl_orderi.kontakt_id
WHERE big_order_id = :boid
GROUP BY artikl_orderi.kontakt_id");
$stmt->execute(array(":boid" => $_GET['big_order_id']));
$bo_sum_po_kupcima = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (sizeof($bo_sum_po_kupcima) === 0){
  //$_SESSION['error']  = "Nije mogla da se izvuce suma po kupcu za order = ". $_GET['big_order_id'];

}
