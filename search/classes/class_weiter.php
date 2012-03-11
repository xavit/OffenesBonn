<?php 
/**
#####################################
# PAPOO CMS 					    #
# (c) Dr. Carsten Euwens 2008       #
# Authors: Carsten Euwens           #
# http://www.papoo.de               #
# Internet                          #
#####################################
# PHP Version >4.3                  #
#####################################

*/

/* Weitere Seiten einbinden. */


/*require_once "./artikel_class.php";
require_once "./cms_class.php";*/

class  weiter  {

	// der weiter Link
	var $weiter_link;
	// weitere Seiten existieren
	var $weitere_seiten;
	// Link zurück
	var $hrefruck;
	// Array mit allen weiteren Seiten
	var $weiter_array;
	// Link weiter
	var $hrefweiter;
	// Anzahl
	var $result_anzahl="";

	// Konstruktor
	function weiter() {
		/**
		Klassen und Variablen globalisieren
		*/

		// checkedblen Klasse einbinden
		global $checked;
		//content Klasse
		

		/**
		und einbinden in die Klasse
		*/

		// Hier die Klassen als Referenzen

		$this->checked	= & $checked;
	}


	function do_weiter($what){
		global $template_dat;
		
		if (empty($this->checked->search)){
			$this->checked->search="";
		}
		// Seiten Nr. einbinden kommt als $_GET aus der url oder ist leer
		if (!empty($this->checked->page))
		{
			$page = $this->checked->page;
		}
		else
		{
			$page=0;
		}
		if (is_numeric($page))
		{
			$template_dat['page']=$page;
		}
		// $search einbinden
		$search = $this->checked->search;
		// cms Klasse einbinden

		// maximale Anzahl der Ergebnisse
		$pagesize = PAGINATING;
		#echo $pagesize = 1;
		
		//echo $this->surl_dat;
		// tatsächliche Anzahl der Ergebnisse
		$result_anzahl = $this->result_anzahl;
		//Mod by khmweb
		//dient zur Anzeige der Gesamtanzahl der Ergebnisseiten
		$page_result = 1;
		
		// wenn $page belegt ist oder die Anzahl der Suchergebnisse größer ist als die maximale Ergebnis Anzahl
		if ((isset($page) and $page > 1) or $result_anzahl > $pagesize) {
			// für Template  Eigenschaften zuweisen
			//$template_dat['weitere_seiten'] = "1";
			$template_dat['weiter'] = "1";
			$weiter_data = array();

			// Wenn es weitere Seiten gibt und >1 ist
			if (isset($page) and $page > 1) {
				// Link zur vorherigen Seite erzeugen
				switch ($what) {
					// Die Suche läuft
					case "search":
					$lt_text_links = "" . $this->weiter_link . "&amp;page=" . ($page-1) . "&amp;search=" . $search;
					break;
					// Startseite
					case "teaser":
					#echo $this->weiter_link;
					$lt_text_links = "" . $this->weiter_link . "&amp;page=" . ($page-1);

					break;
				}
				// Link dem Template zuweisen
			$template_dat['hrefruck']= $lt_text_links;
			#echo "<br />";
			}

			// Anzahl der möglichen Seiten ermitteln
			//Mod by khmweb
			//$page_result = round($result_anzahl / $pagesize, 0) + 1;
			$page_result = ceil($result_anzahl / $pagesize);
			$template_dat['weiter_anzahl_pages']=$page_result;
			$this->weiter_data=array();
			if (empty($page)) {
				$page=1;
			}
			// Zahl der zur Verfügung stehenden Seiten
			//Mod by khmweb
			//Vergleich auf nur kleiner ist inkorrekt. Hierdurch zeigt das paginating eine Seite zu wenig an.
			//for($i = 1; $i < $page_result; $i++) {
			$page2=0;
			if ($page<=6)
			{
				$page3=10;
			}
			else
			{
				#echo $page_result;
				#echo $page;
				if ($page<($page_result-4))
				{
					$page3=$page+4;
				}
				else 
				{
					$page3=$page+($page_result-$page);
				}
				$page2=$page-4;
				$this->make_one($what);
			}
			if($page2<1)$page2=1;
			#echo $page2;
			//WEnn $page_result>10 dann nur 1 ... 234356 ... letzter anzeigen sonst wird das zuviel
			if ($page_result>10)
			{

				//wenn page kleiner 6 dann normal ab 1 und nur letzter
				if ($page<$page3)
				{
					$maxzahl=$page3;
				}
				else
				{
					$maxzahl=$page3;
				}
				
				//ansonsten abzählen
				for($i = $page2; $i <= $maxzahl; $i++) {
					// Wenn es weitere mögliche Seiten gibt
					if ($i > 0) {
						// Seite aktiv, dann kein Link, nur anzeigen
						if ($page == $i) {
							$aktivhref = 1;
						}
						// Seite nicht aktiv, dann Link anzeigen
						else {
							$aktivhref = "";
						}
						// Link erstellen
						switch ($what){
							// Suche aktiv
							case "search":
							$href_zahl = "" . $this->weiter_link . "&amp;page=" . $i . "&amp;search=" . $search;
							break;
							// Startseite
							case "teaser":
							$href_zahl = "" . $this->weiter_link . "&amp;page=" . $i;
						
							break;
						}
						//added by khmweb
						//dient zur Anzeige der Suchresultate "von/bis"
						if ($aktivhref)
						{
							if ($i != 1) $olstart = $i*$pagesize-($pagesize-1);
							else $olstart = 1;
						}
						if ($i*$pagesize < $result_anzahl) $olend = $i*$pagesize;
						else $olend = $result_anzahl;
						// array füllen für template
						array_push($this->weiter_data, array(
						'aktivhref' => $aktivhref,
						'linkname' => $href_zahl,
						'linknummer' => $i,
						//Mod by khmweb
						//dient zur Anzeige der Gesamtanzahl der Ergebnisseiten in _module_intern/mod_weiter.html und
						//der Anzeige v. "Resultate von bis"
						'page_result' => $page_result,
						'olstart' => $olstart,
						'olend' => $olend,
						));
						$template_dat['weiter_array']=$this->weiter_data;
					}
				}
				if ($page<$page_result-4)
				{
					$this->make_max($what);
					$template_dat['weiter_array']=$this->weiter_data;
				}

			}

			//pageresult < 6
			else {
				#echo $page_result;
				for($i = 1; $i <= $page_result; $i++) {
					// Wenn es weitere mögliche Seiten gibt
					if ($i > 0) {
						// Seite aktiv, dann kein Link, nur anzeigen
						if ($page == $i) {
							$aktivhref = 1;
						}
						// Seite nicht aktiv, dann Link anzeigen
						else {
							$aktivhref = "";
						}
						#echo $i;
						// Link erstellen
						switch ($what){
							// Suche aktiv
							case "search":
							$href_zahl = "" . $this->weiter_link . "&amp;page=" . $i . "&amp;search=" . $search;
							break;
							// Startseite
							case "teaser":
							$href_zahl = "" . $this->weiter_link . "&amp;page=" . $i;
	
							break;
						}
						//added by khmweb
						//dient zur Anzeige der Suchresultate "von/bis"
						if ($aktivhref)
						{
							if ($i != 1) $olstart = $i*$pagesize-($pagesize-1);
							else $olstart = 1;
						}
						if ($i*$pagesize < $result_anzahl) $olend = $i*$pagesize;
						else $olend = $result_anzahl;
						// array füllen für template
						array_push($weiter_data, array(
						'aktivhref' => $aktivhref,
						'linkname' => $href_zahl,
						'linknummer' => $i,
						//Mod by khmweb
						//dient zur Anzeige der Gesamtanzahl der Ergebnisseiten in _module_intern/mod_weiter.html und
						//der Anzeige v. "Resultate von bis"
						'page_result' => $page_result,
						'olstart' => $olstart,
						'olend' => $olend,
						));
						$template_dat['weiter_array']=$this->weiter_data=$weiter_data;
					}
				}
			}
		#	print_r($template_dat['weiter_array']);
			// Wenn es noch eine weitere Seite als die aktuelle gibt
			if ($result_anzahl > $pagesize * $page) {
				
				// Wenn Seite nicht gesetzt ist
				if (!isset($page)){
					$page = 1;
				}
				// Link zur nächsten Seite erzeugen
				switch ($what){
					// Suche aktiv
					case "search":
					$href = "" . $this->weiter_link . "&amp;page=" . ($page + 1) . "&amp;search=" . $search;
					break;
					// Startseite
					case "teaser":
					$href = "" . $this->weiter_link . "&amp;page=" . ($page + 1);
					

					break;
				}
				// Link dem Template zuweisen
				$template_dat['hrefweiter']= $href;
			}

		}
		// es exitieren keine weiteren Seiten
		else {
			$template_dat['weitere_seiten']="";
		}
	}
	
	
	/**
	 * den ersten Eintrag erstellen
	 *
	 * @param unknown_type $limit
	 */
	function make_one($what)
	{
		global $template_dat;
		if (empty($this->checked->search)){
			$this->checked->search="";
		}
		// Seiten Nr. einbinden kommt als $_GET aus der url oder ist leer
		$page = $this->checked->page;
		// $search einbinden
		$search = $this->checked->search;
		// cms Klasse einbinden

		// maximale Anzahl der Ergebnisse
		$pagesize = PAGINATING;
		// tatsächliche Anzahl der Ergebnisse
		$result_anzahl = $this->result_anzahl;
		

		//Mod by khmweb
		//dient zur Anzeige der Gesamtanzahl der Ergebnisseiten
		$page_result = 1;
		$i=1;
		// Link erstellen
		switch ($what){
			// Suche aktiv
			case "search":
			$href_zahl = "" . $this->weiter_link . "&amp;page=" . $i . "&amp;search=" . $search;
			break;
			// Startseite
			case "teaser":
			$href_zahl = "" . $this->weiter_link . "&amp;page=" . $i;
			
			break;
		}
		//added by khmweb
		//dient zur Anzeige der Suchresultate "von/bis"

		#echo $href_zahl ;
		// array füllen für template
		array_push($this->weiter_data, array(
		'aktivhref' => $aktivhref,
		'linkname' => $href_zahl,
		'linknummer' => $i,
		'first' => "ok",
		//Mod by khmweb
		//dient zur Anzeige der Gesamtanzahl der Ergebnisseiten in _module_intern/mod_weiter.html und
		//der Anzeige v. "Resultate von bis"
		'page_result' => $page_result,
		'olstart' => $olstart,
		'olend' => $olend,
		));
	}
	/**
	 * den ersten Eintrag erstellen
	 *
	 * @param unknown_type $limit
	 */
	function make_max($what)
	{
		global $template_dat;
		if (empty($this->checked->search)){
			$this->checked->search="";
		}
		// Seiten Nr. einbinden kommt als $_GET aus der url oder ist leer
		$page = $this->checked->page;
		// $search einbinden
		$search = $this->checked->search;
		// cms Klasse einbinden

		// maximale Anzahl der Ergebnisse
		$pagesize = PAGINATING;
		// tatsächliche Anzahl der Ergebnisse
		$result_anzahl = $this->result_anzahl;
		//Mod by khmweb
		//dient zur Anzeige der Gesamtanzahl der Ergebnisseiten

		$i=$page_result = ceil($result_anzahl / $pagesize);
		// Link erstellen
		switch ($what){
			// Suche aktiv
			case "search":
			$href_zahl = "" . $this->weiter_link . "&amp;page=" . $i . "&amp;search=" . $search;
			break;
			// Startseite
			case "teaser":
			$href_zahl = "" . $this->weiter_link . "&amp;page=" . $i;
			
			break;
		}
		//added by khmweb
		//dient zur Anzeige der Suchresultate "von/bis"

		#echo $href_zahl ;
		// array füllen für template
		array_push($this->weiter_data, array(
		'aktivhref' => $aktivhref,
		'linkname' => $href_zahl,
		'linknummer' => $i,
		'last' => "ok",
		//Mod by khmweb
		//dient zur Anzeige der Gesamtanzahl der Ergebnisseiten in _module_intern/mod_weiter.html und
		//der Anzeige v. "Resultate von bis"
		'page_result' => $page_result,
		'olstart' => $olstart,
		'olend' => $olend,
		));
	}


	function make_limit($limit=PAGINATING){
		global $template_dat;

		$this->pagesize=$limit;
		$plus = 0;
		if (!empty($this->checked->page))
		{
			$page = $this->checked->page;
		}
		else
		{
			$page=0;
		}
		if ($page > 100000){
			$page = 100000;
		}
		elseif ($page < 1)
		$page = 1;
		elseif (!is_numeric($page))
		unset($page);
		// Limit für die Datenbank abfragen
		if (isset($page)){
			$this->sqllimit = "LIMIT " . (($page-1) * $this->pagesize) . "," . ($this->pagesize);
		}
		else {
			$this->sqllimit = "LIMIT " . ($this->pagesize);
		}
		#echo $this->sqllimit;
	}
	
	function make_html_links()
	{
		global $template_dat;
		global $lang_dat;
		//Die weiteren Seiten im Array
		#print_r($this->weiter_data);
		$whtml='<div id="weiter" class="weiter"><ul class="weiterul">';
		
		//Zurück Link //plugins_seite_zurueck
		if (!empty($template_dat['hrefruck']))
		{
			$whtml.='<li class="weiterli"><a href="'.$template_dat['hrefruck'].'" title="'.$lang_dat['plugins_seite_zurueck_title'].'">'.$lang_dat['plugins_seite_zurueck'].'</a>
				</li>';
		}
		
		
		if (!empty($this->weiter_data))
		{
			if(is_array($this->weiter_data))
			{
				foreach ($this->weiter_data as $key=>$value)
				{
					if ($value['aktivhref']==1)
					{
						$whtml.='<li class="weiterli"><strong title="'.$lang_dat['plugins_seite_weiter_aktuell'].'">'.$value['linknummer'].'</strong> 
					</li>';
					}
					else
					{
						$whtml.='<li class="weiterli">';
						if ($value['last']=="ok")
						{
							$whtml.='...';
						}
						
						
						$whtml.='<a href="'.$value['linkname'].'" title="'.$lang_dat['plugins_seite_weiter'].'  '.$value['linkname'].' ">'.$value['linknummer'].'</a>';
						if ($value['first']=="ok")
						{
							$whtml.='...';
						}
						
						$whtml.='	</li>';
					}
				}
			}
		}
		//Max. Egebnisse
		#echo $template_dat['weiter_anzahl_pages'];
		
		//Eine Seite weiter
		#echo $template_dat['hrefweiter'];
		if (!empty($template_dat['hrefweiter']))
		{
			$whtml.='<li class="weiterli"><a href="'.$template_dat['hrefweiter'].'" title="'.$lang_dat['plugins_seite_weiter_title'].'">'.$lang_dat['plugins_seite_weiter'].'</a>
				</li>';
		}		

		$whtml.='</ul></div>';
		$template_dat['paginating']=$whtml;
	}

}
// Hiermit wird die Klasse initialisiert und kann damit sofort überall benutzt werden.
$weiter= new weiter();
?>