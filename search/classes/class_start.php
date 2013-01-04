<?php

/**
 * @package Papoo
 * @author Papoo Software
 * @copyright 2009
 * @version $Id$
 * @access public
 */
if ( strstr($_SERVER['PHP_SELF'],'class_start.php') ) die( 'You are not allowed to see this page directly' );

class class_start
{

	/**
	 * class_uebungen::class_start()
	 * 
	 * @return void
	 */
	function class_start()
	{
		//Verbindung mit DB
		global $db;
		$this->db=&$db;
		
		$this->make_start();
	}
	
	/**
	 * class_start::make_start()
	 * 
	 * @return void
	 */
	function make_start()
	{
		global $template_dat;
		global $inhalt;
		global $checked;
		
		//Template einlesen
		$inhalt = implode("", file(ABS_PFAD."/templates/index.html"));

		//Auf aktiv schalten
		$template_dat['current_ueber']="current";
		$template_dat['start_suche']=class_divers::make_ausgabe($checked->start_suche);
		
		$page=$checked->page-1;
		if ($page<0)$page=0;
		$template_dat['start_ol']=$page*20+1;
		
		if (!empty($checked->dokument_id))
		{
			$inhalt = implode("", file(ABS_PFAD."/templates/index_single.html"));   
			//Daten suchen
			$search=$this->get_dokument();
		     
			//Daten ausgeben
			$daten=$this->create_ausgabe_single($search);
            
            //Kommentare verarbeiten
            $this->do_comment();
		
			$template_dat['search_ergebniss']=$daten;
            
            
            
		}
		else
		{
			//Daten suchen
			$search=$this->make_search();
		#debug::print_d($search);
			//Daten ausgeben
			$daten=$this->create_ausgabe($search);
		
			$template_dat['search_ergebniss']=$daten;
		}
		
			//Rapid Dev inin
			$this->make_rapid();
		
	}
	
    
    private function do_comment()
    {
        global $checked;
        global $db;
        global $template_dat;
            
        //Wenn eintragen
        if (!empty($checked->uebermittelformular))
        {
            //checken ob sinnvolle Daten da drin sind...
            $this->check_comment();
            
            //Daten sind ok
            if ($this->check_comment_form=="ok")
            {
                $checked->neuvorname=strip_tags($checked->neuvorname);
                $checked->formthema=strip_tags($checked->formthema);
                $checked->inhalt=strip_tags($checked->inhalt);
                //In DB eintragen
                $sql=sprintf("INSERT INTO %s SET
                            userid='11',
                            forumid='20',
                            thema='%s',
                            messagetext='%s',
                            zeitstempel='%s',
                            msg_frei='1',
                            username_guest='%s',
                            comment_article='%s'
                            ",
                            DB_PRAEFIX."papoo_message",
                            $db->escape($checked->formthema),
                            $db->escape($checked->inhalt),
                            time(),
                            $db->escape($checked->neuvorname),
                            $db->escape($checked->dokument_id)
                            
                            );
                 $db->query($sql);
            }
            
        }
        
    }
    /**
     * 
     */
    private function check_comment()
    {
        global $checked;
        global $db;
        global $template_dat;
        if (empty($checked->neuvorname))
        {
            $template_dat['error_neuvorname']="ok";
            $template_dat['error']="ok";
            
        }
        if (empty($checked->formthema))
        {
            $template_dat['error_formthema']="ok";
            $template_dat['error']="ok";
            
        }
        if (empty($checked->inhalt))
        {
            $template_dat['error_inhalt']="ok";
            $template_dat['error']="ok";
            
        }
        
        if (empty($template_dat['error']))
        {
            $this->check_comment_form="ok";
            
        }
        
    }
    
	private function get_dokument()
	{
		global $checked;
		global $db;
		global $template_dat;
		global $weiter;
		
		if (!empty($checked->dokument_id))
		{
			
			$template_dat['api_url']= API_URL."/api.php?dokument_id=".$checked->dokument_id;
			//Daten bei Api abholen
			$data=class_divers::http_request_open(API_URL."/api.php?dokument_id=".$checked->dokument_id);
			//debug::print_d(json_decode($data,true));
			return json_decode($data,true);
		}
	}
	
	/**
	 * make_search function.
	 * 
	 * @access private
	 * @return void
	 */
	private function make_search()
	{
		global $checked;
		global $db;
		global $template_dat;
		global $weiter;
		
		if (!empty($checked->start_suche))
		{
			$checked->start_suche=urlencode($checked->start_suche);    
			//Anzahl rausholen
			$url= API_URL."/api.php?search=".$checked->start_suche."&count=1";
			$data=class_divers::http_request_open($url);
            //debug::print_d($data);
			#debug::print_d(json_decode($data,true));

			$weiter->result_anzahl=json_decode($data,true);
            #debug::print_d($weiter->result_anzahl);
			if (!is_numeric($weiter->result_anzahl))
			{
				$weiter->result_anzahl=0;
			}
			if ($weiter->result_anzahl>0)
			{
				$template_dat['result_anzahl']=$weiter->result_anzahl;
				$weiter->weiter_link='./index.php?start_suche='.$checked->start_suche;
				$weiter->do_weiter("search");
				$weiter->make_html_links();
		
			
				$template_dat['api_url']= API_URL."/api.php?search=".$checked->start_suche."&page=".$checked->page;
				//Daten bei Api abholen
				$data=class_divers::http_request_open(API_URL."/api.php?search=".$checked->start_suche."&page=".$checked->page);
				//debug::print_d(json_decode($data,true));
				return json_decode($data,true);
			}
			return false;
			
			
		}

	}
	/**
	 * create_ausgabe function.
	 * 
	 * @access private
	 * @return void
	 */
	private function create_ausgabe($daten)
	{
		global $lang_dat;
		//$daten=$this->sub_sort($daten);
		//debug::print_d($daten);
		if (is_array($daten))
		{
			foreach ($daten as $key=>$value)
			{	
				$liste.='<li class="result_item"><a href="./index.php?dokument_id='.class_divers::make_ausgabe($value['ob_boris_id_int']).'"><span class="titel">'.class_divers::make_ausgabe($value['ob_kurz_betreff']).'</span></a>';
				
				$liste.='<br /><span class="sub_title">Dokument Nr. '.class_divers::make_ausgabe($value['ob_boris_id']).' vom '.class_divers::make_ausgabe($value['ob_datum']).' - gescannt am '.class_divers::format_date($value['ob_timestamp_erstellung_ob']).'</span></li>';	
			}
		}
		return $liste;
	}
	/**
	 * create_ausgabe_single function.
	 * 
	 * @access private
	 * @param mixed $daten
	 * @return void
	 */
	private function create_ausgabe_single($daten)
	{
		global $lang_dat;
		//$daten=$this->sub_sort($daten);
		/**
         * <ul class="thumbnails">
  <li class="span4">
    <a href="#" class="thumbnail">
      <img src="http://placehold.it/300x200" alt="">
    </a>
  </li>
  ...
</ul>
         * */
         global $template_dat;
		if (is_array($daten))
		{
			foreach ($daten as $key=>$value)
			{
				foreach ($value as $key1=>$value1)
                {    
                    $template_dat[$key1]    = $value1;
                } 
				
				$template_dat['single_dok_header']='<h2>'.class_divers::make_ausgabe($value['ob_kurz_betreff']).'</h2>';
				$liste.='Art des Dokuments: '.class_divers::make_ausgabe($value['ob_formular_art']).'<br />';
				$liste.='Erstellt am: '.class_divers::format_date($value['ob_timestamp_erstellung_ob']).'<br />';
				$liste.='Quelle: <a href="http://www2.bonn.de/bo_ris/ris_sql/'.class_divers::make_ausgabe($value['ob_meta_link']).'">Ratsinfosystem Stadt Bonn</a></p>';
				$liste.='<h2>Dokumente</h2>';
				
				$thumbnails=$this->get_thumbnails($value['ob_thumbnail'],$value['ob_pdf_file_url']);
				$liste.='<ul class="thumbnails">'.$thumbnails.'</ul>';
				$liste.='<div class="subtext"> '.class_divers::make_ausgabe(substr($value['ob_id_data_text'],0,300)).'</div>';
				$liste.='<div class="download"><a href="'.SCRAPER_URL.class_divers::make_ausgabe($value['ob_pdf_file_url']).'">Download Dokument</a></div>';
                
                $liste.='<h2>Kommentare</h2>';
                if (!empty($value['comments']))
                {
                   foreach ($value['comments'] as $ck=>$cv)
                   {
                       
                       $text.='<div class="media"><div class="media-body">
                            <h4 class="media-heading">'.class_divers::make_ausgabe($cv['thema']).'</h4>'.class_divers::make_ausgabe($cv['messagetext']).'<br /><small>Von '.$cv['username_guest'].' am  '.class_divers::format_date($cv['zeitstempel']).'</small>                        </div></div>';
                   }
                       
                  $liste.=$text;  

                    
                }
			}
		}
		return $liste;
	}
	/**
	 * get_thumbnails function.
	 * 
	 * @access private
	 * @param string $thumbnails. (default: "")
	 * @return void
	 */
	private function get_thumbnails($thumbnails="",$link="")
	{
		$thumbs=unserialize($thumbnails);
		//debug::print_d($thumbs);
		$img_html="";
				if (is_array($thumbs))
		{
			foreach ($thumbs as $key=>$value)
			{
				if (!stristr($value,"thumbnail"))
				{
					$im1=explode("scrape",$value);
					//debug::print_d($im1);
					//$im1['1']=str_ireplace(".jpg",".jpg",$im1['1']);
					$scraper_url=SCRAPER_URL;
					$img_html.='<li  class="span3"><a  class="thumbnail" href="'.SCRAPER_URL.class_divers::make_ausgabe($link).'"> <img src="'.$scraper_url.$im1['1'].'" /></a></li>';
				}
			}
		}
		
		return $img_html;
	}

	/**
	 * class_start::make_rapid()
	 * 
	 * @return void
	 */
	function make_rapid()
	{
		global $template_dat;
		//Rapid Template setzen
		$template_dat['rapid_template']="index.html";
		$template_dat['rapid_tbname']="openboris_index";
		$template_dat['rapid_feld_praefix']="start";
	}
}
//Class INI
$dashboard_class= new class_start();
?>