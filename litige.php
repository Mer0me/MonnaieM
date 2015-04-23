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

    if($_POST["tr"]!="")
    {
      $transaction=mysqli_fetch_array(exec_requete("select * from transaction,produit where transaction.idproduit=produit.idproduit and idtransaction=".$_POST["tr"], $conn));
      if($transaction["acheteur"]==$_SESSION["citoyen"]["idcitoyen"] || $transaction["vendeur"]==$_SESSION["citoyen"]["idcitoyen"])
      {
//        $citoyens=exec_requete("select mail from citoyen where idcitoyen='".$transaction["acheteur"]."' or idcitoyen='".$transaction["vendeur"]."' or idcitoyen='Merome'", $conn);
        $citoyens=exec_requete("select mail,idcitoyen from citoyen where idcitoyen='Merome'", $conn);
        while($citoyen1=mysqli_fetch_array($citoyens))
        {
            if(mail($citoyen1["mail"], "[Monnaie M] ".$_SESSION["citoyen"]["idcitoyen"]." signale un problème sur la transaction concernant \"".$transaction["objet"]."\"",
                    "Ce message a été envoyé par ".$_SESSION["citoyen"]["idcitoyen"]." depuis le site Monnaie M. Merci de ne pas utiliser le bouton 'Répondre' de votre messagerie, mais ce lien : http://merome.net/monnaiem/mail.php?id=".$citoyen1["code"]."&c=".urlencode($_SESSION["citoyen"]["idcitoyen"])." pour lui faire une réponse.\r\n\r\n".stripslashes($_POST["contenu"])."\r\n".
                    "\r\nPour répondre à ce message, cliquez ici : http://merome.net/monnaiem/mail.php?id=".$citoyen1["code"]."&c=".urlencode($_SESSION["citoyen"]["idcitoyen"])."\r\n",
                    "From: ".FROM."\r\n"
          					."Reply-To: ".FROM."\r\n"
          					."X-Mailer: PHP/" . phpversion()))
              echo("Message envoyé à ".$citoyen1["idcitoyen"]."<br>");
            else
               echo("Erreur lors de l'envoi du message à ".$citoyen1["idcitoyen"]."<br>");

        }
      }
      else
         echo("Cette transaction ne vous concerne pas");

    }

    if($_GET["t"]>0)
    {
      $transaction=mysqli_fetch_array(exec_requete("select * from transaction,produit where transaction.idproduit=produit.idproduit and idtransaction=".$_GET["t"], $conn));
      if($transaction["acheteur"]==$_SESSION["citoyen"]["idcitoyen"] || $transaction["vendeur"]==$_SESSION["citoyen"]["idcitoyen"])
      {
        echo("Vous pouvez utiliser ce formulaire pour demander l'annulation de la transaction, pour informer l'acheteur ou le vendeur d'un retard ou d'un problème quelconque...<br><br>");
        echo("<b>Signaler un problème sur la transaction concernant l'article <b><i>".$transaction["objet"]."</i></b> :</b><br>");
        ?>
          <form method="post" action="litige.php"><input type="hidden" name="tr" value="<?php  echo($_GET["t"]); ?>">
            <textarea name="contenu" rows="10" cols="80"></textarea><br>
            <input type="submit" value="Envoyer ce message à l'acheteur, au vendeur et à l'administrateur du site">
          </form>
        <?php 
       }
       else
         echo("Cette transaction ne vous concerne pas");

    }
    echo("</div>");



  mysqli_close();



?>
  </body>
</html>
