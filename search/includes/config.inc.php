<?php
//Version
define('VERSION',"0.1.2");
//printf ( "<pre>%s</pre>" , print_r ( $_COOKIE , true )); 

ini_set("memory_limit","128M");
#ini_set("error_reporting","2");

//Standardpfad setzen
define('WEB_PFAD', "/OffenesBonn");
define('ABS_PFAD', $_SERVER['DOCUMENT_ROOT'].WEB_PFAD."/search");
define('ABS_PFAD_ROOT', $_SERVER['DOCUMENT_ROOT'].WEB_PFAD."");

//Url zum Scraper http://www.kobos.de/ob/scrape
define('SCRAPER_URL',"http://localhost/OffenesBonn/scrape");
define('API_URL',"http://localhost/OffenesBonn/search");


//Seitenname
define('SITE_NAME',"offenesbonn.de");

//Anzahl der Eintr�ge pro Seite
define('PAGINATING',"20");

//LIfe oder staging
define('IS_LIFE',"life");

//DB Daten

define('DB_HOST', "localhost");
define('DB_NAME', "openboris");
define('DB_USER', "root");
define('DB_PASSWD', "");
define('DB_PRAEFIX', "openboris_"); 

/**
define('DB_HOST', "localhost");
define('DB_NAME', "xd01378a6");
define('DB_USER', "xd01378a6");
define('DB_PASSWD', "2rwGxVK83Wt6z6Txnw");
define('DB_PRAEFIX', "openboris_"); 
*/
?>