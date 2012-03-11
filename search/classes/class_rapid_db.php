<?php

/**
 * rapid_db_class
 * Damit können adhoc sehr schnell DB Felder und dazugehörige 
 * HTML Felder erstellt werden
 * @package Papoo
 * @author Papoo Software
 * @copyright 2009
 * @version $Id$
 * @access public
 */
class rapid_db_class
{
		// Konstruktor der Klasse "rapid_db_class"
		function rapid_db_class()
		{
				global $db;
				$this->db=&$db;
				
				$this->do_rapid();
				
		}

		/**
		 * rapid_db_class::do_rapid()
		 * 
		 * @return void
		 */
		function do_rapid()
		{
				#echo "do_rapid";
				//Wenn Formular
				if (!empty($_POST['submit_rapid']))
				{
					unset($_SESSION['sportlist_tables']);
						//Inhalte übeergeben
						$this->alle_rapid_variablen_setzen();

						//Wenn brav alles ausgefüllt wurde
						if (!empty($this->praefix) && !empty($this->name) && !empty($this->label) && $this->
								check_ob_feld_schon_existiert_in_der_datenbank())
						{
								//Typ auf normal setzen
								$type = "normal";
								//DB Typ setzen
								$db_type = "normal";

								//Fallunterscheidungen durchführen und dann
								#echo $this->checked->rapid_type;
								switch ($_POST['rapid_type'])
								{
										case "text":
												$this->create_text_feld();
												break;
										case "textarea":
												$this->create_textarea_feld();
												break;
										case "email":
												$this->create_text_feld();
												break;
										case "select":
												$this->create_select_feld();
												$db_type = "varchar";
												break;
										case "radio":
												$this->create_radio_feld();
												$db_type = "varchar";
												break;
										case "check":
												$this->create_check_feld();
												$db_type = "varchar";
												break;
										case "hidden":
												$this->create_hidden_feld();
												$db_type = "varchar";
												break;
										case "password":
												$this->create_password_feld();
												break;
										case "timestamp":
												//
												break;
										case "link":
												$this->create_link();
												$db_type = "no";
												break;
										case "leer":
												$this->create_leer();
												$db_type = "no";
												break;
										case "bild":
												$this->create_image_file_feld();
												break;
										case "file":
												$this->create_file_feld();
												break;
										case "ueberschrift":
												$this->create_h();
												$db_type = "no";
												break;
										case "ueberschrifth3":
												$this->create_h3();
												$db_type = "no";
												break;
										case "absatz":
												$this->create_p();
												$db_type = "no";
												break;
										case "fieldset":
												$type = "fieldset";
												$db_type = "no";
												$this->create_fieldset();
												break;
										case "divblock":
												$type = "divblock";
												$db_type = "no";
												$this->create_div();
												break;

										default:
												break;
								}
								//Ersetzungen im Template durchführen
								$this->baue_neues_feld_ins_template_ein($type);

								//NEuen Spracheintrag vornehmen
								$this->erstelle_neuen_sprachdatei_eintrag();

								//Db Felder erzeugen
								$this->erstelle_neues_db_feld_in_der_tabelle($db_type);

								//Coder erzeugen
								$this->erzeuge_input_output_php_code($this->php_datei_name);

								//Neu laden
								$this->reload();
						}
						else
						{
								$this->content->template['rapid_error1'] = "NO";
						}
				}


		}

		/**
		 * rapid_db_class::erzeuge_input_output_php_code()
		 * Erzeuge den IO Code in der PHP Datei
		 * @param mixed $php_file
		 * @return void
		 */
		function erzeuge_input_output_php_code($php_file)
		{
				//Checken das es nichts böses ist
			#	$php_file = basename($php_file);
			#	$php_file = $php_file . ".php";

				//Inhalt auslesen
				#$file_content = implode("", file(PAPOO_ABS_PFAD . "/plugins/" . $this->
					#	plugin_name . "/lib/" . $php_file));
				//echo $file_content;

				//Eintrag erstellen
				#$php = $this->erzeuge_io_php_codde();

				//Den neuen Inhalt einbauen und den Platzhalter wieder setzen
				#$file_content2 = eregi_replace('#start#', $php . " #start#", $file_content);

				//Inhalt speichern
			#	$this->write_to_file("/plugins/" . $this->plugin_name . "/lib/" . $php_file,
				#		$file_content2);
		}

		/**
		 * rapid_db_class::erzeuge_io_php_codde()
		 * Diese Funktion erzeugt den PHP Code für IO 
		 * @return void
		 */
		function erzeuge_io_php_codde()
		{
				/**
				 * Erstmal offen lassen, das kann man ja mit einer generellen 
				 * Funktion besser erschlagen
				 * 
				 * //Output Vorlage
				 * $output='function #platzhalter_function_name#_output() 
				 * {
				 * $sql=sprintf("SELECT * FROM %s WHERE %s=\'%d\' AND #platzhalter_spezial_praefix#_lang_id=\'%d\'",
				 * $this->cms->tbname[\'#platzhalte_db#\'],
				 * #platzhalter_id_name#,
				 * $this->db->escape($this->checked->#platzhalter_id_name#),
				 * $this->cms->lang_id
				 * );
				 * $result=$this->db->get_results($sql,ARRAY_A);
				 * $this->content->template[\'#platzhalter_outputname#\']=$result;
				 * }';
				 * //Ersetzungen durchführen
				 * 
				 * //Input Vorlage
				 */
		}

		/**
		 * rapid_db_class::check_ob_name_schon_existiert_in_der_datenbank()
		 * 
		 * @return void
		 */
		function check_ob_feld_schon_existiert_in_der_datenbank()
		{
				//Tabelle existiert noch nicht also ok
				if (empty($this->cms->tbname[$this->tb_name]))
				{
						return true;
				}

				//Überprüfen ob spalte existiert
				$sql = sprintf("SHOW COLUMNS FROM %s LIKE '%s'", $this->cms->tbname[$this->
						tb_name], $this->name);
				$result = $this->db->get_results($sql, ARRAY_A);

				//wenn ja- dann false
				if (!empty($result))
				{
						return false;
				}
				return true;
		}
		
		function check_table_exits($tb)
		{
			$sql="SHOW TABLES";
			$result=$this->db->get_results($sql,ARRAY_A);
			#print_r($result);
			foreach ($result as $key=>$value)
			{
				foreach ($value as $key1=>$value1)
				{
					#print_r($value1);
					if ($value1==DB_PRAEFIX.$tb)
					{
						#echo $tab;
						return true;
					}
				}
			}
			return false;
		}
		

		/**
		 * rapid_db_class::erstelle_neues_db_feld_in_der_tabelle()
		 * 
		 * @param string $db_type
		 * @return void
		 */
		function erstelle_neues_db_feld_in_der_tabelle($db_type = "normal")
		{
				if ($db_type != "no")
				{
						#	echo $this->tb_name;
						#echo $this->cms->tbname[$this->tb_name];
						#print_r($this->cms->tbname);
						//Checken ob die Tabelle existiert
						if (!$this->check_table_exits($this->tb_name))
						{
								//Wenn nicht anlegen
								$this->erzeuge_neue_tabelle($this->tb_name);
						}

						if ($db_type == "normal")
						{
								$this->erzeuge_neues_normales_feld();
						}
						
						if ($db_type == "varchar")
						{
								$this->erzeuge_neues_varchar_feld();
						}

				}
		}

		/**
		 * rapid_db_class::erzeuge_neue_tabelle()
		 * Eine Neue tabelle erzeugen
		 * @param mixed $tbname
		 * @return void
		 */
		function erzeuge_neue_tabelle($tbname)
		{
				#global DB_PRAEFIX;
				//Tabelle erzeugen
				if (!eregi("lang",$tbname))
				{
				$sql = sprintf("CREATE TABLE `%s` (`%s` int(11) NOT NULL auto_increment, 
				  							PRIMARY KEY (`%s`)) ENGINE=MyISAM", 
												DB_PRAEFIX . $tbname, 
												$this->praefix_spezial . "_id",
												$this->praefix_spezial . "_id"
												);
				}
				else
				{
					$sql = sprintf("CREATE TABLE `%s` (`%s` int(11) NOT NULL , 
												%s_lang_id int(11) NOT NULL
				  						) ENGINE=MyISAM", 
												DB_PRAEFIX . $tbname, 
												$this->praefix_spezial . "_id", 
												$this->praefix_spezial);
				
				}
				#echo $sql;
				#exit();
				$this->db->query($sql);
				return true;
		}

		/**
		 * rapid_db_class::erzeuge_neues_feld()
		 * Neues Feld in der Tabelle erzeugen
		 * @return void
		 */
		function erzeuge_neues_normales_feld()
		{
				#global DB_PRAEFIX;
				$sql = sprintf("ALTER TABLE `%s` ADD `%s` TEXT", 
				DB_PRAEFIX . 
				$this->tb_name, 
				$this->name
				);
				$this->db->query($sql);
				return true;
		}
		
		/**
		 * rapid_db_class::erzeuge_neues_varchar_feld()
		 * 
		 * @return
		 */
		function erzeuge_neues_varchar_feld()
		{
				#global DB_PRAEFIX;
				$sql = sprintf("ALTER TABLE `%s` ADD `%s` VARCHAR( 255 ) NOT NULL", 
				DB_PRAEFIX . 
				$this->tb_name, 
				$this->name
				);
				$this->db->query($sql);
				return true;
		}


		/**
		 * rapid_db_class::reload()
		 * Seite nue laden
		 * @return void
		 */
		function reload()
		{
				$url = "";
				foreach ($_GET as $key => $value)
				{
						$url .= strip_tags(htmlentities($key)) . "=" . strip_tags(htmlentities($value)) .
								"&";
				}
				$location_url = $_SERVER['PHP_SELF'] . "?" . $url;
				if ($_SESSION['debug_stopallredirect']) echo '<a href="' . $location_url . '">' .
								$this->content->template['plugin']['mv']['weiter'] . '</a>';
				else  header("Location: $location_url");
				exit;
		}


		/**
		 * rapid_db_class::erstelle_neuen_sprachdatei_eintrag()
		 * Diese Funktion erstellt einen neuen Sprachdatei eintrag
		 * @return void
		 */
		function erstelle_neuen_sprachdatei_eintrag()
		{
				$lf="backend";
				if ($_POST['rapid_lang']=="front")
				{
					$lf="frontend";
				}

				//Inhalt auslesen
				$file_content = implode("", file(ABS_PFAD . "/language/lang.de.inc.php"));
				//echo $file_content;

				//Spracheintrag erstellen $lang_dat['uebersicht']="Übersicht";
				$spracheintrag = '$lang_dat[\'plugin_' . $this->praefix . "_" . $this->
						name_org . '\']=\'' . $this->label . "';\n";

				//Den neuen Inhalt einbauen und den Platzhalter wieder setzen
				$file_content2 = eregi_replace('#start#', $spracheintrag . " #start#", $file_content);

				//Inhalt speichern
				$this->write_to_file("/language/lang.de.inc.php", $file_content2);

				

		}

		/**
		 * rapid_db_class::baue_neues_feld_ins_template_ein()
		 * Diese FUnktion baut das neue Feld ins Template ein
		 * @return void
		 */
		function baue_neues_feld_ins_template_ein($type = "normal")
		{
				#echo $type;
				//Dateiname rausholen
				$file = basename($_POST['template']);
				
				//HIer die Plugin Template Dateien einlesen
				if (empty($_POST['is_system']))
				{
				//Inhalt auslesen
				$file_content = implode("", file(ABS_PFAD . "/templates/" . $file));
				}

				#echo $file_content;
				#echo $this->cfeld;
				//NOrmal - dann einfach einfügen
				if ($type == "normal")
				{
						//Den neuen Inhalt einbauen und den Platzhalter wieder setzen
						$file_content2 = eregi_replace('#start#', $this->cfeld . "\n\n #start#", $file_content);
				}
				//Spezialfall fieldset
				if ($type == "fieldset")
				{
						//Den neuen Inhalt einbauen und den Platzhalter wieder setzen
						if (eregi("#start#</fieldset>", $file_content))
						{
								#$file_content2=eregi_replace('#start#',"",$file_content);
								$file_content2 = eregi_replace('#start#</fieldset>', "</fieldset>" . $this->
										cfeld, $file_content);
						}
						else
						{
								$file_content2 = eregi_replace('#start#', $this->cfeld, $file_content);
						}
				}
				#echo $type;
				//Spezialfall fieldset
				if ($type == "divblock")
				{
						//Den neuen Inhalt einbauen und den Platzhalter wieder setzen
						if (eregi("</div><!-- END DIV -->", $file_content))
						{
								$file_content2 = eregi_replace('#start#', "", $file_content);
								$file_content2 = eregi_replace('</div><!-- END DIV -->', "</div>\n" . $this->
										cfeld, $file_content2);
						}
						else
						{
								$file_content2 = eregi_replace('#start#', $this->cfeld, $file_content);
						}
				}
				#echo $file_content2;
				
				//Inhalt speichern
				$this->write_to_file("/templates/" .
						$file, $file_content2);

			
		}
		
		/**
     * Inhalt in eine Datei schreiben
     *
     * VORSICHT KEINE ÜBERPRÜFUNG
     *
     * @param text $inhalt
     * @param name $name
     * @return true or false
     */
    function write_to_file( $file = "", $inhalt = "",$open="w+" )
    {
        if ( empty ( $inhalt ) or empty ( $file ) )
        {
            return false;
        }
        else
        {
            
            $filex = ABS_PFAD . $file;
            $file = fopen( $filex, $open );
            @fwrite( $file, $inhalt );
            @fclose( $file );
            return true;
        }
    }

		/**
		 * rapid_db_class::alle_rapid_variablen_setzen()
		 * 
		 * @return void
		 */
		function alle_rapid_variablen_setzen()
		{
				//Namen raussuchen
				$this->plugin_name = $this->get_plugin_name();
				
				#$this->checked->praefix=eregi_replace(" ","_",$this->checked->praefix);
				#$this->checked->praefix_spezial=eregi_replace(" ","_",$this->checked->praefix_spezial);
				$_POST['rapid_name']=strtolower($_POST['rapid_name']);
				$_POST['rapid_name']=eregi_replace(" ","_",$_POST['rapid_name']);

				//Präfix übergeben
				$this->praefix = preg_replace("/[^a-zA-Z0-9_]_/", "", $_POST['praefix']);
				$this->praefix_spezial = preg_replace("/[^a-zA-Z0-9_]/", "", $_POST['praefix_spezial']);

				//Name der Variable
				$this->name_org = preg_replace("/[^a-z0-9_]/", "", $_POST['rapid_name']);
				$this->name = $this->praefix_spezial . "_" . $this->name_org;

				//LAbel des Eintrages
				$this->label = $this->db->escape(strip_tags(htmlentities($_POST['rapid_label'], ENT_QUOTES, "UTF-8")));

				//php_datei_name
				$this->php_datei_name = preg_replace("/[^a-zA-Z0-9_]/", "", $_POST['php_datei_name']);

				//NAme der DB
				$this->tb_name = preg_replace("/[^a-zA-Z0-9_]/", "", $_POST['tb_name']);
		}

		/**
		 * rapid_db_class::get_plugin_name()
		 * Holt den Plugin Namen raus
		 * @return void
		 */
		function get_plugin_name()
		{
				$split = split('/', $_POST['template']);
				return $split['0'];
		}

		/**
		 * rapid_db_class::get_rapid_content_liste()
		 * Aus den OPtionen ein Array erzeugen
		 * @param mixed $liste
		 * @return
		 */
		function get_rapid_content_liste($liste)
		{
				$liste_array = split("\n", $liste);
				return $liste_array;
		}

		/**
		 * rapid_db_class::create_text_feld()
		 * 
		 * @return void
		 */
		function create_text_feld()
		{
				$cfeld = '<label for="' . $this->name . '">{$plugin_' . $this->praefix . "_" . $this->
						name_org . '}{$plugin_error.'.$this->name.'} </label>';
				$cfeld .= "\n";
				#$cfeld .= '<br />';
				$cfeld .= "\n";
				$cfeld .= '<input type="text" name="' . $this->name . '" value="{$' . $this->name . '}" class="' . $this->name . '" id="' .
						$this->name . '"/>';
				$cfeld .= "\n";
				$cfeld .= '<br />';
				#echo $cfeld;
				$this->cfeld = $cfeld;
		}

		/**
		 * rapid_db_class::create_check_feld()
		 * Eine Checkbox erstellen
		 * @return void
		 */
		function create_check_feld()
		{
				$cfeld = "";
				$cfeld .= '<input type="checkbox" name="' . $this->name . '" value="1" {$checked' . $this->name .$value. '} class="' . $this->name . '" id="' . $this->name .
						'"/>';
				$cfeld .= '<label for="' . $this->name . '">{$plugin_' . $this->praefix . "_" .
						$this->name_org . '}{$plugin_error.'.$this->name.'} </label>';
				$cfeld .= "\n";
				$cfeld .= '<br />';
				#echo $cfeld;
				$this->cfeld = $cfeld;
		}

		/**
		 * rapid_db_class::create_image_file_feld()
		 * Ein Bild Upload Feld erstellen
		 * @return void
		 */
		function create_image_file_feld()
		{
				$cfeld = '<label for="' . $this->name . '">{$plugin_' . $this->praefix . "_" . $this->
						name_org . '}{$plugin_error.'.$this->name.'} </label>';
				$cfeld .= "\n";
				#$cfeld .= '<br />';
				$cfeld .= "\n";
				$cfeld .= '<input type="file" accept="image/*" name="' . $this->name .
						'" value="{$' . $this->praefix_spezial . '.0.' . $this->name . '}" class="' . $this->
						name . '" id="' . $this->name . '"/>';
				$cfeld .= "\n";
				$cfeld .= '<br />';
				#echo $cfeld;
				$this->cfeld = $cfeld;
				$this->cfeld = $cfeld;
		}

		function create_file_feld()
		{
				$cfeld = '<label for="' . $this->name . '">{$plugin_' . $this->praefix . "_" . $this->
						name_org . '}{$plugin_error.'.$this->name.'} </label>';
				$cfeld .= "\n";
			#	$cfeld .= '<br />';
				$cfeld .= "\n";
				$cfeld .= '<input type="file" accept="image/*" name="' . $this->name .
						'" value="{$' . $this->name . '}" class="' . $this->
						name . '" id="' . $this->name . '"/>';
				$cfeld .= "\n";
				$cfeld .= '<br />';
				#echo $cfeld;
				$this->cfeld = $cfeld;
				$this->cfeld = $cfeld;
		}


		/**
		 * rapid_db_class::create_select_feld()
		 * Erstelle eine Selectbox
		 * @return void
		 */
		function create_select_feld()
		{
				$optionen = $this->get_rapid_content_liste($_POST['rapid_content_list']);

				$cfeld = '<label for="' . $this->name . '">{$plugin_' . $this->praefix . "_" . $this->
						name_org . '}{$plugin_error.'.$this->name.'} </label>';
				$cfeld .= "\n";
			#	$cfeld .= '<br />';
				$cfeld .= "\n";
				$cfeld .= '<select name="' . $this->name . '" id="' . $this->name .
						'" size="1"/>';
				$cfeld .= '<option value="0">{$message_160}</option>';
				foreach ($optionen as $key => $value)
				{
						$key = $key + 1;
						$cfeld .= '<option {$selected' . $this->name .$value. '} value="' . $key . '">' . $value . '</option>';
				}
				$cfeld .= "</select>\n";
				$cfeld .= '<br />';
				#echo $cfeld;
				$this->cfeld = $cfeld;
		}

		/**
		 * rapid_db_class::create_select_feld()
		 * Erstelle eine Radiobox
		 * @return void
		 */
		function create_radio_feld()
		{
				//
		}

		/**
		 * rapid_db_class::create_password_feld()
		 * 
		 * @return void
		 */
		function create_password_feld()
		{
				$cfeld = '<label for="' . $this->name . '">{$plugin_' . $this->praefix . "_" . $this->
						name_org . '}{$plugin_error.'.$this->name.'} </label>';
				$cfeld .= "\n";
				#$cfeld .= '<br />';
				$cfeld .= "\n";
				$cfeld .= '<input type="password" name="' . $this->name . '" value="{$' . $this->
						praefix_spezial . '.0.' . $this->name . '}" class="' . $this->name . '" id="' .
						$this->name . '"/>';
				$cfeld .= "\n";
				$cfeld .= '<br />';
				#echo $cfeld;
				$this->cfeld = $cfeld;
		}

		/**
		 * rapid_db_class::create_hidden_feld()
		 * 
		 * @return void
		 */
		function create_hidden_feld()
		{

				$cfeld .= '<input type="hidden" name="' . $this->name . '" value="{$' . $this->
						praefix_spezial . '.0.' . $this->name . '}" class="' . $this->name . '" id="' .
						$this->name . '"/>';
				$cfeld .= "\n";
				#echo $cfeld;
				$this->cfeld = $cfeld;
		}

		/**
		 * rapid_db_class::create_text_feld()
		 * 
		 * @return void
		 */
		function create_textarea_feld()
		{
				$cfeld = '<label for="' . $this->name . '">{$plugin_' . $this->praefix . "_" . $this->
						name_org . '}{$plugin_error.'.$this->name.'} </label>';
				$cfeld .= "\n";
			#	$cfeld .= '<br />';
				$cfeld .= "\n";
				$cfeld .= '<textarea cols="30" rows="6" name="' . $this->name . '"  class="' . $this->name .
						'" id="' . $this->name . '">';
				$cfeld .= '{$' . $this->name . '}';
				$cfeld .= '</textarea>';
				$cfeld .= "\n";
				$cfeld .= '<br />';
				#echo $cfeld;
				$this->cfeld = $cfeld;
		}

		/**
		 * rapid_db_class::create_fieldset()
		 * 
		 * @return void
		 */
		function create_fieldset()
		{
				$cfeld = '<fieldset>';
				$cfeld .= "\n";
				$cfeld .= '<legend>{$plugin_' . $this->praefix . "_" . $this->name_org .
						'}</legend>';
				$cfeld .= "\n";
				$cfeld .= "\n";
				$cfeld .= '#start#';
				$cfeld .= '</fieldset><br />';
				$cfeld .= "\n";
				#echo $cfeld;
				$this->cfeld = $cfeld;
		}

		/**
		 * rapid_db_class::create_div()
		 * 
		 * @return void
		 */
		function create_div()
		{
				$cfeld = '<div id="' . $this->praefix . "_" . $this->name_org .
						'" class="divblock" >';
				$cfeld .= "\n";
				$cfeld .= '#start#';
				$cfeld .= '</div><!-- END DIV --><br />';
				$cfeld .= "\n";
				#echo $cfeld;
				$this->cfeld = $cfeld;
		}
		
		function create_leer()
		{
				$cfeld = '{$plugin_' . $this->praefix . "_" . $this->name_org . '}';
				#echo $cfeld;
				$this->cfeld = $cfeld;
		}
		
		function create_link()
		{
					$cfeld = '<a href="" id="' . $this->praefix . "_" . $this->name_org .
						'" class="132link" >{$plugin_' . $this->praefix . "_" . $this->name_org . '}</a>';
				#echo $cfeld;
				$this->cfeld = $cfeld;
		}

		/**
		 * rapid_db_class::create_h()
		 * EIne Überschrift kreieren
		 * @return void
		 */
		function create_h()
		{
				$cfeld = '<h2 id="' . $this->praefix . "_" . $this->name_org .
						'" class="h1" >{$plugin_' . $this->praefix . "_" . $this->name_org . '}</h2>';
				#echo $cfeld;
				$this->cfeld = $cfeld;
		}
		
		function create_h3()
		{
				$cfeld = '<h3 id="' . $this->praefix . "_" . $this->name_org .
						'" class="h1" >{$plugin_' . $this->praefix . "_" . $this->name_org . '}</h3>';
				#echo $cfeld;
				$this->cfeld = $cfeld;
		}
		
		function create_p()
		{
				$cfeld = '<p id="' . $this->praefix . "_" . $this->name_org .
						'" class="p1" >{$plugin_' . $this->praefix . "_" . $this->name_org . '}</p>';
				#echo $cfeld;
				$this->cfeld = $cfeld;
		}


} // End of class rapid_db_class
$rapid_db = new rapid_db_class();

?>