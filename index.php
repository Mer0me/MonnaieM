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
    include './fonctions_annonces.php';
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

  if($_GET["logoff"]==1)
  {
    session_destroy();
    session_unset();
    die("<div id=\"accueil\"><img src=\"images/logo.png\"><br><br>Merci d'avoir utilisé Monnaie M. A bientôt.<br><a href=\"index.php\">Se reconnecter à Monnaie M.</a></div>");
  }

  if($_POST["id"]!="")
  {


    $verif=exec_requete("select * from citoyen where valide=1 and idcitoyen='".$_POST["id"]."' and md5('".$_POST["pass"]."')=mdp");
    if(mysql_num_rows($verif)==1)
    {
      $_SESSION["citoyen"]=mysql_fetch_array($verif);
      exec_requete("update citoyen set derniereconnexion=now() where idcitoyen='".$_POST["id"]."'");

      $transactions=exec_requete("select * from transaction where statut='confirmé' and to_days( now( ) ) - to_days( datevente ) >30");
      while($transaction=mysql_fetch_array($transactions))
      {
        exec_requete("update transaction set statut='Terminé',note=5,commentaires='(Annonce validée automatiquement après 30 jours)' where idtransaction=".$transaction["idtransaction"]);
        $moyennes=exec_requete("select avg(note) as moy from transaction where vendeur='".$transaction["vendeur"]."' and statut='Terminé'");
        $moyenne=mysql_fetch_array($moyennes);
        exec_requete("update citoyen set solde=solde+".$transaction["prix"].",nbventes=nbventes+1,notevendeur=".$moyenne["moy"]." where idcitoyen='".$transaction["vendeur"]."'");
        $moyennes=exec_requete("select avg(note) as moy from transaction where acheteur='".$transaction["acheteur"]."'");
        $moyenne=mysql_fetch_array($moyennes);
        exec_requete("update citoyen set noteacheteur=".$moyenne["moy"]." where idcitoyen='".$transaction["acheteur"]."'");
      }

      $transactions=exec_requete("select * from transaction where statut='Commandé' and to_days( now( ) ) - to_days( datevente ) >30");
      while($transaction=mysql_fetch_array($transactions))
      {
        exec_requete("update transaction set statut='Annulé',note=5,commentaires='(Annonce annulée automatiquement après 30 jours)' where idtransaction=".$transaction["idtransaction"]);
        exec_requete("update citoyen set solde=solde+".$transaction["prix"]." where idcitoyen='".$transaction["acheteur"]."'");
      }
    }
    else
      echo("<center><b>Nom d'utilisateur ou mot de passe incorrect</b></center>");

  }


  if($_SESSION["citoyen"]["idcitoyen"]=="")
  {
    echo("<div id=\"accueil\"><table align=\"center\"><tr><td><img src=\"images/logo.png\"></td><td>");
    echo("<center><b>Monnaie M</b> est une expérimentation d'initiative citoyenne visant à faire connaître et promouvoir :<br>
    - le fonctionnement et le rôle d'une monnaie,<br>
    - les Systèmes d'Echanges Locaux (SEL),<br>
    - le concept de revenu de base,<br>
    - les monnaies complémentaires.<br><br>
    Le code de Monnaie M est libre ! Pour l'examiner ou contribuer à son développement, <a href=\"https://github.com/Mer0me/MonnaieM\">cliquez ici</a></center></td></tr></table><hr>
<b>Comment ça marche ?</b><br>C'est comme \"Le bon coin\" ou \"Priceminister\", mais avec une monnaie virtuelle et un revenu de base :<br><br>
<a href=\"http://merome.net/monnaiem/compte.php\"><img border=\"0\" src=\"images/1.png\" title=\"Je communique mon adresse postale à l'inscription\"></a>
<img src=\"images/2.png\" title=\"Un crédit de 50 unités m'est attribué pour commencer les échanges\">
<img src=\"images/3.png\" title=\"J'échange des biens et des services avec les autres utilisateurs en utilisant la monnaie M\">
<img src=\"images/4.png\" title=\"Un revenu de base est attribué à chaque utilisateur, sans condition.\">
                          <hr>
<b>Se connecter</b><br>
    <small>
      <a href=\"compte.php\">Je n'ai pas encore de compte sur Monnaie M - Créer un compte</a><br>
      <a href=\"compte.php?activer=1\">J'ai reçu mon code d'activation par courrier postal - Activer mon compte</a><br>
      <a href=\"http://merome.net/monnaiem/compte.php?oubli=1\">J'ai oublié mon mot de passe ou je souhaite le modifier</a>
    </small>
    <br><br>
   <form method=\"post\" action=\"index.php\">
        Identifiant : <input type=\"text\" name=\"id\"><br>
        Mot de passe : <input type=\"password\" name=\"pass\"><br><br>
        <input type=\"submit\" value=\"OK\">
      </form><br>

    <a href=\"http://merome.net/monnaiem/phpBB3\">Accéder au forum de Monnaie M (nécessite la création d'un compte spécifique au forum !)</a><br><br>

    <a href=\"http://merome.net/monnaiem/ReglementMonnaieM.pdf\">Voir le règlement de Monnaie M</a><br><br>");

    echo("<hr><b>Un aperçu des annonces déjà en ligne avec une annonce au hasard<br><table align=\"center\">");
    $annoncehasards=exec_requete("select * from produit,citoyen where produit.valide=1 and nbex>0 and (dateexpiration=0 or dateexpiration>now()) and citoyen.idcitoyen=produit.idcitoyen and citoyen.idcitoyen<>'".$_SESSION["citoyen"]["idcitoyen"]."' order by rand()");
    $annoncehasard=mysql_fetch_array($annoncehasards);
    affiche_annonce($annoncehasard);
    echo("</table></div>");
  }
  else
  {
    echo("<div id=\"accueil\"><img src=\"images/bandeau.png\"><br><br>");

    $verif=exec_requete("select * from citoyen where idcitoyen='".$_SESSION["citoyen"]["idcitoyen"]."'");
    if(mysql_num_rows($verif)==1)
    {
      $_SESSION["citoyen"]=mysql_fetch_array($verif);
    }
    else
      die("erreur");

    echo("<a target=\"_new\" href=\"http://merome.net/monnaiem/phpBB3\">Accéder au forum de monnaie M</a><br>");

    mesannonces();

    $cats=exec_requete("select distinct(categorie) from produit where produit.valide=1 and nbex>0");
    while($cat=mysql_fetch_array($cats))
    {
      $listecat.="<option value=\"".$cat["categorie"]."\">".$cat["categorie"]."</option>";
    }

    echo("<small><a href=\"rechercher.php?criteres=dep\">Les annonces dans votre département</a><form method=\"post\" action=\"rechercher.php\"><select name=\"criteres\">".$listecat."</select>&nbsp;<input type=\"submit\" value=\"Voir les produits de cette catégorie\"></form><br></small>");

    $lesannonces=exec_requete("select * from produit,citoyen where produit.valide=1 and nbex>0 and (dateexpiration=0 or dateexpiration>now()) and citoyen.idcitoyen=produit.idcitoyen and citoyen.idcitoyen<>'".$_SESSION["citoyen"]["idcitoyen"]."' order by datesaisie desc");
    if(mysql_num_rows($lesannonces)>0)
    {

        echo("<form method=\"post\" action=\"rechercher.php\"><input type=\"text\" name=\"criteres\"><input type=\"submit\" value=\"Rechercher\"></form><br>");
        echo("<b>Une annonce au hasard<br><table align=\"center\">");
        $annoncehasards=exec_requete("select * from produit,citoyen where produit.valide=1 and nbex>0 and (dateexpiration=0 or dateexpiration>now()) and citoyen.idcitoyen=produit.idcitoyen and citoyen.idcitoyen<>'".$_SESSION["citoyen"]["idcitoyen"]."' order by rand()");
        $annoncehasard=mysql_fetch_array($annoncehasards);
        affiche_annonce($annoncehasard);


        echo("<tr><td colspan=\"3\" align=\"center\"><b><big>Les dernières annonces saisies (".mysql_num_rows($lesannonces).") :</big></b><br><small><i>Vos annonces n'apparaissent pas dans cette liste</i></small></td></tr>");

        $i=0;
        while(($annonce=mysql_fetch_array($lesannonces)) && $i<(10+$debut))
        {
          if($annonce["idproduit"]!=$annoncehasard["idproduit"])
          {
            if($i>=$debut)
            {
              affiche_annonce($annonce);
            }
            $i++;
          }
        }
        echo("</table></p>");
        if(mysql_num_rows($lesannonces)>$i)
        {
          echo("<a href=\"index.php?debut=".$i."\">Voir les annonces suivantes</a>");
        }
    }
    else
    {
      echo("Aucune offre d'autres utilisateurs pour l'instant");
    }

    echo("</div>");
  }


  mysql_close();



?>
  </body>
</html>
