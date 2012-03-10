-- phpMyAdmin SQL Dump
-- version 2.11.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 10. März 2012 um 13:43
-- Server Version: 5.0.51
-- PHP-Version: 5.2.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Datenbank: `openboris`
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

