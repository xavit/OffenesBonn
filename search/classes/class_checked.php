<?php

/**
 * #####################################
 * # Offenes Bonn                						  	   	   #
 * # (c) Dr. Carsten Euwens 2012       				   #
 * # Authors: Carsten Euwens                              #
 * # http://www.papoo.de                                   #
 * # Internet                                                         #
 * #####################################
 * # PHP Version >5                                             #
 * #####################################
 */

/**
 * Abstrakte Klasse um alle Variblen post/get anzusprechen
 * In dieser Klasse werden alle Variablen eingebunden die von auﬂen kommen. D.h.
 * $_POST und $_GET werden als $this->checked->variablen_name eingebunden.
 *  $Id: variables_class.php 3296 2011-02-28 12:37:56Z carsten $ 
 */

class checked_class {
  /*
	* Alle Variablen aus $_POST und $_GET werden durchgeloopt und zugewiesen
	*/
  function checked_class()
  {
    /*
		* Alle $_GET durchloopen die reinkommen
		* Die Variablen werden ¸berpr¸ft ob sie numerisch, string oder Array sind
		*/
    // print_r($_GET);
    reset($_GET);
    while (list ($key, $val) = each($_GET)) {
      /*
			* Wenn der Inhalt numerisch ist einfach zuweisen
			*/
      if (is_numeric($val)) {
        $this->$key = $val;
        $_GET[$key]=$val;
      }
      /*
			* Wenn der Inhalt ein String ist String ‹berpr¸fung duchf¸hren
			* d.h. Daten werden Datenbankishcer escaped.
			* striptags kann nicht ausgef¸hrt werden, da mitunter auch HTML in der
			* Variable sein kann
			*/
      elseif (is_string($val)) {
        $this->$key = $this->check_xss($key, $val);;
        $_GET[$key]=$this->check_xss($key, $val);
      }
      /*
			* Wenn der Inhalt ein Array ist zuweisen, ‹berpr¸fung muﬂ dann
			* sp‰ter stattfinden
			*/
      elseif (is_array($val)) {
        $this->$key = "0";
        $_GET[$key]="0";
      }
      /*
			* Irgendwas unbekanntes
			*/
      else {
        $this->$key = "null";
        $_GET[$key]="null";
      }
    }

    /*
		* Alle $_POST durchloopen die reinkommen
		* Die Variablen werden ¸berpr¸ft ob sie numerisch, string oder Array sind
		*/
    // print_r($_POST);
    reset($_POST);
    while (list ($key, $val) = each($_POST)) {
      /*
			* Wenn der Inhalt numerisch ist einfach zuweisen
			*/
      if (is_numeric($val)) {
        $this->$key = $val;
        $_POST[$key]=$val;
      }
      /*
			* Wenn der Inhalt ein String ist String ‹berpr¸fung duchf¸hren
			* d.h. Daten werden Datenbankishcer escaped.
			* striptags kann nicht ausgef¸hrt werden, da mitunter auch HTML in der
			* Variable sein kann
			*/
      elseif (is_string($val)) {
      	
        $this->$key = $this->check_xss_post($key, $val);
        #$_POST[$key]=$this->$key;
      }
      /*
			* Wenn der Inhaltein Array ist zuweisen, ‹berpr¸fung muﬂ dann
			* sp‰ter stattfinden
			*/
      elseif (is_array($val)) {
        $this->$key = $this->check_xss_array($key, $val);
        #$_POST[$key]=$this->$key;
      }
      /*
			* Irgendwas unbekanntes
			*/
      else {
        $this->$key = "null";
        #$_POST[$key]= "null";
      }
    }

    if (get_magic_quotes_gpc()) {
      // echo "MagicQuotes aktiv";
      // print_r($this);
      $this->remove_magicquotes();
      // print_r($this);
    }
    // Nochmal checken
    $this->do_check();
    #print_r($this);
    //Variablennamen mitloggen
    $this->do_logg_var();
    
  }
  /**
   * Variablennamen mitloggen
   */
  function do_logg_var()
  {
  	
  }
  /**
   * Zwingende ‹berpr¸fungen auf int
   */
  function do_check()
  {
    // vorgegebene auf Numerisch checken
    $check = array('menuid', 'reporeid', 'style', 'reporeid_print', 'forumid', 'rootid', 'msgid', 'selmenuid', 'page', 'reportage', 'image_id', 'id', 'video_id', 'cat_id', 'userid', 'gruppeid', 'mod_style_id', 'cform_id', 'style_id');
    foreach ($check as $key=>$var) {
      if (!empty($this->$var)) {
        if (!is_numeric($this->$var)) {
          $this->$var = "";
        }
      }
    }
    if (!empty($this->template))
    {
    	$this->template=str_ireplace("\.\.","",$this->template);
    }
    
  }
  // print_r($this);
  /**
   * GET Strings auf xss checken
   */
  function check_xss($key, $val)
  {
    $val = $this->make_save_text($val);
    // $this->$key=$val;
    return $val;
  }
  /**
   * POST Strings auf xss checken
   */
  function check_xss_post($key, $val)
  {
    if ($key != "html" && $key != "myfile" && $key!="banner_code" && $key!="mv_template_one" && $key!="vhs_string" &&$key!="wartungstext" && $key!="ctempl_content" && $key!="freiemodule_code" && $key!="einstellungen_lang_conversion_code_js" && $key!="usefulservices_analytics_key") {
      $val = $this->make_save_text_post($val);
    }
    // $this->$key=$val;
    return $val;
  }
  /**
   * HTML etc. sicher entfernen oder escapen
   */
  function make_save_text($text = "")
  {
    $text = strip_tags($text);
    $text = $this->html2txt($text);
	$text = $this->clean_db($text);
    return $text;
  }
  /**
  * DB Hack Versuche ausfiltern
  */
  function clean_db($search) {
    //$search auf unerlaubte Zeichen √ºberpr√ºfen und evtl. bereinigen
    $search = trim($search);
    $remove = "<>'\"%*\\";
    for ($i = 0; $i < strlen($remove); $i ++)
    $search = str_replace(substr($remove, $i, 1), "", $search);
    return $search;
  }
  /**
   * Scripts etc. sicher entfernen oder escapen
   */
  function make_save_text_post($text = "")
  {
    $text = $this->html2txt_post($text);
    return $text;
  }
  /**
   * HTML als Text machen
   *
   * @param unknown_type $document
   * @return unknown
   */
  function html2txt($document)
  {
    $search = array('@<script[^>]*?>.*?</script>@si', // Strip out javascript
      '@<[\\/\\!]*?[^<>]*?>@si', // Strip out HTML tags
      '@<style[^>]*?>.*?</style>@siU', // Strip style tags properly
      '@<![\\s\\S]*?--[ \\t\\n\\r]*>@' // Strip multi-line comments including CDATA
      );
    $text = preg_replace($search, '', $document);
    return $text;
  }
  /**
   * Scripts als Text machen
   *
   * @param unknown_type $document
   * @return unknown
   */
  function html2txt_post($document)
  {
    $search = array('@<script[^>]*?>.*?</script>@si', // Strip out javascript
      );
    $text = preg_replace($search, '', $document);
    return $text;
  }

  /**
   * ¸berpr¸fen ob ein Eintrag irgendwo im Array ist
   */

  function check_xss_array($okey = "", $array)
  {
    $check = array('inhalt', 'teaser', '1', '2', '3', '4', '5', '6', '7');
    $neuar = array();

    if (!empty ($array)) {
      foreach ($array as $key => $item) {
        if (!is_array($item)) {
          if (!in_array($key, $check)) {
            $neuar[$key] = $this->check_xss_post($key, $item);
          } else {
            $neuar[$key] = $item;
          }
        } else {
          $neuar[$key] = $this->check_xss_array($key, $item);
        }
      }
    }
    // echo $array;
    return $neuar;
    // print_r($this->$array);
  }
  // Bei aktivem MagicQuotes die Escape-Zeichen entfernen
  // ===================================================================
  function remove_magicquotes()
  {
    // Elemente durchgehen
    if (!empty($this)) {
      foreach($this as $name => $wert) {
        // Array-Element in die Rekursion schicken
        if (is_array($wert)) $this->$name = $this->remove_magicquotes_array($wert);
        // sonstige Werte mit Stripslasehs bearbeiten
        else $this->$name = stripslashes($wert);
      }
    }
  }
  // Rekursive Funktion f¸r die Abarbeitung von Arrays.
  function remove_magicquotes_array($data)
  {
    $temp_array = array();
    // Elemente durchgehen
    if (!empty($data)) {
      foreach($data as $name => $wert) {
        // Array-Element in die Rekursion schicken
        if (is_array($wert)) $temp_array[$name] = $this->remove_magicquotes_array($wert);
        // sonstige Werte mit Stripslasehs bearbeiten
        else $temp_array[$name] = stripslashes($wert);
      }
    }

    return $temp_array;
  }
}
// Hiermit wird die Klasse initialisiert und kann damit sofort ¸berall benutzt werden.
$checked = new checked_class();

?>