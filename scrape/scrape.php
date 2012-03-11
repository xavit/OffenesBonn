<?php 
/**
 * Notwendige Klassen einbinden
 * Alle Klassen sind notwendig
 * 
 */
require_once("./lib/class_debug.php");
require_once("./lib/config.php");
require_once("./lib/methods.php");
require_once("./lib/get_rows.php");
require_once("./lib/geo_class.php");
require_once("./lib/class.pdf2txt.inc.php");
require_once("./lib/makefile.php");
require_once("./lib/class_ez_sql.php");
require_once("./lib/save_data.php");

session_start();
/**
 * erstmal die Daten rausholen und komplett indizieren 
 * inkl. aller Metadaten
 * 
 * Alle Dokumente auslesen in einer Schleife
 * 
 */
//if (empty($_SESSION['row_data_complete']))
//{
$rows=new get_rows();
$row_data=$rows->get_rows_now();

/**
 * Dann die Daten geolokalisieren
 * Dazu werden Straßen und Ortsteile ausgelesen
 * und direkt mit Hilfe von OSM lokalisiert
 * 
 * Dann geht die Pin Zuweisung später schneller
 * Eine einfache Umkreissucher geht damit natürlich auch besser
 * 
 */
$geo = new geo_class();
$row_data_geo=$geo->create_lokalisierung($row_data);


/**
* Einmal zwischenspeichern, damit die Rohdaten schon vorhanden sind
*/
$save_data=new class_save_data();
$save_data->save_now($row_data_complete,"noreload");

/**
 * Dann PDF und Images erstellen
 * Dazu werden die Rohdaten genommen und mit Hilfe
 * von mpdf werden PDFs erzeugt und 
 * direkt abgelegt
 * 
 * Von jedem PDF gibt es einen Screenshot als Vorschau
*/


$mkfile=new class_make_file();
$row_data_complete=$mkfile->create_files($row_data_geo);


//$_SESSION['row_data_complete']=$row_data_complete;
//}

/**
 * Dann die fertigen Daten speichern
 * Alle Rohdaten mit Ausnahme 
 * der PDF Dokumente und HTML Inhalte 
 * werden in der Datenbank gespeichert
*/
$save_data->save_now($row_data_complete);


#print_r($row_data_geo);



?>