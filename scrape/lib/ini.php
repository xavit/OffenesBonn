<?php 
/**
 * INI
 */
 
//Version
define('VERSION',"1.3.5");
//printf ( "<pre>%s</pre>" , print_r ( $_COOKIE , true )); 
//error reporting auf Produktiv Niveau setzen
//ini_set('error_reporting', 'E_ALL & ~E_DEPRECATED & ~E_NOTICE');

//MemoryLimit hochsetzen
ini_set("memory_limit","128M");

//Execution Time hochsetzen
ini_set("max_execution_time","900");

//DB Daten
define('DB_HOST', "localhost");
define('DB_NAME', "d01378a6");
define('DB_USER', "d01378a6");
define('DB_PASSWD', "2rwGVK83Wt6z6Tnw");
define('DB_PRAEFIX', "sdfppx07_"); 

?>