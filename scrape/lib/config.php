<?php 
/**
 * Konfiguration des Scrapers
 * Hier k�nnen diverse Einstellungen get�tigt werden
  */
 
//Version
define('VERSION',"0.1.2");

//MemoryLimit hochsetzen, wenn n�tig noch h�her
ini_set("memory_limit","128M");

//Execution Time hochsetzen
ini_set("max_execution_time","900");

//DB Verbindungsdaten

define('DB_HOST', "localhost");
define('DB_NAME', "openboris");
define('DB_USER', "root");
define('DB_PASSWD', "");
define('DB_PRAEFIX', "openboris_"); 


/**
* PDF Dokuemten mit scrapen true / false == 1/9
*/
define('SCRAPE_PDF', '1');

/**
* Maximale Anzahl der Dokumente per Durchlauf
*/
define('MAX_DOK', '10');



?>