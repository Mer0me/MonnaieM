/*
 +-------------------------------------------------------------------------+
 | Monnaie M - http://merome.net/monnaiem                                                              |
 +-------------------------------------------------------------------------+
 | Auteur : Jérôme VUITTENEZ - Merome : postmaster@merome.net              |
 +-------------------------------------------------------------------------+
*/
 function FormatDate(champ_date) {
    var value_date = champ_date.value;


    if ( (value_date.length == 2) && (value_date.indexOf("/") == -1) ) {
        value_date = value_date  + "/";
        champ_date.value = value_date;
    }

    if ( (value_date.length == 5) && (value_date.lastIndexOf("/") == 2) ) {
        value_date = value_date  + "/";
        champ_date.value = value_date;
    }

    // Empêche la double saisie d'un "/"
    p = "\/\/";
    if (value_date.match(p)) {
        value_date = value_date.replace(p, "/");
        champ_date.value = value_date;
    }
}

 function verifInt(champ)
 {
 reg = new RegExp('[^0-9]+', 'g');
 valeur = champ.value;
 if(reg.test(valeur))
  champ.value=champ.value.replace(/[^0-9]+/, '');
 else
   return true;
 }
