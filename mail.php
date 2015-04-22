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

  if($_GET["id"]!="")
  {
    $verif=exec_requete("select * from citoyen where md5(concat(idcitoyen,nom))='".$_GET["id"]."'", $conn);
    if(mysqli_num_rows($verif)==1)
    {
      $_SESSION["citoyen"]=mysqli_fetch_array($verif);
    }
    else
      echo("<center><b>Nom d'utilisateur ou mot de passe incorrect</b></center>");
  }


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

    if($_POST["cmail"]!="")
    {
      $citoyens=exec_requete("select mail,md5(concat(idcitoyen,nom)) as code from citoyen where idcitoyen='".$_POST["cmail"]."'", $conn);
      if(mysqli_num_rows($citoyens)==1)
      {
          $citoyen1=mysqli_fetch_array($citoyens);
          if(mail($citoyen1["mail"], "Message de la part de ".$_SESSION["citoyen"]["idcitoyen"]." depuis monnaie M",
                  "Ce message a été envoyé par ".$_SESSION["citoyen"]["idcitoyen"]." depuis le site Monnaie M. Merci de ne pas utiliser le bouton 'Répondre' de votre messagerie, mais ce lien : http://merome.net/monnaiem/mail.php?id=".$citoyen1["code"]."&c=".urlencode($_SESSION["citoyen"]["idcitoyen"])." pour lui faire une réponse.\r\n\r\n".stripslashes($_POST["contenu"])."\r\n".
                  "\r\nPour répondre à ce message, cliquez ici : http://merome.net/monnaiem/mail.php?id=".$citoyen1["code"]."&c=".urlencode($_SESSION["citoyen"]["idcitoyen"])."\r\n",
                  "From: ".FROM."\r\n"
        					."Reply-To: ".FROM."\r\n"
        					."X-Mailer: PHP/" . phpversion()))
            echo("Message envoyé");
          else
             echo("Erreur lors de l'envoi du message");

      }
    }

    if($_GET["c"]!="")
    {
      $transactions=exec_requete("select * from citoyen,transaction,produit where vendeur=citoyen.idcitoyen and produit.idproduit=transaction.idproduit and statut='Terminé' and citoyen.idcitoyen='".$_GET["c"]."' order by datevente", $conn);
      if(mysqli_num_rows($transactions)>0)
      {
        echo("<b>Les dernières transactions de ".$_GET["c"]." :</b><br><br><table border=\"1\" align=\"center\"><tr><td>Date de la transaction</td><td>Catégorie de produit</td><td>Note</td><td>Commentaires de l'acheteur</td></tr>");
        while ($transaction=mysqli_fetch_array($transactions))
        {
          echo("<tr><td>".$transaction["datevente"]."</td><td>".$transaction["categorie"]."</td><td>".$transaction["note"]."/5</td><td>".$transaction["commentaires"]."</td></tr>");
        }
        echo("</table><br><br>");
      }
      echo("<b>Envoyer un message à ".$_GET["c"]." :</b><br>");
      ?>
        <form method="post" action="mail.php"><input type="hidden" name="cmail" value="<?php  echo($_GET["c"]); ?>">
          <textarea name="contenu" rows="10" cols="80"></textarea><br>
          <input type="submit" value="Envoyer le message">
        </form>
      <?php 

    }
    echo("</div>");



  mysqli_close();



?>
  </body>
</html>
