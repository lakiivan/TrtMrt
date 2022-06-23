function search_table(el_id, search_table, column_search) {
  //funckija pretrauje dinamicno tabelu preko polja za unos
  //unosi se id input polja, id tabele i broj kolone koju zelimo da pretrazimo
  console.log('pokrenuta je funkcija search_table');
  var el_id, filter, table, tr, td, i, txtValue;
  input = document.getElementById(el_id);
  filter = input.value.toUpperCase();
  table = document.getElementById(search_table);
  tr = table.getElementsByTagName("tr");
  //TEST FUNKCIJE
  console.log("s_t filter je " + filter);

  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[column_search];
    if (td) {
      txtValue = td.textContent || td.innerText;
      //provera text txtValue
      //console.log('txtValue je: ' + txtValue);
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }
  }
}

function search_table_numbers(el_id, search_table, column_search) {
  //funckija pretrauje dinamicno tabelu preko polja za unos
  //unosi se id input polja, id tabele i broj kolone koju zelimo da pretrazimo
  console.log('pokrenuta je funkcija search_table');
  var el_id, filter, table, tr, td, i, txtValue;
  input = document.getElementById(el_id);
  filter = input.value.toUpperCase();
  table = document.getElementById(search_table);
  tr = table.getElementsByTagName("tr");
  //TEST FUNKCIJE
  console.log("s_t filter je " + filter);

  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[column_search];
    if (td) {
      txtValue = td.textContent || td.innerText;
      //provera text txtValue
      //console.log('txtValue je: ' + txtValue);
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }
  }
}
