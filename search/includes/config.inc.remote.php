<?php
//Version
define('VERSION',"0.0.1");
//printf ( "<pre>%s</pre>" , print_r ( $_COOKIE , true )); 

ini_set("memory_limit","128M");
#ini_set("error_reporting","2");

//Standardpfad setzen
define('WEB_PFAD', "ob/");
define('ABS_PFAD', $_SERVER['DOCUMENT_ROOT'].WEB_PFAD."/search");
define('ABS_PFAD_ROOT', $_SERVER['DOCUMENT_ROOT'].WEB_PFAD."");

//Url zum Scraper http://www.kobos.de/ob/scrape
define('SCRAPER_URL',"http://www.kobos.de/ob/scrape");


//Seitenname
define('SITE_NAME',"offenesbonn.de");

//Anzahl der EintrŠge pro Seite
define('PAGINATING',"20");

//LIfe oder staging
define('IS_LIFE',"staging");

//DB Daten

define('DB_HOST', "localhost");
define('DB_NAME', "openboris");
define('DB_USER', "root");
define('DB_PASSWD', "");
define('DB_PRAEFIX', "openboris_"); 

 

?>