function myFunction() {
  var x = document.getElementById("myTopnav");
  if (x.className === "topnav") {
    x.className += " responsive";
  } else {
    x.className = "topnav";
  }
}

function openCity(evt, cityName) {
  // Ocultar todas las pestañas
  var i, tabcontent, tablinks;
  tabcontent = document.getElementsByClassName("tabcontent");
  for (i = 0; i < tabcontent.length; i++) {
      tabcontent[i].style.display = "none";
  }

  // Eliminar la clase "active" de todas las pestañas
  tablinks = document.getElementsByClassName("tablinks");
  for (i = 0; i < tablinks.length; i++) {
      tablinks[i].className = tablinks[i].className.replace(" active", "");
  }

  // Mostrar la pestaña actual y añadir la clase "active" al botón correspondiente
  document.getElementById(cityName).style.display = "block";
  evt.currentTarget.className += " active";
}


