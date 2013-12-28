<?php 
/*
 +-------------------------------------------------------------------------+
 | Monnaie M - http://merome.net/monnaiem                                  |
 +-------------------------------------------------------------------------+
 | Auteur : Jérôme VUITTENEZ - Merome : postmaster@merome.net              |
 +-------------------------------------------------------------------------+
*/
  session_start();

  if($_SESSION["citoyen"]["idcitoyen"]=="" && $_GET["a"]>0)
  {
    echo("Vous n'&ecirc;tes pas connect&eacute; &agrave; Monnaie M. Pour utiliser Monnaie M, il faut cr&eacute;er un compte et se connecter. <a href=\"index.php\">Merci de cliquer ici</a>");
  }
  else
  {
      if($_SESSION["citoyen"]["idcitoyen"]=="")
        die("Vous n'&ecirc;tes pas connect&eacute; &agrave; Monnaie M. Pour utiliser Monnaie M, il faut cr&eacute;er un compte et se connecter. <a href=\"index.php\">Merci de cliquer ici</a>");
  }
  include './requete.php';
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
    echo("<a target=\"_new\" href=\"http://merome.net/monnaiem/phpBB3\">Accéder au forum de monnaie M</a><br><br>");

    if($_POST["achatannonce"]>0)
    {

      $annonces=exec_requete("select * from produit,citoyen where produit.idcitoyen=citoyen.idcitoyen and idproduit=".$_POST["achatannonce"]);
      if(mysql_num_rows($annonces)>0)
      {
        $annonce=mysql_fetch_array($annonces);
        if($_POST["port"]==1)
        {
          $livraison=" (livraison comprise)";
          $prixfinal=$annonce["prix"]+$annonce["fdp"];
        }
        else
        {
          $livraison=" (remise en mains propres)";
          $prixfinal=$annonce["prix"];
        }
        echo("Vous êtes sur le point de proposer \"".$annonce["objet"]."\" pour ".$prixfinal." <img align=\"middle\" src=\"images/m.png\"> ".$livraison."<br><br>");
      ?>
        <form method="post" action="demande.php">
              <input type="hidden" name="achatconfirme" value="<?php  echo($_POST["achatannonce"]); ?>">
              <input type="hidden" name="port" value="<?php  echo($_POST["port"]); ?>">
              <input type="submit" value="Je confirme ma proposition">
        </form>
        <form method="post" action="index.php">
            <input type="submit" value="Oups, non c'est une erreur">
        </form>
      <?php 
     }
     die();
    }

    if($_POST["achatconfirme"]>0)
    {
        $annonces=exec_requete("select * from produit,citoyen where produit.idcitoyen=citoyen.idcitoyen and idproduit=".$_POST["achatconfirme"]);
        if(mysql_num_rows($annonces)>0)
        {
          $annonce=mysql_fetch_array($annonces);

          if($_POST["port"]==1)
          {
            $livraison=" (livraison comprise)";
            $prixfinal=$annonce["prix"]+$annonce["fdp"];
          }
          else
          {
            $livraison=" (remise en mains propres)";
            $prixfinal=$annonce["prix"];
          }

          if($annonce["nbex"]<1 || $annonce["valide"]==0)
            echo("Cette annonce n'est plus disponible. Merci de votre compréhension<br>");
          else
          {
            if($annonce["nbex"]>=1)
            {
                  exec_requete("INSERT INTO `monnaiem`.`transaction` (`acheteur` ,`vendeur` ,`idproduit` ,`datevente` ,`statut` ,`prix` ,port, `note` ,`commentaires`)
                                VALUES ('".$annonce["idcitoyen"]."', '".$_SESSION["citoyen"]["idcitoyen"]."', '".$_POST["achatconfirme"]."', now() , 'Proposé', ".$prixfinal.",".$_POST["port"].", '', '')");

                  mail($annonce["mail"], "Un vendeur vous propose un produit conforme à votre demande sur Monnaie M",
                        $_SESSION["citoyen"]["idcitoyen"]." vient de proposer le produit ou service ".$annonce["objet"]." pour ".$prixfinal." M ".$livraison.". Pour confirmer cette proposition, merci de vous connecter à Monnaie M : http://merome.net/monnaiem \r\n",
                        "From: ".FROM."\r\n"
  							."Reply-To: ".FROM."\r\n"
  							."X-Mailer: PHP/" . phpversion());

                mail($_SESSION["citoyen"]["mail"], "Enregistrement de votre proposition sur Monnaie M",
                        "Vous venez de proposer sur Monnaie M le produit ou service ".$annonce["objet"]." pour ".$prixfinal." M ".$livraison.".\n".$annonce["idcitoyen"]." a été prévenu par mail de votre proposition. \r\n",
                        "From: ".FROM."\r\n"
  							."Reply-To: ".FROM."\r\n"
  							."X-Mailer: PHP/" . phpversion());

                  die("Un mail vient d'être envoyé au demandeur pour l'avertir de votre proposition.<br>");
            }


          }
        }
    }

    if($_GET["a"]>0)
    {
        $annonces=exec_requete("select * from produit,citoyen where produit.idcitoyen=citoyen.idcitoyen and idproduit=".$_GET["a"]);
        if(mysql_num_rows($annonces)>0)
        {
          $annonce=mysql_fetch_array($annonces);
          if($annonce["idcitoyen"]==$_SESSION["citoyen"]["idcitoyen"])
          {
            $_GET["modif"]=$_GET["a"];
          }
          else
          {
            if($annonce["nbex"]<1)
              echo("Cette annonce n'est plus disponible. Merci de votre compréhension<br>");
            else
            {
              echo("<table align=\"center\"><tr><td>".$annonce["categorie"]." - <b>".$annonce["objet"]."</b> demandé par <a title=\"envoyer un message à ".$annonce["idcitoyen"]."\" href=\"mail.php?c=".urlencode($annonce["idcitoyen"])."\">".$annonce["idcitoyen"]."</a>. (".$annonce["notevendeur"]."/5 pour ".$annonce["nbventes"]." vente(s)).</td></tr>");
              if($annonce["photo"]!="")
                echo("<tr><td><img width=\"400\" src=\"".$annonce["photo"]."\"></td></tr>");
              else
                echo("<tr><td><i>Pas de photo disponible</i></td></tr>");

              echo("<tr><td>".$annonce["description"]."</td></tr>
                    <tr><td>Prix : <b>".$annonce["prix"]."</b>&nbsp;<img align=\"middle\" src=\"images/m.png\"></td></tr>
                    <tr><td>Envoi par la Poste possible : <b>".$annonce["envoipossible"]."</b>&nbsp; Frais de port : ".$annonce["fdp"]."&nbsp;<img src=\"images/m.png\" align=\"middle\"></td></tr>");

            	if($_SESSION["citoyen"]["idcitoyen"]!="")
            	{
                    if($_SESSION["citoyen"]["solde"]>=$annonce["prix"]+$annonce["fdp"] && $annonce["envoipossible"]=="Oui")
                      echo("<td><form method=\"post\" action=\"demande.php\"><input type=\"hidden\" name=\"port\" value=\"1\"><input type=\"hidden\" name=\"achatannonce\" value=\"".$annonce["idproduit"]."\"><input type=\"submit\" value=\"Proposer la livraison de ce produit ou service pour ".($annonce["prix"]+$annonce["fdp"])." M\"></form></td></tr>");
                    if($_SESSION["citoyen"]["solde"]<$annonce["prix"]+$annonce["fdp"] && $annonce["envoipossible"]=="Oui")
                      echo("<td><b>Pas assez de M pour acheter ce produit avec le port</b></td></tr>");

                    if($annonce["mainspropres"]=="Oui")
                    {
                      echo("<tr><td>Remise en mains propres possible (autour de ".$annonce["cp"]." ".$annonce["ville"]." ou en ligne/par téléphone): <b>".$annonce["mainspropres"]."</b></td></tr>");
                      echo("<td><form method=\"post\" action=\"demande.php\"><input type=\"hidden\" name=\"port\" value=\"0\"><input type=\"hidden\" name=\"achatannonce\" value=\"".$annonce["idproduit"]."\"><input type=\"submit\" value=\"Proposer ce produit ou service en mains propres pour ".$annonce["prix"]." M\"></form></td></tr>");
                    }
	             }
               else
               {
                echo("<tr><td><form method=\"post\" action=\"compte.php\"><center><input type=\"submit\" value=\"Pour proposer cet article au demandeur, je m'inscris\"></center></form></td></tr>");
               }
               echo("</table>");
            }
            die();
          }
        }
    
    else
	die("Cette annonce n'est plus disponible");
}
    if($_POST["objet"]!="")
    {

      if($fichier_name!="")
      {
        	if(!ereg(".jpeg$", strtolower($fichier_name)) && !ereg(".jpg$", strtolower($fichier_name)))
        	{
        	    echo("La photo n'est pas au format JPG");
        	}

        	$repedest = $_SERVER['DOCUMENT_ROOT']."/monnaiem/images/produits/";
        	$search  = array ('ë', 'ï', 'à', 'ç', 'á', 'é', 'í', 'ó', 'ú', 'ã', 'õ', 'â', 'ê', 'î', 'ô', 'û', ' ', "'");
        	$replace = array ('e', 'i', 'a', 'c', 'a', 'e', 'i', 'o', 'u', 'a', 'o', 'a', 'e', 'i', 'o', 'u', '_', "_");
        	$nomdest = substr(rawurlencode(str_replace($search, $replace,date("YmdHis")."_".$fichier_name)),0,500);
        	$cheminphoto="http://merome.net/monnaiem/images/produits/".$nomdest;

        	if (file_exists($fichier))
        	{
        	    // ici on déplace le fichier ou on veut
        	    if (!move_uploaded_file($fichier,$repedest.$nomdest))
        	    {
        	        echo ("Impossible de copier la photo");
        	    }
        	}
        	else
        	{
        	    echo ("upload de la photo impossible");
        	}
        	$im=imagecreatefromjpeg ( $repedest.$nomdest);
        	$thumbX = 128;
        	$imageX = imagesx($im);
        	$imageY = imagesy($im);
        	$thumbY = (int)(($thumbX*$imageY) / $imageX );
        	$dest  = imagecreatetruecolor($thumbX, $thumbY);
        	imagecopyresampled ($dest, $im, 0, 0, 0, 0, $thumbX, $thumbY, $imageX, $imageY);
        	imagejpeg($dest,$repedest."icones/".$nomdest);
          $cheminicone="http://merome.net/monnaiem/images/produits/icones/".$nomdest;
        	imagedestroy($im);
        	imagedestroy($dest);
      }
      if($_POST["annoncemodif"]>0)
      {
        $annonces=exec_requete("select * from produit where idproduit=".$_POST["annoncemodif"]);
        if(mysql_num_rows($annonces)>0)
        {
          $annonce=mysql_fetch_array($annonces);
          if($annonce["idcitoyen"]==$_SESSION["citoyen"]["idcitoyen"])
          {
            if($fichier_name=="")
            {
              $cheminphoto=$annonce["photo"];
              $cheminicone=$annonce["icone"];
            }

              exec_requete("update `produit` set`objet`= '".strip_tags($_POST["objet"])."',`typeproduit`= '".$_POST["typeproduit"]."',`description` ='".strip_tags($_POST["description"]).
                        "',`photo`='".$cheminphoto."' ,`icone` ='".$cheminicone."' ,`valide`=1 ,`prix`='".$_POST["prix"]."' ,`dateexpiration`='".to_date($_POST["dateexpiration"])."' ,`etat`='".$_POST["etat"].
                        "',`envoipossible`='".$_POST["envoipossible"]."' ,fdp=".$_POST["fdp"].",`mainspropres`='".$_POST["mainspropres"]."' ,`categorie`='".$_POST["categorie"]."' where idproduit=".$_POST["annoncemodif"]);
            die("Annonce modifiée");
          }
          else
            die("Vous ne pouvez pas modifier les annonces des autres.<br>");
        }
        else
          die("Annonce introuvable.<br>");
      }
      else
      {
      	if($_POST["fdp"]=="")
      		$_POST["fdp"]=0;
        exec_requete("INSERT INTO `monnaiem`.`produit` (`idcitoyen` ,`objet` ,`typeproduit` ,typeannonce,`description` ,`photo` ,`icone` ,`nbex` ,`valide` ,`prix` , fdp, `datesaisie` , dateexpiration, `etat` ,`envoipossible` ,`mainspropres` ,`categorie`) VALUES ('".
                       $_SESSION["citoyen"]["idcitoyen"]."', '".strip_tags($_POST["objet"])."', '".$_POST["typeproduit"]."','demande', '".strip_tags($_POST["description"])."', '".$cheminphoto."', '".$cheminicone."', '1', 1, '".$prix."', ".$_POST["fdp"].",now(), '".to_date($_POST["dateexpiration"])."', '".$_POST["etat"]."', '".$envoipossible."', '".$mainspropres."', '".$categorie."')");
        echo("Votre annonce est enregistrée. Elle apparait dans la liste de vos annonces en cours (vous ne pouvez voir vos propres annonces dans la liste des dernières annonces saisies)<br><br>");
      }

    }

   if($_GET["modif"]>0 || $_GET["suppr"]>0)
   {
    if($_GET["modif"]>0)
      $annonces=exec_requete("select * from produit where idproduit=".$_GET["modif"]);
    else
      $annonces=exec_requete("select * from produit where idproduit=".$_GET["suppr"]);
    if(mysql_num_rows($annonces)>0)
    {
      $annonce=mysql_fetch_array($annonces);
      if($annonce["idcitoyen"]==$_SESSION["citoyen"]["idcitoyen"])
      {
        if($_GET["suppr"]==$annonce["idproduit"])
        {
           $mesannonces=exec_requete("select * from produit where nbex>0 and valide>0 and idcitoyen='".$_SESSION["citoyen"]["idcitoyen"]."' and idproduit<>".$_GET["suppr"]);
           if(mysql_num_rows($mesannonces)==0)
           {
              $mestransactions=exec_requete("select * from transaction where acheteur='".$_SESSION["citoyen"]["idcitoyen"]."' and statut<>'Terminé' and statut<>'Annulé'");
              if(mysql_num_rows($mestransactions)>0)
                die("Vous ne pouvez pas supprimer cette annonce car vous avez une transaction en cours.");
           }
           else
              exec_requete("delete from produit where idproduit=".$annonce["idproduit"]);
		die("Annonce supprimée");
        }
        if($_GET["modif"]==$annonce["idproduit"])
        {
          $champmodif="<input type=\"hidden\" name=\"annoncemodif\" value=\"".$annonce["idproduit"]."\">";
          $annonceamodifier=$annonce;
        }
      }
      else
        echo("Vous ne pouvez pas modifier une annonce qui ne vous appartient pas.<br>");
    }
    else
      echo("L'annonce que vous cherchez est introuvable");
   }



    if($_GET["modif"]>0)
        echo("<center><b>Modifier une de vos annonces</b></center><br><br>");
    else
        echo("<center><b>Vous recherchez un bien ou un service</b><br><small><i>Toute annonce illégale sera supprimée</i></small></center><br><br>");



    ?>
    <table align="center">
      <form method="post" action="demande.php" enctype="multipart/form-data">
      <?php  if ($champmodif!="") echo($champmodif); ?>
      <tr><td align="right">Vous recherchez :</td><td><input type="text" name="objet" value="<?php  echo($annonceamodifier["objet"]); ?>"></td></tr>
      <tr><td align="right">typeproduit :</td><td><select name="typeproduit"><option value="bien" <?php  if ($annonceamodifier["typeproduit"]=="bien") echo("selected"); ?>>Objet, bien</option><option value="service" <?php  if ($annonceamodifier["typeproduit"]=="service") echo("selected"); ?>>Service</option></select></td></tr>
      <tr><td align="right">Catégorie :</td><td><select name="categorie">
                        <option value="0">&laquo;Choisissez la cat&eacute;gorie&raquo;</option>
                        <option value='1' style='background-color:#dcdcc3' disabled id='cat1' >-&#45; VEHICULES -&#45;</option>
                            <option value='Voitures' <?php  if ($annonceamodifier["categorie"]=="Voitures") echo("selected"); ?>>Voitures</option>
                            <option value='Motos' <?php  if ($annonceamodifier["categorie"]=="Motos") echo("selected"); ?>>Motos</option>
                            <option value='Vélos' <?php  if ($annonceamodifier["categorie"]=="Vélos") echo("selected"); ?>>V&eacute;los</option>
                            <option value='Equipement Auto' <?php  if ($annonceamodifier["categorie"]=="Equipement Auto") echo("selected"); ?>>Equipement Auto</option>
                            <option value='Equipement Moto' <?php  if ($annonceamodifier["categorie"]=="Equipement Moto") echo("selected"); ?>>Equipement Moto</option>
                            <option value='Nautisme' <?php  if ($annonceamodifier["categorie"]=="Nautisme") echo("selected"); ?>>Nautisme</option>
                            <option value='Equipement Nautisme' <?php  if ($annonceamodifier["categorie"]=="Equipement Nautisme") echo("selected"); ?>>Equipement Nautisme</option>
                            <option value='14' style='background-color:#dcdcc3' disabled id='cat14' >-&#45; MULTIMEDIA -&#45;</option>
                            <option value='Informatique'  <?php  if ($annonceamodifier["categorie"]=="Informatique") echo("selected"); ?>>Informatique</option>
                            <option value='Console & Jeux vidéos'  <?php  if ($annonceamodifier["categorie"]=="Console & Jeux vidéos") echo("selected"); ?>>Consoles &amp; Jeux vid&eacute;o</option>
                            <option value='Image & son' <?php  if ($annonceamodifier["categorie"]=="Image & son") echo("selected"); ?> >Image &amp; Son</option>
                            <option value='Téléphonie' <?php  if ($annonceamodifier["categorie"]=="Téléphonie") echo("selected"); ?> >T&eacute;l&eacute;phonie</option>
                            <option value='18' style='background-color:#dcdcc3' disabled id='cat18' >-&#45; MAISON -&#45;</option>
                            <option value='Ameublement' <?php  if ($annonceamodifier["categorie"]=="Ameublement") echo("selected"); ?> >Ameublement</option>
                            <option value='Electroménager' <?php  if ($annonceamodifier["categorie"]=="Electroménager") echo("selected"); ?> >Electrom&eacute;nager</option>
                            <option value='Arts de la table' <?php  if ($annonceamodifier["categorie"]=="Arts de la table") echo("selected"); ?> >Arts de la table</option>
                            <option value='Décoration' <?php  if ($annonceamodifier["categorie"]=="Décoration") echo("selected"); ?> >D&eacute;coration</option>
                            <option value='Linge de maison' <?php  if ($annonceamodifier["categorie"]=="Linge de maison") echo("selected"); ?> >Linge de maison</option>
                            <option value='Bricolage' <?php  if ($annonceamodifier["categorie"]=="Bricolage") echo("selected"); ?> >Bricolage</option>
                            <option value='Jardinage' <?php  if ($annonceamodifier["categorie"]=="Jardinage") echo("selected"); ?> >Jardinage</option>
                            <option value='Vêtements' <?php  if ($annonceamodifier["categorie"]=="Vêtements") echo("selected"); ?> >V&ecirc;tements</option>
                            <option value='Chaussures' <?php  if ($annonceamodifier["categorie"]=="Chaussures") echo("selected"); ?> >Chaussures</option>
                            <option value='Accessoires & Bagagerie' <?php  if ($annonceamodifier["categorie"]=="Accessoires & Bagagerie") echo("selected"); ?> >Accessoires &amp; Bagagerie</option>
                            <option value='Montres & Bijoux' <?php  if ($annonceamodifier["categorie"]=="Montres & Bijoux") echo("selected"); ?> >Montres &amp; Bijoux</option>
                            <option value='Equipement bébé' <?php  if ($annonceamodifier["categorie"]=="Equipement bébé") echo("selected"); ?> >Equipement b&eacute;b&eacute;</option>
                            <option value='Vêtements bébé' <?php  if ($annonceamodifier["categorie"]=="Vêtements bébé") echo("selected"); ?> >V&ecirc;tements b&eacute;b&eacute;</option>
                            <option value='24' style='background-color:#dcdcc3' disabled id='cat24' >-&#45; LOISIRS -&#45;</option>
                            <option value='DVD / Films' <?php  if ($annonceamodifier["categorie"]=="DVD / Films") echo("selected"); ?> >DVD / Films</option>
                            <option value='CD / Musique' <?php  if ($annonceamodifier["categorie"]=="CD / Musique") echo("selected"); ?> >CD / Musique</option>
                            <option value='Livres' <?php  if ($annonceamodifier["categorie"]=="Livres") echo("selected"); ?> >Livres</option>
                            <option value='Animaux' <?php  if ($annonceamodifier["categorie"]=="Animaux") echo("selected"); ?> >Animaux</option>
                            <option value='Sports' <?php  if ($annonceamodifier["categorie"]=="Sports") echo("selected"); ?> >Sports &amp; Hobbies</option>
                            <option value='Instruments de musique' <?php  if ($annonceamodifier["categorie"]=="Instruments de musique") echo("selected"); ?> >Instruments de musique</option>
                            <option value='Collection' <?php  if ($annonceamodifier["categorie"]=="Collection") echo("selected"); ?> >Collection</option>
                            <option value='Jeux & Jouets' <?php  if ($annonceamodifier["categorie"]=="Jeux & Jouets") echo("selected"); ?> >Jeux &amp; Jouets</option>
                            <option value='Vins & Gastronomie' <?php  if ($annonceamodifier["categorie"]=="Vins & Gastronomie") echo("selected"); ?> >Vins &amp; Gastronomie</option>
                            <option value='31' style='background-color:#dcdcc3' disabled id='cat31' >-&#45; EMPLOI &amp; SERVICES -&#45;</option>
                            <option value='Matériel professionnel' <?php  if ($annonceamodifier["categorie"]=="Matériel professionnel") echo("selected"); ?> >Mat&eacute;riel professionnel</option>
                            <option value='Services' <?php  if ($annonceamodifier["categorie"]=="Services") echo("selected"); ?>>Services</option>
                            <option value='Cours particuliers'   <?php  if ($annonceamodifier["categorie"]=="Cours particuliers") echo("selected"); ?> >Cours particuliers</option>
                            <option value='37' style='background-color:#dcdcc3' disabled id='cat37' >-&#45; -&#45; -&#45;</option>
                            <option value='Autres'  <?php  if ($annonceamodifier["categorie"]=="Autres") echo("selected"); ?> >Autres</option>
                    </select></td></tr>
      <tr><td align="right">Description complète :</td><td><textarea name="description" rows="10" cols="80"><?php  echo($annonceamodifier["description"]); ?></textarea></td></tr>
      <?php 
        if($annonceamodifier["photo"]!="")
          echo("<tr><td align=\"right\">Photo actuelle : <td><img src=\"".$annonceamodifier["icone"]."\"></td></tr>");
      ?>
      <tr><td align="right">Photo (JPG) :</td><td><input type="file" name="fichier"></td></tr>
      <tr><td align="right">Prix (ce que vous offririez pour obtenir ce bien ou service) :</td><td><input onKeyUp="javascript:verifInt(this);" type="text" size="4" name="prix" value="<?php  echo($annonceamodifier["prix"]); ?>">&nbsp;<img align="middle" src="images/m.png"></td></tr>
      <tr><td align="right">Envoi souhaité par la Poste :</td><td><select name="envoipossible"><option value="Oui" <?php  if ($annonceamodifier["envoipossible"]=="Oui") echo("selected"); ?>>Oui</option><option value="Non" <?php  if ($annonceamodifier["envoipossible"]=="Non") echo("selected"); ?>>Non</option></select>&nbsp;Frais de port à prévoir : <input onKeyUp="javascript:verifInt(this);" type="text" name="fdp" size="4" value="<?php  echo($annonceamodifier["fdp"]); ?>">&nbsp;<img src="images/m.png" align="middle"></td></tr>
      <tr><td align="right">Remise en mains propres possible :</td><td><select name="mainspropres"><option value="Oui" <?php  if ($annonceamodifier["mainspropres"]=="Oui") echo("selected"); ?>>Oui</option><option value="Non" <?php  if ($annonceamodifier["mainspropres"]=="Non") echo("selected"); ?>>Non</option></select></td></tr>

      <tr><td colspan="2" align="center"><input type="submit" value="Valider"></td></tr>

      </form>
    </table>

    <?php 

    echo("</div>");



  mysql_close();



?>
  </body>
</html>
