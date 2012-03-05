-- phpMyAdmin SQL Dump
-- version 3.4.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 28. Feb 2012 um 15:47
-- Server Version: 5.5.16
-- PHP-Version: 5.3.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Datenbank: `openboris`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `openboris_ausschuesse`
--

DROP TABLE IF EXISTS `openboris_ausschuesse`;
CREATE TABLE IF NOT EXISTS `openboris_ausschuesse` (
  `ob_auid` int(11) NOT NULL AUTO_INCREMENT,
  `ob_ausschuss_name` varchar(255) NOT NULL,
  `ob_ausschuss_link` text NOT NULL,
  PRIMARY KEY (`ob_auid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=35 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `openboris_ausschuss_lookup`
--

DROP TABLE IF EXISTS `openboris_ausschuss_lookup`;
CREATE TABLE IF NOT EXISTS `openboris_ausschuss_lookup` (
  `ob_aulid` int(11) NOT NULL,
  `ob_au_basis_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `openboris_basis`
--

DROP TABLE IF EXISTS `openboris_basis`;
CREATE TABLE IF NOT EXISTS `openboris_basis` (
  `ob_id` int(11) NOT NULL AUTO_INCREMENT,
  `ob_boris_id` varchar(255) CHARACTER SET latin1 NOT NULL,
  `ob_boris_id_int` int(11) NOT NULL,
  `ob_id_link` text CHARACTER SET latin1 NOT NULL,
  `ob_meta_link` text CHARACTER SET latin1 NOT NULL,
  `ob_kurz_betreff` text CHARACTER SET latin1 NOT NULL,
  `ob_ausschuss` varchar(255) CHARACTER SET latin1 NOT NULL,
  `ob_ausschuss_link` text CHARACTER SET latin1 NOT NULL,
  `ob_datum` varchar(255) CHARACTER SET latin1 NOT NULL,
  `ob_datum_date` date NOT NULL,
  `ob_timestamp` int(11) NOT NULL,
  `ob_timestamp_erstellung_ob` int(11) NOT NULL,
  `ob_id_data_text` text CHARACTER SET latin1 NOT NULL,
  `ob_meta_daten` text CHARACTER SET latin1 NOT NULL,
  `ob_zugriffsart` varchar(255) CHARACTER SET latin1 NOT NULL,
  `ob_partei` varchar(255) CHARACTER SET latin1 NOT NULL,
  `ob_formular_art` varchar(255) CHARACTER SET latin1 NOT NULL,
  `ob_kosten_liste` text CHARACTER SET latin1 NOT NULL,
  `ob_kosten_gesamt` varchar(255) CHARACTER SET latin1 NOT NULL,
  `ob_ablauf_verwaltung` text CHARACTER SET latin1 NOT NULL,
  `ob_pdf_text` text CHARACTER SET latin1 NOT NULL,
  `ob_geo_strasse` varchar(255) CHARACTER SET latin1 NOT NULL,
  `ob_geo_ortsteil` varchar(255) CHARACTER SET latin1 NOT NULL,
  `ob_osm_raw_data` text CHARACTER SET latin1 NOT NULL,
  `ob_osm_long` varchar(255) CHARACTER SET latin1 NOT NULL,
  `ob_osm_lat` varchar(255) CHARACTER SET latin1 NOT NULL,
  `ob_pdf_file_url` text CHARACTER SET latin1 NOT NULL,
  `ob_thumbnail` text CHARACTER SET latin1 NOT NULL,
  `ob_antragstellering` text CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`ob_id`),
  UNIQUE KEY `ob_boris_id` (`ob_boris_id`),
  KEY `ob_id` (`ob_id`),
  FULLTEXT KEY `ob_kurz_betreff` (`ob_kurz_betreff`),
  FULLTEXT KEY `ob_id_data_text` (`ob_id_data_text`),
  FULLTEXT KEY `ob_pdf_text` (`ob_pdf_text`),
  FULLTEXT KEY `ob_boris_id_2` (`ob_boris_id`),
  FULLTEXT KEY `ob_geo_strasse` (`ob_geo_strasse`),
  FULLTEXT KEY `ob_geo_ortsteil` (`ob_geo_ortsteil`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=136 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `openboris_metadaten_verwaltung`
--

DROP TABLE IF EXISTS `openboris_metadaten_verwaltung`;
CREATE TABLE IF NOT EXISTS `openboris_metadaten_verwaltung` (
  `ob_mid` int(11) NOT NULL AUTO_INCREMENT,
  `ob_meta_amt` varchar(255) NOT NULL,
  `ob_meta_zeit` varchar(255) NOT NULL,
  `ob_meta_datum` varchar(255) NOT NULL,
  `ob_meta_unterschrift` varchar(255) NOT NULL,
  `ob_meta_basis_id` int(11) NOT NULL,
  PRIMARY KEY (`ob_mid`),
  KEY `ob_meta_basis_id` (`ob_meta_basis_id`),
  FULLTEXT KEY `ob_meta_amt` (`ob_meta_amt`),
  FULLTEXT KEY `ob_meta_unterschrift` (`ob_meta_unterschrift`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=217 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `openboris_parteien`
--

DROP TABLE IF EXISTS `openboris_parteien`;
CREATE TABLE IF NOT EXISTS `openboris_parteien` (
  `ob_pid` int(11) NOT NULL AUTO_INCREMENT,
  `ob_parteiname` varchar(255) NOT NULL,
  `ob_partei_link` text NOT NULL,
  `ob_partei_realname` varchar(255) NOT NULL,
  PRIMARY KEY (`ob_pid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `openboris_parteien_lookup`
--

DROP TABLE IF EXISTS `openboris_parteien_lookup`;
CREATE TABLE IF NOT EXISTS `openboris_parteien_lookup` (
  `ob_partei_id` int(11) NOT NULL,
  `ob_basis_pid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `openboris_thumbnails`
--

DROP TABLE IF EXISTS `openboris_thumbnails`;
CREATE TABLE IF NOT EXISTS `openboris_thumbnails` (
  `ob_thid` int(11) NOT NULL AUTO_INCREMENT,
  `ob_thumb_url` text NOT NULL,
  `ob_thumb_basis_id` int(11) NOT NULL,
  PRIMARY KEY (`ob_thid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=15 ;
