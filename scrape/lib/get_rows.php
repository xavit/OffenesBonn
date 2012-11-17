<?php 
/**
Curl Klasse - für den Aufruf

*/
class get_rows
{

	/**
	Class INI
	 */
	function get_rows()
	{}
	
	public function get_rows_now()
	{
		/**
		
		Aufbau der Url
		
		Basis URL
		http://www2.bonn.de/bo_ris/ris_sql/sum_profi_result.asp?
		
		Parameter:
		e_search_1= //Suchbegriff
		&e_und_oder=and
		&e_search_2=
		&e_formulartyp=00
		&e_adl=* 
		&e_gre_id=0 //ID Gremium
		&e_operator=gre_dat_termin+%3E%3D
		&e_search_tt=01 //Tag
		&e_search_mm=01 // Monat
		&e_search_jjjj=2012 // Jahr
		*/
		#echo 'Nächste Seite in 20 Sekunden... (get_rows) '.$url.'<meta http-equiv="refresh" content="20; URL='.$url.' ">';
		
		$method=new class_methods();
		$this->count=0;
		
		//DB INI
		$db = new db_class(EZSQL_DB_USER, EZSQL_DB_PASSWORD, EZSQL_DB_NAME, EZSQL_DB_HOST);
		
		//Seitenzahl rausholen und setzen
		if (is_numeric($_GET['page_count']))
		{
			$page_aktu['0']['op_counter']=$_GET['page_count'];
		}
		else
		{
			$page_aktu=$this->get_aktu_page($db);
		}
		
		//TEst Modus
		if ($_GET['page_count'] > 20)
		{
			exit();
		}
		
		$dbpage="DBPAGE=".$page_aktu['0']['op_counter'];
		$this->page_count=$page_aktu['0']['op_counter'];
		
		//Datum für den Link setzen
		$datum_array=explode(".",$page_aktu['0']['op_counter_datum']);
		//debug::print_d($datum_array);
		
		$tag=$datum_array['0'];
		$monat=$datum_array['1'];
		$jahr=$datum_array['2'];
		
		//Fallback mit dem heutigen Datum starten
		if (empty($tag))
		{
			$tag=date("d",time());
			$monat=date("m",time());
			$jahr=date("Y",time());
		}
		
		//Url
		$url='http://www2.bonn.de/bo_ris/ris_sql/sum_profi_result.asp?e_search_1=&e_und_oder=and&e_search_2=&e_formulartyp=00&e_adl=*&e_gre_id=0&e_operator=gre_dat_termin+%3E%3D&e_search_tt='.$tag.'&e_search_mm='.$monat.'&e_search_jjjj='.$jahr.'&'.$dbpage;
		#echo $this->url=$url;
		#exit();
		//http://www2.bonn.de/bo_ris/ris_sql/sum_profi_result.asp?e_search_1=&e_und_oder=and&e_search_2=&e_formulartyp=00&e_adl=*&e_gre_id=0&e_operator=gre_dat_termin+%3E%3D&e_search_tt=01&e_search_mm=01&e_search_jjjj=1997
		
		//$url='http://www2.bonn.de/bo_ris/ris_sql/sum_profi_result.asp?e_search_1=&e_und_oder=and&e_search_2=&e_formulartyp=00&e_adl=SPD%27+or+adl_kuerzel+%3D+%27CSG%27+or+adl_kuerzel+%3D+%27S%2BG%27+or+adl_kuerzel+%3D+%27C%2BS%27+or+adl_kuerzel+%3D+%27AMPEL&e_gre_id=0&e_operator=gre_dat_termin+%3E%3D&e_search_tt=01&e_search_mm=01&e_search_jjjj=2012';
		
		//Seite auslesen
		$site=$method->get_site($url);
		
		//anzahl der Seiten rausholen
		$pages=$this->get_pages($site);
		
		if ($page_aktu<$pages)
		{
			$this->reset_counter($db );
		}

		//HTML Rows auslesen
		$html_rows=$this->get_html_table_rows($site);
		
		//Inhalte aus Rows in Array einlesen
		$array_rows=$this->get_array_rows($html_rows);
		#debug::print_d($array_rows);
		#exit();
		
		//Dann die Inhalte der Links und Dokumente noch einlesen
		$complete1_array=$this->get_complete_data1($array_rows,$db);
		#debug::print_d($complete1_array);
		#exit();
		
		//Kein Dokument mehr gefunden das nicht in der DB ist
		if (empty($complete1_array))
		{
			debug::print_d("Kein DOk mehr?");
			#exit();
			$this->reload_new_page($page_aktu,$db);
		}
		#debug::print_d($complete1_array);
		#exit();
		return $complete1_array;
	}
	
	/**
	 * reload_new_page function.
	 * 
	 * An dieser Stelle wird dann eine Seite weiter gegangen
	 * Weiter durchlaufen brauchen wir hier nicht.
	 *
	 * @access private
	 * @return void
	 */
	private function reload_new_page($page_aktu,$db)
	{
		//Counter eins hochsetzen
		$sql=sprintf("UPDATE %s SET op_counter = op_counter + 1",
									'openboris_pagecount'
		);
		$result=$db->query($sql);
		
		$page_aktu['0']['op_counter']=$page_aktu['0']['op_counter']+1;
		
		//Neu laden
		$url='./scrape.php?page_count='.$page_aktu['0']['op_counter'];
		//header("Location: $url");
		
		echo 'Nächste Seite in 2 Sekunden... (get_rows) '.$url.'<meta http-equiv="refresh" content="2; URL='.$url.' ">';
		exit();
	}
	
	/**
	 * get_rows::reset_counter()
	 * 
	 * @return void
	 */
	private function reset_counter($db )
	{
		$sql=sprintf("UPDATE %s SET op_counter ='1',
									op_counter_datum='%s'",
									'openboris_pagecount',
									date("d.m.Y")
		);
		$result=$db->query($sql);
	}
	
	/**
	 * get_rows::get_aktu_page()
	 * 
	 * @param mixed $db
	 * @return
	 */
	private function get_aktu_page($db)
	{
		$sql=sprintf("SELECT *  FROM %s",
									'openboris_pagecount'
		);
		$result=$db->get_results($sql,ARRAY_A);
		//exit();
		
		return $result;
	}
	
	/**
	 * get_rows::get_pages()
	 * 
	 * @param mixed $site
	 * @return void
	 */
	private function get_pages($site)
	{
		$s1=explode("Seite",$site);
		$s2=explode("von",$s1['1']);
		$aktuelle_seite=(int)$s2['0'];
		$s3=(int) substr($s2['1'],0,5);
		$gesamt= $s3;
		//exit();
		return $gesamt;
	}
	
	/**
	 * check_ob_dokument_vorhanden function.
	 * 
	 * @access private
	 * @param mixed $id
	 * @return void
	 */
	private function check_ob_dokument_vorhanden($id,$db)
	{
		//Wenn leer dann so tun als ob vorhanden
		if (empty($id))
		{
			return true;
		}
		$sql=sprintf("SELECT ob_boris_id  FROM %s
								WHERE ob_boris_id='%s'
								",
									'openboris_basis',
									$db->escape($id)
		);
		$result=$db->get_results($sql,ARRAY_A);
		//exit();
		if (!empty($result))
		{
			//Schon vorhanden
			return true;
		}
		//NIcht vorhanden
		return false;
	}
	
	/**
	 * get_complete_data1 function.
	 * Jetzt noch die verlinkten Dokumente auslesen
	 * @access private
	 * @return void
	 */
	private function get_complete_data1($data_org=array(),$db)
	{
		foreach ($data_org as $key=>$value)
		{
			if ($this->count >= MAX_DOK)
			{
				continue;
			}
			
			//Checken ob Dokument schon in DB, wenn ja weiter, wenn nein scannen und neu laden
			if ($this->check_ob_dokument_vorhanden($value['id'],$db)==true or $this->noch_nicht_vorhanden==true)
			{
				continue;
			}
			else
			{
				//Wenn x Dokumente erreicht sind
				if ($this->count > MAX_DOK)
				{
					$this->noch_nicht_vorhanden=true;
				}
				$this->count=$this->count+1;
			}
			
			$data[$key]=$value;
			//debug::print_d($value);
			
			//Zuerst das Dokument selber aber mit Fallunterscheidung
			if (!empty($value['id_link_html']))
			{
				//Fall 1 . htm
				$dok=class_methods::get_site('http://www2.bonn.de/bo_ris/'.$value['id_link_html']);
				$row['html']=$dok;
				$row['html_text']=class_methods::get_clean_text($dok);
				$row['html_meta'] = $this->get_extra_infos_dokument($dok);
			}
				
			if (!empty($value['id_link_pdf']))
			{
					$dok=class_methods::get_site('http://www2.bonn.de/bo_ris/'.$value['id_link_pdf']);
					$row['pdf']=$dok;
					//PDF Daten auslesen wenn möglich / geht natürlich nicht bei Bildern usw.
					$convert=new pdf2txt();
					$result=$convert->convert($dok);
					$row['pfd_text']=class_methods::get_clean_text($result);
			}
				
			if (!empty($value['id_link_rtf']))
			{
					$dok=class_methods::get_site('http://www2.bonn.de/bo_ris/'.$value['id_link_rtf']);
					$row['sonstiges_dokument']=$dok;
					$row['sonstiges_text']=class_methods::get_clean_text($dok);
			}
				
			//Daten in Hauptarray übergeben
			$data[$key]['id_data']=$row;
				
			
			
			//Dann die Metainformationen zum Dokument
			if (!empty($value['meta_link']))
			{
			    //echo 'http://www2.bonn.de/bo_ris/ris_sql/'.$value['meta_link'];
				$dok=class_methods::get_site('http://www2.bonn.de/bo_ris/ris_sql/'.$value['meta_link']);
				$row['meta_data']=$dok;
				$row['meta_data_text']=class_methods::get_clean_text($dok);
				$row['meta_data_extra']=$this->get_extra_infos($dok);
				
				//Daten in Hauptarray übergeben
				$data[$key]['id_data']=$row;
			}
			//$data[]=$value;
		}
		return $data;
		//print_r($data);
	}
	
	/**
	 * get_extra_infos_dokument function.
	 * 
	 * @access private
	 * @param mixed $html
	 * @return void
	 */
	private function get_extra_infos_dokument($html)
	{
		//INI
		$ende=false;
		$extra=array();
		
		//Verwaltungsinterne Abstimmung
		$d1=explode("TM_Abstimmung",$html);
		
		if (!empty($d1['1']))
		{
			$d2=explode("<tr",$d1['1']);
			//print_r($d2);
			
			if (is_array($d2))
			{
				foreach ($d2 as $key=>$zeile)
				{
					#print_r($zeile);
					if ($ende) continue;
					//Keine tabelle, dann ist es ein sinnvoller Eintrag
					if ($key>0)
					{
						//Einzelne Rows rausholen
						$d3=explode("<td",$zeile);
	
						//Das ist in der letzten Zeile, danach fischen
						if(stristr($zeile,"</table>"))
						{
							$ende=true;
						}
						
						//Amt
						$row2['amt']=class_methods::get_clean_text("<td".$d3['1']);
						
						//Uhrzeit
						$row2['zeit']=class_methods::get_clean_text("<td".$d3['2']);
						
						//Datum
						$row2['datum']=class_methods::get_clean_text("<td".$d3['3']);
						
						//Unterschrift
						$row2['unterschrift']=class_methods::get_clean_text("<td".$d3['4']);
					}
					
					//Daten in oberes Array zuweisen, aber nur wenn vorhanden
					if (!empty($row2['amt']))
					{
						$extra['dokument_meta_verwaltung'][]=$row2;
					}
					
				}
			}	
		}
			

		return $extra;
	}
	
	/**
	 * get_extra_infos function.
	 * 
	 * @access private
	 * @param string $html. (default: "")
	 * @return void
	 */
	private function get_extra_infos($html="")
	{
		//INI
		$kosten_auflistung="";
		$ende="";
		#echo $html;
		//Zuerst mal Zugriffsart
		$d1=explode("Zugriff",$html);
		$d2=explode("</tr>",$d1['1']);
		$extra['zugriff']=class_methods::get_clean_text($d2['0']);
		
		//Dann Formularart
		$d1=explode("Formularart",$html);
		$d2=explode("</tr>",$d1['1']);
		$extra['formular_art']=class_methods::get_clean_text($d2['0']);
		
		//Dann Kosten Auflistung
		$d1=explode("<!--",$d2['2']);
		$d2=explode("-->",$d1['1']);
		$d3=trim(strip_tags($d2['0']));
		$d4=explode("\n",$d3);
		
        //Dann Geo Daten rausholen
        $geo=explode("&mlon=",$html);
        $geo_2=explode("&mlat=",$geo['1']);
        $geo_3=explode("&icon=",$geo_2['1']);
        #debug::print_d($geo_3['0']);
        $extra['geo_referenz']['lon']=$geo_2['0'];
        $extra['geo_referenz']['lat']=$geo_3['0'];
		//Leer Zeilen entfernen
		if (is_array($d4))
		{
			foreach ($d4 as $zeile)
			{
				$zeile=html_entity_decode(trim($zeile));
				if (!empty($zeile))
				{
					$kosten_auflistung.=$zeile."\n";
				}
			}
		}
		$extra['kosten_auflistung']=$kosten_auflistung;
		
		//Dann Kosten Einzeln ----------
		$d1=explode("----------",$kosten_auflistung);
		$d2=explode("Summe",$d1['1']);
		$extra['kosten_einzeln']=(int) class_methods::get_clean_text($d2['0']);
		
		//Dann Sitzungen wann das Dokument besprochen wurde
		//Trenner: <!-- ***** Tabellenfuss -->
		$d1=explode("<!-- ***** Tabellenfuss -->",$html);
		//Dann die Zeilen durchlaufen
		if (is_array($d1))
		{
			foreach ($d1 as $key=>$zeile)
			{
				#print_r($zeile);
				if ($ende) continue;
				//Keine tabelle, dann ist es ein sinnvoller Eintrag
				if ($key>0)
				{
					//Einzelne Rows rausholen
					$d2=explode("<td>",$zeile);

					//Das ist in der letzten Zeile, danach fischen
					if(stristr($d2['4'],"Debug"))
					{
						$ende=true;
					}
					
					//Kommission
					$row2['kommission']=class_methods::get_clean_text($d2['1']);
					
					//Datum
					$row2['datum']=class_methods::get_clean_text($d2['2']);
					
					//Zuständigjeit
					$row2['zustaendig']=class_methods::get_clean_text($d2['3']);
					
					//Ergebnis, da letzte Zeile überflüssiges entfernen
					$d3=explode("</table>",$d2['4']);
					$row2['ergebnis']=class_methods::get_clean_text($d3['0']);
				}
				
				//Daten in oberes Array zuweisen, aber nur wenn vorhanden
				if (!empty($row2['kommission']))
				{
					$extra['ablauf'][]=$row2;
				}
				
			}
		}		
		
		//antragssteller / Auch Auflisutng Partei auslesen 
		$d1=explode("Antragsteller/in",$html);

		$d2=explode("</tr>",$d1['1']);
		$extra['antragsstellerin']=class_methods::get_clean_text($d2['0']);
		$extra['antragsstellerin_partei'] = $this->get_partei($extra['antragsstellerin']);
        //debug::print_d($extra);
		return $extra;
	}
	
	
	/**
	 * get_partei function.
	 * 
	 * @access private
	 * @param mixed $text
	 * @return void
	 */
	private function get_partei($text)
	{
		//Textarray für Vergleich
		$text=strtolower($text);
		$text=str_ireplace("-"," ",$text);
		$tar=explode(" ",$text);
		
		$gruene=("Grünen");
		//Parteienarray
		$par=array("piraten","cdu","spd","90","bbb","linke","fdp","big","nrw");
		
		$partei = array_intersect($tar, $par);

		//debug::print_d($partei);
		return $partei;
	}
	
	/**
	 * get_array_rows function.
	 * 
	 * @access private
	 * @param string $rows. (default: "")
	 * @return void
	 */
	private function get_array_rows($rows=array())
	{
		//Daten durchlaufen
		foreach ($rows as $key=>$value)
		{
			//Jetzt die einzelnen tds auflösen
			$tds= explode("<td",$value);
			
			//Dann die Daten rausholen
			$td_array[]=$this->get_td_data($tds);
		
		}

		return $td_array;
	}
	
	/**
	 * get_td_data function.
	 * 
	 * @access private
	 * @param array $td. (default: array()
	 * @return void
	 */
	private function get_td_data($td=array())
	{
		//INI
		$row=array();
		//Daten bereinigen
		foreach ($td as $key=>$value)
		{
			//td wieder einfügen wg. striptags
			$value="<td ".html_entity_decode($value);
			
			//HTML entfernen außer Links
			$value=strip_tags($value,"<a><i>");
			
			//Neu zuweisen
			$neu_td[$key]=$value;
		}
		#debug::print_d($neu_td);
		//Zeile 1= Indikation ob sinnvoller Inhalt oder nicht
		if (!empty($neu_td['1']))
		{
			/**
				* Zeile 1 = Link zum Dokument und Name des Dokumentes, erste 2 Ziffen = Jahreszahl
				* Änderung nachdem BORIS Anzeige geändert wurde.... GRRrrr
				*
			*/
			$row_data			= $this->get_dokument_id($neu_td['5']);
			$row['id']			= $row_data['id'];
			$row['id_int']		= $row_data['id_int'];
			
			//Die Integer ID korrekt setzen
			if ($row['id']==$row['id_int'])
			{
				$vorgaenger=$row['id'];
			}
			else
			{
				if (stristr($row['id_int'],$vorgaenger))
				{
					$row['id_int']=$vorgaenger;
				}
			}
			
			$row['id_link_html'] 	= $this->get_url_js($neu_td['1']);
			$row['id_link_rtf'] 		= $this->get_url_js($neu_td['2']);
			$row['id_link_pdf'] 	= $this->get_url_js($neu_td['3']);
			
			//Zeile 2 = Metainformationen zum Dokument
			$row['meta_link'] 	= $this->get_url_html($neu_td['4']);
			
			//Zeile 3 = Kurzbetreff, oft schon mit Geo INfos
			$row['kurz_betreff'] 	= class_methods::get_clean_text($row_data['betreff']);
			
			//Zeile 4 = Name / Link des Ausschusses
			$row['ausschuss_link'] 	= $this->get_url_html($neu_td['6']);
			$row['ausschuss'] 	= class_methods::get_clean_text($neu_td['6']);
			
			//Zeile 5 = Datum der Sitzung in dem das Dokument auftaucht (nächstes oder letztes mal)
			$row['datum'] 	= class_methods::get_clean_text($neu_td['7']);
		}
		#debug::print_d($row);
		return $row;
	}
	
	/**
	 * get_dokument_id function.
	 * 
	 * @access private
	 * @return void
	 */
	private function get_dokument_id($text)
	{
		//Dokumentenname ist jetzt in einem i tag drin.
		$i_array=explode("<i",$text);
        if (empty($i_array['2']))$i_array['2']=$i_array['1'];
		#debug::print_d($i_array);
		//Erste Zeile = Dokument
		#(int) class_methods::get_clean_text
		$i_array['1']=class_methods::get_clean_text(str_ireplace(">","",$i_array['1']));
		$dok['id']=class_methods::get_clean_text(str_ireplace("Drucksache","",$i_array['1']));
		$dok['id_int']= preg_replace("/[^0-9]/","",trim($dok['id']));
        
		$dok['betreff']= class_methods::get_clean_text($i_array['2']);
		$dok['betreff']=str_ireplace(">","",$dok['betreff']);
		//debug::print_d($dok);
		
		return $dok;
	}
	
	
	/**
	 * get_url_js function.
	 * 
	 * @access private
	 * @param string $html. (default: "")
	 * @return void
	 */
	private function get_url_html($html="")
	{
		//URL rausholen - wieder mit stringfunktion - geht schneller
		$u1=explode('href="',$html);
		if (!empty($u1['1']))
		{
			$u2=explode('"',$u1['1']);
		
			return $u2['0'];
		}
		return false;
	}
	
	/**
	 * get_url_js function.
	 * 
	 * @access private
	 * @param string $html. (default: "")
	 * @return void
	 */
	private function get_url_js($html="")
	{
		//URL rausholen - wieder mit stringfunktion - geht schneller
		$u1=explode("window.open('",$html);
		if (!empty($u1['1']))
		{
			$u2=explode("',",$u1['1']);
			$u2['0']=str_replace("../","",$u2['0']);
		
			return $u2['0'];
		}
		return false;
		
	}
	
	/**
	 * get_html_table_rows function.
	 * Inhalte aufbrechen 
	 * Nach Möglichkeit String Methoden da die schneller sind als 
	 * reguläre Ausdrücke
	 * @access private
	 * @param string $data. (default: "")
	 * @return void
	 */
	private function get_html_table_rows($site="")
	{

		$data1=explode("<form",$site);
		
		//Jetzt sollte die Ergebnistabelle in data1[1] drin sein - die weiter aufbrechen
		$data2 = explode("<table",$data1['1']);

		//Ergebnisse jetzt in 3 - einzelne rows, TR noch in tr umwandeln...
		$data2['3']=str_ireplace("<tr","<tr",$data2['3']);
		$data3 = explode("<tr",$data2['3']);
		
		return $data3;
	}
}
?>