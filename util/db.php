<?php

class Db {

  //napraviti upit koji dobavlja sve podatke za kontakt potrebne za kontakt form edit i view
  function get_kontakt_info($pdo, $kontakt_id) {
    $stmt = $pdo -> prepare ("SELECT k.kontakt_id as kontakt_id,
      k.ime as ime, k.telefon as telefon, k.adresa as adresa,
      gr.grad as grad, k.grad_id as grad_id,
      k.klub_id as klub_id, kl.naziv as klub, k.komentar as komentar,
      k.pop_grupa_id as pop_grupa_id, pop.grupa as pop_grupa, pop.popust_procenat as popust
      FROM kontakti as k
      INNER JOIN gradovi AS gr ON gr.grad_id = k.grad_id
      INNER JOIN klubovi AS kl ON kl.klub_id = k.klub_id
      INNER JOIN popgrupe AS pop ON pop.pop_id = k.pop_grupa_id
      WHERE kontakt_id = :kontakt_id");
      $stmt->execute(array(
        ":kontakt_id" => $kontakt_id
      ));
    $kontakt = $stmt -> fetch(PDO::FETCH_ASSOC);
    return $kontakt;
  }

  function get_kontakt_and_bo_from_ao($pdo, $artikl_order_id) {
    $stmt = $pdo -> prepare ("SELECT
      k.ime as ime, bo.oznaka
      FROM artikl_orderi as ao
      INNER JOIN kontakti AS k ON k.kontakt_id = ao.kontakt_id
      INNER JOIN big_orderi AS bo ON bo.big_order_id = ao.big_order_id
      WHERE artikl_order_id = :artikl_order_id");
      $stmt->execute(array(
        ":artikl_order_id" => $artikl_order_id
      ));
    $kontakt_and_bo = $stmt -> fetch(PDO::FETCH_ASSOC);
    return $kontakt_and_bo;
  }

function get_kontakti($pdo) {
  $stmt = $pdo -> prepare ("SELECT kontakt_id, ime, telefon, kontakti.adresa as adresa,
    gr.grad as grad, kontakti.grad_id as grad_id,
    kontakti.klub_id as klub_id, kl.naziv as klub,
    kontakti.pop_grupa_id as pop_grupa_id
    FROM kontakti
    INNER JOIN gradovi AS gr ON gr.grad_id = kontakti.grad_id
    INNER JOIN klubovi AS kl ON kl.klub_id = kontakti.klub_id
    ORDER BY ime");
  $stmt -> execute();
  $kontakti = $stmt -> fetchAll(PDO::FETCH_ASSOC);
  return $kontakti;
}

function get_kontaktov_popust($pdo, $kontakt_id) {
  $stmt = $pdo->prepare("SELECT pg.popust_procenat, kontakti.kontakt_id
    FROM kontakti
    INNER JOIN popgrupe as pg ON pg.pop_id = kontakti.pop_grupa_id
    WHERE kontakt_id = :kon_id");
  $stmt->execute(array(":kon_id" => $_GET['kontakt_id']));
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  if ($row === false){
    $_SESSION['error']  = "Bad value for kontakt_id = ".$_GET['kontakt_id'];
    header('Location: index.php');
    return;
  } else {
    $popust  = htmlentities($row['popust_procenat']);
    return $popust;
  }
}

//*********************GET DATA FOR GRAD***********************
function get_gradovi($pdo) {
  $stmt = $pdo -> prepare ("SELECT * FROM gradovi
    ORDER BY grad");
    $stmt -> execute();
    $gradovi = $stmt -> fetchAll(PDO::FETCH_ASSOC);
    return $gradovi;
}

  //*********************GET DATA FOR KLUB***********************
  function get_klubovi($pdo) {
    $stmt = $pdo -> prepare ("SELECT * FROM klubovi
      ORDER BY naziv");
      $stmt -> execute();
      $klubovi = $stmt -> fetchAll(PDO::FETCH_ASSOC);
      return $klubovi;
  }

  //****************GET DATA FOR POPUST**************************
  function get_pop_grupe($pdo) {
    $stmt = $pdo -> prepare ("SELECT * FROM popgrupe
      ORDER BY pop_id");
      $stmt -> execute();
      $pop_grupe = $stmt -> fetchAll(PDO::FETCH_ASSOC);
      return $pop_grupe;
  }

  //**************GET ARTIKLI***********************************
  function get_artikli($pdo) {
    //dobijanje liste svih aktivnih artikala
    $stmt = $pdo -> prepare ("SELECT *
      FROM artikli
      ORDER BY part_number");
    $stmt -> execute();
    $artikli = $stmt -> fetchAll(PDO::FETCH_ASSOC);
    return $artikli;
  }

  function get_artikl_data($pdo, $artikl_id) {
    //dobijanje svih informacija o artiklu za dati artikl id
    $stmt = $pdo->prepare("SELECT * FROM artikli WHERE artikl_id = :xyz");
    $stmt->execute(array(":xyz" => $artikl_id));
    $artikl = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($artikl === false){
      $_SESSION['error']  = "Bad value for artikl id = ".$_GET['artikl_id'];
      header('Location: artikli_view.php');
      return;
    } else {
      return $artikl;
    }
  }

  function get_bo_artikl_orderi($pdo, $big_order_id, $kontakt_id) {
    //dobijanje tabele svih artikala koje je korisnik (kontakt_id) porucio u ovom velikom orderu($big_order_id)
    $stmt = $pdo -> prepare ("SELECT ao.artikl_order_id as aoid, ao.artikl_id as aid,
      ao.kontakt_id as kontakt_id, ao.big_order_id as big_order_id,
      ao.kolicina as kolicina, a.part_number as part_number, a.opis as opis, ao.validno as validno, ao.prodajna_cena as cena,
      ao.datum_porudzbine as dporudzbine, ao.datum_modifikovanja as dmodifikovanja, ao.komentar as komentar,
      ao.validno as validno, ao.poruceno as poruceno, ao.pregledano as pregledano,
      ao.pregledana_kolicina as pregledana_kolicina, 
      ao.isporuceno as isporuceno, ao.isporucena_kolicina as isporucena_kolicina, ao.naplaceno as naplaceno
      FROM artikl_orderi as ao
      INNER JOIN artikli as a ON a.artikl_id = ao.artikl_id
      WHERE ao.kontakt_id = :kid AND ao.big_order_id = :boid
      ORDER BY part_number ASC");
      $stmt -> execute(array(
        ':kid' => $_GET['kontakt_id'],
        ':boid' => $_GET['big_order_id']
    ));
      $artikl_orderi = $stmt -> fetchAll(PDO::FETCH_ASSOC);
      return $artikl_orderi;
  }

  function get_ime_grada($pdo, $grad_id) {
    $stmt = $pdo->prepare("SELECT grad FROM gradovi WHERE grad_id = :grad_id");
         $stmt->execute(array(":grad_id" => $grad_id));
         $row = $stmt->fetch(PDO::FETCH_ASSOC);
         if ($row === false){
           return '';
         } else {
           $grad = htmlentities($row['grad']);
           return $grad;
         }
  }

  function pronadji_iz_kog_grada_je_kontakt($pdo, $kontakt_id) {
    $stmt = $pdo -> prepare ("SELECT grad
      FROM kontakti as k
      INNER JOIN gradovi as g ON g.grad_id = k.grad_id
      WHERE kontakt_id = :kid");
      $stmt -> execute(array(
        ':kid' => $kontakt_id
      ));
      $row = $stmt -> fetch(PDO::FETCH_ASSOC);
      if ($row == false) {
        return ' ';
      } else {
        return $row['grad'];
      }
  }

  function get_klub($pdo, $klub_id) {
        $stmt = $pdo->prepare("SELECT naziv FROM klubovi WHERE klub_id = :klub_id");
        $stmt->execute(array(":klub_id" => $k_klub_id));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row === false){
          return '';
        } else {
          $klub = htmlentities($row['naziv']);
          return $klub;
        }
  }

  function get_popust_za_kontakt($pdo, $k_pop_grupa_id) {
        $stmt = $pdo->prepare("SELECT grupa, popust_procenat FROM popgrupe
          WHERE pop_id = :pop_grupa_id");
        $stmt->execute(array(":pop_grupa_id" => $k_pop_grupa_id));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row === false){
          $grupa_i_procenat = array('', 0);
        } else {
          $grupa = htmlentities($row['grupa']);
          $popust_procenat = htmlentities($row['popust_procenat']);
          $grupa_i_procenat = array($grupa, $popust_procenat);
        }
        return $grupa_i_procenat;
  }

  function get_magacin_max_kol($pdo, $artikl_id) {
    $sql = "SELECT magacin.magacin_id as magacin_id,
      magacin.artikl_id as artikl_id, SUM(magacin.kolicina) as m_kolicina,
      ao.kontakt_id as kontakt_id
      FROM magacin
      INNER JOIN artikl_orderi  as ao ON ao.artikl_order_id = magacin.artikl_order_id
      WHERE kontakt_id = 42 AND magacin.artikl_id = :artikl_id
      GROUP BY artikl_id";
    $stmt = $pdo -> prepare($sql);
    $stmt -> execute(array(
      ':artikl_id' => $artikl_id
    ));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row === False) {
      return 0;
    }
    return $row['m_kolicina'];
  }

  function statistika_kontakta($pdo, $kontakt_id) {
        $stmt = $pdo->prepare("SELECT COUNT(DISTINCT big_order_id) as ukupno_porudzbina,
        SUM(kolicina) as ukupno_artikala, SUM(ukupna_net) as ukupna_net,
        SUM(prodajna_cena) as ukupna_list, kontakt_id
        FROM `artikl_orderi`
        WHERE kontakt_id = :kid");
        $stmt->execute(array(
          ":kid" => $kontakt_id
        ));
        $kontakt_stat = $stmt->fetch(PDO::FETCH_ASSOC);
        return $kontakt_stat;
  }

  function get_svi_artikli_u_magacinu($pdo) {
    $stmt = $pdo -> prepare ("WITH m as (SELECT magacin.magacin_id as magacin_id,
      magacin.artikl_id as artikl_id, ao.kontakt_id as kontakt_id,
      magacin.kolicina as kolicina, magacin.artikl_order_id as artikl_order_id,
      a.part_number as part_number, a.opis as opis, a.link as link,
      a.cena_net as cena_net, a.cena_list as cena_list, ao.big_order_id as big_order_id
      FROM magacin
      INNER JOIN artikli as a ON a.artikl_id=magacin.artikl_id
      INNER JOIN artikl_orderi as ao ON ao.artikl_order_id=magacin.artikl_order_id)
      SELECT magacin_id, artikl_id, part_number, opis, kolicina, cena_net,
      artikl_order_id, k.ime as ime, bo.oznaka oznaka
      FROM m
      INNER JOIN kontakti AS k ON k.kontakt_id = m.kontakt_id
      INNER JOIN big_orderi AS bo ON bo.big_order_id = m.big_order_id
      ORDER BY artikl_id");
    $stmt -> execute();
    $m_artikli = $stmt -> fetchAll(PDO::FETCH_ASSOC);
    return $m_artikli;
  }

  function get_magacin_data($pdo) {
    $stmt = $pdo -> prepare ("SELECT magacin.magacin_id as magacin_id,
      magacin.artikl_id as artikl_id, SUM(magacin.kolicina) as kolicina,
      a.part_number as part_number, a.opis as opis, a.link as link,
      SUM(cena_net * magacin.kolicina) as cena_net, a.cena_list as cena_list, ao.kontakt_id as kontakt_id
      FROM magacin
      INNER JOIN artikli        as a  ON a.artikl_id        = magacin.artikl_id
      INNER JOIN artikl_orderi  as ao ON ao.artikl_order_id = magacin.artikl_order_id
      WHERE kontakt_id = 42
      GROUP BY part_number
      ORDER BY artikl_id");
    $stmt -> execute();
    $m_artikli = $stmt -> fetchAll(PDO::FETCH_ASSOC);
    return $m_artikli;
  }

  function get_artikl_order_edit_form_data($pdo, $artikl_order_id) {
    $sql = "SELECT ao.artikl_order_id as aoid, ao.big_order_id as aoboid, ao.kontakt_id as aokid,
    ao.kolicina as kolicina, ao.ukupna_net as ukupna_net, ao.validno as validno,
    ao.prodajna_cena as prodajna_cena, ao.datum_porudzbine as datum_porudzbine,
    ao.datum_modifikovanja as datum_modifikovanja, ao.komentar as komentar,
    a.artikl_id as artikl_id, a.part_number as part_number, a.opis as opis, a.link as link,
    a.cena_net as cena_net, a.cena_list as cena_list, a.komentar as a_komentar,
    k.ime as ime
    FROM artikl_orderi as ao
    INNER JOIN artikli as a ON a.artikl_id = ao.artikl_id
    INNER JOIN kontakti as k ON k.kontakt_id = ao.kontakt_id
    WHERE artikl_order_id = :aid";
    $stmt = $pdo -> prepare($sql);
    $stmt -> execute(array(
      ':aid' => $_GET['artikl_order_id']
    ));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row === false){
      $_SESSION['error']  = "Bad value for artikl id = ".$_GET['artikl_order_id'];
      header('Location: http://localhost/trt_mrt/big_order/porucivanje/single_order_interface.php?kontakt_id='.$_GET['kontakt_id'].'&big_order_id='.$_GET['big_order_id']);
      return;
    } else {
      return $row;
    }
  }

    function get_sve_kontaktove_porudzbine($pdo, $kotankt_id) {
      $sql = "SELECT ao.artikl_order_id as artikl_order_id, ao.big_order_id as big_order_id,
      ao.isporucena_kolicina as isp_kol,
      ao.kolicina as kolicina, ao.ukupna_net as ukupna_net, ao.prodajna_cena as prodajna_cena,
      ao.validno as validno, ao.datum_porudzbine as datum_porudzbine, ao.komentar as komentar,
      a.part_number as part_number, a.opis as opis,
      a.cena_net as cena_net
      FROM artikl_orderi as ao
      INNER JOIN artikli as a ON a.artikl_id = ao.artikl_id
      INNER JOIN kontakti as k ON k.kontakt_id = ao.kontakt_id
      WHERE ao.kontakt_id = :kid";
      $stmt = $pdo -> prepare($sql);
      $stmt -> execute(array(
        ':kid' => $kotankt_id
      ));
      $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
      return $rows;
  }

  function get_ao_form_data($pdo, $artikl_order_id) {
    $sql = "SELECT ao.artikl_order_id as aoid, ao.big_order_id as aoboid, ao.kontakt_id as aokid,
    ao.kolicina as kolicina, ao.ukupna_net as ukupna_net, ao.validno as validno,
    ao.prodajna_cena as prodajna_cena, ao.datum_porudzbine as datum_porudzbine,
    ao.datum_modifikovanja as datum_modifikovanja, ao.komentar as komentar,
    a.artikl_id as artikl_id, a.part_number as part_number, a.opis as opis, a.link as link,
    a.cena_net as cena_net, a.cena_list as cena_list, a.komentar as a_komentar,
    k.ime as ime, ao.isporuceno as isporuceno, ao.isporucena_kolicina as isporucena_kolicina
    FROM artikl_orderi as ao
    INNER JOIN artikli as a ON a.artikl_id = ao.artikl_id
    INNER JOIN kontakti as k ON k.kontakt_id = ao.kontakt_id
    WHERE artikl_order_id = :aid";
    $stmt = $pdo -> prepare($sql);
    $stmt -> execute(array(
      ':aid' => $_GET['artikl_order_id']
    ));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row === false){
      $_SESSION['error']  = "Bad value for artikl id = ".$_GET['artikl_order_id'];
      header('Location: http://localhost/trt_mrt/big_order/porucivanje/single_order_interface.php?kontakt_id='.$_GET['kontakt_id'].'&big_order_id='.$_GET['big_order_id']);
      return;
    } else {
      return $row;
    }
  }



}
