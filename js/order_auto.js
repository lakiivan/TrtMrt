
$(function() {
  $("#odabir_artikla").autocomplete({ source: "bo_pn_auto.php" });
  console.log('auto started');
});

function get_artikl_data(){
  console.log('funkcija get artikl data je uspesno startovana');
  var odabrani_artikal = $("#odabir_artikla").val();
  var podaci = odabrani_artikal.split('-');
  console.log('podaci 0 - ' + podaci);
  console.log('podaci 1 - ' + podaci[1]);
  console.log('podaci 2 - ' + podaci[2]);
  var part_number = podaci[0];
  var artikl_id = podaci[1];
  var opis = podaci[2];
  console.log('part number je - ' + opis);
  $("#artikl_id").val(artikl_id);
  $("#part_number").val(part_number);
  $("#opis").val(opis);
}
