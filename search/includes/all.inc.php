<?php

//Config einbinden
require_once("./includes/config.inc.php");

//DB Klasse
require_once("./includes/includes.php");

//Je nach Template Klasse laden, schont die Resourcen!
switch ($template) {
	case "index.html":
		//Startseite umsetzen
		require_once(ABS_PFAD."/classes/class_start.php");
		
	break;
	
	case "api.html":
		//Startseite umsetzen
		require_once(ABS_PFAD."/classes/class_api.php");
		
	break;

	case "index2.html":
		//Meine Daten
		require_once(ABS_PFAD."/classes/class_start2.php");
	break;
	
	
		default:
	die( 'Diese Seite gibt es nicht.' );
		exit();
	break;
}

//Version
$template_dat['version']=VERSION;
#$template_dat['provision']=PROVISION;

$template_dat['time_stamp']=time();

/**
 * Nur wŠhrend Entwicklung aktivieren * 
 */

if (IS_LIFE=="staging")
{
	$rapidform = implode("", file(ABS_PFAD."/templates/rapid_felder.htm"));
}
else
{
	$rapidform = implode("", file(ABS_PFAD."/templates/footer.html"));
}


//Ersetzungen im Template durchfŸhren
require_once("./includes/replace.php");

//Ausgabe
print_r($inhalt);

?>