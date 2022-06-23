<?php
class Upit {

  //***************** QUERIES ************************
  function svi_big_orderi($pdo){
    $stmt = $pdo -> prepare ("SELECT *
      FROM big_orderi
      ORDER BY datum_otvaranja DESC");
      $stmt -> execute();
      $big_orderi = $stmt -> fetchAll(PDO::FETCH_ASSOC);
      return $big_orderi;
  }

  function get_svi_artikl_orderi($pdo) {
    $stmt = $pdo -> prepare ("SELECT
      ao.artikl_order_id as artikl_order_id, ao.big_order_id as big_order_id,
      a.part_number as part_number, a.opis as opis, ao.prodajna_cena as prodajna_cena,
      ao.kolicina as kolicina, ao.pregledana_kolicina as preg_kol, ao.isporucena_kolicina as isp_kol,
      ao.datum_porudzbine as datum_porudzbine,
      k.ime as ime, k.kontakt_id
      FROM artikl_orderi as ao
      INNER JOIN kontakti as k on k.kontakt_id = ao.kontakt_id
      INNER JOIN artikli as a on a.artikl_id = ao.artikl_id
      ORDER BY artikl_order_id DESC");
      $stmt -> execute();
      $svi_artikl_orderi = $stmt -> fetchAll(PDO::FETCH_ASSOC);
      return $svi_artikl_orderi;
  }

  function get_big_order_stat_data($pdo, $big_order_id){
    $stmt = $pdo -> prepare("SELECT SUM(kolicina) as sum_kolicina,
    SUM(ukupna_net) as sum_ukupna_net, SUM(prodajna_cena) as sum_ukupna_list,
    (SUM(prodajna_cena) - SUM(ukupna_net)) as sum_zarada
    FROM artikl_orderi
    WHERE big_order_id = :boid
    GROUP BY big_order_id");
    $stmt->execute(array(":boid" => $big_order_id));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row == false) {
      return 0;
    } else {
      return $row;
    }
  }

  function stanje_magacina($pdo){
    $stmt = $pdo -> prepare(  "SELECT a.part_number as part_number, a.opis as opis,
      m.kolicina  as dostupna_kolicina, a.artikl_id as artikl_id,
      m.magacin_id as magacin_id
      FROM `magacin` as m
      INNER JOIN artikli as a ON a.artikl_id = m.artikl_id
      INNER JOIN artikl_orderi as ao ON ao.artikl_order_id = m.artikl_order_id
      WHERE ao.kontakt_id = 42
      GROUP BY m.artikl_id");
      $stmt -> execute(array());
      $artikli = $stmt -> fetchAll(PDO::FETCH_ASSOC);
      return $artikli;
  }

  function kolika_je_ukupna_net($pdo, $big_order_id){
    $stmt = $pdo -> prepare("SELECT SUM(ukupna_net) as sum_ukupna_net
    FROM artikl_orderi
    WHERE big_order_id = :boid
    GROUP BY big_order_id");
    $stmt->execute(array(":boid" => $big_order_id));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row == false) {
      return 0;
    } else {
      return $row['sum_ukupna_net'];
    }
  }

  function big_order_ukupan_rashod($pdo, $big_order_id) {
    $stmt = $pdo -> prepare("SELECT SUM(trosak_eura) as ukupan_trosak
    FROM rashodi
    WHERE big_order_id = :boid
    GROUP BY big_order_id");
    $stmt->execute(array(":boid" => $big_order_id));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row == false) {
      return 0;
    } else {
      return $row['ukupan_trosak'];
    }
  }

  function big_order_ukupna_net($pdo, $big_order_id) {
    $stmt = $pdo -> prepare("SELECT SUM(ukupna_net) as suma_ukupnih_net
    FROM artikl_orderi
    WHERE big_order_id = :boid
    GROUP BY big_order_id");
    $stmt->execute(array(":boid" => $big_order_id));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row == false) {
      return 0;
    } else {
      return $row['suma_ukupnih_net'];
    }
  }

  function kolika_je_ukupna_list($pdo, $big_order_id){
    $stmt = $pdo -> prepare("SELECT SUM(prodajna_cena) as sum_ukupna_list
    FROM artikl_orderi
    WHERE big_order_id = :boid
    GROUP BY big_order_id");
    $stmt->execute(array(":boid" => $big_order_id));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row == false) {
      return 0;
    } else {
      return $row['sum_ukupna_list'];
    }
  }

  function update_bo_status($pdo, $big_order_id, $status) {
    $sql = "INSERT INTO bo_statusi (big_order_id, status)
    VALUES (:big_order_id, :status)";
    $stmt = $pdo -> prepare($sql);
    $stmt -> execute(array(
      ':big_order_id' => $_GET['big_order_id'],
      ':status' => $status
    ));
  }

  function get_bo_last_status($pdo, $big_order_id) {
    //funkcija vraca psolednji status za trzani big order
    $stmt = $pdo -> prepare ("SELECT status
    FROM bo_statusi
    WHERE big_order_id = :big_order_id
    ORDER BY id DESC");
    $stmt -> execute(array(
      ':big_order_id' => $big_order_id
    ));
    $bo_statusi = $stmt -> fetchAll(PDO::FETCH_ASSOC);
    return htmlentities($bo_statusi[0]['status']);
  }

  function get_bo_svi_statusi($pdo, $big_order_id) {
    //funkcija vraca psolednji status za trzani big order
    $stmt = $pdo -> prepare ("SELECT *
      FROM bo_statusi
      WHERE big_order_id = :big_order_id
      ORDER BY id DESC");
      $stmt -> execute(array(
        ':big_order_id' => $big_order_id
      ));
      $bo_statusi = $stmt -> fetchAll(PDO::FETCH_ASSOC);
      return $bo_statusi;
  }

  function find_datum_statusa($bo_statusi, $status) {
    foreach ($bo_statusi as $bo_status) {
      if(strcmp($bo_status['status'], $status) === 0) {
        return $bo_status['datum_statusa'];
      }
    }
  }

  function calc_bar_style($status) {
    //na osnovu trnutnog statusa popunjava bar progresa
    $bar_style = 'style="width: 0%;"';
    if(strcmp($status, 'poruceno') === 0) {
      $bar_style = 'style="width: 20%;"';
    }
    if(strcmp($status, 'pregledano') === 0) {
      $bar_style = 'style="width: 40%;"';
    }
    if(strcmp($status, 'isporuceno') === 0) {
      $bar_style = 'style="width: 60%;"';
    }
    if(strcmp($status, 'naplaceno') === 0) {
      $bar_style = 'style="width: 80%;"';
    }
    if(strcmp($status, 'zatvoreno') === 0) {
      $bar_style = 'style="width: 100%;"';
    }
    return $bar_style;
  }

  function get_isporuka_podaci($pdo, $is_isporuceno) {
    $stmt = $pdo -> prepare ("SELECT
      ao.artikl_order_id as artikl_order_id,
      ao.kolicina as ao_kolicina, k.ime as ime,
      a.part_number as part_number, a.link as link,
      a.opis as opis, ao.pregledana_kolicina as pregledana_kolicina,
      ao.isporucena_kolicina as isporucena_kolicina,
      ao.isporuceno as isporuceno, ao.datum_isporuke as datum_isporuke,
      ao.datum_modifikovanja as datum_modifikovanja,
      ao.big_order_id as big_order_id, ao.artikl_id as artikl_id,
      ao.kontakt_id as kontakt_id, ao.kolicina as ao_kolicina,
      ao.ukupna_net as ukupna_net, ao.datum_porudzbine as datum_porudzbine,
      bo.oznaka as bo_oznaka
      FROM artikl_orderi as ao
      INNER JOIN artikli as a ON a.artikl_id = ao.artikl_id
      INNER JOIN kontakti as k ON k.kontakt_id = ao.kontakt_id
      INNER JOIN big_orderi as bo ON bo.big_order_id = ao.big_order_id
      WHERE ao.big_order_id = :boid AND ao.isporuceno = $is_isporuceno
      AND ao.pregledano = 1 AND ao.validno = 1
      ORDER BY ime ASC, part_number ASC");
      $stmt -> execute(array(
        ':boid' => $_GET['big_order_id']
      ));
      $isporuka_podaci = $stmt -> fetchAll(PDO::FETCH_ASSOC);
      return $isporuka_podaci;
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

  function kolika_je_ukupna_zarada_po_kontaktu($pdo, $kontakt_id) {
    $sql = "SELECT SUM(ukupno_eura) as ukupna_zarada FROM bo_naplate
    WHERE kontakt_id = :kontakt_id";
    $stmt = $pdo -> prepare($sql);
    $stmt -> execute(array(
      ':kontakt_id' => $kontakt_id
    ));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row === false){
      return 0;
    } else {
      return floatval($row['ukupna_zarada']);
    }
  }

  function get_datum_last_big_order($pdo, $kontakt_id) {
    $sql = "SELECT datum_porudzbine FROM artikl_orderi
    WHERE kontakt_id = :kontakt_id
    ORDER BY datum_porudzbine DESC";
    $stmt = $pdo -> prepare($sql);
    $stmt -> execute(array(
      ':kontakt_id' => $kontakt_id
    ));
    $rows = $stmt -> fetchAll(PDO::FETCH_ASSOC);
    if (count($rows) == 0) {
      return 'Nikad';
    } else {
      return $rows[0]['datum_porudzbine'];
    }
  }

  function get_kurs($pdo) {
    $stmt = $pdo -> prepare ("SELECT kurs
      FROM kurs
      ORDER BY kurs_id DESC");
      $stmt -> execute(array(
      ));
      $row = $stmt -> fetchAll(PDO::FETCH_ASSOC);
      return floatval($row[0]['kurs']);
  }

  function get_bo_svi_validni_artikli($pdo, $big_order_id) {
    $stmt = $pdo -> prepare ("SELECT ao.artikl_order_id as artikl_order_id, ao.big_order_id as big_order_id,
      ao.kontakt_id as kid, ao.artikl_id aid, ao.iz_magacina as iz_magacina,
      ao.kolicina as o_kolicina, ao.datum_porudzbine, bo.oznaka as bo_oznaka,
      a.part_number as part_number, a.opis as opis, a.cena_net as cena_net
      FROM artikl_orderi as ao
      INNER JOIN artikli as a ON a.artikl_id = ao.artikl_id
      INNER JOIN big_orderi as bo ON bo.big_order_id = ao.big_order_id
      WHERE ao.big_order_id = :boid AND ao.validno = 1
      ORDER BY a.part_number");
      $stmt -> execute(array(
        ':boid' => $big_order_id
      ));
      return $bo_svi_artikli = $stmt -> fetchAll(PDO::FETCH_ASSOC);
  }

  function get_bo_svi_vec_poruceni_artikli($pdo, $big_order_id) {
    $stmt = $pdo -> prepare ("SELECT ao.artikl_order_id as artikl_order_id, ao.big_order_id as big_order_id,
      ao.kontakt_id as kid, ao.artikl_id as artikl_id, ao.iz_magacina as iz_magacina,
      ao.kolicina as o_kolicina, ao.datum_porudzbine,
      a.part_number as part_number, a.opis as opis, a.cena_net as cena_net
      FROM artikl_orderi as ao
      INNER JOIN artikli as a ON a.artikl_id = ao.artikl_id
      WHERE ao.big_order_id = :boid AND ao.poruceno = 1 AND ao.validno = 1
      ORDER BY a.part_number");
      $stmt -> execute(array(
        ':boid' => $big_order_id
      ));
      return $bo_svi_artikli = $stmt -> fetchAll(PDO::FETCH_ASSOC);
  }

  function get_bo_svi_validni_artikli_zbirno($pdo, $big_order_id) {
    $stmt = $pdo -> prepare ("SELECT ao.big_order_id as big_order_id,
      ao.kontakt_id as kid, ao.artikl_id as artikl_id, ao.iz_magacina as iz_magacina,
      SUM(ao.kolicina) as sum_artikala, SUM(ao.ukupna_net) as sum_ukupna_net,
      a.part_number as part_number, a.opis as opis, bo.oznaka as bo_oznaka
      FROM artikl_orderi as ao
      INNER JOIN artikli as a ON a.artikl_id = ao.artikl_id
      INNER JOIN big_orderi as bo ON bo.big_order_id = ao.big_order_id
      WHERE ao.big_order_id = :boid AND ao.validno = 1 AND ao.iz_magacina = 0
      GROUP BY artikl_id
      ORDER BY a.part_number");
      $stmt -> execute(array(
        ':boid' => $big_order_id
      ));
      return $bo_svi_artikli = $stmt -> fetchAll(PDO::FETCH_ASSOC);
  }

  function get_bo_svi_jos_neporuceni_artikli($pdo, $big_order_id) {
    $stmt = $pdo -> prepare ("SELECT ao.artikl_order_id as artikl_order_id, ao.big_order_id as big_order_id,
      ao.kontakt_id as kid, ao.artikl_id aid, ao.iz_magacina as iz_magacina,
      ao.kolicina as o_kolicina, ao.datum_porudzbine,
      a.part_number as part_number, a.opis as opis, a.cena_net as cena_net
      FROM artikl_orderi as ao
      INNER JOIN artikli as a ON a.artikl_id = ao.artikl_id
      WHERE ao.big_order_id = :boid AND ao.poruceno = 0 AND ao.validno = 1
      ORDER BY a.part_number");
      $stmt -> execute(array(
        ':boid' => $big_order_id
      ));
      return $bo_svi_artikli = $stmt -> fetchAll(PDO::FETCH_ASSOC);
  }

  function update_ao_porucen($pdo, $artikl_order_id) {
    $sql = " UPDATE artikl_orderi
    SET poruceno = :poruceno, datum_ordera = :datum_ordera
    WHERE artikl_order_id= :artikl_order_id";

    $date = date("Y-m-d H:i:s");
    $stmt = $pdo -> prepare($sql);
    $stmt -> execute(array(
      ':poruceno'         => 1,
      ':artikl_order_id'  => $artikl_order_id,
      ':datum_ordera'     => $date
    ));
  }

  function is_pk_valid($narucena_kolicina, $pregledana_kolicina) {
    //ukoliko je upisana pregledana kolicina jednaka ili manja od narucene
    //ova funkcija vraca vrednsot True, ako nije vraca False
    if ($pregledana_kolicina <= $narucena_kolicina) {
      return True;
    } else {
      return False;
    }
  }

  function get_pregled_podaci($pdo, $big_order_id, $is_pregledano) {
    $stmt = $pdo -> prepare ("SELECT ao.artikl_order_id as artikl_order_id, ao.kolicina as ao_kolicina,
      ao.pregledano as pregledano, ao.big_order_id as big_order_id,
      ao.datum_pregleda as datum_pregleda, ao.datum_modifikovanja as datum_modifikovanja,
      ao.artikl_id as artikl_id, ao.kontakt_id as kontakt_id,
      ao.pregledana_kolicina as pregledana_kolicina, ao.validno as validno,
      ao.ukupna_net as ukupna_net, ao.datum_porudzbine as datum_porudzbine,
      ao.pregledano as pregledano, ao.ukupna_net as ukupna_net,
      a.part_number as part_number, a.opis as opis, a.link as link,
      bo.oznaka as bo_oznaka, k.ime as ime
      FROM artikl_orderi as ao
      INNER JOIN artikli as a ON a.artikl_id = ao.artikl_id
      INNER JOIN kontakti as k ON k.kontakt_id = ao.kontakt_id
      INNER JOIN big_orderi as bo ON bo.big_order_id = ao.big_order_id
      WHERE ao.big_order_id = :boid AND ao.pregledano = $is_pregledano
      ORDER BY ime ASC, part_number ASC");
      $stmt -> execute(array(
        ':boid' => $big_order_id
      ));
      $pregled_podaci = $stmt -> fetchAll(PDO::FETCH_ASSOC);
      return $pregled_podaci;
  }

  function update_ao_pregled($pdo, $artikl_order_id, $pregledana_kolicina) {
    //funckiaj zaurira tabelu bo_pregledi, upisuje kolicinu i nije pregeldano prevodi u jeste
    $sql = " UPDATE artikl_orderi
    SET pregledana_kolicina = :pregledana_kolicina, pregledano = :pregledano,
    datum_pregleda = :datum_pregleda
    WHERE artikl_order_id= :artikl_order_id";

    $date = date("Y-m-d H:i:s");
    $stmt = $pdo -> prepare($sql);
    $stmt -> execute(array(
      ':artikl_order_id' => $_POST['artikl_order_id'],
      ':pregledana_kolicina' => $_POST['pregledana_kolicina'],
      ':pregledano' => 1,
      ':datum_pregleda' => $date
    ));
  }

  function update_ao_isporuka($pdo, $artikl_order_id, $isporucena_kolicina) {
    //funckiaj zaurira tabelu bo_pregledi, upisuje kolicinu i nije pregeldano prevodi u jeste
    $sql = " UPDATE artikl_orderi
    SET isporucena_kolicina = :isporucena_kolicina, isporuceno = :isporuceno,
    datum_isporuke = :datum_isporuke
    WHERE artikl_order_id= :artikl_order_id";

    $date = date("Y-m-d H:i:s");
    $stmt = $pdo -> prepare($sql);
    $stmt -> execute(array(
      ':artikl_order_id'      => $artikl_order_id,
      ':isporucena_kolicina'  => $isporucena_kolicina,
      ':isporuceno'           => 1,
      ':datum_isporuke'       => $date
    ));
  }

  function update_magacin($pdo, $artikl_order_id, $pregledana_kolicina) {
    //funckiaj zaurira tabelu bo_pregledi, upisuje kolicinu i nije pregeldano prevodi u jeste
    $sql = " UPDATE magacin
    SET kolicina = :pregledana_kolicina
    WHERE artikl_order_id = :aoid";
    $stmt = $pdo -> prepare($sql);
    $stmt -> execute(array(
      ':aoid' => $artikl_order_id,
      ':pregledana_kolicina' => $pregledana_kolicina
    ));
  }

  function otpremi_artikl_iz_magacina($pdo, $aoid) {
    $sql = "DELETE FROM magacin
    WHERE artikl_order_id= :artikl_order_id";
    $stmt = $pdo -> prepare($sql);
    $stmt -> execute(array(
      ':artikl_order_id' => $_POST['artikl_order_id']
    ));
  }

  function skini_sa_stanja($pdo, $artikl_id, $kolicina) {
    //provertiti da li uposte ima artikla u zeljenoj kolicina_u_magacinu
    $raspoloziva_kolicina = ($this -> magacin_stanje_artikla($pdo, $artikl_id)) - $kolicina;
    if($raspoloziva_kolicina >= 0) {
      //dobiti sve pojedinacno upisane artikle u magacin preko magacin id
      $magacinski_artikli_pojedinacno = $this -> pronadji_sve_magacin_id($pdo, $artikl_id);
      foreach ($magacinski_artikli_pojedinacno as $map) {
        //za svaki pojedinacno upisan artikal proveriti da li zadovoljava potrebe porudzbine
        $kolicina = $kolicina - intval($map['kolicina']);
        if ($kolicina = 0) {
          //ako zadovoljava obrisati magacin id i prekinuti foreach
          $this -> magacin_delete_artikl($pdo, $map['magacin_id']);
          break;
        } else if ($kolicina < 0){
          //ako zadovoljava i vise nego sto treba updateovati magacin id i prekinuti foreach
          $this -> magacin_update_artikl($pdo, $map['magacin_id'], (-$kolicina));
          break;
        } else {
          //ako ne zadovoljava obrisati tog koji ne dozvoljava i vreteti upit dalje
          $this -> magacin_delete_artikl($pdo, $map['magacin_id']);
        }
      }
    }
  }

  function pronadji_sve_magacin_id($pdo, $artikl_id) {
    $sql = "SELECT magacin_id, artikl_id, kolicina
    FROM MAGACIN
    WHERE artikl_id = :artikl_id
    ORDER BY magacin_id ASC";
    $stmt = $pdo -> prepare($sql);
    $stmt -> execute(array(
      ':artikl_id' => $artikl_id
    ));
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $rows;
  }

  //help za skini sa stanja
  function magacin_update_artikl($pdo, $magacin_id, $rasp_kolicina) {
    $sql = "UPDATE magacin
    SET kolicina = :rasp_kolicina
    WHERE magacin_id= :magacin_id";
    $stmt = $pdo -> prepare($sql);
    $stmt -> execute(array(
      ':magacin_id' => $magacin_id,
      ':rasp_kolicina' => $rasp_kolicina
    ));
  }

  //help za skini sa stanja
  function magacin_delete_artikl($pdo, $magacin_id) {
    $sql = "DELETE FROM magacin
    WHERE magacin_id= :magacin_id";
    $stmt = $pdo -> prepare($sql);
    $stmt -> execute(array(
      ':magacin_id' => $magacin_id
    ));
  }

  //help za skini sa stanja
  function magacin_stanje_artikla($pdo, $artikl_id) {
    $sql = "SELECT SUM(kolicina) as m_kolicina FROM MAGACIN WHERE artikl_id = :artikl_id";
    $stmt = $pdo -> prepare($sql);
    $stmt -> execute(array(
      ':artikl_id' => $artikl_id
    ));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row === false){
      return 0;
    } else {
      $kolicina = intval($row['m_kolicina']);
      return $kolicina;
    }
  }


  //help za skini sa stanja
  function kolicina_artikla_u_magacinu($pdo, $artikl_order_id) {
    $sql = "SELECT kolicina
    FROM magacin
    WHERE artikl_order_id = :aid";
    $stmt = $pdo -> prepare($sql);
    $stmt -> execute(array(
      ':aid' => $artikl_order_id
    ));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row === false){
      return 0;
    } else {
      return $row['kolicina'];
    }
  }

  //help za skini sa stanja
  function dodaj_pregledani_artikl_u_magacin($pdo, $artikl_id, $artikl_order_id, $pregledana_kolicina) {
    $kolicina_u_magacinu = $this -> kolicina_artikla_u_magacinu($pdo, $artikl_order_id);
    $kolicina_za_upis_u_magacin = $pregledana_kolicina - $kolicina_u_magacinu;
    $sql = "INSERT INTO magacin (artikl_id, artikl_order_id, kolicina)
    VALUES (:artikl_id, :artikl_order_id, :kolicina)";
    $stmt = $pdo -> prepare($sql);
    $stmt -> execute(array(
      ':artikl_id' => $artikl_id,
      ':artikl_order_id' => $artikl_order_id,
      ':kolicina' => $kolicina_za_upis_u_magacin
    ));
  }

  function insert_aoid_u_magacin($pdo, $artikl_order_id, $artikl_id, $kolicina) {
    $sql = "INSERT INTO magacin
    (artikl_order_id, artikl_id, kolicina)
    VALUES (:artikl_order_id, :artikl_id, :kolicina)";
    $stmt = $pdo -> prepare($sql);
    $stmt -> execute(array(
      ':artikl_order_id' => $artikl_order_id,
      ':artikl_id' => $artikl_id,
      ':kolicina' => $kolicina
    ));
  }

  function prebaci_u_vlasnistvo_trtmrta($pdo, $aoid, $big_order_id, $artikl_id, $kolicina) {
    $this -> set_ao_nevalidno($pdo, $aoid);
    $this -> otpremi_artikl_iz_magacina($pdo, $aoid);
    $new_aoid = $this -> insert_new_trtmrt_aoid($pdo, $artikl_id, $big_order_id, $kolicina);
    $this -> insert_aoid_u_magacin($pdo, $new_aoid, $artikl_id, $kolicina);
  }

  //helper za prebaci_u_vlasnistvo_trtmrta
  function set_ao_nevalidno($pdo, $aoid) {
    $sql = "UPDATE `artikl_orderi`
    SET `validno`= 0, `isporucena_kolicina` = 0, `isporuceno` = 0
    WHERE artikl_order_id = :aoid";
    $stmt = $pdo -> prepare($sql);
    $stmt -> execute(array(
      ':aoid' => $aoid
    ));
  }
  //helper za prebaci_u_vlasnistvo_trtmrta
  function insert_new_trtmrt_aoid($pdo, $artikl_id, $big_order_id, $kolicina) {
    $sql = "INSERT INTO artikl_orderi (big_order_id, kontakt_id,
      artikl_id, kolicina, iz_magacina, ukupna_net, prodajna_cena, komentar)
      VALUES (:big_order_id, :kontakt_id, :artikl_id, :kolicina,
        :iz_magacina, :ukupna_net, :prodajna_cena, :komentar)";
        $stmt = $pdo -> prepare($sql);
        $stmt -> execute(array(
          ':big_order_id' => $big_order_id,
          ':kontakt_id' => "42",
          ':artikl_id' => $artikl_id,
          ':kolicina' => $kolicina,
          ':iz_magacina' => 0,
          ':ukupna_net' => 0,
          ':prodajna_cena' => 0,
          ':komentar' => 'Nije isporuceno kupcu'
        ));
        return ($pdo -> lastInsertId());
  }

  function insert_u_bo_naplate($pdo, $big_order_id, $kontakt_id) {
    if ($this -> naplata_nije_registrovana($pdo, $big_order_id, $kontakt_id)) {
      $sql = "INSERT INTO bo_naplate
      (big_order_id, kontakt_id)
      VALUES (:big_order_id, :kontakt_id)";
      $stmt = $pdo -> prepare($sql);
      $stmt -> execute(array(
        ':big_order_id' => $big_order_id,
        ':kontakt_id' => $kontakt_id
      ));
    }
  }

  function naplata_nije_registrovana($pdo, $big_order_id, $kontakt_id){
    $sql = "SELECT * FROM bo_naplate
    WHERE big_order_id = :big_order_id AND kontakt_id = :kontakt_id";
    $stmt = $pdo -> prepare($sql);
    $stmt -> execute(array(
      ':big_order_id' => $big_order_id,
      ':kontakt_id' => $kontakt_id
    ));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row === false){
      return true;
    } else {
      return false;
    }
  }

  function broj_porucenih_artikala_kontakta_u_ovoj_porudzbini($pdo, $big_order_id, $kontakt_id) {
    $stmt = $pdo -> prepare ("SELECT SUM(kolicina) as sum_kolicina
    FROM artikl_orderi
    WHERE big_order_id = :boid AND kontakt_id = :kontakt_id");
    $stmt -> execute(array(
      ':boid' => $big_order_id,
      ':kontakt_id' => $kontakt_id
    ));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row === false){
      return 0;
    } else {
      return $row['sum_kolicina'];
    }
  }

  function ukupna_net_kontakta_u_ovoj_porudzbini($pdo, $big_order_id, $kontakt_id) {
    $stmt = $pdo -> prepare ("SELECT SUM(ukupna_net) as sum_ukupna_net
    FROM artikl_orderi
    WHERE big_order_id = :boid AND kontakt_id = :kontakt_id");
    $stmt -> execute(array(
      ':boid' => $big_order_id,
      ':kontakt_id' => $kontakt_id
    ));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row === false){
      return 0;
    } else {
      return $row['sum_ukupna_net'];
    }
  }

  function ukupna_prodajna_kontakta_u_ovoj_porudzbini($pdo, $big_order_id, $kontakt_id) {
    $stmt = $pdo -> prepare ("SELECT SUM(prodajna_cena) as sum_prodajna_cena
    FROM artikl_orderi
    WHERE big_order_id = :boid AND kontakt_id = :kontakt_id");
    $stmt -> execute(array(
      ':boid' => $big_order_id,
      ':kontakt_id' => $kontakt_id
    ));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row === false){
      return 0;
    } else {
      return $row['sum_prodajna_cena'];
    }
  }

  function sta_sve_od_porucenog_nije_stiglo($pdo, $big_order_id, $kontakt_id) {
    $stmt = $pdo -> prepare ("SELECT a.part_number as part_number,
      a.opis as opis, ao.kolicina as kolicina
      FROM artikl_orderi as ao
      INNER JOIN artikli as a ON a.artikl_id = ao.artikl_id
      WHERE ao.big_order_id = :boid AND ao.kontakt_id = :kontakt_id
      AND ao.kolicina > ao.pregledana_kolicina");
      $stmt -> execute(array(
        ':boid' => $big_order_id,
        ':kontakt_id' => $kontakt_id
      ));
      $nisu_stigli = $stmt -> fetchAll(PDO::FETCH_ASSOC);
      return $nisu_stigli;
  }

  function sta_sve_od_porucenog_nije_isporuceno($pdo, $big_order_id, $kontakt_id) {
    $stmt = $pdo -> prepare ("SELECT a.part_number as part_number,
      a.opis as opis, ao.kolicina as kolicina
      FROM artikl_orderi as ao
      INNER JOIN artikli as a ON a.artikl_id = ao.artikl_id
      WHERE ao.big_order_id = :boid AND ao.kontakt_id = :kontakt_id
      AND ao.kolicina > ao.isporucena_kolicina");
      $stmt -> execute(array(
        ':boid' => $big_order_id,
        ':kontakt_id' => $kontakt_id
      ));
      $neisporuceno = $stmt -> fetchAll(PDO::FETCH_ASSOC);
      return $neisporuceno;
  }

  function sta_je_od_artikala_stiglo_u_visku($pdo, $big_order_id){
    $stmt = $pdo -> prepare ("SELECT a.part_number as part_number,
      a.opis as opis, ao.kolicina as kolicina
      FROM artikl_orderi as ao
      INNER JOIN artikli as a ON a.artikl_id = ao.artikl_id
      WHERE ao.big_order_id = :boid AND ao.kontakt_id = :kontakt_id
      AND ao.pregledana_kolicina > ao.isporucena_kolicina");
      $stmt -> execute(array(
        ':boid' => $big_order_id,
        ':kontakt_id' => $kontakt_id
      ));
      $ostalo_u_visku = $stmt -> fetchAll(PDO::FETCH_ASSOC);
      return $ostalo_u_visku;
  }

  function da_li_su_isporuceni_svi_artikli($pdo, $big_order_id, $kontakt_id) {
    $stmt = $pdo -> prepare ("SELECT ao.pregledana_kolicina as pregledana_kolicina,
      ao.isporucena_kolicina as isporucena_kolicina
      FROM artikl_orderi as ao
      WHERE ao.big_order_id = :boid AND ao.kontakt_id = :kontakt_id
      AND ao.pregledana_kolicina > ao.isporucena_kolicina");
      $stmt -> execute(array(
        ':boid' => $big_order_id,
        ':kontakt_id' => $kontakt_id
      ));
      $nije_isporuceno = $stmt -> fetchAll(PDO::FETCH_ASSOC);
      if (empty($nije_isporuceno)) {
        return true;
      } else {
        return false;
      }
    }

  function da_li_su_stigli_svi_artikli($pdo, $big_order_id, $kontakt_id) {
    $stmt = $pdo -> prepare ("SELECT ao.kolicina as kolicina,
      ao.pregledana_kolicina as pregledana_kolicina
      FROM artikl_orderi as ao
      WHERE ao.big_order_id = :boid AND ao.kontakt_id = :kontakt_id
      AND ao.kolicina > ao.pregledana_kolicina");
      $stmt -> execute(array(
        ':boid' => $big_order_id,
        ':kontakt_id' => $kontakt_id
      ));
      $nije_stiglo = $stmt -> fetchAll(PDO::FETCH_ASSOC);
      if (empty($nije_stiglo)) {
        return true;
      } else {
        return false;
      }
  }

  function svi_poruceni_artikli($pdo, $big_order_id){
    $stmt = $pdo -> prepare ("SELECT k.ime as ime, a.part_number as part_number,
      a.opis as opis, ao.kolicina as kolicina,
      ao.ukupna_net as net_cena, ao.prodajna_cena as prodajna_cena
      FROM artikl_orderi as ao
      INNER JOIN artikli as a ON a.artikl_id = ao.artikl_id
      INNER JOIN kontakti as k ON k.kontakt_id = ao.kontakt_id
      WHERE ao.big_order_id = :boid
      ORDER BY ime ASC");
      $stmt -> execute(array(
        ':boid' => $big_order_id
      ));
      $svi_poruceni = $stmt -> fetchAll(PDO::FETCH_ASSOC);
      return $svi_poruceni;
  }

  function svi_artikli_po_statusu_pregleda($pdo, $big_order_id, $status_pregleda){
    $stmt = $pdo -> prepare ("SELECT k.ime as ime, a.part_number as part_number,
      a.opis as opis, ao.kolicina as kolicina,
      ao.ukupna_net as net_cena, ao.prodajna_cena as prodajna_cena
      FROM artikl_orderi as ao
      INNER JOIN artikli as a ON a.artikl_id = ao.artikl_id
      INNER JOIN kontakti as k ON k.kontakt_id = ao.kontakt_id
      WHERE ao.big_order_id = :boid AND ao.pregledano = :sp
      ORDER BY ime ASC");
      $stmt -> execute(array(
        ':boid' => $big_order_id,
        ':sp'   => $status_pregleda
      ));
      $svi_pregledani_po_statusu = $stmt -> fetchAll(PDO::FETCH_ASSOC);
      return $svi_pregledani_po_statusu;
  }

  function svi_artikli_stigli_u_visku($pdo, $big_order_id){

  }

  function svi_artikli_po_statusu_isporuke($pdo, $big_order_id, $status_isporuke){
    $stmt = $pdo -> prepare ("SELECT k.ime as ime, a.part_number as part_number,
      a.opis as opis, ao.kolicina as kolicina,
      ao.ukupna_net as net_cena, ao.prodajna_cena as prodajna_cena
      FROM artikl_orderi as ao
      INNER JOIN artikli as a ON a.artikl_id = ao.artikl_id
      INNER JOIN kontakti as k ON k.kontakt_id = ao.kontakt_id
      WHERE ao.big_order_id = :boid AND ao.isporuceno = :si
      ORDER BY ime ASC");
      $stmt -> execute(array(
        ':boid' => $big_order_id,
        ':si'   => $status_isporuke
      ));
      $svi_isporuceni_po_statusu = $stmt -> fetchAll(PDO::FETCH_ASSOC);
      return $svi_isporuceni_po_statusu;
  }

  function svi_artikli_po_statusu_naplate($pdo, $big_order_id, $status_naplate){
    $stmt = $pdo -> prepare ("SELECT k.ime as ime, bon.ukupno_eura as ukupno_eura,
      bon.datum_naplate as datum_naplate
      FROM bo_naplate as bon
      INNER JOIN kontakti as k ON k.kontakt_id = bon.kontakt_id
      WHERE bon.big_order_id = :boid AND bon.naplaceno = :sn
      ORDER BY ime ASC");
      $stmt -> execute(array(
        ':boid' => $big_order_id,
        ':sn'   => $status_naplate
      ));
      $svi_naplaceni_po_statusu = $stmt -> fetchAll(PDO::FETCH_ASSOC);
      return $svi_naplaceni_po_statusu;
  }



  //*******************FINANSIJE IZVESTAJ UPITI*******************

  function big_order_zarada ($pdo, $big_order_id){
    $stmt = $pdo -> prepare ("SELECT SUM(ukupno_eura) as ukupno_eura
    FROM bo_naplate as bon
    WHERE bon.big_order_id = :boid AND bon.naplaceno = 1");
    $stmt -> execute(array(
      ':boid' => $big_order_id
    ));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row === false){
      return 0;
    } else {
      return $row['ukupno_eura'];
    }
  }

  function big_order_troskovi($pdo, $big_order_id){
    $stmt = $pdo -> prepare ("SELECT SUM(trosak_eura) as trosak_eura
    FROM rashodi
    WHERE big_order_id = :boid");
    $stmt -> execute(array(
      ':boid' => $big_order_id
    ));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row === false){
      return 0;
    } else {
      return $row['trosak_eura'];
    }
  }

  function masa_lena_rekviziti_trosak($pdo, $big_order_id, $masa_lena_id){
    $stmt = $pdo -> prepare ("SELECT SUM(prodajna_cena) as trosak
    FROM artikl_orderi as ao
    WHERE ao.big_order_id = :boid AND ao.kontakt_id = :kid");
    $stmt -> execute(array(
      ':boid' => $big_order_id,
      ':kid' => $masa_lena_id
    ));
    $row = $stmt -> fetch(PDO::FETCH_ASSOC);
    if ($row === false){
      return 0.0;
    } else {
      return $row['trosak'];
    }
  }

function koliko_ima_kupaca($pdo, $big_order_id){
  $stmt = $pdo -> prepare ("SELECT COUNT(DISTINCT kontakt_id) as broj_kupaca
  FROM artikl_orderi as ao
  WHERE ao.big_order_id = :boid");
  $stmt -> execute(array(
    ':boid' => $big_order_id
  ));
  $row = $stmt -> fetch(PDO::FETCH_ASSOC);
  if ($row === false){
    return 0;
  } else {
    return $row['broj_kupaca'];
  }
}

  function koliko_je_ukupno_artikala_poruceno($pdo, $big_order_id){
    $stmt = $pdo -> prepare ("SELECT SUM(ao.kolicina) as ukupno_artikala
    FROM artikl_orderi as ao
    INNER JOIN artikli as a ON a.artikl_id = ao.artikl_id
    WHERE ao.big_order_id = :boid");
    $stmt -> execute(array(
      ':boid' => $big_order_id
    ));
    $row = $stmt -> fetch(PDO::FETCH_ASSOC);
    if ($row === false){
      return 0;
    } else {
      return $row['ukupno_artikala'];
    }
  }

  function ukupno_artikala_po_statusu_pregleda($pdo, $big_order_id, $status){
    $stmt = $pdo -> prepare ("SELECT SUM(ao.kolicina) as ukupno_artikala_stiglo
    FROM artikl_orderi as ao
    WHERE ao.big_order_id = :boid AND ao.pregledano = :st");
    $stmt -> execute(array(
      ':boid' => $big_order_id,
      ':st'   => $status
    ));
    $row = $stmt -> fetch(PDO::FETCH_ASSOC);
    if ($row === false){
      return 0;
    } else {
      return $row['ukupno_artikala_stiglo'];
    }
  }

  function koliko_je_ukupno_artikala_stiglo($pdo, $big_order_id, $status){
    $stmt = $pdo -> prepare ("SELECT SUM(ao.pregledana_kolicina) as ukupno_artikala_stiglo
    FROM artikl_orderi as ao
    WHERE ao.big_order_id = :boid AND ao.pregledano = :st");
    $stmt -> execute(array(
      ':boid' => $big_order_id,
      ':st'   => $status
    ));
    $row = $stmt -> fetch(PDO::FETCH_ASSOC);
    if ($row === false){
      return 0;
    } else {
      return $row['ukupno_artikala_stiglo'];
    }
  }

  function koliko_je_ukupno_artikala_isporuceno($pdo, $big_order_id, $status){
    $stmt = $pdo -> prepare ("SELECT SUM(ao.isporucena_kolicina) as ukupno_artikala_isporuceno
    FROM artikl_orderi as ao
    WHERE ao.big_order_id = :boid AND ao.isporuceno = :st");
    $stmt -> execute(array(
      ':boid' => $big_order_id,
      ':st'   => $status
    ));
    $row = $stmt -> fetch(PDO::FETCH_ASSOC);
    if ($row === false){
      return 0;
    } else {
      return $row['ukupno_artikala_isporuceno'];
    }
  }

  function kada_je_bio_udpate_statusa_big_ordera($pdo, $big_order_id, $status){

  }

  function izvestaj_detaljan_prikaz($pdo, $big_order_id){
    $stmt = $pdo -> prepare ("SELECT k.ime as ime, a.part_number as part_number,
      a.opis as opis, ao.kolicina as kolicina,
      ao.ukupna_net as net_cena, ao.prodajna_cena as prodajna_cena,
      ao.pregledano as pregledano, ao.isporuceno as isporuceno
      FROM artikl_orderi as ao
      INNER JOIN artikli as a ON a.artikl_id = ao.artikl_id
      INNER JOIN kontakti as k ON k.kontakt_id = ao.kontakt_id
      WHERE ao.big_order_id = :boid
      ORDER BY ime ASC");
      $stmt -> execute(array(
        ':boid' => $big_order_id
      ));
      $artikl_order_izvetaji = $stmt -> fetchAll(PDO::FETCH_ASSOC);
      return $artikl_order_izvetaji;
    }

  //*************** LOGIC ***************************************
  function izracunaj_procenat_bara($neisporuceni_count, $isporuceni_count) {
    $procenat = 0;
    if ($neisporuceni_count + $isporuceni_count !== 0) {
      $procenat = ($isporuceni_count / ($neisporuceni_count + $isporuceni_count)) * 100;
    }
    return $procenat;
  }

  //********************** UTIL *************************************
  function session_message() {
    if (isset($_SESSION['success'])) {
      echo "<h4 style='color:green'>".$_SESSION['success']."</h4>\n";
      unset($_SESSION['success']);
    } else if(isset($_SESSION['error'])) {
      echo "<h4 style='color:red'>".$_SESSION['error']."</h4>\n";
      unset($_SESSION['error']);
    }
  }

}
