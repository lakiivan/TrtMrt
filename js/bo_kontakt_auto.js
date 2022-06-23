$(function() {
  $("#kontakt").autocomplete({ source: "bo_kontakt_auto.php" });
  console.log('auto started');
});

function get_kontakt_data(){
  console.log('funkcija get kontakt data je uspesno startovana');
  var odabrani_artikal = $("#kontakt").val();
  var podaci = odabrani_artikal.split('-');
  console.log('podaci 0 - ' + podaci);
  console.log('podaci 1 - ' + podaci[1]);
  var ime = podaci[0];
  var kontakt_id = podaci[1];
  console.log('Ime - ' + ime);
  $("#kontakt_id").val(kontakt_id);
  $("#ime").val(ime);
}
