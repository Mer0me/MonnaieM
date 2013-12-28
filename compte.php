<?php 
/*
 +-------------------------------------------------------------------------+
 | Monnaie M - http://merome.net/monnaiem                                                              |
 +-------------------------------------------------------------------------+
 | Auteur : Jérôme VUITTENEZ - Merome : postmaster@merome.net              |
 +-------------------------------------------------------------------------+
*/
  session_start();
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

    echo("<div id=\"accueil\"><img src=\"images/bandeau.png\"><br><br>");

    if($_GET["activer"]==1)
    {
      if($_POST["code"]>0)
      {
          include './requete.php';

          $req_compte=exec_requete("select * from citoyen where idcitoyen like '".$_POST["pseudo"]."' and activation=".$_POST["code"]);
          if(mysql_num_rows($req_compte)==1)
          {
            exec_requete("update citoyen set valide=1,solde=50,notevendeur=5,activation=0 where idcitoyen like '".$_POST["pseudo"]."' and activation=".$_POST["code"]);
            $req_mail=exec_requete("select mail from citoyen where idcitoyen like '".$_POST["pseudo"]."'");
            $mail=mysql_fetch_array($req_mail);

            mail($mail["mail"], "Validation de votre compte sur Monnaie M", "Félicitations, votre compte est maintenant entièrement validé. Vous pouvez commencer à utiliser l'espace d'échange Monnaie M.\nhttp://merome.net/monnaiem\n\n
    Merci de votre intérêt pour cette expérimentation.\n\nMerome.","From: ".FROM."\r\n"
    							."Reply-To: ".FROM."\r\n"
    							."X-Mailer: PHP/" . phpversion());
            die("Compte validé.<br><a href=\"index.php\">Vous pouvez maintenant vous connecter à Monnaie M</a>");
          }
          else
            die("Code d'activation ou pseudo invalide");
      }
      ?>
          <center>
          <form method="post" action="compte.php?activer=1">
            Votre pseudo : <input type="text" name="pseudo"><br>
            Le code d'activation reçu par courrier postal : <input type="text" name="code"><br><br>
            <input type="submit" value="Activer mon compte">
          </form>
          </center>
      <?php 
      die();
    }

    if($_POST["c2"]!="")
    {
      include './requete.php';

      $req_compte=exec_requete("select * from citoyen where md5(concat(idcitoyen,mail)) like '".$_POST["c2"]."' and valide=1");
      if(mysql_num_rows($req_compte)==1)
      {
        $compte=mysql_fetch_array($req_compte);

        if($_POST["pass1"]!=$_POST["pass2"])
        {
          echo("Les deux mots de passe ne correspondent pas !<br><br>");
          die("Identifiant de votre compte (à retenir !) : ".$compte["idcitoyen"]."<br><form method=\"post\" action=\"compte.php\">
              Choisissez votre nouveau mot de passe : <input type=\"password\" name=\"pass1\"><br>
              Saisissez à nouveau votre nouveau mot de passe pour vérification :<input type=\"password\" name=\"pass2\"><br>
              <input type=\"hidden\" name=\"c2\" value=\"".$_POST["c2"]."\">
              <input type=\"submit\" value=\"Valider votre changement de mot de passe\"></form>");
        }
        else
        {
          echo("Identifiant de votre compte (à retenir !) : ".$compte["idcitoyen"]."<br>");
          exec_requete("update citoyen set mdp=md5('".$_POST["pass1"]."') where md5(concat(idcitoyen,mail)) like '".$_POST["c2"]."' and valide=1");
          die("Votre mot de passe a été modifié, vous pouvez maintenant essayer <a href=\"index.php\">de vous connecter avec votre nouveau mot de passe</a>");
        }
      }
      else
        die("Impossible de modifier votre compte. Merci de <a href=\"mailto:simplceommebonjour@merome.net\">prendre contact avec l'administrateur</a>");
    }

    if($_GET["c"]!="")
    {
      include './requete.php';

      $req_compte=exec_requete("select * from citoyen where md5(concat(idcitoyen,mail)) like '".$_GET["c"]."' and valide=1");
      if(mysql_num_rows($req_compte)==1)
      {
        $compte=mysql_fetch_array($req_compte);
        die("Identifiant de votre compte (à retenir !) : ".$compte["idcitoyen"]."<br><form method=\"post\" action=\"compte.php\">
            Choisissez votre nouveau mot de passe : <input type=\"password\" name=\"pass1\"><br>
            Saisissez à nouveau votre nouveau mot de passe pour vérification :<input type=\"password\" name=\"pass2\"><br>
            <input type=\"hidden\" name=\"c2\" value=\"".$_GET["c"]."\">
            <input type=\"submit\" value=\"Valider votre changement de mot de passe\"></form>");
      }
      else
        die("Impossible de modifier votre compte. Merci de <a href=\"mailto:simplceommebonjour@merome.net\">prendre contact avec l'administrateur</a>");
    }
    
    if($_POST["oublimail"]!="")
    {
      include './requete.php';

      $req_mail=exec_requete("select * from citoyen where mail like '".$_POST["oublimail"]."' and valide=1");
      if(mysql_num_rows($req_mail)==1)
      {
        $compte=mysql_fetch_array($req_mail);
        mail($compte["mail"], "Réinitialisation du mot de passe", "Une demande de réinitialisation du mot de passe de votre compte Monnaie M vient d'être effectuée avec votre adresse mail.\nSi vous n'en êtes pas à l'origine, ne tenez pas compte de ce message.\n\nCliquez ici pour réinitialiser votre mot de passe : http://merome.net/monnaiem/compte.php?c=".md5($compte["idcitoyen"].$compte["mail"])."\n\n
        Merci de votre intérêt pour cette expérimentation.\n\nMerome.","From: ".FROM."\r\n"
							."Reply-To: ".FROM."\r\n"
							."X-Mailer: PHP/" . phpversion());
        die("Il y a bien un compte correspondant à cette adresse mail. Un message vient de vous être envoyé, il vous permettra de changer votre mot de passe");
      }
      else
        die("Aucun compte n'est associé à cette adresse email<br><br><form method=\"post\" action=\"compte.php\">Avec quelle adresse mail vous êtes-vous inscrit sur Monnaie M ?<input type=\"text\" name=\"oublimail\"><input type=\"submit\" value=\"Réinitialiser mon mot de passe\"></form>");
    }

    if($_GET["oubli"]==1)
    {
      echo("<center><form method=\"post\" action=\"compte.php\">Avec quelle adresse mail vous êtes-vous inscrit sur Monnaie M ?&nbsp;<input type=\"text\" name=\"oublimail\"><br><input type=\"submit\" value=\"Réinitialiser mon mot de passe\"></form></center>");
      die("");
    }

    if($_POST["refuse"]!="")
    {
      include './requete.php';
        $req_mail=exec_requete("select mail from citoyen where idcitoyen='".$_POST["refuse"]."'");
        $mail=mysql_fetch_array($req_mail);
        mail($mail["mail"], "Refus de votre compte sur Monnaie M", "Votre compte sur Monnaie M n'a pas été accepté pour la raison suivante :\n".$_POST["raison"]."\n\nVous pouvez essayer de vous réinscrire en tenant compte de cette remarque.\n\nhttp://merome.net/monnaiem/compte.php\n\n
Merci de votre intérêt pour cette expérimentation.\n\nMerome.","From: ".FROM."\r\n"
							."Reply-To: ".FROM."\r\n"
							."X-Mailer: PHP/" . phpversion());
         exec_requete("delete from citoyen where idcitoyen='".$_POST["refuse"]."'");
         mysql_close();
         die("Utilisateur prévenu, compte supprimé.");
    }

    if($_GET["avalider"]!="")
    {
      include './requete.php';

      if($_SESSION["citoyen"]["mail"]==ADMIN)
      {




        if($_GET["ok"]==1)
        {
          exec_requete("update citoyen set valide=1,solde=50,notevendeur=5 where idcitoyen='".addslashes($_GET["avalider"])."'");
          $req_mail=exec_requete("select mail from citoyen where idcitoyen='".$_GET["avalider"]."'");
          $mail=mysql_fetch_array($req_mail);

          mail($mail["mail"], "Validation de votre compte sur Monnaie M", "Félicitations, votre compte est maintenant entièrement validé. Vous pouvez commencer à utiliser l'espace d'échange Monnaie M.\nhttp://merome.net/monnaiem\n\n
  Merci de votre intérêt pour cette expérimentation.\n\nMerome.","From: ".FROM."\r\n"
  							."Reply-To: ".FROM."\r\n"
  							."X-Mailer: PHP/" . phpversion());
          die("Compte validé");

        }
        else
        {
          $ps=exec_requete("select * from citoyen where idcitoyen = '".$_GET["avalider"]."'");
          if(mysql_num_rows($ps)==1)
          {
            $compte=mysql_fetch_array($ps);
            print_r($compte);
            echo("<a href=\"compte.php?avalider=".urlencode($_GET["avalider"])."&ok=1\">OK</a><br><form method=\"post\" action=\"compte.php\"><input type=\"hidden\" name=\"refuse\" value=\"".$_GET["avalider"]."\"><input type=\"text\" name=\"raison\"><input type=\"submit\" value=\"Refuser\"></form>");
          }
        }

        mysql_close();
      }
      else
        echo("Seul l'administrateur du site a accès à cette page");
      die();
    }

    if($_GET["valide"]!="")
    {
      include './requete.php';

      $ps=exec_requete("select idcitoyen,valide from citoyen where md5(idcitoyen) = '".$_GET["valide"]."'");
      if(mysql_num_rows($ps)==1)
      {
        $pseudo=mysql_fetch_array($ps);
        if($pseudo["valide"]==1)
          echo("Le compte a déjà été validé par un administrateur, si vous n'avez pas reçu d'email, merci de vérifier votre filtre indésirables");
        else
        {
            mail(ADMIN,"Nouvel inscrit sur Monnaie M","A valider : http://merome.net/monnaiem/compte.php?avalider=".urlencode($pseudo["idcitoyen"]),"From: ".FROM."\r\n"
							."Reply-To: ".FROM."\r\n"
							."X-Mailer: PHP/" . phpversion());
            echo("Votre adresse email a été validée. Votre compte doit maintenant être vérifié par un administrateur. Vous recevrez un email lorsque ce sera fait. Merci de votre patience.<br>");
        }
        mysql_close();
        die();
      }
      else
    	{
        mail(ADMIN,"Erreur - Nouvel inscrit sur Monnaie M",$_GET["valide"]." A valider : http://merome.net/monnaiem/compte.php?avalider=".urlencode($pseudo["idcitoyen"]),"From: ".FROM."\r\n"
                                                            ."Reply-To: ".FROM."\r\n"
                                                            ."X-Mailer: PHP/" . phpversion());

    	}
      mysql_close();

    }

    if($_POST["pseudo"]!="" && $_POST["pass1"]==$_POST["pass2"] && $_POST["pass1"]!="" && $_POST["mail"]!="" && $_POST["valide1"]=="on" && $_POST["valide2"]=="on")
    {
      include './requete.php';

      $ps=exec_requete("select idcitoyen from citoyen where idcitoyen like '".$pseudo."'");
      if(mysql_num_rows($ps)>0)
        echo("Ce pseudo est déjà utilisé, merci d'en choisir un autre<br>");
      else
      {

        $res=exec_requete("INSERT INTO `monnaiem`.`citoyen` (idcitoyen,`mdp`,`nom`,`prenom`,`adresse`,`cp`,`ville`,`tel`,`valide`,`mail`,dateadhesion) VALUES
                  ('".$pseudo."', '".md5($pass1)."', '".$nom."', '".$prenom."', '".$adresse."', '".$cp."', '".$ville."', '".$tel."', 0, '".$mail."',now())");
        mysql_close();

        if($res)
        {
          mail($_POST["mail"], "Validation de votre inscription sur Monnaie M", "Votre adresse mail a été utilisée pour l'inscription sur le site Monnaie M.\nPour confirmer votre inscription, merci de cliquer sur ce lien :\n http://merome.net/monnaiem/compte.php?valide=".md5($_POST["pseudo"])."\n\n
Ce faisant, vous avez conscience de participer à une expérimentation qui peut s'arrêter à tout moment, et pendant laquelle vous êtes entièrement responsable de vos actes.\n
Par ailleurs, vous vous engagez à ne pas utiliser le site pour autre chose que ce pour quoi il est prévu : l'échange de biens et de services à caractère non professionnel.\n\n
Vous recevrez un mail lorsque votre compte sera validé de manière définitive.\n\n
Merci de votre intérêt pour cette expérimentation.\n\nMerome.","From: ".FROM."\r\n"
							."Reply-To: ".FROM."\r\n"
							."X-Mailer: PHP/" . phpversion());
          die("Votre inscription a été prise en compte, après vérification de votre mail, elle sera examinée par un administrateur pour validation définitive.");
        }
        else
          echo("Une erreur s'est produite à la validation du formulaire. Un mail a été envoyé à l'administrateur pour régler le problème. Ressayez dans quelques heures.");

        die();
       }
    }
    else
    {
      if($_POST["pseudo"]!="")
        echo("Le formulaire n'est pas complet ou invalide. Merci de recommencer...<br>");
    }


    ?>
      
      <form method="post" action="compte.php">
        <table align="center">
          <tr><td colspan="2" align="center"><b>Formulaire d'inscription</b><br><br><small><i>Ce site a fait l'objet d'une déclaration à la CNIL, sous le n°1670381
 le 02/05/2013<br>Pour tout accès, modification ou suppression de vos données personnelles, merci de prendre contact avec l'administrateur à l'adresse : simplecommebonjour@merome.net</i></small><br>&nbsp;</td></tr>
          <tr><td align="right">Choisissez un pseudo </td><td><input type="text" name="pseudo"></td></tr>
          <tr><td align="right">Mot de passe</td><td><input type="password" name="pass1"></td></tr>
          <tr><td align="right">Confirmation du mot de passe</td><td><input type="password" name="pass2"></td></tr>
          <tr><td align="right">Nom</td><td><input type="text" name="nom"></td></tr>
          <tr><td align="right">Prénom</td><td><input type="text" name="prenom"></td></tr>
          <tr><td colspan="2"><br><b>Cette adresse exclusivement sera utilisée pour l'envoi de vos achats et permettra la validation de votre compte :</b></td></tr>
          <tr><td align="right">Adresse</td><td><input type="text" name="adresse"></td></tr>
          <tr><td align="right">Code postal</td><td><input type="text" name="cp"></td></tr>
          <tr><td align="right">Ville</td><td><input type="text" name="ville"></td></tr>
          <tr><td colspan="2"><br><b>Le n° de téléphone fixe permet uniquement de valider l'adresse (éviter les multi-comptes) et ne sera pas utilisé ni visible par les autres utilisateurs du site :</b><br><small>Si vous n'avez pas de téléphone fixe, ou si votre adresse ne peut être vérifiée dans les pages blanches, un justificatif de domicile vous sera demandé.</small></td></tr>
          <tr><td align="right">Téléphone fixe</td><td><input type="text" name="tel"></td></tr>
          <tr><td colspan="2"><br><b>Un mail de confirmation vous sera envoyé à cette adresse :</b></td></tr>
          <tr><td align="right">Adresse mail</td><td><input type="text" name="mail"></td></tr>
          <tr><td colspan="2"><input type="checkbox" name="valide1">J'ai conscience de participer à une expérimentation qui peut s'arrêter à tout moment, et pendant laquelle je suis entièrement responsable de mes actes.</td></tr>
          <tr><td colspan="2"><input type="checkbox" name="valide2">Je m'engage à ne pas utiliser le site pour autre chose que ce pour quoi il est prévu : l'échange de biens et de services à caractère non professionnel.</td></tr>
          <tr><td colspan="2" align="center">En validant ce formulaire, j'accepte sans réserve <a href="http://merome.net/monnaiem/ReglementMonnaieM.pdf">le règlement de monnaie M.</a></td></tr>
          <tr><td colspan="2" align="center"><input type="submit" value="Je m'inscris"></td></tr>
        </table>
      </form>
    <?php 



?>
  </body>
</html>
