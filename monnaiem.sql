-- phpMyAdmin SQL Dump
-- version 3.3.8
-- http://www.phpmyadmin.net
--
-- Serveur: localhost
-- Généré le : Sam 14 Décembre 2013 à 12:07
-- Version du serveur: 5.0.51
-- Version de PHP: 5.2.6-1+lenny3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `monnaiem`
--

-- --------------------------------------------------------

--
-- Structure de la table `citoyen`
--

CREATE TABLE IF NOT EXISTS `citoyen` (
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
  PRIMARY KEY  (`idcitoyen`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `historique`
--

CREATE TABLE IF NOT EXISTS `historique` (
  `datemesure` date NOT NULL,
  `nbutilisateurs` int(11) NOT NULL,
  `massetotale` int(11) NOT NULL,
  `massemoyenne` float NOT NULL,
  `rdb` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `produit`
--

CREATE TABLE IF NOT EXISTS `produit` (
  `idcitoyen` varchar(30) NOT NULL,
  `idproduit` bigint(20) NOT NULL auto_increment,
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
  PRIMARY KEY  (`idproduit`),
  KEY `categorie` (`categorie`),
  KEY `valide` (`valide`),
  KEY `prix` (`prix`),
  KEY `idcitoyen` (`idcitoyen`),
  FULLTEXT KEY `ft` (`objet`,`description`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=298 ;

-- --------------------------------------------------------

--
-- Structure de la table `transaction`
--

CREATE TABLE IF NOT EXISTS `transaction` (
  `acheteur` varchar(30) NOT NULL,
  `vendeur` varchar(30) NOT NULL,
  `idproduit` bigint(20) NOT NULL,
  `idtransaction` bigint(20) NOT NULL auto_increment,
  `datevente` datetime NOT NULL,
  `statut` varchar(20) NOT NULL,
  `prix` bigint(20) NOT NULL,
  `port` int(11) NOT NULL,
  `note` tinyint(4) NOT NULL,
  `commentaires` varchar(400) NOT NULL,
  PRIMARY KEY  (`idtransaction`),
  KEY `acheteur` (`acheteur`,`vendeur`,`idproduit`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=195 ;
