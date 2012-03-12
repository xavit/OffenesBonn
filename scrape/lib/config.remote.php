<?php 
/**
 * Konfiguration des Scrapers
 * Hier kšnnen diverse Einstellungen getŠtigt werden
  */
 
//Version
define('VERSION',"0.1.2");

//MemoryLimit hochsetzen, wenn nštig noch hšher
ini_set("memory_limit","128M");

//Execution Time hochsetzen
ini_set("max_execution_time","900");

//DB Verbindungsdaten

define('DB_HOST', "localhost");
define('DB_NAME', "");
define('DB_USER', "");
define('DB_PASSWD', "");
define('DB_PRAEFIX', "openboris_"); 

/**
define('DB_HOST', "localhost");
define('DB_NAME', "openboris");
define('DB_USER', "root");
define('DB_PASSWD', "");
define('DB_PRAEFIX', "openboris_"); 
*/

/**
* PDF Dokuemten mit scrapen true / false == 1/9
*/
define('SCRAPE_PDF', '1');

/**
* Maximale Anzahl der Dokumente per Durchlauf
*/
define('MAX_DOK', '5');



?>