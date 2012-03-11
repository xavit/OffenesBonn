<?php

//Sprachdaten
require_once("./language/lang.de.inc.php");

//DB Klasse
require_once("./classes/class_ez_sql.php");

//DB Querys korrekt auf UTF-8 setzen
//Muss hier passieren da in der divers Klasse auch Aufrufe passieren kšnnen
$db->query("SET NAMES 'utf8'");
	
//Checke Klasse
require_once("./classes/class_checked.php");

//Entwicklung
require_once("./classes/class_rapid_db.php");

//DB Abstraktion
require_once("./classes/class_dbabs.php");

//Diverse Methoden Klasse
require_once("./classes/class_divers.php");

//Debug Klasse
require_once(ABS_PFAD."/classes/class_debug.php");

//Mail Klasse
require_once(ABS_PFAD."/classes/class_mail.php");

//Paginating Klasse
require_once(ABS_PFAD."/classes/class_weiter.php");


?>