<?php

/**
 * @package Papoo
 * @author Papoo Software
 * @copyright 2009
 * @version $Id$
 * @access public
 */
if ( strstr($_SERVER['PHP_SELF'],'class_api.php') ) die( 'You are not allowed to see this page directly' );

class class_api
{

	/**
	 * class_uebungen::class_uebungen()
	 * 
	 * @return void
	 */
	function class_api()
	{
		//Verbindung mit DB
		global $db;
		$this->db=&$db;
		
		$this->make_api();
	}
	
	/**
	 * class_api::make_dashboard()
	 * 
	 * @return void
	 */
	function make_api()
	{
		global $template_dat;
		global $inhalt;
		global $checked;
		
        //debug::print_d($checked);
		if (!empty($checked->count))
		{
			print_r(json_encode($this->get_search_count()));
			exit();
		}
		
				//Daten zusammentragen
		$this->get_data();
		

	}
	/**
	 * get_search_count function.
	 * 
	 * @access private
	 * @return void
	 */
	private function get_search_count()
	{
		global $checked;
		global $db;
		$sql=sprintf("SELECT COUNT(ob_id) FROM %s 
								WHERE 
								ob_ausschuss LIKE '%s' 
								OR ob_id_data_text 	LIKE '%s'
								OR ob_geo_strasse 	LIKE '%s'
								OR ob_geo_ortsteil 	LIKE '%s'
								OR ob_pdf_text 	LIKE '%s'
								OR ob_partei 	LIKE '%s'	
								OR ob_kurz_betreff LIKE '%s'
                                OR ob_zugriffsart LIKE '%s'
                                OR ob_antragstellering LIKE '%s'
                                							
								LIMIT 20",
								"openboris_basis",
								"%".$db->escape($checked->search)."%",
								"%".$db->escape($checked->search)."%",
								"%".$db->escape($checked->search)."%",
								"%".$db->escape($checked->search)."%",
								"%".$db->escape($checked->search)."%",
								"%".$db->escape($checked->search)."%",
                                "%".$db->escape($checked->search)."%",
                                "%".$db->escape($checked->search)."%",
								"%".$db->escape($checked->search)."%"
								
								);

		return $db->get_var($sql);
			
	}
	
	/**
	 * get_data function.
	 * 
	 * @access private
	 * @return void
	 */
	private function get_data()
	{
		global $checked;
		global $db;
		global $weiter;
	
		if (!empty($checked->search))
		{
			
			//Weiter Daten initialisieren
			$weiter->result_anzahl=$this->get_search_count();
			$weiter->do_weiter("teaser");
			//Dann mit Limit die nächsten
			
			$weiter->make_limit(20);

			if ($checked->xpage=="all")
			{
			    $weiter->make_limit(20000);
		
			   }
			//Daten durchsuchen
			$sql=sprintf("SELECT * FROM %s 
								WHERE 
								ob_ausschuss LIKE '%s' 
                                OR ob_id_data_text  LIKE '%s'
                                OR ob_geo_strasse   LIKE '%s'
                                OR ob_geo_ortsteil  LIKE '%s'
                                OR ob_pdf_text  LIKE '%s'
                                OR ob_partei    LIKE '%s'   
                                OR ob_kurz_betreff LIKE '%s'
                                OR ob_zugriffsart LIKE '%s'
                                OR ob_antragstellering LIKE '%s'							
								%s",
								"openboris_basis",
								"%".$db->escape($checked->search)."%",
								"%".$db->escape($checked->search)."%",
								"%".$db->escape($checked->search)."%",
								"%".$db->escape($checked->search)."%",
								"%".$db->escape($checked->search)."%",
								"%".$db->escape($checked->search)."%",
								"%".$db->escape($checked->search)."%",
                                "%".$db->escape($checked->search)."%",
                                "%".$db->escape($checked->search)."%",
								$weiter->sqllimit
								
								);
				$result = $this->db->get_results($sql);
				//debug::print_d($result);
				print_r(json_encode($result));
		}
		
		
		if (!empty($checked->dokument_id))
		{
			$checked->dokument_id=preg_replace("/[^0-9]/","",$checked->dokument_id);
			
			//Daten durchsuchen
			$sql=sprintf("SELECT * FROM %s 
								WHERE 
								ob_boris_id_int = '%s'								
								",
								"openboris_basis",
								$db->escape($checked->dokument_id)
								
								);
			$result = $this->db->get_results($sql,ARRAY_A);
            
            //Dann die zugehörigen KOmmentare
            $sql=sprintf("SELECT * FROM %s 
                                WHERE 
                                comment_article = '%s'                              
                                ",
                                "openboris_papoo_message",
                                $db->escape($checked->dokument_id)
                                
                                );
            $result2 = $this->db->get_results($sql,ARRAY_A);
            
            $result['0']['comments']=$result2;
            
            //Dann weitere zugehörige Dokumente
            $dok_id=substr($checked->dokument_id,0,7);
            
            $sql=sprintf("SELECT ob_boris_id_int, ob_kurz_betreff FROM %s 
                                WHERE 
                                ob_boris_id  LIKE '%s'                              
                                ",
                                "openboris_basis",
                                "%".$db->escape($dok_id)."%"
                                
                                );
            $result3 = $this->db->get_results($sql,ARRAY_A);
            
            $result['0']['sonstige_dokumente']=$result3;
            
			//debug::print_d($result);
			print_r(json_encode($result));
		}
	}

	
	
	
}

$api_class= new class_api();

?>