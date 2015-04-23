<?php 
/*
 +-------------------------------------------------------------------------+
 | Monnaie M - http://merome.net/monnaiem                                                              |
 +-------------------------------------------------------------------------+
 | Auteur : Jérôme VUITTENEZ - Merome : postmaster@merome.net              |
 +-------------------------------------------------------------------------+
*/

  include '/var/www/monnaiem/requete.php';
 ?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
  <meta http-equiv="content-type" content="text/html; charset=utf-8">
  <title>Monnaie M - Expérimentation d'une monnaie complémentaire assortie d'un revenu de base</title>
  <link rel="stylesheet" href="monnaiem.css" typeproduit="text/css">
  <meta name="description" content="Monnaie M est une expérimentation visant à faire connaître et promouvoir le fonctionnement et le rôle d'une monnaie, 
  les Systèmes d'Echanges Locaux, le concept de revenu de base, les monnaies complémentaires.">
  <meta name="keywords" lang="fr" content="monnaie bitcoin openudc création monétaire SEL revenu de base dividende universel">
  </head>
  <body>
<?php 

  if(date("d")==1)
  {
    $massem1=mysqli_fetch_array(exec_requete("select sum(solde) as massem1 from citoyen where valide=1", $conn));
    $massem2=mysqli_fetch_array(exec_requete("select sum(prix) as massem2 from transaction where statut<>'Terminé' and statut<>'Proposé' and statut<>'Annulé'", $conn));
    $populations=mysqli_fetch_array(exec_requete("select count(*) as population from citoyen where valide=1", $conn));

    $revenu=ceil((0.8*($massem1["massem1"]+$massem2["massem2"])/100/$populations["population"]));
    exec_requete("update citoyen set solde=solde+".$revenu." where valide=1", $conn);

    $citoyens=exec_requete("select idcitoyen,mail,solde from citoyen where valide=1", $conn);
    while($citoyen=mysqli_fetch_array($citoyens))
    {
      mail($citoyen["mail"], "Vous avez reçu votre revenu de base sur Monnaie M",
                  "Conformément au réglement de Monnaie M ( http://merome.net/monnaiem/ReglementMonnaieM.pdf ), votre compte vient d'être crédité de ".$revenu." M au titre du revenu de base mensuel, soit 0.8% de la masse monétaire totale (".($massem1["massem1"]+$massem2["massem2"]).") divisée par le nombre d'utilisateurs inscrits (".$populations["population"]."), arrondi à l'entier supérieur.\r\n\r\n Votre solde s'élève aujourd'hui à ".$citoyen["solde"]." M.\r\n\r\nMonnaie M est une initiative citoyenne qui fonctionne à la mesure de l'investissement de ses utilisateurs :\r\n- En utilisant vos M pour acheter des biens et des services aux autres utilisateurs\r\n- En déposant vous-même des annonces (on a tous quelque chose à offrir)\r\n- En faisant connaitre le site à d'autres\r\n\r\nJe compte sur vous...\r\n\r\nhttp://merome.net/monnaiem\r\n\r\nMerome",
                  "From: ".FROM."\r\n"
        					."Reply-To: ".FROM."\r\n"
        					."X-Mailer: PHP/" . phpversion());
    }

    $nouvellemassetotale=($massem1["massem1"]+$massem2["massem2"]+$revenu*$populations["population"]);
    $nouvellemassemoyenne=round($nouvellemassetotale/$populations["population"],2);
    exec_requete("insert into historique (datemesure,nbutilisateurs,massetotale,massemoyenne,rdb) values (now(),".$populations["population"].",".$nouvellemassetotale.",".$nouvellemassemoyenne.",".$revenu.")", $conn);

  }



  mysqli_close();



?>
  </body>
</html>
