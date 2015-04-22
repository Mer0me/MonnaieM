<?php 
/*
 +-------------------------------------------------------------------------+
 | Monnaie M - http://merome.net/monnaiem                                                              |
 +-------------------------------------------------------------------------+
 | Auteur : Jérôme VUITTENEZ - Merome : postmaster@merome.net              |
 +-------------------------------------------------------------------------+
*/

  session_start();
  include './requete.php';

  if($_SESSION["citoyen"]["idcitoyen"]=="")
  {
    die("Session perdue. <a href=\"index.php\">Merci de cliquer ici</a>");
  }
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

    echo("<div id=\"accueil\"><a href=\"index.php\"><img border=\"0\" src=\"images/bandeau.png\"></a><br><br>");

    echo("<a onclick=\"javascript:if(document.getElementById('global').style.display=='block') document.getElementById('global').style.display='none'; else document.getElementById('global').style.display='block'\">Cliquez ici pour voir ou faire disparaitre l'historique global de Monnaie M</a><br><br>");

    $historiques=exec_requete("select * from historique order by datemesure", $conn);
    echo("<div id=\"global\" style=\"display:none;\"><table border=\"1\" align=\"center\"><tr align=\"center\"><td>Date</td><td>Nombre d'utilisateurs</td><td>Masse monétaire totale (après revenu)</td><td>Masse monétaire moyenne</td><td>Revenu de base</td></tr>");
    while($historique=mysqli_fetch_array($historiques))
    {
      echo("<tr align=\"center\"><td>".to_str($historique["datemesure"])."</td><td>".$historique["nbutilisateurs"]."</td><td>".$historique["massetotale"]."&nbsp;<img align=\"middle\" src=\"images/m.png\"></td><td>".$historique["massemoyenne"]."&nbsp;<img align=\"middle\" src=\"images/m.png\"></td><td>".$historique["rdb"]."&nbsp;<img align=\"middle\" src=\"images/m.png\"></td></tr>");
    }
    echo("</table><br><br></div>");

    $listes=exec_requete("select idcitoyen from citoyen where valide=1 and citoyen.idcitoyen='".$_SESSION["citoyen"]["idcitoyen"]."'", $conn);
    while($liste=mysqli_fetch_array($listes))
    {


    $nomh=$liste["idcitoyen"];
    echo("Historique de <b>".$nomh."</b>");

    $ccitoyen=mysqli_fetch_array(exec_requete("select dateadhesion,solde from citoyen where idcitoyen='".$nomh."'"), $conn);
    $solde=50;

    echo("<table border=\"1\" align=\"center\"><tr><td>Date</td><td>Acheteur</td><td>Vendeur</td><td>Evènement</td><td>Note</td><td>Commentaires de l'acheteur</td><td>Solde</td></tr>
          <tr><td>".to_str($ccitoyen["dateadhesion"])."</td><td>&nbsp;</td><td>&nbsp;</td><td>Inscription à Monnaie M</td><td>&nbsp;</td><td>&nbsp;</td><td>50&nbsp;<img align=\"middle\" src=\"images/m.png\"></td>");

      $transactions=exec_requete("select *,transaction.prix as prixt from citoyen,transaction,produit where ((vendeur=citoyen.idcitoyen) or (acheteur=citoyen.idcitoyen)) and produit.idproduit=transaction.idproduit and citoyen.idcitoyen='".$nomh."' order by datevente", $conn);
      if(mysqli_num_rows($transactions)>0)
      {
        while ($transaction=mysqli_fetch_array($transactions))
        {
          if($transaction["vendeur"]==$nomh)
          {
            switch($transaction["statut"])
            {
              case "Terminé":
                $solde+=$transaction["prixt"];
                echo("<tr bgcolor=\"#CCFF99\"><td>".to_str($transaction["datevente"])."</td><td>".$transaction["acheteur"]."</td><td>".$transaction["vendeur"]."</td><td>Vente / ".$transaction["categorie"]."</td><td>".$transaction["note"]."/5</td><td>".$transaction["commentaires"]."</td><td>".$solde."&nbsp;<img align=\"middle\" src=\"images/m.png\">&nbsp;(+".$transaction["prixt"]."&nbsp;<img align=\"middle\" src=\"images/m.png\">)</td></tr>");
                break;
              case "Commandé":
              case "confirmé":
                echo("<tr bgcolor=\"#FFFF99\"><td>".to_str($transaction["datevente"])."</td><td>".$transaction["acheteur"]."</td><td>".$transaction["vendeur"]."</td><td>Vente / ".$transaction["categorie"]."</td><td>".$transaction["note"]."/5</td><td>En attente de finalisation</td><td>(+".$transaction["prixt"]."&nbsp;<img align=\"middle\" src=\"images/m.png\">&nbsp;en&nbsp;attente)</td></tr>");
                break;
              case "Annulé":
                echo("<tr bgcolor=\"#FF9999\"><td>".to_str($transaction["datevente"])."</td><td>".$transaction["acheteur"]."</td><td>".$transaction["vendeur"]."</td><td>Vente / ".$transaction["categorie"]."</td><td>-</td><td>Annulé : ".$transaction["commentaires"]."</td><td>-</td></tr>");
                break;
            }
          }
          else
          {
            switch($transaction["statut"])
            {
              case "Terminé":
                $solde-=$transaction["prixt"];
                echo("<tr bgcolor=\"#FFCCFF\"><td>".to_str($transaction["datevente"])."</td><td>".$transaction["acheteur"]."</td><td>".$transaction["vendeur"]."</td><td>Achat / ".$transaction["categorie"]."</td><td>".$transaction["note"]."/5</td><td>".$transaction["commentaires"]."</td><td>".$solde."&nbsp;<img align=\"middle\" src=\"images/m.png\">&nbsp;(-".$transaction["prixt"]."&nbsp;<img align=\"middle\" src=\"images/m.png\">)</td></tr>");
                break;
              case "Commandé":
              case "confirmé":
                $solde-=$transaction["prixt"];
                echo("<tr bgcolor=\"#FFCCFF\"><td>".to_str($transaction["datevente"])."</td><td>".$transaction["acheteur"]."</td><td>".$transaction["vendeur"]."</td><td>Achat / ".$transaction["categorie"]."</td><td>En attente</td><td>En attente de finalisation</td><td>".$solde."&nbsp;<img align=\"middle\" src=\"images/m.png\">&nbsp;(-".$transaction["prixt"]."&nbsp;<img align=\"middle\" src=\"images/m.png\">)</td></tr>");
                break;
              case "Annulé":
                echo("<tr bgcolor=\"#FF9999\"><td>".to_str($transaction["datevente"])."</td><td>".$transaction["acheteur"]."</td><td>".$transaction["vendeur"]."</td><td>Achat / ".$transaction["categorie"]."</td><td>Annulé</td><td>".$transaction["commentaires"]."</td><td>-</td></tr>");
                break;
            }
          }
        }
      }
      echo("<tr bgcolor=\"#CCFF99\"><td>Chaque mois depuis le ".to_str($ccitoyen["dateadhesion"])."</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>Revenu de base</td><td>".$ccitoyen["solde"]."&nbsp;<img align=\"middle\" src=\"images/m.png\">&nbsp;(+".($ccitoyen["solde"]-$solde)."&nbsp;<img align=\"middle\" src=\"images/m.png\">)</td></tr>");
      echo("</table><br><br>");
    }
    echo("</div>");

    mysqli_close();
?>
  </body>
</html>
