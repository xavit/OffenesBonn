<?php
//Version
define('VERSION',"0.0.1");
//printf ( "<pre>%s</pre>" , print_r ( $_COOKIE , true )); 

ini_set("memory_limit","128M");
#ini_set("error_reporting","2");

//Standardpfad setzen
define('WEB_PFAD', "");
define('ABS_PFAD', $_SERVER['DOCUMENT_ROOT'].WEB_PFAD."");
define('ABS_PFAD_ROOT', $_SERVER['DOCUMENT_ROOT'].WEB_PFAD."");

//Url zum Scraper http://www.kobos.de/ob/scrape
define('SCRAPER_URL',"http://myopenbonn.de/scrape");
define('API_URL',"http://myopenbonn.de/");


//Seitenname
define('SITE_NAME',"myopenbonn.de");

//Anzahl der Eintr�ge pro Seite
define('PAGINATING',"20");

//LIfe oder staging
define('IS_LIFE',"life");

//DB Daten

define('DB_HOST', "localhost");
define('DB_NAME', "d01599a6");
define('DB_USER', "d01599a6");
define('DB_PASSWD', "8CZa4WtavFbrefsB");
define('DB_PRAEFIX', "openboris_"); 


?>