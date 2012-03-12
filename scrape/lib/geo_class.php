<?php 
/**
 * class_methods class.
 * Diverse Methoden
 */
class geo_class
{

	/**
	 * class_methods function.
	 * 
	 * @access public
	 * @return void
	 */
	function geo_class(){}
	
	/**
	 * create_lokalisierung function.
	 * 
	 * @access public
	 * @param mixed $rdata
	 * @return void
	 */
	public function create_lokalisierung($rdata)
	{
		//Straßen und Ortsteile einlesen
		$str_ort_array=$this->get_strassen_und_ortsteile();
		
		//Dann mit Daten abgleichen und zuweisen
		$geo_daten_str_ort=$this->make_geo_abgleich($rdata,$str_ort_array);
		
		return $geo_daten_str_ort;
	}
	
	/**
	 * make_geo_abgleich function.
	 * 
	 * @access private
	 * @param array $rdata
	 * @param array $str_ort_array
	 * @return void
	 */
	private function make_geo_abgleich($rdata,$str_ort_array)
	{
		//Daten durchgehen
		if (is_array($rdata))
		{
			//print_r($str_ort_array['strassen']);
			foreach ($rdata as $key=>$value)
			{
				//Mögliche Geodaten herausholen
				$geo_moeglich_array=$this->get_roh_daten($value);
				#debug::print_d($geo_moeglich_array);
				$result=array();
				
				//Zuerst mit Headline probieren in Straßen
				if(!empty($geo_moeglich_array['headline']))
				{
					//print_r($geo_moeglich_array['headline']);
					$result['strasse'][] = array_intersect($geo_moeglich_array['headline'], $str_ort_array['strassen']);
					
					//Echte Strasse und GEO real
					$result=$this->get_real_street($result,$key,$str_ort_array,$rdata);
				}
				
				//Wenn leer dann mit Text
				if(!empty($geo_moeglich_array['text']) && empty($result))
				{
					$result['strasse'][] = array_intersect($geo_moeglich_array['text'], $str_ort_array['strassen']);
					
					//Echte Strasse und GEO real
					$result=$this->get_real_street($result,$key,$str_ort_array,$rdata);
				}
				
				//Dann die Ortsteile
				if(!empty($geo_moeglich_array['headline']))
				{
					$result['ortsteile'][] = array_intersect($geo_moeglich_array['headline'], $str_ort_array['ortsteile']);
					
					//Ortsteil und GEO
					$result=$this->get_real_ortsteil($result,$key,$str_ort_array,$rdata);
				}
				
				//Wenn leer dann mit Text
				if(!empty($geo_moeglich_array['text']) && empty($result['head']['ortsteile']))
				{
					$result['ortsteile'][] = array_intersect($geo_moeglich_array['text'], $str_ort_array['ortsteile']);
					
					//Ortsteil und GEO
					$result=$this->get_real_ortsteil($result,$key,$str_ort_array,$rdata);
				}
				//debug::print_d($result);
				$rdata[$key]['geo']=$result;
				
			}
			$int="";
			//Jetzt noch für alle Unterdokumente ebenfalls die Geodaten setzen
			foreach ($rdata as $key=>$value)
			{
				$value['id_int']=(string) $value['id_int'];
				//Erstmal Daten setzen vom Basisdokument
				if ($value['id']==$value['id_int'])
				{
					$geo=$value['geo'];
					$int=$value['id'];
				}

				//Wenn leer
				if (empty($value['geo']['strasse']['0']) && empty($value['geo']['ortsteile']['0']))
				{
					//Und ein Unterdokument
					if($int==$value['id_int'] && $int!=$value['id'])
					{
						$rdata[$key]['geo']=$geo;
					}
				}
			}
		}
		
		#print_r($rdata);
		//exit();
		return $rdata;
	}
	
	/**
	 * get_real_street function.
	 * 
	 * Den echten Straßennamen rausholen
	 * Ist notwendig weil wir mit Kürzelnarbeiten
	 * und dann auch gleich 
	 * die Long / Lat Daten auslesen
	 * 
	 * @access private
	 * @param array $result. (default: array()
	 * @return void
	 */
	private function get_real_street($result=array(),$key,$str_ort_array,$rdata)
	{
		//Wenn nicht leer, nach Hausnummer fischen
		if(is_array($result['strasse']))
		{
			foreach ($result['strasse'] as $keyd=>$valued)
			{
				foreach ($valued as $keyd2=>$valued2)
				{
					$head_t=explode($valued2,$rdata[$key]['kurz_betreff']);
					$hausnummer = preg_replace("/[^0-9]/", "", substr($head_t['1'],0,30));
					$result['strasse'][$keyd][$keyd2]=$str_ort_array['strassen_org'][$valued2]." ".$hausnummer;
					
					$geo_long=$this->get_osm_geo_daten($result['strasse'][$keyd][$keyd2]);
					$result['strasse'][$keyd]['osm']=$geo_long;
				}
			}
		}
		
		return $result;
	}
	
	/**
	 * geo_class::get_real_ortsteil()
	 * 
	 * Den echten Ortsnamen rausholen 
	 * und dann auch gleich 
	 * die Long / Lat Daten auslesen
	 * 
	 * @param mixed $result
	 * @param mixed $key
	 * @param mixed $str_ort_array
	 * @param mixed $rdata
	 * @return
	 */
	private function get_real_ortsteil($result=array(),$key,$str_ort_array,$rdata)
	{
		//Wenn nicht leer, nach Hausnummer fischen
		if(is_array($result['ortsteile']))
		{
			foreach ($result['ortsteile'] as $keyd=>$valued)
			{
				foreach ($valued as $keyd2=>$valued2)
				{	
					$geo_long=$this->get_osm_geo_daten($valued2);
					$result['strasse'][$keyd]['osm']=$geo_long;
				}
			}
		}
		
		return $result;
	}
	
	/**
	 * get_osm_geo_daten function.
	 * 
	 * Geo Daten aus OSM auslesen 
	 * 
	 * @access private
	 * @return void
	 */
	private function get_osm_geo_daten($data)
	{
		$data=str_ireplace(" ","+",$data)."+Bonn";
		//URL für Long / Lat: http://nominatim.openstreetmap.org/search?q=Rheinaue+Bonn&format=xml
		$url='http://nominatim.openstreetmap.org/search?q='.$data.'&format=xml';
		$site=class_methods::get_site($url);
		
		//XML to Array Conversion
		$xml = simplexml_load_string($site);
		$json = json_encode($xml);
		$array = json_decode($json,TRUE);
		
		return($array);
		
	}
	
	/**
	 * get_roh_daten function.
	 * 
	 * Headline und Texte in Array einlesen
	 * Vergleich geht dann schneller
	 * 
	 * @access private
	 * @param mixed $data
	 * @return void
	 */
	private function get_roh_daten($data)
	{
		//INI
		$geo_data=array();
		
		//nur wenn auch Dokument vorhanden ist
		if (!empty($data['id']))
		{
			//kurz_betreff "/" entfernen und in Array umwandeln
			$data['kurz_betreff']=utf8_encode($this->entferne_satzzeichen($data['kurz_betreff']));
			#print_r($data['kurz_betreff'])."\n";
			$headline=explode(" ",$data['kurz_betreff']);
			#print_r($headline)."\n";
			if (is_array($headline))
			{
				foreach ($headline as $key=>$dat)
				{
					if (!empty($dat))
					{
						$geo_data['headline'][]=trim($dat);
					}
				}
			}
			
			//meta_data_text "/" entfernen und in Array umwandeln
			if (!empty($data['id_data']))
			{
				$data['id_data']['html_text']=utf8_encode($this->entferne_satzzeichen($data['id_data']['html_text']));
				$text=explode(" ",$data['id_data']['html_text']);
				
				if (is_array($text))
				{
					foreach ($text as $key=>$dat)
					{
						if (!empty($dat))
						{
							$geo_data['text'][]=trim($dat);
						}
					}
				}
			}
		}
		
		return $geo_data;
	}
	
	/**
	 * entferne_satzzeichen function.
	 * 
	 * @access private
	 * @param mixed $text
	 * @return void
	 */
	private function entferne_satzzeichen($text)
	{
		$text=str_ireplace("/"," ",$text);
		$text=str_ireplace(","," ",$text);
		$text=str_ireplace(";"," ",$text);
		$text=str_ireplace("."," ",$text);
		$text=str_ireplace(":"," ",$text);
		
		//Straßen usw entfernen für eine bessere Trefferquote
		$text=$this->entferne_str($text,true);
		
		$text=class_methods::get_clean_text($text);
		return $text;
	}
	
	
	/**
	 * get_strassen_und_ortsteile function.
	 * 
	 * Straßennamen und Ortsteile einlesen
	 * 
	 * @access private
	 * @return void
	 */	
	private function get_strassen_und_ortsteile()
	{
		//TODO Aufteilen in Str und Ort - dann kann man das doppelt zuweisen!!!
		
		//Zuerst mal die Strassen
		$strassen=(file_get_contents("./daten/strassen.txt"));
				
		//In Array umwandeln
		$str_array=explode("\n",$strassen);
		
		//Durchlaufen und bereinigen
		if (is_array($str_array))
		{
			foreach ($str_array as $key=>$value)
			{
				//Kommentare entfernen
				$value_array=explode(";",$value);
				$value=trim($value_array['0']);
				
				//Straßen usw entfernen für eine bessere Trefferquote
				$value_org=$value;
				$value=$this->entferne_str($value);
				
				//bereinigen
				$value=class_methods::get_clean_text($value);
				
				//zurücklesen
				$str_array2[$value_org]=$value;
				$str_array3[$value]=$value_org;
			}
		}
		
		//Dann die Ortsteile
		$ortsteile=utf8_decode(file_get_contents("./daten/ortsteile.txt"));
		
		//In Array umwandeln
		$ort_array=explode("\n",$ortsteile);
		
		//Durchlaufen und bereinigen
		if (is_array($ort_array))
		{
			foreach ($ort_array as $key=>$value)
			{
				//Kommentare entfernen
				$value_array=explode(";",$value);
				$value=trim($value_array['0']);
				
				//Straßen usw entfernen für eine bessere Trefferquote
				$value=$this->entferne_str($value);
				
				//bereinigen
				$value=class_methods::get_clean_text($value);
				
				//zurücklesen
				$ort_array[$key]=$value;
			}
		}
		
		$data['strassen']		=$str_array2;
		$data['strassen_org']	=$str_array3;
		$data['ortsteile']		=$ort_array;
		
		return $data;
	}	
	
	/**
	 * entferne_str function.
	 * 
	 * @access private
	 * @param mixed $value
	 * @return void
	 */
	private function entferne_str($value,$utf8=false)
	{
		//Ausnahmen mit richtigen Vornahmen
		if (!stristr($value,"Michael")
			&& !stristr($value,"Paul")
			&& !stristr($value,"Köln")
			&& !stristr($value,"Bonn"))
			{

				$value=str_ireplace(utf8_encode("Straße"),"",$value);
				$value=str_ireplace(utf8_decode("Straße"),"",$value);
				$value=str_ireplace(("Straße"),"",$value);
				//$value=str_ireplace("weg","",$value);
				$value=str_ireplace("am ","",$value);
				$value=str_ireplace("str. ","",$value);
				$value=str_ireplace("strasse ","",$value);
		}
		return $value;
	
	}
	
}
?>