<?php

class FinansijeUpiti {

  function get_troskovi_ordera($pdo, $big_order_id) {
    $stmt = $pdo -> prepare ("SELECT SUM(trosak_eura) as trosak
    FROM rashodi
    WHERE ig_order_id = :big_order_id");
    $stmt -> execute(array(
      'big_order_id '=> $big_order_id
    ));
    $row = $stmt -> fetchAll(PDO::FETCH_ASSOC);
    return floatval($row[0]['trosak']);
  }

  function get_svi_rashodi_u_eurima($pdo) {
    $stmt = $pdo -> prepare ("SELECT SUM(trosak_eura) as ukupan_trosak
      FROM rashodi");
      $stmt -> execute();
      $row = $stmt -> fetch(PDO::FETCH_ASSOC);
      if ($row == false) {
        return '0';
      } else {
        return $row['ukupan_trosak'];
      }
    }

  function get_svi_rashodi($pdo) {
    $stmt = $pdo -> prepare ("SELECT *
      FROM rashodi
      ORDER BY rashod_id DESC");
      $stmt -> execute();
      $rashodi = $stmt -> fetchAll(PDO::FETCH_ASSOC);
      return $rashodi;
    }

  function get_svi_prihodi_u_eurima($pdo) {
      $stmt = $pdo -> prepare ("SELECT SUM(ukupno_eura) as ukupan_prihod
      FROM bo_naplate");
      $stmt -> execute(array(
      ));
      $row = $stmt -> fetch(PDO::FETCH_ASSOC);
      if ($row == false) {
        return '0';
      } else {
        return $row['ukupan_prihod'];
      }
  }

  function get_ukupan_prihod_big_ordera($pdo, $big_order_id) {
      $stmt = $pdo -> prepare ("SELECT SUM(ukupno_eura) as ukupan_prihod
      FROM bo_naplate
      WHERE big_order_id = :big_order_id");
      $stmt -> execute(array(
        ':big_order_id' => $big_order_id
      ));
      $row = $stmt -> fetch(PDO::FETCH_ASSOC);
      if ($row == false) {
        return '0';
      } else {
        return $row['ukupan_prihod'];
      }
  }

  function get_masa_lena_trosak_u_big_orderu($pdo, $big_order_id, $masa_lena_kontakt_id) {
    $stmt = $pdo -> prepare ("SELECT ukupno_eura
    FROM bo_naplate
    WHERE big_order_id = :big_order_id, kontakt_id =: kontakt_id");
    $stmt -> execute(array(
      ':big_order_id' => $big_order_id,
      ':kontakt_id' => $masa_lena_kontakt_id
    ));
    $row = $stmt -> fetch(PDO::FETCH_ASSOC);
    if ($row == false) {
      return '0';
    } else {
      return $row['ukupno_eura'];
    }
  }

  function get_masa_lena_ukupan_trosak($pdo, $masa_lena_kontakt_id) {
    $stmt = $pdo -> prepare ("SELECT SUM(ukupno_eura) as trosak_rekvizita
    FROM bo_naplate
    WHERE kontakt_id = :kontakt_id");
    $stmt -> execute(array(
      ':kontakt_id' => $masa_lena_kontakt_id
    ));
    $row = $stmt -> fetch(PDO::FETCH_ASSOC);
    if ($row == false) {
      return '0';
    } else {
      return $row['trosak_rekvizita'];
    }
  }

  function get_svi_big_orderi($pdo) {
    $stmt = $pdo -> prepare ("SELECT * FROM big_orderi
      ORDER BY big_order_id");
      $stmt -> execute();
      $big_orderi = $stmt -> fetchAll(PDO::FETCH_ASSOC);
      return $big_orderi;
  }

}
