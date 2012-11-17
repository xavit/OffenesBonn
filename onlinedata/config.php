<?php 
/**
 * Konfiguration des Scrapers
 * Hier k�nnen diverse Einstellungen get�tigt werden
  */
 
//Version
define('VERSION',"0.1.3");

//MemoryLimit hochsetzen, wenn n�tig noch h�her
ini_set("memory_limit","128M");

//Execution Time hochsetzen
ini_set("max_execution_time","900");

//DB Verbindungsdaten
define('DB_HOST', "localhost");
define('DB_NAME', "d01599a6");
define('DB_USER', "d01599a6");
define('DB_PASSWD', "8CZa4WtavFbrefsB");
define('DB_PRAEFIX', "openboris_"); 


/**
* PDF Dokuemten mit scrapen true / false == 1/9
*/
define('SCRAPE_PDF', '1');

/**
* Maximale Anzahl der Dokumente per Durchlauf
*/
define('MAX_DOK', '1');



?>