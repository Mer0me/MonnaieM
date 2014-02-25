<?php
/*
 +-------------------------------------------------------------------------+
 | Monnaie M - http://merome.net/monnaiem                                                              |
 | Version 1.0                                                             |
 |                                                                         |
 | Copyright (C) 2013, Merome                                              |
 |                                                                         |
 | Ce programme est un logiciel libre, vous pouvez l'utiliser et le        |
 | distribuer sous les termes de la licence GPL V2, publié par la          |
 | Free Software Foundation.                                               |
 +-------------------------------------------------------------------------+
 | Auteur : Olivier DALMAS - Ophiuchus : utilisezlinux@gmail.com           |
 +-------------------------------------------------------------------------+
*/
include './config.php';

if($user=="")
  die("Merci de compléter le fichier config.php avec les identifiants nécessaires à la connexion à la base de données");

$cree_mysqlid=mysql_connect( $host, $user, $pwd) or die("Problème de connexion à la base, merci de ressayer plus tard.");
mysql_select_db($db) or die("Problème de connexion à la base, merci de ressayer plus tard.");


if(isset($_GET['action']) && $_GET['action'] == 'backup'){
  backupTables();
  die();
  }

/***********************/
/* Sauvegarde des tables */
/***********************/
echo 'Sauvegarde des tables<br /><br />';

mysql_query('CREATE TABLE IF NOT EXISTS `sauv_citoyen` (
  `idcitoyen` varchar(30) NOT NULL,
  `mdp` varchar(100) NOT NULL,
  `nom` varchar(30) NOT NULL,
  `prenom` varchar(30) NOT NULL,
  `adresse` varchar(100) NOT NULL,
  `cp` varchar(6) NOT NULL,
  `ville` varchar(100) NOT NULL,
  `tel` varchar(20) NOT NULL,
  `valide` tinyint(4) NOT NULL,
  `notevendeur` float NOT NULL,
  `noteacheteur` float NOT NULL,
  `solde` bigint(20) NOT NULL,
  `nbventes` bigint(20) NOT NULL,
  `dateadhesion` date NOT NULL,
  `mail` varchar(200) NOT NULL,
  `derniereconnexion` datetime NOT NULL,
  `activation` int(11) NOT NULL,
  PRIMARY KEY (`idcitoyen`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;');


mysql_query('INSERT INTO `sauv_citoyen` SELECT * FROM `citoyen`;');


mysql_query('CREATE TABLE IF NOT EXISTS `sauv_historique` (
  `datemesure` date NOT NULL,
  `nbutilisateurs` int(11) NOT NULL,
  `massetotale` int(11) NOT NULL,
  `massemoyenne` float NOT NULL,
  `rdb` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;');

mysql_query('INSERT INTO `sauv_historique` SELECT * FROM `historique`;');



mysql_query('CREATE TABLE IF NOT EXISTS `sauv_produit` (
  `idcitoyen` varchar(30) NOT NULL,
  `idproduit` bigint(20) NOT NULL AUTO_INCREMENT,
  `objet` varchar(200) NOT NULL,
  `typeproduit` varchar(15) NOT NULL,
  `typeannonce` varchar(7) NOT NULL,
  `description` varchar(2000) NOT NULL,
  `photo` varchar(500) NOT NULL,
  `icone` varchar(500) NOT NULL,
  `nbex` int(11) NOT NULL,
  `valide` tinyint(4) NOT NULL,
  `prix` int(11) NOT NULL,
  `fdp` int(11) NOT NULL,
  `datesaisie` datetime NOT NULL,
  `dateexpiration` date NOT NULL,
  `etat` varchar(20) NOT NULL,
  `envoipossible` varchar(3) NOT NULL,
  `mainspropres` varchar(3) NOT NULL,
  `categorie` varchar(100) NOT NULL,
  `nbconsultations` bigint(20) NOT NULL,
  PRIMARY KEY (`idproduit`),
  KEY `categorie` (`categorie`),
  KEY `valide` (`valide`),
  KEY `prix` (`prix`),
  KEY `idcitoyen` (`idcitoyen`),
  FULLTEXT KEY `ft` (`objet`,`description`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;');


mysql_query('INSERT INTO `sauv_produit` SELECT * FROM `produit`;');


mysql_query('CREATE TABLE IF NOT EXISTS `sauv_transaction` (
  `acheteur` varchar(30) NOT NULL,
  `vendeur` varchar(30) NOT NULL,
  `idproduit` bigint(20) NOT NULL,
  `idtransaction` bigint(20) NOT NULL AUTO_INCREMENT,
  `datevente` datetime NOT NULL,
  `statut` varchar(20) NOT NULL,
  `prix` bigint(20) NOT NULL,
  `port` int(11) NOT NULL,
  `note` tinyint(4) NOT NULL,
  `commentaires` varchar(400) NOT NULL,
  PRIMARY KEY (`idtransaction`),
  KEY `acheteur` (`acheteur`,`vendeur`,`idproduit`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;');

mysql_query('INSERT INTO `sauv_transaction` SELECT * FROM `transaction`;');


/***********************/
/* Création des tables */
/***********************/
echo 'Création des tables<br /><br />';

/* table annonce */
echo 'Création de la table produit_tmp<br />';
$nbErreurs = 0;
if( mysql_query('
CREATE TABLE IF NOT EXISTS `produit_tmp` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`nom` varchar(255) NOT NULL,
`detail` varchar(2000) NOT NULL,
`typeProduit` int(2) UNSIGNED NOT NULL DEFAULT \'0\',
`photo` varchar(255) NOT NULL,
`icone` varchar(255) NOT NULL,
`quantite` int(3) UNSIGNED NOT NULL,
PRIMARY KEY (`id`),
FULLTEXT KEY `ft` (`detail`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1')
)echo 'table produit_tmp correctement créée';
else{
  echo 'Erreur à la création de la table produit_tmp<br />';
  echo mysql_error().'<br />';
  $nbErreurs++;
}
echo '<br />';

/* table catégorie */
echo 'Création de la table categorie<br />';
$nbErreurs = 0;
if( mysql_query('
  CREATE TABLE IF NOT EXISTS `categorie` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) UNSIGNED NOT NULL,
  `idParent` int(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
  ) ENGINE=MyISAM DEFAULT CHARSET=latin1;')
)echo 'table categorie correctement créée';
else{
  echo 'Erreur à la création de la table categorie';
  echo mysql_error().'<br />';
  $nbErreurs++;
}
echo '<br />';

/* table lot */
echo 'Création de la table lot<br />';
$nbErreurs = 0;
if( mysql_query('
  CREATE TABLE IF NOT EXISTS `lot` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`idAnnonce` int(11) UNSIGNED NOT NULL DEFAULT \'0\',
`idProduit` int(11) UNSIGNED NOT NULL DEFAULT \'0\',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;')
)echo 'table lot correctement créée';
else{
  echo 'Erreur à la création de la table lot';
  echo mysql_error().'<br />';
  $nbErreurs++;
}
echo '<br />';


echo '<br /><br />';
echo $nbErreurs>0?die($nbErreurs.' problèmes à la création des tables, migration interrompue<br/><a href="?action=backup">Restaurer les tables?</a><br/>'):'Création des tables réussie';

echo '<br /><br />';

/***********************/
/* Migration des tables */
/***********************/
echo 'Migration des tables<br /><br />';

/* table citoyen */
echo 'Migration de la table citoyen<br />';
$nbErreurs = 0;

$requetes = array(
    'ALTER TABLE citoyen
  DROP PRIMARY KEY',
    'ALTER TABLE citoyen
  ADD `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST',
    'ALTER TABLE citoyen
  CHANGE `idcitoyen` `pseudo` VARCHAR(255)  NOT NULL',
    'ALTER TABLE citoyen
  CHANGE `mdp` `motDePasse` VARCHAR(100) NOT NULL',
    'ALTER TABLE citoyen
  CHANGE `cp` `codePostal` VARCHAR(6) NOT NULL',
    'ALTER TABLE citoyen
  CHANGE `tel` `telephone` VARCHAR(20) NOT NULL',
    'ALTER TABLE citoyen
  MODIFY `valide` TINYINT(1) UNSIGNED NOT NULL',
    'ALTER TABLE citoyen
  CHANGE `notevendeur` `noteVendeur`FLOAT UNSIGNED NOT NULL',
    'ALTER TABLE citoyen
  CHANGE `noteacheteur` `noteAcheteur` FLOAT UNSIGNED NOT NULL',
    'ALTER TABLE citoyen
  CHANGE `nbventes` `nombreVentes` MEDIUMINT UNSIGNED NOT NULL',
    'ALTER TABLE citoyen
  CHANGE `dateadhesion` `dateAdhesion` DATE NOT NULL',
    'ALTER TABLE citoyen
  CHANGE `derniereconnexion` `derniereConnexion` DATETIME NOT NULL',
    'ALTER TABLE citoyen
  MODIFY `activation` SMALLINT(4) UNSIGNED NOT NULL'
);
        
foreach($requetes as $requete){
  if( !mysql_query($requete))
  {
    echo '<span style="color:red">Erreur à la création de la requete</span> :'.$requete.'<br /><br/><a href="?action=backup">Restaurer les tables?</a><br/>';
    echo mysql_error().'<br />';
    $nbErreurs++;
  }
}
echo '<br /><br />';



/* table transaction */
echo 'Sauvegarde de la table transaction<br /><br />';

if(!mysql_query('CREATE TABLE `transaction_bak` (
`acheteur` varchar( 30 ) NOT NULL ,
`vendeur` varchar( 30 ) NOT NULL ,
`idproduit` bigint( 20 ) NOT NULL ,
`idtransaction` bigint( 20 ) NOT NULL AUTO_INCREMENT ,
`datevente` datetime NOT NULL ,
`statut` varchar( 20 ) NOT NULL ,
`prix` bigint( 20 ) NOT NULL ,
`port` int( 11 ) NOT NULL ,
`note` tinyint( 4 ) NOT NULL ,
`commentaires` varchar( 400 ) NOT NULL ,
PRIMARY KEY ( `idtransaction` ) ,
KEY `acheteur` ( `acheteur` , `vendeur` , `idproduit` )
) ENGINE = MYISAM DEFAULT CHARSET = latin1;')){
  echo '<span style="color:red">Erreur à la création de la copie de la table transaction</span> :'.$requete.'<br /><br/><a href="?action=backup">Restaurer les tables?</a><br/>';
    echo mysql_error().'<br />';
    die();
}

mysql_query("SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';");

if(!mysql_query('INSERT INTO `transaction_bak`
SELECT *
FROM `transaction` ;')){
  echo '<span style="color:red">Erreur à la sauvegarde de la table transaction</span> :'.$requete.'<br /><br/><a href="?action=backup">Restaurer les tables?</a><br/>';
    echo mysql_error().'<br />';
    die();
}

echo 'Table transaction sauvegardée<br /><br />';
echo 'Migration de la table transaction<br />';
$nbErreurs = 0;

$requetes = array(
    'ALTER TABLE transaction
  DROP `vendeur`',
    'ALTER TABLE transaction
  CHANGE `acheteur` `idAcheteur` INT(11) UNSIGNED NOT NULL',
    'ALTER TABLE transaction
  CHANGE `idProduit` `idAnnonce` INT(11) UNSIGNED NOT NULL',
    'ALTER TABLE transaction
  CHANGE `idtransaction` `id` INT(11) UNSIGNED NOT NULL',
    'ALTER TABLE transaction
  CHANGE `datevente` `dateVente` DATETIME NOT NULL',
    'ALTER TABLE transaction
  MODIFY `statut` SMALLINT(1) UNSIGNED NOT NULL',
    'ALTER TABLE transaction
  MODIFY `prix` INT(11) UNSIGNED NOT NULL'
);
        
foreach($requetes as $requete){
  if( !mysql_query($requete))
  {
    echo '<span style="color:red">Erreur à la création de la requete</span> :'.$requete.'<br /><br/><a href="?action=backup">Restaurer les tables?</a><br/>';
    echo mysql_error().'<br />';
    $nbErreurs++;
  }
}
echo '<br />Transformation des données de la table transaction<br />';


echo 'Migration de la table historique<br />';
$nbErreurs = 0;

$requetes = array(
    'ALTER TABLE historique
  CHANGE `datemesure` `dateMesure` DATE',
    'ALTER TABLE historique
  CHANGE `nbutilisateurs` `nombreUtilisateurs` INT(11) UNSIGNED NOT NULL',
    'ALTER TABLE historique
  CHANGE `massetotale` `masseTotale` INT(11) UNSIGNED NOT NULL',
    'ALTER TABLE historique
  CHANGE `massemoyenne` `masseMoyenne`  FLOAT UNSIGNED NOT NULL',
    'ALTER TABLE historique
  CHANGE `rdb` `revenuDeBase`  INT(11) UNSIGNED NOT NULL'
);
        
foreach($requetes as $requete){
  if( !mysql_query($requete))
  {
    echo '<span style="color:red">Erreur à la création de la requete</span> :'.$requete.'<br /><br/><a href="?action=backup">Restaurer les tables?</a><br/>';
    echo mysql_error().'<br />';
    $nbErreurs++;
  }
}
echo '<br />Transformation des données de la table historique<br />';

/* Conversion des indicateurs
 * 
 * statut :
 *  - Proposé = 1
 *  - en cours = 2
 *  - confirmé = 3
 *  - Commandé = 4
 *  - Terminé = 5
 *  - Annulé = 6
 * 
 */

if(!mysql_query("UPDATE transaction t
INNER JOIN transaction_bak tb ON tb.idtransaction = t.id
LEFT JOIN citoyen c ON c.pseudo = tb.acheteur
  SET t.idAcheteur =  c.id,
  t.statut = IF (tb.statut = 'Proposé', 1,
    IF (tb.statut = 'en cours', 2,
      IF (tb.statut = 'confirmé', 3,
        IF (tb.statut = 'Commandé', 4,
          IF (tb.statut = 'Terminé', 5,
            IF (tb.statut = 'Annulé', 6,0
    ))))))")){
  echo '<span style="color:red">Erreur à la transformation des id citoyens</span> :'.$requete.'<br /><br/><a href="?action=backup">Restaurer les tables?</a><br/>';
    echo mysql_error().'<br />';
    die();
}



/* table annonce */
echo 'Sauvegarde de l\'ancienne table produit<br /><br />';

if(!mysql_query('
 CREATE  TABLE  `monnaiem`.`produit_bak` (  `idcitoyen` varchar( 30  )  NOT  NULL ,
 `idproduit` bigint( 20  )  NOT  NULL  AUTO_INCREMENT ,
 `objet` varchar( 200  )  NOT  NULL ,
 `typeproduit` varchar( 15  )  NOT  NULL ,
 `typeannonce` varchar( 7  )  NOT  NULL ,
 `description` varchar( 2000  )  NOT  NULL ,
 `photo` varchar( 500  )  NOT  NULL ,
 `icone` varchar( 500  )  NOT  NULL ,
 `nbex` int( 11  )  NOT  NULL ,
 `valide` tinyint( 4  )  NOT  NULL ,
 `prix` int( 11  )  NOT  NULL ,
 `fdp` int( 11  )  NOT  NULL ,
 `datesaisie` datetime NOT  NULL ,
 `dateexpiration` date NOT  NULL ,
 `etat` varchar( 20  )  NOT  NULL ,
 `envoipossible` varchar( 3  )  NOT  NULL ,
 `mainspropres` varchar( 3  )  NOT  NULL ,
 `categorie` varchar( 100  )  NOT  NULL ,
 `nbconsultations` bigint( 20  )  NOT  NULL ,
 PRIMARY  KEY (  `idproduit`  ) ,
 KEY  `categorie` (  `categorie`  ) ,
 KEY  `valide` (  `valide`  ) ,
 KEY  `prix` (  `prix`  ) ,
 KEY  `idcitoyen` (  `idcitoyen`  ) ,
 FULLTEXT  KEY  `ft` (  `objet` ,  `description`  )  ) ENGINE  =  MyISAM  DEFAULT CHARSET  = latin1;')){
  echo '<span style="color:red">Erreur à la création de la copie de l\'ancienne table produit</span> :'.$requete.'<br /><br/><a href="?action=backup">Restaurer les tables?</a><br/>';
    echo mysql_error().'<br />';
    die();
}


mysql_query("SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';");

if(!mysql_query('INSERT INTO `produit_bak`
SELECT *
FROM `produit` ;')){
  echo '<span style="color:red">Erreur à la sauvegarde de la table produit</span> :'.$requete.'<br /><br/><a href="?action=backup">Restaurer les tables?</a><br/>';
    echo mysql_error().'<br />';
    die();
}

echo 'Table produit sauvegardée<br /><br />';
echo 'Renommage de l\'ancienne table produit vers annonce<br />';
$nbErreurs = 0;

mysql_query("RENAME TABLE `produit` TO `annonce` ;");

echo '<br />Migration de la table annonce<br />';

$requetes = array(
    'ALTER TABLE annonce
  CHANGE `idproduit` `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT FIRST ',
    'ALTER TABLE annonce
  CHANGE `idcitoyen` `idCitoyen` INT(11) UNSIGNED NOT NULL',
    'ALTER TABLE annonce
  DROP `objet`',
    'ALTER TABLE annonce
  DROP `typeproduit`',
    'ALTER TABLE annonce
  CHANGE `typeannonce` `typeAnnonce` TINYINT(1) UNSIGNED NOT NULL',
    'ALTER TABLE `annonce` 
  ADD `idCategorie` INT(11) UNSIGNED NOT NULL AFTER `description`',
    'ALTER TABLE annonce
  CHANGE `nbex` `quantite` INT(11) UNSIGNED NOT NULL',
    'ALTER TABLE annonce
  MODIFY `valide` TINYINT(1) UNSIGNED NOT NULL',
    'ALTER TABLE annonce
  CHANGE `fdp` `fraisDePort` INT(11) UNSIGNED NOT NULL',
    'ALTER TABLE annonce
  CHANGE `datesaisie` `dateSaisie` DATETIME',
    'ALTER TABLE annonce
  CHANGE `dateexpiration` `dateExpiration` DATE',
    'ALTER TABLE annonce
  MODIFY `etat` TINYINT(1) UNSIGNED NOT NULL',
    'ALTER TABLE annonce
  CHANGE `envoipossible` `envoiPossible` TINYINT(1) UNSIGNED NOT NULL',
    'ALTER TABLE annonce
  CHANGE `mainspropres` `mainsPropres` TINYINT(1) UNSIGNED NOT NULL',
    'ALTER TABLE annonce
  DROP `categorie`'
);
        
foreach($requetes as $requete){
  if( !mysql_query($requete))
  {
    echo '<span style="color:red">Erreur à la création de la requete</span> :'.$requete.'<br /><br/><a href="?action=backup">Restaurer les tables?</a><br/>';
    echo mysql_error().'<br />';
    die();
  }
}
echo '<br />Transformation des données de la table annonce<br />';

/* Conversion des indicateurs
 * 
 * typeannonce :
 *  - offre = 1
 *  - demande = 2
 * 
 * etat :
 *  - Comme neuf = 1
 *  - Bon état = 2
 *  - Fonctionnel = 3
 *  - Hors d'usage = 4
 * 
 * envoipossible :
 *  - Oui = 1
 *  - Non = 2
 * 
 * mainspropres :
 *  - Oui = 1
 *  - Non = 2
 */
if(!mysql_query("UPDATE annonce a
INNER JOIN produit_bak pb ON pb.idproduit = a.id
LEFT JOIN citoyen c ON c.pseudo = pb.idcitoyen
  SET a.idCitoyen =  c.id,
  a.typeAnnonce = IF (pb.typeannonce = 'offre', 1,
    IF (pb.typeannonce = 'demande', 2,0
    )),
    a.etat = IF (pb.etat = 'Comme neuf', 1,
    IF (pb.etat = 'Bon état', 2,
      IF (pb.etat = 'Fonctionnel', 3,
        IF (pb.etat LIKE 'Hors d%', 4,0
    )))),
    a.envoiPossible = IF(pb.envoipossible = 'Oui',1,0),
    a.mainsPropres = IF(pb.mainspropres = 'Oui',1,0)")){
  echo '<span style="color:red">Erreur à la transformation des données annonce</span> :'.$requete.'<br /><br/><a href="?action=backup">Restaurer les tables?</a><br/>';
    echo mysql_error().'<br />';
    die();
}


if(!mysql_query("ALTER TABLE produit_tmp RENAME produit")){
  echo '<span style="color:red">Erreur au renommage de la table produit_tmp</span> :'.$requete.'<br /><br/><a href="?action=backup">Restaurer les tables?</a><br/>';
    echo mysql_error().'<br />';
    die();
}

/* Conversion des indicateurs
 * 
 * typeproduit :
 *  - bien = 1
 *  - service = 2
 * 
 */
if(!mysql_query("INSERT INTO produit 
  SELECT idproduit, objet, description, IF (typeproduit = 'bien', 1, 2), photo, icone, nbex 
  FROM produit_bak
")){
  echo '<span style="color:red">Erreur à la transformation des données produit</span> :'.$requete.'<br /><br/><a href="?action=backup">Restaurer les tables?</a><br/>';
    echo mysql_error().'<br />';
    die();
}

echo '<br />Insertion des données de la table lot<br />';
if(!mysql_query("INSERT INTO lot SELECT NULL, idproduit, idproduit
 FROM produit_bak
")){
  echo '<span style="color:red">Erreur à l\'insertion des données de la table lot</span> :'.$requete.'<br /><br/><a href="?action=backup">Restaurer les tables?</a><br/>';
    echo mysql_error().'<br />';
    die();
}

echo '<br />Insertion des données de la table categorie<br />';
if(!mysql_query("INSERT INTO categorie SELECT NULL, categorie, NULL
 FROM produit_bak GROUP BY categorie
")){
  echo '<span style="color:red">Erreur à l\'insertion des données de la table categorie</span> :'.$requete.'<br /><br/><a href="?action=backup">Restaurer les tables?</a><br/>';
    echo mysql_error().'<br />';
    die();
}
   
echo '<br />Mise à jour des categorie dans la table annonce<br />';
if(!mysql_query("UPDATE annonce a
  INNER JOIN produit_bak pb ON pb.idproduit = a.id
  LEFT JOIN categorie c ON c.nom = pb.categorie
  SET idCategorie = c.id
")){
  echo '<span style="color:red">Erreur à la mise à jour des categorie dans la table annonce</span> :'.$requete.'<br /><br/><a href="?action=backup">Restaurer les tables?</a><br/>';
    echo mysql_error().'<br />';
    die();
}
echo '<br /><br />';

/* Conversion en utf8 */

/* il faut premièrement changer les interclassements

    de la base
        ALTER DATABASE nomBase CHARACTER SET UTF8
    des tables
        ALTER TABLE nomTable CHARACTER SET UTF8
    des colonnes
        ALTER TABLE nomTable CONVERT TO CHARACTER SET UTF8



Puis il faut prévenir MySQL que vos interractions se feront en UTF8 en envoyant à chaque connexion :
SET NAMES UTF8

*/
echo '<br />Conversion de la base en utf8<br />';
if(!mysql_query("
    ALTER DATABASE monnaiem CHARACTER SET UTF8
")){
  echo '<span style="color:red">Erreur à la conversion de la base en utf8</span> :'.$requete.'<br /><br/><a href="?action=backup">Restaurer les tables?</a><br/>';
    echo mysql_error().'<br />';
    die();
}
echo '<br /><br />';

echo '<br />Conversion des tables en utf8<br />';
$requetes = array(
    'ALTER TABLE annonce CHARACTER SET UTF8',
    'ALTER TABLE annonce CONVERT TO CHARACTER SET UTF8',
    'ALTER TABLE categorie CHARACTER SET UTF8',
    'ALTER TABLE categorie CONVERT TO CHARACTER SET UTF8',
    'ALTER TABLE citoyen CHARACTER SET UTF8',
    'ALTER TABLE citoyen CONVERT TO CHARACTER SET UTF8',
    'ALTER TABLE historique CHARACTER SET UTF8',
    'ALTER TABLE historique CONVERT TO CHARACTER SET UTF8',
    'ALTER TABLE lot CHARACTER SET UTF8',
    'ALTER TABLE lot CONVERT TO CHARACTER SET UTF8',
    'ALTER TABLE produit CHARACTER SET UTF8',
    'ALTER TABLE produit CONVERT TO CHARACTER SET UTF8',
    'ALTER TABLE transaction CHARACTER SET UTF8',
    'ALTER TABLE transaction CONVERT TO CHARACTER SET UTF8',
);
foreach($requetes as $requete){
  if( !mysql_query($requete))
  {
    echo '<span style="color:red">Erreur à la conversion des tables en utf8</span> :'.$requete.'<br /><br/><a href="?action=backup">Restaurer les tables?</a><br/>';
    echo mysql_error().'<br />';
    die();
  }
}
echo '<br /><br />';
/* Suppresison des tables temporaires */
echo 'Suppression des tables temporaires<br /><br />';
mysql_query("DROP TABLE transaction_bak");
mysql_query("DROP TABLE produit_bak");

echo 'Suppression des tables backup<br /><br />';
mysql_query("DROP TABLE sauv_transaction");
mysql_query("DROP TABLE sauv_produit");
mysql_query("DROP TABLE sauv_citoyen");
mysql_query("DROP TABLE sauv_historique");

echo '<br/><br/><span style="color:red">Migration des tables effectuée avec succès</span>';

echo '<br /><br />';
echo '<br /><br />';





function backupTables(){
  /***************************/
  /* Restauration des tables */
  /***************************/
  echo '<br/><br/><span style="color:red">Restauration des tables à leur état initial, échec de la migration</span>';
  
  mysql_query('DROP TABLE IF EXISTS `annonce`;');
  mysql_query('DROP TABLE IF EXISTS `categorie`;');
  mysql_query('DROP TABLE IF EXISTS `citoyen`;');
  mysql_query('DROP TABLE IF EXISTS `historique`;');
  mysql_query('DROP TABLE IF EXISTS `produit`;');
  mysql_query('DROP TABLE IF EXISTS `transaction_bak`;');
  mysql_query('DROP TABLE IF EXISTS `produit_bak`;');
  mysql_query('DROP TABLE IF EXISTS `lot`;');
  mysql_query('DROP TABLE IF EXISTS `transaction`;');
  

  mysql_query('CREATE TABLE IF NOT EXISTS `citoyen` (
    `idcitoyen` varchar(30) NOT NULL,
    `mdp` varchar(100) NOT NULL,
    `nom` varchar(30) NOT NULL,
    `prenom` varchar(30) NOT NULL,
    `adresse` varchar(100) NOT NULL,
    `cp` varchar(6) NOT NULL,
    `ville` varchar(100) NOT NULL,
    `tel` varchar(20) NOT NULL,
    `valide` tinyint(4) NOT NULL,
    `notevendeur` float NOT NULL,
    `noteacheteur` float NOT NULL,
    `solde` bigint(20) NOT NULL,
    `nbventes` bigint(20) NOT NULL,
    `dateadhesion` date NOT NULL,
    `mail` varchar(200) NOT NULL,
    `derniereconnexion` datetime NOT NULL,
    `activation` int(11) NOT NULL,
    PRIMARY KEY (`idcitoyen`)
  ) ENGINE=MyISAM DEFAULT CHARSET=latin1;');


  mysql_query('INSERT INTO `citoyen` SELECT * FROM `sauv_citoyen`;');


  mysql_query('CREATE TABLE IF NOT EXISTS `historique` (
    `datemesure` date NOT NULL,
    `nbutilisateurs` int(11) NOT NULL,
    `massetotale` int(11) NOT NULL,
    `massemoyenne` float NOT NULL,
    `rdb` int(11) NOT NULL
  ) ENGINE=MyISAM DEFAULT CHARSET=latin1;');

  mysql_query('INSERT INTO `historique` SELECT * FROM `sauv_historique`;');



  mysql_query('CREATE TABLE IF NOT EXISTS `produit` (
    `idcitoyen` varchar(30) NOT NULL,
    `idproduit` bigint(20) NOT NULL AUTO_INCREMENT,
    `objet` varchar(200) NOT NULL,
    `typeproduit` varchar(15) NOT NULL,
    `typeannonce` varchar(7) NOT NULL,
    `description` varchar(2000) NOT NULL,
    `photo` varchar(500) NOT NULL,
    `icone` varchar(500) NOT NULL,
    `nbex` int(11) NOT NULL,
    `valide` tinyint(4) NOT NULL,
    `prix` int(11) NOT NULL,
    `fdp` int(11) NOT NULL,
    `datesaisie` datetime NOT NULL,
    `dateexpiration` date NOT NULL,
    `etat` varchar(20) NOT NULL,
    `envoipossible` varchar(3) NOT NULL,
    `mainspropres` varchar(3) NOT NULL,
    `categorie` varchar(100) NOT NULL,
    `nbconsultations` bigint(20) NOT NULL,
    PRIMARY KEY (`idproduit`),
    KEY `categorie` (`categorie`),
    KEY `valide` (`valide`),
    KEY `prix` (`prix`),
    KEY `idcitoyen` (`idcitoyen`),
    FULLTEXT KEY `ft` (`objet`,`description`)
  ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;');


  mysql_query('INSERT INTO `produit` SELECT * FROM `sauv_produit`;');


  mysql_query('CREATE TABLE IF NOT EXISTS `transaction` (
    `acheteur` varchar(30) NOT NULL,
    `vendeur` varchar(30) NOT NULL,
    `idproduit` bigint(20) NOT NULL,
    `idtransaction` bigint(20) NOT NULL AUTO_INCREMENT,
    `datevente` datetime NOT NULL,
    `statut` varchar(20) NOT NULL,
    `prix` bigint(20) NOT NULL,
    `port` int(11) NOT NULL,
    `note` tinyint(4) NOT NULL,
    `commentaires` varchar(400) NOT NULL,
    PRIMARY KEY (`idtransaction`),
    KEY `acheteur` (`acheteur`,`vendeur`,`idproduit`)
  ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;');

  mysql_query('INSERT INTO `transaction` SELECT * FROM `sauv_transaction`;');

  mysql_query('DROP TABLE `sauv_citoyen`;');
  mysql_query('DROP TABLE `sauv_historique`;');
  mysql_query('DROP TABLE `sauv_produit`;');
  mysql_query('DROP TABLE `sauv_transaction`;');
}
