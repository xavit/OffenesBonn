<?php

// #####################################
// # CMS Papoo                         #
// # (c) Carsten Euwens 2009           #
// # Authors: Carsten Euwens           #
// # http://www.papoo.de               #
// # Internet                          #
// #####################################
// # PHP Version 5                     #
// #####################################
/*
*/

/* --------------------------------------------------------------------
* Diese Klasse bietet eine DB ABstraktion
* Normale Statements brauchen nicht mehr gebaut zu werden hiermit
* Voraussetzung ist das die Variablen im Template gleich heißen wie
* die DB Felder,
* 
* ----------------------------------------------------------------------*/

class db_abs
{
	// Konstruktor
	function db_abs()
	{

		// einbinden des Objekts der Datenbak Abstraktionsklasse ez_sql
		global $db;
		$this->db = &$db;
		
		global $checked;
		$this->checked = &$checked;

	}
	


	function get_colums_from_db($db="")
		{
			#print_r($_SESSION['sportlist_tables']);
			#if (!empty($_SESSION['sportlist_tables'][$db]))
			{
				$sql=sprintf("SHOW COLUMNS FROM %s",
											$db
											);
				$colums=$this->db->get_results($sql,ARRAY_A);
			}
			#print_r($colums);
			foreach ($colums as $col)
			{
				$cols[]=$col['Field'];
			}
			#print_r($cols);
			return $cols;
			#print_r($cols);
		}

		
		/**
			* FUNCTION: validateEmail
			*
			* Validates an email address and return true or false.
			*
			* @param string $address email address
			* @return bool true/false
			* @access public
			*/
		function validateEmail($address)
			{
				return preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9_-]+(\.[_a-z0-9-]+)+$/i', $address);
			}


		/**
		 * shop_class::hole_passende_eintraege_aus_checked_raus()
		 * Holt die Vars aus dem checked Objekt die passend zur Tabelle sind.
		 * @param mixed $xpr
		 * @return
		 */
		function hole_passende_eintraege_aus_checked_raus($xpr, $must,$not="xyzxyz",$db="")
		{
				//INI
				$ret = array();
				global $template_dat;
				global $lang_dat;
				
				$count = 0;
				$return_to_template = array();
				if (empty($not))$not="xyzxyz";

				//Alle felder Rausholen die passen könnten
				$cols=$this->get_colums_from_db($db);
				#print_r($cols);

				//Anzahl der MUST Felder
				$must_count = count($must);
				if (is_array($cols))
				{
					//Die checked Daten durchgehen
					foreach ($this->checked as $key => $value)
					{
							//sicherstellen dass es sich nur um korrekte Zeichen handelt
							$key = preg_replace("/[^a-zA-Z0-9_]_/", "", $key);
							#echo "<br />";
							//Wenn vorkommt in Returm Array übergeben
							//alt: eregi($xpr, $key) && !eregi($not,$key) &&
							if (in_array($key,$cols))
							{
									$ret[$key] = $value;

									//WEnn Eintrag enthalten ist, eintragen
									if (!empty($value))
									{
											//WEnn der Eintrag auch im Must Array ist den Zähler hochsetzen
											if (is_array($must))
											{
												if (in_array($key, $must))
												{
														$count++;
												}
											}
									}
									//Nochmal DAten ausgeben
									$return_to_template['0'][$key] =$value;
							}
							else
							{
									//Nochmal DAten ausgeben
									$template_dat[$key] = $value;
							}
					}
				}
				//DAten
				$template_dat[$xpr] = $return_to_template;

				#echo $count;
				#echo "<br />";
				##echo $must_count;
				#echo "<br />";
				//Wenn alle drin
				if ($count == $must_count)
				{
					$this->alle_must_enthalten = 1;
				}
				//Nicht alle drin
				else
				{
					
					//Einträge durchgehen
					if (is_array($must))
					{
						foreach ($must as $var)
						{
							if (empty($this->checked->$var))
							{
									
								if ($var!="extended_user_email")
								{

							    $template_dat['plugin_error'][$var]=$lang_dat['plugin_fehlt_eintrag'];
								}
								else
								{
								
									#echo $this->checked->$var;
									if (!empty($this->checked->extended_user_email_false))
									{
									
										$template_dat['plugin_error'][$var]=$lang_dat['plugin_fehlt_eintrag'];
										
										
									}
									else
									{
										#echo "HIER";
										$template_dat['plugin_error'][$var]=$lang_dat['plugin_fehlt_eintrag_email_exist'];
									}
								}
							}
							else
							{
								if ($var=="extended_user_email")
								{
									if (!$this->validateEmail($this->checked->$var))
									{
										$template_dat['plugin_error'][$var]=$lang_dat['plugin_fehlt_eintrag_email_falsch'];
									}
								}
							}
							
						}
					}
				}
				if (is_array($must))
					{
					foreach ($must as $var)
					{
							if ($var=="extended_user_email")
							{
								#echo $this->checked->$var;
								if (!$this->validateEmail($this->checked->$var))
								{
									$template_dat['plugin_error'][$var]=$lang_dat['plugin_fehlt_eintrag_email_falsch'];
									$this->alle_must_enthalten = "";
								}
							}
							//extended_user_benutzername_false
							if ($var=="extended_user_benutzername")
							{
								#echo $this->checked->$var;
								if (($this->checked->extended_user_benutzername_false))
								{
									$template_dat['plugin_error'][$var]=$lang_dat['plugin_fehlt_eintrag_username_falsch'];
									$this->alle_must_enthalten = "";
								}
							}
	
					}
				}
				#print_r($template_dat['plugin_error']);
				#print_r($must);
				if (!is_array($must))
				{
					 $this->alle_must_enthalten = 1;
				}
				#print_r($template_dat['error']);
				#exit();
				#print_r($ret);
				return $ret;
		}

		/**
		 * shop_class::insert_new_eintrag_in_db()
		 * Neuen Eintrag in eine übergeben Tabell erstellen
		 * @param mixed $xsql
		 * @return void
		 */
		function insert_new_eintrag_in_db($xsql,$show=0)
		{
				//Initialisierung
				$insert = "";
				#print_r($this->checked);
				//Neu setzen
				$this->alle_must_enthalten = 0;
				
				#print_r($xsql);

				//Die passenden Einträge rausholen
				$post_vars = $this->hole_passende_eintraege_aus_checked_raus($xsql['praefix'], $xsql['must'],$xsql['not_praefix'],$xsql['dbname']);

				//Die durchgehen und Statement erstellen
				foreach ($post_vars as $key => $value)
				{
						$insert .= $key . "='" . $this->db->escape($value) . "', ";
				}

				//Sprachid setzen
				if (!empty($xsql['lang_id']))
				{
				$insert .= $xsql['praefix'] . "_lang_id='" . $xsql['lang_id'] .
						"', ";
				}

				$insert = substr($insert,0,-2);

				//Statement erzeugen
				$sql = sprintf("INSERT INTO %s SET %s",
											$xsql['dbname'],
											$insert
											);
				$this->alle_must_enthalten;
				//Wenn was drin ist
				if ($this->alle_must_enthalten == 1)
				{
						//Insert durchführen
						$this->db->query($sql);
						
						//Wenn anzeigen
						if ($show==1)
						{
							echo $sql;
						}

						//Id übergeben
						$return['insert_id'] = $this->db->insert_id;


				}
				else
				{
						//Werte wieder zurückgeben
						$this->werte_nochmal_zurueckgeben();
						#echo "NO";
				}

				return $return;
		}

		/**
		 * shop_class::insert_new_eintrag_in_db()
		 * Neuen Eintrag in eine übergeben Tabell erstellen
		 * @param mixed $xsql
		 * @return void
		 */
		function delete_eintrag_in_db($xsql,$show="")
		{
				//Initialisierung
				$insert = "";

				//Statement erzeugen
				$sql = sprintf("DELETE FROM %s WHERE %s",
											$xsql['dbname'],
											$xsql['del_where_wert']
											);
				if ($show==1)
				{
					echo $sql;
				}
				//Löschen durchführen
				$this->db->query($sql);

				return $return;
		}

		/**
		 * shop_class::insert_new_eintrag_in_db()
		 * Neuen Eintrag in eine übergeben Tabell erstellen
		 * @param mixed $xsql
		 * @return void
		 */
		function update_eintrag_in_db($xsql,$show=0)
		{
				//Initialisierung
				$insert = "";
				
				if (defined(admin))
				{
					unset($_SESSION['shop_settings']);
				}
				
				//Neu setzen
				$this->alle_must_enthalten = 0;

				//Die passenden Einträge rausholen
				$post_vars = $this->hole_passende_eintraege_aus_checked_raus($xsql['praefix'], $xsql['must'],$xsql['not_praefix'],$xsql['dbname']);

				//Die durchgehen und Statement erstellen
				foreach ($post_vars as $key => $value)
				{
						$insert .= $key . "='" . $this->db->escape($value) . "', ";
				}
				if (!empty($xsql['where_second_name']))
				{
					$insert = substr($insert,0,-2);	
					//Statement erzeugen
					$sql = sprintf("UPDATE %s SET %s WHERE %s='%d' AND %s='%d' LIMIT 1",
												$_SESSION['sportlist_tables'][$xsql['dbname']],
												$insert,
												$xsql['where_name'],
												$this->db->escape($this->checked->$xsql['where_name']),
												$xsql['where_second_name'],
												$this->db->escape($this->checked->$xsql['where_second_name'])
												);
				}
				else
				{
					if (!empty($xsql['where_lang_name']))
					{
						#echo $this->cms->lang_back_content_id;
						//Sprachid setzen
					$insert .= $xsql['praefix'] . "_lang_id='" . $this->cms->lang_back_content_id .
							"'";
	
	
					//Statement erzeugen
					$sql = sprintf("UPDATE %s SET %s WHERE %s='%d' AND %s='%d' LIMIT 1",
												$_SESSION['sportlist_tables'][$xsql['dbname']],
												$insert,
												$xsql['where_name'],
												$this->db->escape($this->checked->$xsql['where_name']),
												$xsql['where_lang_name'],
												$this->db->escape($this->cms->lang_back_content_id)
												);
					}
					else
					{
						$insert = substr($insert,0,-2);
						//Statement erzeugen
					  $sql = sprintf("UPDATE %s SET %s WHERE %s='%d' LIMIT 1",
												$_SESSION['sportlist_tables'][$xsql['dbname']],
												$insert,
												$xsql['where_name'],
												$this->db->escape($this->checked->$xsql['where_name'])
												);
					}
				}
				#echo $sql;
			  #exit();
				#echo $this->alle_must_enthalten;
				//Wenn was drin ist
				if ($this->alle_must_enthalten == 1)
				{
						#echo $sql;
						//Wenn anzeigen
						if ($show==1)
						{
							echo $sql;
						}
						
						//Insert durchführen
						$this->db->query($sql);
						#echo $xsql['where_name'];
						//Id übergeben
						$return['insert_id'] = $this->checked->$xsql['where_name'];


				}
				else
				{
						if ($show==1)
						{
							echo "Nicht alle Werte enthalten";
						}
						#echo "reture";
						//Werte wieder zurückgeben
						$this->werte_nochmal_zurueckgeben();
				}

				return $return;
		}
		
		
		/**
		 * shop_class::werte_nochmal_zurueckgeben()
		 * Gibt alle Werte nochmal ans Template zurück
		 * @return void
		 */
		function werte_nochmal_zurueckgeben()
		{
				//Nachricht übergeben
				$template_dat['is_eingetragen'] = "no";
		}





}
// Hiermit wird die Klasse initialisiert und kann damit sofort überall benutzt werden.
$db_abs = new db_abs();

?>