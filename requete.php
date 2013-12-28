<?php
/*
 +-------------------------------------------------------------------------+
 | Monnaie M - http://merome.net/monnaiem                                                              |
 +-------------------------------------------------------------------------+
 | Auteur : Jérôme VUITTENEZ - Merome : postmaster@merome.net              |
 +-------------------------------------------------------------------------+
*/
include './config.php';

if(USER=="")
  die("Merci de compléter le fichier config.php avec les identifiants nécessaires à la connexion à la base de données");

$cree_mysqlid=mysql_connect(HOST, USER, PWD) or die("Problème de connexion à la base, merci de ressayer plus tard.");
mysql_select_db(DB) or die("Problème de connexion à la base, merci de ressayer plus tard.");

function exec_requete($phrase,$debug=0)
{
    if($debug==1)
       echo($phrase."<br>");
    
    $exec_requete_resultat=@mysql_query($phrase);
    if($exec_requete_resultat>0)
      return($exec_requete_resultat);
    else
    {
      mail(ADMIN,"Erreur sur une page de monnaiem"
                                                                ,"Impossible d'exécuter la requète (".$phrase.") sur la page ".$_SERVER['PHP_SELF'],
                                                                "From: ".FROM."\r\n"
                                                                ."Reply-To: ".FROM."\r\n"
                                                                ."X-Mailer: PHP/" . phpversion());
      echo("Une erreur a été rencontrée, un mail automatique a été envoyé à l'administrateur");
      return($exec_requete_resultat);
    }
}



function to_date($d)
{
  if($d!="")
    return(substr($d,6,4).substr($d,3,2).substr($d,0,2));
  else
    return("");
}

function to_str($d)
{
  if($d!="" && $d!=0)
  {
    if(strlen($d)>10)
      return(substr($d,8,2)."/".substr($d,5,2)."/".substr($d,0,4)." à ".substr($d,10,9));
    else
      return(substr($d,8,2)."/".substr($d,5,2)."/".substr($d,0,4));
  }
  else
    return("");
}
?>
