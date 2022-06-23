<?php
class NaplataQueries {

  function bo_naplata_query($pdo, $is_naplaceno) {
    $stmt = $pdo -> prepare ("SELECT bon.naplata_id as naplata_id,
      bon.kontakt_id as kontakt_id, bon.ukupno_eura as ukupno_eura,
      bon.naplaceno as naplaceno, bon.datum_naplate as datum_naplate,
      bon.datum_modifikovanja as datum_modifikovanja, k.ime as ime,
      bo.oznaka as bo_oznaka, bon.big_order_id as big_order_id
      FROM bo_naplate as bon
      INNER JOIN kontakti as k ON k.kontakt_id = bon.kontakt_id
      INNER JOIN big_orderi as bo ON bo.big_order_id = bon.big_order_id
      WHERE bon.big_order_id = :boid AND naplaceno = :naplaceno
      ORDER BY ime ASC");
      $stmt -> execute(array(
        ':boid' => $_GET['big_order_id'],
        ':naplaceno' => $is_naplaceno
      ));
      $naplata_podaci = $stmt -> fetchAll(PDO::FETCH_ASSOC);
      return $naplata_podaci;
    }

    function koliko_je_naplaceno($pdo, $big_order_id, $kontakt_id) {
      $stmt = $pdo -> prepare ("SELECT ukupno_eura as ukupno_eura
      FROM bo_naplate
      WHERE big_order_id = :boid AND kontakt_id = :kontakt_id");
      $stmt -> execute(array(
        ':boid' => $big_order_id,
        ':kontakt_id' => $kontakt_id
      ));
      $row = $stmt -> fetch(PDO::FETCH_ASSOC);
      if ($row == false) {
        return '0';
      } else {
        return $row['ukupno_eura'];
      }
    }

    function za_naplatu_na_osnovu_isporucenog($pdo, $big_order_id, $kotnakt_id) {
      $stmt = $pdo -> prepare ("SELECT SUM(ao.prodajna_cena * ao.isporucena_kolicina / ao.kolicina) as ukupno_eura
      FROM artikl_orderi as ao
      WHERE ao.big_order_id = :boid AND ao.kontakt_id = :kontakt_id
      AND ao.isporuceno = 1");
      $stmt -> execute(array(
        ':boid' => $big_order_id,
        ':kontakt_id' => $kotnakt_id
      ));
      $row = $stmt -> fetch(PDO::FETCH_ASSOC);
      if ($row == false) {
        return '0';
      } else {
        return number_format($row['ukupno_eura'], 1);
      }
    }

    function poruceno_a_nije_isporuceno($pdo, $big_order_id, $kontakt_id) {
      $stmt = $pdo -> prepare ("SELECT a.part_number as part_number,
        a.opis as opis, ao.kolicina as kolicina
        FROM artikl_orderi as ao
        INNER JOIN artikli as a ON a.artikl_id = ao.artikl_id
        WHERE ao.big_order_id = :boid AND ao.kontakt_id = :kontakt_id
        AND ao.isporuceno = 0");
        $stmt -> execute(array(
          ':boid' => $big_order_id,
          ':kontakt_id' => $kontakt_id
        ));
        $neisporuceno = $stmt -> fetchAll(PDO::FETCH_ASSOC);
        return $neisporuceno;
      }

      function da_li_su_isporuceni_svi_artikli($pdo, $big_order_id, $kontakt_id) {
        $stmt = $pdo -> prepare ("SELECT a.part_number as part_number,
          a.opis as opis, ao.kolicina as kolicina
          FROM artikl_orderi as ao
          INNER JOIN artikli as a ON a.artikl_id = ao.artikl_id
          WHERE ao.big_order_id = :boid AND ao.kontakt_id = :kontakt_id
          AND ao.isporuceno = 0");
          $stmt -> execute(array(
            ':boid' => $big_order_id,
            ':kontakt_id' => $kontakt_id
          ));
          $neisporuceno = $stmt -> fetchAll(PDO::FETCH_ASSOC);
          if (empty($neisporuceno)) {
            return true;
          } else {
            return false;
          }
      }
    }

    ?>
