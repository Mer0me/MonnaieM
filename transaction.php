<?php 
/*
 +-------------------------------------------------------------------------+
 | Monnaie M - http://merome.net/monnaiem                                                              |
 +-------------------------------------------------------------------------+
 | Auteur : Jérôme VUITTENEZ - Merome : postmaster@merome.net              |
 +-------------------------------------------------------------------------+
*/
  session_start();

  if($_SESSION["citoyen"]["idcitoyen"]=="")
  {
    die("Session perdue. <a href=\"index.php\">Merci de cliquer ici</a>");
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

    if($_POST["recu"]>0)
    {
      $transactions=exec_requete("select *,transaction.prix as prixt from transaction,citoyen,produit where produit.idproduit=transaction.idproduit and vendeur=citoyen.idcitoyen and idtransaction=".$_POST["recu"], $conn);
      if(mysqli_num_rows($transactions)==1)
      {
        $transaction=mysqli_fetch_array($transactions);
        // Je suis bien le vendeur
        if($transaction["acheteur"]==$_SESSION["citoyen"]["idcitoyen"])
        {
            exec_requete("update transaction set statut='Terminé',note=".$_POST["note"].",commentaires='".$_POST["commentaires"]."' where idtransaction=".$_POST["recu"], $conn);
            $moyennes=exec_requete("select avg(note) as moy from transaction where vendeur='".$transaction["vendeur"]."' and statut='Terminé'", $conn);
            $moyenne=mysqli_fetch_array($moyennes);
            exec_requete("update citoyen set solde=solde+".$transaction["prixt"].",nbventes=nbventes+1,notevendeur=".$moyenne["moy"]." where idcitoyen='".$transaction["vendeur"]."'", $conn);
            $moyennes=exec_requete("select avg(note) as moy from transaction where acheteur='".$transaction["acheteur"]."'", $conn);
            $moyenne=mysqli_fetch_array($moyennes);
            exec_requete("update citoyen set noteacheteur=".$moyenne["moy"]." where idcitoyen='".$transaction["acheteur"]."'", $conn);
            echo("Cette transaction est maintenant terminée. Merci.<br>");
        }
        else
          die("Je ne suis pas concerné par cela");
      }

    }

    if($_POST["annule"]>0)
    {
      $transactions=exec_requete("select *,transaction.prix as prixt from transaction,citoyen,produit where produit.idproduit=transaction.idproduit and acheteur=citoyen.idcitoyen and idtransaction=".$_POST["annule"], $conn);
      if(mysqli_num_rows($transactions)==1)
      {
        $transaction=mysqli_fetch_array($transactions);
        // Je suis bien le vendeur
        if($transaction["vendeur"]==$_SESSION["citoyen"]["idcitoyen"])
        {
            exec_requete("update transaction set statut='Annulé' where idtransaction=".$_POST["annule"], $conn);
            echo("Cette transaction est maintenant annulée.<br>");
            exec_requete("update citoyen set solde=solde+".$transaction["prixt"]." where idcitoyen='".$transaction["acheteur"]."'", $conn);
            mail($transaction["mail"], "Annulation de votre achat sur Monnaie M",
                      "Le vendeur vient d'annuler la vente du produit ".$transaction["objet"].". ".$transaction["prixt"]." M ont été recrédités sur votre compte.\r\n",
                      "From: ".FROM."\r\n"
							."Reply-To: ".FROM."\r\n"
							."X-Mailer: PHP/" . phpversion());
        }
        if($transaction["acheteur"]==$_SESSION["citoyen"]["idcitoyen"])
        {
            exec_requete("update transaction set statut='Annulé' where idtransaction=".$_POST["annule"], $conn);
            echo("Cette transaction est maintenant annulée.<br>");
        }
      }
    }

    if($_POST["confirme"]>0)
    {
      $transactions=exec_requete("select *,transaction.prix as prixt from transaction,citoyen,produit where produit.idproduit=transaction.idproduit and acheteur=citoyen.idcitoyen and idtransaction=".$_POST["confirme"], $conn);
      if(mysqli_num_rows($transactions)==1)
      {
        $transaction=mysqli_fetch_array($transactions);
        // Je suis bien le vendeur
        if($transaction["vendeur"]==$_SESSION["citoyen"]["idcitoyen"] && $transaction["statut"]=="Commandé")
        {
            exec_requete("update transaction set statut='confirmé' where idtransaction=".$_POST["confirme"], $conn);
            if($transaction["port"]==1)
            {
              echo("Cette transaction est maintenant confirmée. L'acheteur a choisi un envoi par la Poste.<br>Vous avez une semaine pour la transmettre à l'acheteur :<br>".
                $transaction["nom"]." ".$transaction["prenom"]."<br>".
                $transaction["adresse"]."<br>".
                $transaction["cp"]." ".$transaction["ville"]);
              echo("<br>Vous pouvez communiquer avec l'acheteur <a href=\"http://merome.net/monnaiem/mail.php?c=".urlencode($transaction["acheteur"])."\">en cliquant ici</a>");
            }
            else
            {
              echo("Cette transaction est maintenant confirmée. L'acheteur a choisi une remise en mains propres.<br>");
              echo("Vous pouvez communiquer avec l'acheteur <a href=\"http://merome.net/monnaiem/mail.php?c=".urlencode($transaction["acheteur"])."\">en cliquant ici</a>");
            }

            mail($transaction["mail"], "Confirmation de votre achat sur Monnaie M",
                      "Le vendeur vient de confirmer la vente du produit ".$transaction["objet"]." pour ".$transaction["prixt"]." M.\nMerci de noter le vendeur au moment de la réception du produit. Sans validation de votre part après 30 jours, la note maximale sera attribuée au vendeur.\r\n",
                      "From: ".FROM."\r\n"
							."Reply-To: ".FROM."\r\n"
							."X-Mailer: PHP/" . phpversion());


        }
        else
        {
          if($transaction["acheteur"]==$_SESSION["citoyen"]["idcitoyen"] && $transaction["statut"]=="Proposé")
          {
	    $transactions=exec_requete("select *,transaction.prix as prixt from transaction,citoyen,produit where produit.idproduit=transaction.idproduit and vendeur=citoyen.idcitoyen and idtransaction=".$_POST["confirme"], $conn);
	    $transaction=mysqli_fetch_array($transactions);

            exec_requete("update transaction set statut='confirmé' where idtransaction=".$_POST["confirme"], $conn);
              echo("Cette transaction est maintenant confirmée.<br>Vous pouvez communiquer avec le vendeur <a href=\"http://merome.net/monnaiem/mail.php?c=".urlencode($transaction["vendeur"])."\">en cliquant ici</a><br><br>");

              exec_requete("update citoyen set solde=solde-".$transaction["prixt"]. " where idcitoyen='".$_SESSION["citoyen"]["idcitoyen"]."'", $conn);

              mail($transaction["mail"], "Votre proposition a été validée sur Monnaie M",
                    $_SESSION["citoyen"]["idcitoyen"]." vient de valider votre proposition pour le produit ou service ".$transaction["objet"]." pour ".$transaction["prixt"]." M. Merci de lui faire parvenir rapidement sa commande. Votre compte sera crédité lorsqu'il l'aura réceptionnée.\r\n",
                    "From: ".FROM."\r\n"
						."Reply-To: ".FROM."\r\n"
						."X-Mailer: PHP/" . phpversion());

            mail($_SESSION["citoyen"]["mail"], "Enregistrement de votre achat sur Monnaie M",
                    "Vous venez d'accepter sur Monnaie M la proposition pour le produit ou service ".$transaction["objet"]." pour ".$transaction["prixt"]." M.\n".$transaction["vendeur"]." a été prévenu par mail. \r\n",
                    "From: ".FROM."\r\n"
						."Reply-To: ".FROM."\r\n"
						."X-Mailer: PHP/" . phpversion());

              die("Un mail vient d'être envoyé au vendeur pour l'avertir de votre achat. Lorsque vous recevrez votre commande, merci de confirmer la réception et de noter le vendeur<br>");

          }
          else
            die("Je ne suis pas concerné par cela");
        }
      }

    }

    if($_GET["t"]>0)
    {
      $transactions=exec_requete("select *,transaction.prix as prixt from transaction,produit where transaction.idproduit=produit.idproduit and idtransaction=".$_GET["t"], $conn);
      if(mysqli_num_rows($transactions)==1)
      {
        $transaction=mysqli_fetch_array($transactions);
        // Je suis l'acheteur
        if($transaction["acheteur"]==$_SESSION["citoyen"]["idcitoyen"])
        {
          if($transaction["statut"]=="Proposé")
          {
              if($transaction["port"]==1)
                $port=" (avec envoi par la poste).";
              else
                $port=" (avec remise en mains propres à organiser avec le vendeur).";
              if($transaction["icone"]!="" && file_exists(str_replace("http://merome.net/","/var/www/",$transaction["icone"])))
                echo("Cette proposition est en attente de validation.<br>Validez-vous la transaction ".$port."?<br>
                    <img src=\"".$transaction["icone"]."\"><br>".$transaction["objet"]." (".$transaction["prixt"]." <img align=\"middle\" src=\"images/m.png\">)<br><b>Commandé le : </b>".$transaction["datevente"]." à ".$transaction["vendeur"]);
              else
                echo("Cette proposition est en attente de validation.<br>Validez-vous la transaction ".$port."?<br>
                    <i>Pas d'image disponible</i><br>".$transaction["objet"]." (".$transaction["prixt"]." <img align=\"middle\" src=\"images/m.png\">)<br><b>Commandé le : </b>".$transaction["datevente"]." à ".$transaction["vendeur"]);
              ?>
                <br>
                <form method="post" action="transaction.php"><input type="hidden" name="confirme" value="<?php  echo($transaction["idtransaction"]); ?>">
                <input type="submit" value="Je valide cette proposition"></form>

                <form method="post" action="transaction.php"><input type="hidden" name="annule" value="<?php  echo($transaction["idtransaction"]); ?>">
                <input type="submit" value="J'annule cette proposition"></form>

              <?php 


          }
          if($transaction["statut"]=="confirmé")
          {
            if($transaction["icone"]!="" && file_exists(str_replace("http://merome.net/","/var/www/",$transaction["icone"])))
              echo("Cette commande est en attente de réception. L'avez-vous reçue ?<br>
                  <img src=\"".$transaction["icone"]."\"><br>".$transaction["objet"]." (".$transaction["prixt"]." <img align=\"middle\" src=\"images/m.png\">)<br><b>Commandé le : </b>".$transaction["datevente"]." à ".$transaction["vendeur"]);
            else
              echo("Cette commande est en attente de réception. L'avez-vous reçue ?<br>
                  <i>Pas d'image disponible</i><br>".$transaction["objet"]." (".$transaction["prixt"]." <img align=\"middle\" src=\"images/m.png\">)<br><b>Commandé le : </b>".$transaction["datevente"]." à ".$transaction["vendeur"]);

            ?>
              <br><br>
              <form method="post" action="transaction.php"><input type="hidden" name="recu" value="<?php  echo($transaction["idtransaction"]); ?>">
              Je confirme que j'ai reçu cette commande et je note le vendeur sur cette transaction : <select name="note">
              <option value="5">Parfait</option>
              <option value="4">Bon</option>
              <option value="3">Correct</option>
              <option value="2">Médiocre</option>
              <option value="1">Mauvais</option>
              <option value="0">Très mauvais</option>
              </select><br><br>
              Commentaires : <input type="text" name="commentaires" size="70"><br><br>
              <input type="submit" value="OK"></form>
            <?php 
          }
        }
        else
        {
          // Je suis le vendeur
          if($transaction["vendeur"]==$_SESSION["citoyen"]["idcitoyen"])
          {
            if($transaction["statut"]=="Commandé")
            {
              if($transaction["port"]==1)
                $port=" (avec envoi par la poste à vos frais).";
              else
                $port=" (avec remise en mains propres à organiser avec l'acheteur).";
                if($transaction["icone"]!="" && file_exists(str_replace("http://merome.net/","/var/www/",$transaction["icone"])))
                  echo("Cette commande est en attente de confirmation.<br>Confirmez-vous la vente ".$port."?<br>
                    <img src=\"".$transaction["icone"]."\"><br>".$transaction["objet"]." (".$transaction["prixt"]." <img align=\"middle\" src=\"images/m.png\">)<br><b>Commandé le : </b>".$transaction["datevente"]." à ".$transaction["vendeur"]);
                else
                  echo("Cette commande est en attente de confirmation.<br>Confirmez-vous la vente ".$port."?<br>
                    <i>Pas d'image disponible</i><br>".$transaction["objet"]." (".$transaction["prixt"]." <img align=\"middle\" src=\"images/m.png\">)<br><b>Commandé le : </b>".$transaction["datevente"]." à ".$transaction["vendeur"]);

              ?>
                <br>
                <form method="post" action="transaction.php"><input type="hidden" name="confirme" value="<?php  echo($transaction["idtransaction"]); ?>">
                <input type="submit" value="Je confirme cette vente"></form>

                <form method="post" action="transaction.php"><input type="hidden" name="annule" value="<?php  echo($transaction["idtransaction"]); ?>">
                <input type="submit" value="J'annule cette vente"></form>

              <?php 
            }
          }
          else
          {
            echo("Vous n'êtes pas concerné par cette transaction<br>");
          }
        }
      }
      else
        echo("Transaction introuvable");

    }

    echo("</div>");



  mysqli_close();



?>
  </body>
</html>
