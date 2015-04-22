<?php
/*
 +-------------------------------------------------------------------------+
 | Monnaie M - http://merome.net/monnaiem                                  |
 +-------------------------------------------------------------------------+
 | Auteur : Jérôme VUITTENEZ - Merome : postmaster@merome.net              |
 +-------------------------------------------------------------------------+
*/

function affiche_annonce($annonce)
{
    if($annonce["nbventes"]>0)
      $note=$annonce["notevendeur"]."/5 pour ";
    else
      $note="";
    if($annonce["typeannonce"]=="demande")
    {
      if($annonce["icone"]!="" && file_exists(str_replace("http://merome.net/","/var/www/",$annonce["icone"])))
        echo("<tr bgcolor=\"#D0F000\" onmouseover=\"this.style.backgroundColor='#FFFF99';\" onmouseout=\"this.style.backgroundColor='#D0F000';\" onclick=\"location.href='demande.php?a=".$annonce["idproduit"]."'\"><td><img src=\"".$annonce["icone"]."\"></td><td><b>".$annonce["categorie"]."</b><br>".$demande."<a href=\"demande.php?a=".$annonce["idproduit"]."\"><br>".$annonce["objet"]."</a> (".$annonce["prix"]." <img align=\"middle\" src=\"images/m.png\">)</td><td>Recherché par <a href=\"http://merome.net/monnaiem/mail.php?c=".urlencode($annonce["idcitoyen"])."\">".$annonce["idcitoyen"]."</a> (".$note.$annonce["nbventes"]." vente(s)).</td></tr>");
      else
        echo("<tr bgcolor=\"#D0F000\" onmouseover=\"this.style.backgroundColor='#FFFF99';\" onmouseout=\"this.style.backgroundColor='#D0F000';\" onclick=\"location.href='demande.php?a=".$annonce["idproduit"]."'\"><td><i>Pas de photo disponible</i></td><td>".$demande."<a href=\"demande.php?a=".$annonce["idproduit"]."\">".$annonce["objet"]."</a> (".$annonce["prix"]." <img align=\"middle\" src=\"images/m.png\">)</td><td>Recherché par <a href=\"http://merome.net/monnaiem/mail.php?c=".urlencode($annonce["idcitoyen"])."\">".$annonce["idcitoyen"]."</a> (".$note.$annonce["nbventes"]." vente(s)).</td></tr>");
    }
    else
    {
      if($annonce["prix"]==0)
        $px="(Prix libre, fixé par l'acheteur)";
      else
        $px="(".$annonce["prix"]." <img align=\"middle\" src=\"images/m.png\">)";
      if($annonce["icone"]!="" && file_exists(str_replace("http://merome.net/","/var/www/",$annonce["icone"])))
        echo("<tr onmouseover=\"this.style.backgroundColor='#FFFF99';\" onmouseout=\"this.style.backgroundColor='#FFFFFF';\" onclick=\"location.href='annonce.php?a=".$annonce["idproduit"]."'\"><td><img src=\"".$annonce["icone"]."\"></td><td><b>".$annonce["categorie"]."</b><br><a href=\"annonce.php?a=".$annonce["idproduit"]."\"><br>".$annonce["objet"]."</a> ".$px."</td><td>Mis en vente par <a href=\"http://merome.net/monnaiem/mail.php?c=".urlencode($annonce["idcitoyen"])."\">".$annonce["idcitoyen"]."</a> (".$note.$annonce["nbventes"]." vente(s)).</td></tr>");
      else
        echo("<tr onmouseover=\"this.style.backgroundColor='#FFFF99';\" onmouseout=\"this.style.backgroundColor='#FFFFFF';\" onclick=\"location.href='annonce.php?a=".$annonce["idproduit"]."'\"><td><i>Pas de photo disponible</i></td><td>".$demande."<a href=\"annonce.php?a=".$annonce["idproduit"]."\">".$annonce["objet"]."</a> ".$px."</td><td>Mis en vente par <a href=\"http://merome.net/monnaiem/mail.php?c=".urlencode($annonce["idcitoyen"])."\">".$annonce["idcitoyen"]."</a> (".$note.$annonce["nbventes"]." vente(s)).</td></tr>");
    }
}

function mesannonces()
{
    $massem1=mysql_fetch_array(exec_requete("select sum(solde) as massem1 from citoyen where valide=1"));
    $massem2=mysql_fetch_array(exec_requete("select sum(prix) as massem2 from transaction where statut<>'Terminé' and statut<>'Proposé' and statut<>'Annulé'"));
    $citoyens=mysql_fetch_array(exec_requete("select count(*) as population from citoyen where valide=1"));

    if(date("m")<12)
    {
      $mois=date("m")+1;$annee=date("Y");
    }
    else
    {
      $mois=1;$annee=date("Y")+1;
    }

    echo("<a href=\"index.php?logoff=1\">Se déconnecter</a><br><p><b>Solde de votre compte : </b>".$_SESSION["citoyen"]["solde"]."&nbsp;<img align=\"middle\" src=\"images/m.png\">&nbsp;|&nbsp;<b>Masse monétaire totale : </b>".($massem1["massem1"]+$massem2["massem2"])."&nbsp;<img align=\"middle\" src=\"images/m.png\">&nbsp;|&nbsp;<b>Nombre d'utilisateurs valides : </b>".($citoyens["population"])."&nbsp;| <b>Solde moyen : </b>".round(($massem1["massem1"]+$massem2["massem2"])/$citoyens["population"],2)." <img align=\"middle\" src=\"images/m.png\"><br>");
    echo("<b>Prochain revenu de base le 1/".$mois."/".$annee." </b>: 0,8% * ".($massem1["massem1"]+$massem2["massem2"])." / ".($citoyens["population"])." = ".(ceil((0.8*($massem1["massem1"]+$massem2["massem2"])/100/$citoyens["population"])))." <img align=\"middle\" src=\"images/m.png\"><br></p>");


    $mestransactions=exec_requete("select *,transaction.prix as prixt,to_days( now( ) ) - to_days( datevente ) as delai from transaction,produit,citoyen where acheteur=citoyen.idcitoyen and vendeur='".$_SESSION["citoyen"]["idcitoyen"]."' and statut<>'Terminé' and statut<>'Annulé' and transaction.idproduit=produit.idproduit");
    if(mysql_num_rows($mestransactions)>0)
    {
      echo("<b>Vos ventes en cours : (".mysql_num_rows($mestransactions).")<b><br><table align=\"center\">");
      while($annonce=mysql_fetch_array($mestransactions))
      {
        if($annonce["icone"]!="" && file_exists(str_replace("http://merome.net/","/var/www/",$annonce["icone"])))
          echo("<tr><td><img src=\"".$annonce["icone"]."\"></td><td>".$annonce["objet"]."</a> (".$annonce["prixt"]." <img align=\"middle\" src=\"images/m.png\">)</td><td>Vendu le : ".to_str($annonce["datevente"])." à ".$annonce["acheteur"]."<br>");
        else
          echo("<tr><td><i>Pas de photo disponible</i></td><td>".$annonce["objet"]."</a> (".$annonce["prixt"]." <img align=\"middle\" src=\"images/m.png\">)</td><td>Proposé le : ".to_str($annonce["datevente"])." à ".$annonce["acheteur"]."<br>");
        if($annonce["statut"]=="confirmé")
        {
          echo("<b>Statut : </b>".$annonce["statut"].".<br>En attente de réception par l'acheteur : <a href=\"http://merome.net/monnaiem/mail.php?c=".urlencode($annonce["idcitoyen"])."\">".$annonce["idcitoyen"]."</a><br>La transaction sera validée automatiquement dans ".(31-$annonce["delai"])." jour(s).<br><a href=\"litige.php?t=".$annonce["idtransaction"]."\">Signaler un problème</a><br>");
            if($annonce["port"]==1)
              echo($annonce["nom"]." ".$annonce["prenom"]."<br>".
              $annonce["adresse"]."<br>".
              $annonce["cp"]." ".$annonce["ville"]);
        }
        else
        {
          if($annonce["statut"]=="Commandé")
              echo("<b>Statut : </b>Commandé<br>L'acheteur attend une confirmation.<br> <a href=\"transaction.php?t=".$annonce["idtransaction"]."\">Je confirme ou j'annule la vente</a>.<br>Sans action de votre part, la transaction sera annulée automatiquement dans ".(31-$annonce["delai"])." jour(s).<br><a href=\"litige.php?t=".$annonce["idtransaction"]."\">Signaler un problème</a></td></tr>");
          else
          {
            if($annonce["statut"]=="Proposé")
                echo("<b>Statut : </b>Proposé<br>En attente de confirmation par le demandeur.</td></tr>");
          }
        }
        echo("</td></tr>");

      }
      echo("</table>");
    }

    $mestransactions=exec_requete("select *,transaction.prix as prixt,to_days( now( ) ) - to_days( datevente ) as delai from transaction,produit where acheteur='".$_SESSION["citoyen"]["idcitoyen"]."' and statut<>'Terminé' and statut<>'Annulé' and transaction.idproduit=produit.idproduit");
    if(mysql_num_rows($mestransactions)>0)
    {
      echo("<b>Vos achats en cours : (".mysql_num_rows($mestransactions).")<b><br><table align=\"center\">");

      while($annonce=mysql_fetch_array($mestransactions))
      {
        if($annonce["icone"]!="" && file_exists(str_replace("http://merome.net/","/var/www/",$annonce["icone"])))
          echo("<tr><td><img src=\"".$annonce["icone"]."\"></td><td>".$annonce["objet"]."</a> (".$annonce["prixt"]." <img align=\"middle\" src=\"images/m.png\">)</td><td><b>Commandé le : </b>".to_str($annonce["datevente"])." à <a href=\"mail.php?c=".$annonce["vendeur"]."\">".$annonce["vendeur"]."</a><br>");
        else
          echo("<tr><td><i>Pas de photo disponible</i></td><td>".$annonce["objet"]."</a> (".$annonce["prixt"]." <img align=\"middle\" src=\"images/m.png\">)</td><td><b>Commandé le : </b>".to_str($annonce["datevente"])." à <a href=\"mail.php?c=".$annonce["vendeur"]."\">".$annonce["vendeur"]."</a><br>");

        if($annonce["statut"]=="en cours")
          echo("<b>Statut : </b>".$annonce["statut"].". En attente de confirmation par le vendeur.<br>Sans action de la part du vendeur, la transaction sera annulée automatiquement dans ".(31-$annonce["delai"])." jour(s).<br><a href=\"litige.php?t=".$annonce["idtransaction"]."\">Signaler un problème</a></td></tr>");
        if($annonce["statut"]=="confirmé")
          echo("<b>Statut : </b>".$annonce["statut"]."<br>Confirmé par le vendeur, en attente de réception.<br> <a href=\"transaction.php?t=".$annonce["idtransaction"]."\">J'ai bien reçu ma commande</a>.<br>Sans action de votre part, la transaction sera validée automatiquement dans ".(31-$annonce["delai"])." jour(s).<br><a href=\"litige.php?t=".$annonce["idtransaction"]."\">Signaler un problème</a></td></tr>");
        if($annonce["statut"]=="Proposé")
          echo("<b>Statut : </b>".$annonce["statut"]."<br>Proposé par le vendeur, en attente de validation.<br> <a href=\"transaction.php?t=".$annonce["idtransaction"]."\">J'accepte ou je refuse la proposition de ce vendeur</a>.</td></tr>");
      }
      echo("</table><hr>");
    }


    $mesannonces=exec_requete("select * from produit where nbex>0 and valide>0 and idcitoyen='".$_SESSION["citoyen"]["idcitoyen"]."'");
    if(mysql_num_rows($mesannonces)==0)
    {
      echo("Vous n'avez rien à vendre pour l'instant.<br><br>");
    }
    else
    {
      echo("<a onclick=\"javascript:if(document.getElementById('mesannonces').style.display=='block') document.getElementById('mesannonces').style.display='none'; else document.getElementById('mesannonces').style.display='block'\">Cliquez ici pour voir ou faire disparaitre vos annonces en cours</a><br><div id=\"mesannonces\" style=\"display:none;\"><br>");
      echo("<b>Vos annonces en cours : (".mysql_num_rows($mesannonces).")<b><br><table align=\"center\">");
      while($annonce=mysql_fetch_array($mesannonces))
      {
        if($annonce["typeannonce"]=="demande")
          echo("<tr bgcolor=\"#D0F000\"><td><img src=\"".$annonce["icone"]."\"></td><td><a href=\"demande.php?modif=".$annonce["idproduit"]."\">".$annonce["objet"]."</a> (".
            $annonce["prix"]." <img align=\"middle\" src=\"images/m.png\">)<br>".$annonce["nbex"]." exemplaire(s).<br>
            URL de votre annonce : <br>http://merome.net/monnaiem/demande.php?a=".$annonce["idproduit"]."<br>
            <a target=\"_new\" href=\"https://twitter.com/intent/tweet?original_referer=http%3A%2F%2Fmerome.net%2Fmonnaiem%2F&text=Je%20cherche%20sur%20Monnaie%20M%20".utf8_encode($annonce["objet"])."%20pour%20".$annonce["prix"]."%20M%20:&tw_p=tweetbutton&url=http%3A%2F%2Fmerome.net%2Fmonnaiem%2F/demande.php?a=".$annonce["idproduit"]."\"><img title=\"Tweeter votre annonce !\" border=\"0\" src=\"images/tweet.png\"</a></td>
            <td><a href=\"demande.php?modif=".$annonce["idproduit"]."\">Modifier l'annonce<br><br>
            <a href=\"demande.php?suppr=".$annonce["idproduit"]."\">Supprimer l'annonce<br></td></tr>");
        else
          echo("<tr><td><img src=\"".$annonce["icone"]."\"></td><td><a href=\"annonce.php?modif=".$annonce["idproduit"]."\">".$annonce["objet"]."</a> (".
            $annonce["prix"]." <img align=\"middle\" src=\"images/m.png\">)<br>".$annonce["nbex"]." exemplaire(s).<br>
            URL de votre annonce : <br>http://merome.net/monnaiem/annonce.php?a=".$annonce["idproduit"]."<br>
            <a target=\"_new\" href=\"https://twitter.com/intent/tweet?original_referer=http%3A%2F%2Fmerome.net%2Fmonnaiem%2F&text=Je%20vends%20sur%20Monnaie%20M%20".utf8_encode($annonce["objet"])."%20pour%20".$annonce["prix"]."%20M%20:&tw_p=tweetbutton&url=http%3A%2F%2Fmerome.net%2Fmonnaiem%2F/annonce.php?a=".$annonce["idproduit"]."\"><img title=\"Tweeter votre annonce !\" border=\"0\" src=\"images/tweet.png\"</a></td>
            <td><a href=\"annonce.php?modif=".$annonce["idproduit"]."\">Modifier l'annonce<br><br>
            <a href=\"annonce.php?suppr=".$annonce["idproduit"]."\">Supprimer l'annonce<br></td></tr>");

      }
      echo("</table></div>");

    }


    echo("<br><a href=\"historique.php\">Historique</a><br><a href=\"annonce.php\">Déposer une nouvelle offre</a> | <a href=\"demande.php\">Déposer une nouvelle demande</a><br>");
    echo("<hr>");
}
?>
