-- phpMyAdmin SQL Dump
-- version 2.11.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 10. März 2012 um 17:38
-- Server Version: 5.0.51
-- PHP-Version: 5.2.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Datenbank: `openboris`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `openboris_ausschuesse`
--

DROP TABLE IF EXISTS `openboris_ausschuesse`;
CREATE TABLE IF NOT EXISTS `openboris_ausschuesse` (
  `ob_auid` int(11) NOT NULL auto_increment,
  `ob_ausschuss_name` varchar(255) NOT NULL,
  `ob_ausschuss_link` text NOT NULL,
  PRIMARY KEY  (`ob_auid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `openboris_ausschuesse`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `openboris_ausschuss_lookup`
--

DROP TABLE IF EXISTS `openboris_ausschuss_lookup`;
CREATE TABLE IF NOT EXISTS `openboris_ausschuss_lookup` (
  `ob_aulid` int(11) NOT NULL,
  `ob_au_basis_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `openboris_ausschuss_lookup`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `openboris_basis`
--

DROP TABLE IF EXISTS `openboris_basis`;
CREATE TABLE IF NOT EXISTS `openboris_basis` (
  `ob_id` int(11) NOT NULL auto_increment,
  `ob_boris_id` varchar(255) character set latin1 NOT NULL,
  `ob_boris_id_int` int(11) NOT NULL,
  `ob_id_link` text character set latin1 NOT NULL,
  `ob_meta_link` text character set latin1 NOT NULL,
  `ob_kurz_betreff` text character set latin1 NOT NULL,
  `ob_ausschuss` varchar(255) character set latin1 NOT NULL,
  `ob_ausschuss_link` text character set latin1 NOT NULL,
  `ob_datum` varchar(255) character set latin1 NOT NULL,
  `ob_datum_date` date NOT NULL,
  `ob_timestamp` int(11) NOT NULL,
  `ob_timestamp_erstellung_ob` int(11) NOT NULL,
  `ob_id_data_text` text character set latin1 NOT NULL,
  `ob_meta_daten` text character set latin1 NOT NULL,
  `ob_zugriffsart` varchar(255) character set latin1 NOT NULL,
  `ob_partei` varchar(255) character set latin1 NOT NULL,
  `ob_formular_art` varchar(255) character set latin1 NOT NULL,
  `ob_kosten_liste` text character set latin1 NOT NULL,
  `ob_kosten_gesamt` varchar(255) character set latin1 NOT NULL,
  `ob_ablauf_verwaltung` text character set latin1 NOT NULL,
  `ob_pdf_text` text character set latin1 NOT NULL,
  `ob_geo_strasse` varchar(255) character set latin1 NOT NULL,
  `ob_geo_ortsteil` varchar(255) character set latin1 NOT NULL,
  `ob_osm_raw_data` text character set latin1 NOT NULL,
  `ob_osm_long` varchar(255) character set latin1 NOT NULL,
  `ob_osm_lat` varchar(255) character set latin1 NOT NULL,
  `ob_pdf_file_url` text character set latin1 NOT NULL,
  `ob_thumbnail` text character set latin1 NOT NULL,
  `ob_antragstellering` text character set latin1 NOT NULL,
  PRIMARY KEY  (`ob_id`),
  UNIQUE KEY `ob_boris_id` (`ob_boris_id`),
  KEY `ob_id` (`ob_id`),
  FULLTEXT KEY `ob_kurz_betreff` (`ob_kurz_betreff`),
  FULLTEXT KEY `ob_id_data_text` (`ob_id_data_text`),
  FULLTEXT KEY `ob_pdf_text` (`ob_pdf_text`),
  FULLTEXT KEY `ob_boris_id_2` (`ob_boris_id`),
  FULLTEXT KEY `ob_geo_strasse` (`ob_geo_strasse`),
  FULLTEXT KEY `ob_geo_ortsteil` (`ob_geo_ortsteil`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `openboris_basis`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `openboris_metadaten_verwaltung`
--

DROP TABLE IF EXISTS `openboris_metadaten_verwaltung`;
CREATE TABLE IF NOT EXISTS `openboris_metadaten_verwaltung` (
  `ob_mid` int(11) NOT NULL auto_increment,
  `ob_meta_amt` varchar(255) NOT NULL,
  `ob_meta_zeit` varchar(255) NOT NULL,
  `ob_meta_datum` varchar(255) NOT NULL,
  `ob_meta_unterschrift` varchar(255) NOT NULL,
  `ob_meta_basis_id` int(11) NOT NULL,
  PRIMARY KEY  (`ob_mid`),
  KEY `ob_meta_basis_id` (`ob_meta_basis_id`),
  FULLTEXT KEY `ob_meta_amt` (`ob_meta_amt`),
  FULLTEXT KEY `ob_meta_unterschrift` (`ob_meta_unterschrift`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `openboris_metadaten_verwaltung`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `openboris_pagecount`
--

DROP TABLE IF EXISTS `openboris_pagecount`;
CREATE TABLE IF NOT EXISTS `openboris_pagecount` (
  `op_counter_id` varchar(255) NOT NULL,
  `op_counter` varchar(255) NOT NULL,
  `op_counter_datum` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `openboris_pagecount`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `openboris_parteien`
--

DROP TABLE IF EXISTS `openboris_parteien`;
CREATE TABLE IF NOT EXISTS `openboris_parteien` (
  `ob_pid` int(11) NOT NULL auto_increment,
  `ob_parteiname` varchar(255) NOT NULL,
  `ob_partei_link` text NOT NULL,
  `ob_partei_realname` varchar(255) NOT NULL,
  PRIMARY KEY  (`ob_pid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `openboris_parteien`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `openboris_parteien_lookup`
--

DROP TABLE IF EXISTS `openboris_parteien_lookup`;
CREATE TABLE IF NOT EXISTS `openboris_parteien_lookup` (
  `ob_partei_id` int(11) NOT NULL,
  `ob_basis_pid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `openboris_parteien_lookup`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `openboris_thumbnails`
--

DROP TABLE IF EXISTS `openboris_thumbnails`;
CREATE TABLE IF NOT EXISTS `openboris_thumbnails` (
  `ob_thid` int(11) NOT NULL auto_increment,
  `ob_thumb_url` text NOT NULL,
  `ob_thumb_basis_id` int(11) NOT NULL,
  PRIMARY KEY  (`ob_thid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `openboris_thumbnails`
--

