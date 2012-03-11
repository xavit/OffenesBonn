<?php 
/**
 * class_methods class.
 * Diverse Methoden
 */
class class_save_data
{

	/**
	 * class_methods function.
	 * 
	 * @access public
	 * @return void
	 */
	function class_save_data()
	{}
	
	/**
	 * class_save_data::save_now()
	 * 
	 * @param mixed $rdata
	 * @return void
	 */
	public function save_now($rdata,$reload="")
	{
		mysql_ping();
		$db = new db_class(EZSQL_DB_USER, EZSQL_DB_PASSWORD, EZSQL_DB_NAME, EZSQL_DB_HOST,true);
		//$db->query("SET NAMES 'utf8'");
		$db->query("SET sql_mode=''");
		mysql_ping();
		//durchloopen
		if (is_array($rdata))
		{
			foreach ($rdata as $key=>$value)
			{
				//Nur wenn auch ein Dokument vorhanden ist Dann speichern
				if (is_numeric($value['id_int']) && $value['id_int']>0)
				{
					mysql_ping();
					//Die Daten aufbereiten
					$idat=$this->create_basis_daten($value);
					#debug::print_d($idat);
					//checken ob schon vorhanden
					$insert_id=$this->check_vorhanden($idat,$db);
					
					//schon vorhanden
					if (is_numeric($insert_id))
					{
						$this->update_db($idat,$db);
						
					}
					//Wenn nicht, neu eintragen
					else
					{
						$insert_id=$this->insert_db($idat,$db);
					}

					//Jetzt die Lookups befüllen
					$this->insert_lookups($value,$insert_id,$db);
				}
			}
		}
		
		//DB Page eins raufzählen
		//$this->update_counter($db);
		
		//Seite neu laden
		if (empty($reload))
		{
		$this->reload_page();
		}
		
		
		#print_r($rdata);
	}
	/**
	 * reload_page function.
	 * 
	 * @access private
	 * @return void
	 */
	private function reload_page()
	{
		global $rows;
		
		$url='./scrape.php?page_count='.$rows->page_count;
		//header("Location: $url");
		echo '<br />Nächste Seite in 2 Sekunden... (Neue Seite) '.$url.'<meta http-equiv="refresh" content="2; URL='.$url.' ">';
		exit();
	}
	
	private function update_counter($db)
	{
		#$sql=sprintf("UPDATE %s SET op_counter = op_counter + 1",
		#							'openboris_pagecount'
		#);
		#$result=$db->query($sql);
		//echo "FERTIG";
		//return $result;
	}
	
	/**
	 * class_save_data::insert_lookups()
	 * 
	 * @param mixed $value
	 * @param mixed $insert_id
	 * @return void
	 */
	private function insert_lookups($value,$insert_id,$db)
	{
		//Zuerst den Ausschuß eintragen
		$this->insert_ausschuss($value,$insert_id,$db);
		
		//Metadaten Verwaltung eintragen
		$this->insert_metadaten_verwaltung($value,$insert_id,$db);
		
		//Parteidaten eintragen
		$this->insert_partei_daten($value,$insert_id,$db);
		
		//Thumbnails eintragen
		$this->insert_thumbnail_daten($value,$insert_id,$db);
	}
	
	
	private function insert_thumbnail_daten($dat,$insert_id,$db)
	{
		#print_r($dat);
		#exit();
		//Lookup löschen
		$sql=sprintf("DELETE  FROM %s
												WHERE ob_thumb_basis_id='%d'",
												'openboris_thumbnails',
												$db->escape($insert_id)
		);
		$db->query($sql);

		if (is_array($dat['thumbnails']))
		{
			foreach ($dat['thumbnails'] as $key2=>$value2)
			{
				//Lookup eintragen
				$sql=sprintf("INSERT INTO %s
									SET ob_thumb_url ='%s',
											ob_thumb_basis_id='%s'
									
									",
									"openboris_thumbnails",
									$db->escape($value2),
									$db->escape($insert_id)
								
				);
				//print_r($sql);
				$result=$db->query($sql);
			}
		}
		
	}
	
	
	/**
	 * class_save_data::insert_partei_daten()
	 * 
	 * @param mixed $dat
	 * @param mixed $insert_id
	 * @param mixed $db
	 * @return void
	 */
	private function insert_partei_daten($dat,$insert_id,$db)
	{
		#print_r($dat['id_data']['meta_data_extra']['antragsstellerin_partei']);
		if (is_array($dat['id_data']['meta_data_extra']['antragsstellerin_partei']))
		{
			foreach ($dat['id_data']['meta_data_extra']['antragsstellerin_partei'] as $key2=>$value2)
			{
				#print_r($value2);
	
				//Checken ob schon vorhanden
				$sql=sprintf("SELECT 		ob_pid 	 FROM %s
											WHERE ob_parteiname='%s'",
											'openboris_parteien',
											$db->escape($value2)
				);
				
				$insert_id_partei=$db->get_var($sql);
				
				if (empty($insert_id_partei))
				{
					$sql=sprintf("INSERT INTO %s
											SET ob_parteiname 	='%s'
																							
											",
											"openboris_parteien",
											$db->escape($value2)
										
					);
					#print_r($sql);
					$result=$db->query($sql);
					$insert_id_partei=$db->insert_id;
				}
				
				//Lookup löschen
				$sql=sprintf("DELETE  FROM %s
														WHERE ob_basis_pid='%d'",
														'openboris_parteien_lookup',
														$db->escape($insert_id)
				);
				$db->query($sql);
				
				//Lookup eintragen
				$sql=sprintf("INSERT INTO %s
											SET ob_basis_pid 	='%d',
												ob_partei_id='%d'
											
											",
											"openboris_parteien_lookup",
											$db->escape($insert_id),
											$db->escape($insert_id_partei)
										
					);
					//print_r($sql);
					$result=$db->query($sql);
		
		
				
				
				
			}
		}
		/**
		
			*/
	}
	
	/**
	 * class_save_data::insert_metadaten()
	 * Abläufe in der 
	 * @param mixed $dat
	 * @param mixed $insert_id
	 * @param mixed $db
	 * @return void
	 */
	private function insert_metadaten_verwaltung($dat,$insert_id,$db)
	{
		#print_r($dat);
		#exit();
		//Lookup löschen
		$sql=sprintf("DELETE  FROM %s
												WHERE ob_meta_basis_id='%d'",
												'openboris_metadaten_verwaltung',
												$db->escape($insert_id)
		);
		$db->query($sql);

		if (is_array($dat['id_data']['html_meta']['dokument_meta_verwaltung']))
		{
			foreach ($dat['id_data']['html_meta']['dokument_meta_verwaltung'] as $key2=>$value2)
			{
				//Lookup eintragen
				$sql=sprintf("INSERT INTO %s
									SET ob_meta_amt ='%s',
											ob_meta_zeit='%s',
											ob_meta_datum='%s',
											ob_meta_unterschrift='%s',
											ob_meta_basis_id='%s'
									
									",
									"openboris_metadaten_verwaltung",
									$db->escape($value2['amt']),
									$db->escape($value2['zeit']),
									$db->escape($value2['datum']),
									$db->escape($value2['unterschrift']),
									$db->escape($insert_id)
								
				);
				//print_r($sql);
				$result=$db->query($sql);
			}
		}
		
	}
	
	/**
	 * class_save_data::insert_ausschuss()
	 * 
	 * @param mixed $value
	 * @param mixed $insert_id
	 * @return void
	 */
	private function insert_ausschuss($dat,$insert_id,$db)
	{
		//Checken ob schon vorhanden
		$sql=sprintf("SELECT 	ob_auid 	 FROM %s
									WHERE ob_ausschuss_name='%s'",
									'openboris_ausschuesse',
									$db->escape($dat['ausschuss'])
		);
		
		$insert_id_ausschuss=$db->get_var($sql);
		
		if (empty($insert_id_ausschuss))
		{
			$sql=sprintf("INSERT INTO %s
									SET ob_ausschuss_name 	='%s',
										ob_ausschuss_link='%s'
									
									",
									"openboris_ausschuesse",
									$db->escape($dat['ausschuss']),
									$db->escape($dat['ausschuss_link'])
								
			);
			#print_r($sql);
			$result=$db->query($sql);
			$insert_id_ausschuss=$db->insert_id;
		}
		
		//Lookup löschen
		$sql=sprintf("DELETE  FROM %s
												WHERE ob_au_basis_id='%d'",
												'openboris_ausschuss_lookup',
												$db->escape($insert_id)
		);
		$db->query($sql);
		
		//Lookup eintragen
		$sql=sprintf("INSERT INTO %s
									SET ob_au_basis_id 	='%d',
										ob_aulid='%d'
									
									",
									"openboris_ausschuss_lookup",
									$db->escape($insert_id),
									$db->escape($insert_id_ausschuss)
								
			);
			//print_r($sql);
			$result=$db->query($sql);
	}
	
	/**
	 * class_save_data::insert_db()
	 * 
	 * @return void
	 */
	private function insert_db($idat,$db)
	{
		//Zuerst die Basisdaten
		$sql=sprintf("INSERT INTO %s
									SET ob_boris_id='%s',
									ob_boris_id_int='%d',
									ob_id_link='%s',
									ob_meta_link='%s',
									ob_kurz_betreff='%s',
									ob_ausschuss='%s',
									ob_ausschuss_link='%s',
									ob_datum='%s',
									ob_datum_date='%s',
									ob_timestamp='%s',
									ob_timestamp_erstellung_ob='%s',
									ob_id_data_text='%s',
									ob_meta_daten='%s',
									ob_zugriffsart='%s',
									ob_partei='%s',
									ob_formular_art='%s',
									ob_kosten_liste='%s',
									ob_kosten_gesamt='%s',
									ob_ablauf_verwaltung='%s',
									ob_pdf_text='%s',
									ob_antragstellering='%s',
									ob_geo_strasse='%s',
									ob_geo_ortsteil='%s',
									ob_osm_raw_data='%s',
									ob_osm_long='%s',
									ob_osm_lat='%s',
									ob_pdf_file_url='%s',
									ob_thumbnail='%s'
									",
									"openboris_basis",
									$db->escape($idat['id']),
									$db->escape($idat['id_int']),
									$db->escape($idat['id_link']),
									$db->escape($idat['meta_link']),
									$db->escape($idat['kurz_betreff']),
									$db->escape($idat['ausschuss']),
									$db->escape($idat['ausschuss_link']),
									$db->escape($idat['datum']),
									$db->escape($idat['datum']),
									$db->escape(strtotime($idat['datum'])),
									$db->escape(time()),
									$db->escape($idat['ob_id_data_text']),
									$db->escape($idat['ob_meta_daten']),
									$db->escape($idat['ob_zugriffsart']),
									$db->escape($idat['ob_partei']),
									$db->escape($idat['ob_formular_art']),
									$db->escape($idat['ob_kosten_liste']),
									$db->escape($idat['ob_kosten_gesamt']),
									$db->escape($idat['ob_ablauf_verwaltung']),
									$db->escape($idat['ob_pdf_text']),
									$db->escape($idat['ob_antragstellering']),
									$db->escape($idat['ob_geo_strasse']),
									$db->escape($idat['ob_geo_ortsteil']),
									$db->escape($idat['ob_osm_raw_data']),
									$db->escape($idat['ob_osm_long']),
									$db->escape($idat['ob_osm_lat']),
									$db->escape($idat['ob_pdf_file_url']),
									$db->escape($idat['ob_thumbnail'])
		);
		//print_r($sql);
		$result=$db->query($sql);
		return $db->insert_id;
	}
	
	private function update_db($idat,$db)
	{
		//Zuerst die Basisdaten
		$sql=sprintf("UPDATE %s
									SET 
									ob_boris_id_int='%d',
									ob_id_link='%s',
									ob_meta_link='%s',
									ob_kurz_betreff='%s',
									ob_ausschuss='%s',
									ob_ausschuss_link='%s',
									ob_datum='%s',
									ob_datum_date='%s',
									ob_timestamp='%s',
									ob_timestamp_erstellung_ob='%s',
									ob_id_data_text='%s',
									ob_meta_daten='%s',
									ob_zugriffsart='%s',
									ob_partei='%s',
									ob_formular_art='%s',
									ob_kosten_liste='%s',
									ob_kosten_gesamt='%s',
									ob_ablauf_verwaltung='%s',
									ob_pdf_text='%s',
									ob_antragstellering='%s',
									ob_geo_strasse='%s',
									ob_geo_ortsteil='%s',
									ob_osm_raw_data='%s',
									ob_osm_long='%s',
									ob_osm_lat='%s',
									ob_pdf_file_url='%s',
									ob_thumbnail='%s'
									WHERE ob_boris_id='%s'
									",
									"openboris_basis",
									$db->escape($idat['id_int']),
									$db->escape($idat['id_link']),
									$db->escape($idat['meta_link']),
									$db->escape($idat['kurz_betreff']),
									$db->escape($idat['ausschuss']),
									$db->escape($idat['ausschuss_link']),
									$db->escape($idat['datum']),
									$db->escape($idat['datum']),
									$db->escape(strtotime($idat['datum'])),
									$db->escape(time()),
									$db->escape($idat['ob_id_data_text']),
									$db->escape($idat['ob_meta_daten']),
									$db->escape($idat['ob_zugriffsart']),
									$db->escape($idat['ob_partei']),
									$db->escape($idat['ob_formular_art']),
									$db->escape($idat['ob_kosten_liste']),
									$db->escape($idat['ob_kosten_gesamt']),
									$db->escape($idat['ob_ablauf_verwaltung']),
									$db->escape($idat['ob_pdf_text']),
									$db->escape($idat['ob_antragstellering']),
									$db->escape($idat['ob_geo_strasse']),
									$db->escape($idat['ob_geo_ortsteil']),
									$db->escape($idat['ob_osm_raw_data']),
									$db->escape($idat['ob_osm_long']),
									$db->escape($idat['ob_osm_lat']),
									$db->escape($idat['ob_pdf_file_url']),
									$db->escape($idat['ob_thumbnail']),
									$db->escape($idat['id'])
		);
		//print_r($sql);
		$result=$db->query($sql);
		
	}
	
	
	/**
	 * class_save_data::check_vorhanden()
	 * 
	 * @param mixed $dat
	 * @return void
	 */
	private function check_vorhanden($dat,$db)
	{
		$sql=sprintf("SELECT ob_id 	 FROM %s
									WHERE ob_boris_id='%s'",
									'openboris_basis',
									$db->escape($dat['id'])
		);
		$result=$db->get_var($sql);
		
		if (!empty($result))
		{
			return $result;
		}
		return false;
	}
	
	/**
	 * class_save_data::create_basis_daten()
	 * 
	 * @param mixed $value
	 * @return
	 */
	private function create_basis_daten($value_dat)
	{
		#print_r($value_dat);
		
		if (is_array($value_dat))
		{
			foreach ($value_dat as $key=>$value)
			{
				if (!is_array($value))
				{
					$neu[$key]=$value;
				}
			}
		}

		$neu['ob_id_data_text']=$value_dat['id_data']['html_text'];
		$neu['ob_meta_daten']=serialize($value_dat['id_data']['html_meta']);
		$neu['ob_zugriffsart']=($value_dat['id_data']['meta_data_extra']['zugriff']);
		$neu['ob_partei']=serialize($value_dat['id_data']['meta_data_extra']['antragsstellerin_partei']);
		$neu['ob_formular_art']=($value_dat['id_data']['meta_data_extra']['formular_art']);
		$neu['ob_kosten_liste']=($value_dat['id_data']['meta_data_extra']['kosten_auflistung']);
		$neu['ob_kosten_gesamt']=($value_dat['id_data']['meta_data_extra']['kosten_einzeln']);
		$neu['ob_ablauf_verwaltung']=serialize($value_dat['id_data']['meta_data_extra']['ablauf']);
		$neu['ob_pdf_text']=($value_dat['pdf_text']);
		$neu['ob_antragstellering']=($value_dat['meta_data_extra']['antragsstellerin']);
		$neu['ob_geo_strasse']=$this->get_strasse($value_dat['geo']['strasse']['0']);
		$neu['ob_geo_ortsteil']=$this->get_ortsteile($value_dat['geo']['ortsteile']);
		$neu['ob_pdf_file_url']=($value_dat['pdf_file_url']);
		$neu['ob_thumbnail']=serialize($value_dat['thumbnails']);
		$neu['ob_osm_long']=$this->get_long($value_dat['geo']);
		$neu['ob_osm_lat']=$this->get_lat($value_dat['geo']);
		$neu['ob_osm_raw_data']=serialize($value_dat['geo']);
		
		#print_r($neu);
		
		return $neu;
	}
	
	/**
	 * class_save_data::get_long()
	 * 
	 * @param mixed $geo
	 * @return
	 */
	private function get_long($geo)
	{
		
		$long=$geo['strasse']['0']['osm']['place']['@attributes']['lon'];
		if (empty($long))
		{
			$long=$geo['strasse']['0']['osm']['place']['0']['@attributes']['lon'];
			
			if (!stristr($geo['strasse']['0']['osm']['place']['0']['@attributes']['display_name'],"Bonn"))
			{
				$long=$geo['strasse']['0']['osm']['place']['1']['@attributes']['lon'];
				
				if (!stristr($geo['strasse']['0']['osm']['place']['1']['@attributes']['display_name'],"Bonn"))
				{
					$long=$geo['strasse']['0']['osm']['place']['2']['@attributes']['lon'];
				}
			}	
		}
		return $long;
	}
	
	/**
	 * class_save_data::get_lat()
	 * 
	 * @param mixed $geo
	 * @return
	 */
	private function get_lat($geo)
	{
		$lat=$geo['strasse']['0']['osm']['place']['@attributes']['lat'];
		if (empty($lat))
		{
			$lat=$geo['strasse']['0']['osm']['place']['0']['@attributes']['lat'];
			
			if (!stristr($geo['strasse']['0']['osm']['place']['0']['@attributes']['display_name'],"Bonn"))
			{
				$lat=$geo['strasse']['0']['osm']['place']['1']['@attributes']['lat'];
				
				if (!stristr($geo['strasse']['0']['osm']['place']['1']['@attributes']['display_name'],"Bonn"))
				{
					$lat=$geo['strasse']['0']['osm']['place']['2']['@attributes']['lat'];
				}
			}
		}
		
		return $lat;
	}
	
	/**
	 * class_save_data::get_strasse()
	 * 
	 * @return void
	 */
	private function get_strasse($data)
	{
		if (is_array($data))
		{
			foreach ($data as $key=>$value)
			{
				//Dann ist es die Straße
				if (is_numeric($key) && empty($strasse))
				{
					$strasse= $value;
				}
			}
		}
		#debug::print_d($data);
		//Ortsteil noch aus Array auslesen
		$orsteil1=$data['osm']['place']['@attributes']['display_name'];
		if (empty($orsteil1))
		{
			$orsteil1=$data['osm']['place']['0']['@attributes']['display_name'];
			
			if (!stristr($orsteil1,"Bonn"))
			{
				$orsteil1=$data['osm']['place']['1']['@attributes']['display_name'];
			}
			
			if (!stristr($orsteil1,"Bonn"))
			{
				$orsteil1=$data['osm']['place']['2']['@attributes']['display_name'];
			}
		}
		$orsteil12=explode(",",$orsteil1);
		$this->ortsteil="";
		if (is_array($orsteil12))
		{
			foreach ($orsteil12 as $key=>$value)
			{
				if (stristr($value,"Bonn") && empty($this->ortsteil))
				{
					$this->ortsteil=$orsteil12[$key-1];
					
				}
			}
		}
		
		return $strasse;
	}
	
	/**
	 * class_save_data::get_strasse()
	 * 
	 * @return void
	 */
	private function get_ortsteile($data)
	{
		if (is_array($data))
		{
			foreach ($data as $key=>$value)
			{
				//Ortsteile
				if (!empty($value))
				{
					$value=array_unique($value);
					$ortsteil.=implode(" ",$value);
				}
				
			}
			//Wenn leer dann den aus der Strasse nehmen
			if (empty($ortsteil))
			{
				$ortsteil=$this->ortsteil;
			}
			
			return $ortsteil;
		}
	}
	
}
?>