$(function() {
  calc_popust();
  check_max_kol();
});

function calc_popust() {
  var popust = parseInt($("#popust").val()) / 100;
  var cena_net = parseFloat($("#cena_net").val());
  var cena_list = parseFloat($("#cena_list").val());
  var umanjena_zarada = (cena_list - cena_net) - popust*(cena_list - cena_net)
  var cena_sa_popustom = cena_net + umanjena_zarada;
  var kolicina = parseInt($("#kolicina").val());
  var ukupna_net_cena = kolicina * cena_net;
  var ukupna_cena_sa_popustom = cena_sa_popustom * kolicina;
  console.log("Kolicina: " + kolicina);
  console.log("Popust je: " + popust);
  console.log("Cena net je: " + cena_net);
  console.log("Cena list je: " + cena_list);
  console.log("Ukupna net cena je: " + ukupna_net_cena);
  console.log("Umanjena zarada je: " + umanjena_zarada);
  console.log("Cena sa popustom: " + cena_sa_popustom);
  console.log("Ukupna Cena sa popustom: " + ukupna_cena_sa_popustom);
  //izracunavanje polja ukupno za naplatu
  $(ukupna_net).val(ukupna_net_cena);
  $(konacna_cena).val(ukupna_cena_sa_popustom);
}

$('#btn_delete').click(function () {
  $('#action').val("delete");
  console.log($('#action').val());
});

$('#btn_trtmrt').click(function () {
  $('#action').val("trtmrt");
  console.log($('#action').val());
});

function check_max_kol() {
  var max_kol = $('#mag_max_kolicina');
  var kol     = $('#mag_kolicina').val();
  console. log('Max kolicina je ' + max_kol);
  if (kol > max_kol) {
    alert('Nema ovog artikla u trazenoj kolicini u magacinu. Mozete dodati u proudzbinu samo ' + max_kol + ' ovog artikla');
    $('#kolicina').val(0);
  }

// function check_max_kol() {
//   var max_kol = $('#kolicina').attr('max');
//   var kol     = $('#kolicina').val();
//   console. log('Max kolicina je ' + max_kol);
//   if (kol > max_kol) {
//     alert('Nema ovog artikla u trazenoj kolicini u magacinu. Mozete dodati u proudzbinu samo ' + max_kol + ' ovog artikla');
//     $('#kolicina').val(1);
//   }

}
