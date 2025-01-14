//

////////////////////////////////////////////////////////////////////////////
// publications.html //

function AppearBib(bib_id) {
    var x = document.getElementById(bib_id);
    if (x.style.display === "none") {
      x.style.display = "block";
    } else {
      x.style.display = "none";
    }
  }

  function AppearAbstract(abstract_id) {
    var x = document.getElementById(abstract_id);
    if (x.style.display === "none") {
      x.style.display = "block";
    } else {
      x.style.display = "none";
    }
  }