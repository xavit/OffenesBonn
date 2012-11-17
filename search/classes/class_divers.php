<?php

/**
 * Uebungen Klasse
 * 
 * Dazu gehören noch einige weitere Klassen 
 * die entsprechend eingebunden werden 
 * @package Papoo
 * @author Papoo Software
 * @copyright 2009
 * @version $Id$
 * @access public
 */
if ( strstr($_SERVER['PHP_SELF'],'class_diverse.php') ) die( 'You are not allowed to see this page directly' );

class class_divers
{

	/**
	 * class_uebungen::class_uebungen()
	 * 
	 * @return void
	 */
	function class_divers()
	{
		//Verbindung mit DB
		global $db;
		$this->db=&$db;
	}
	
		/**
	 * class_account::make_ausgabe()
	 * 
	 * @return void
	 */
	static function make_ausgabe($value="")
	{
		if (is_array($value))
		{
			foreach ($value as $key1=>$value1)
			{
				 if (!is_array($value1))
				 {
				 	$value[$key]=htmlentities(strip_tags($value1),ENT_QUOTES,'UTF-8');
				 }
			}
			return $value;
		}
		
		 $value=htmlentities(strip_tags($value),ENT_QUOTES,'UTF-8');
		 return $value=str_replace("xxbrxx","<br />",$value);
	}
	
	/**
	 * class_divers::make_ausgabe_html()
	 * 
	 * @param string $value
	 * @return
	 */
	function make_ausgabe_html($value="")
	{
		if (is_array($value))
		{
			foreach ($value as $key1=>$value1)
			{
				 $value[$key]=htmlentities(strip_tags($value1,'<p><h1><h2><h3><h4><h5><div><ul></ol><li><table><tr><td><span><br>'),ENT_QUOTES,'UTF-8');
			}
			return $value;
		}
		
		 $value=htmlentities(strip_tags($value,'<p><h1><h2><h3><h4><h5><div><ul></ol><li><table><tr><td><span><br>'),ENT_QUOTES,'UTF-8');
		 return $value=str_replace("xxbrxx","<br />",$value);
	}
	
	/**
	 * class_account::make_error()
	 * 
	 * @param string $value
	 * @return void
	 */
	function make_error($value="")
	{
		return $value='<div class="error">'.$value.'</div>';
	}
	
	/**
	 * class_divers::make_message()
	 * 
	 * @param string $value
	 * @return
	 */
	function make_message($value="")
	{
		return $value='<div class="message">'.$value.'</div>';
	}
	
	
	
	/**
	 * class_divers::format_date()
	 * 
	 * @param mixed $time
	 * @return
	 */
	function format_date($time)
	{
		if (empty($time))$time=time();
        return (date("d.m.y - G:i:s",$time));
	}
	
	/**
	 * class_divers::check_kontostand()
	 * DB_PRAEFIX
	 * @return void
	 */
	
	
	/**
	 * class_divers::replace()
	 * 
	 * @param mixed $inhalt
	 * @param mixed $temp_dat1
	 * @return
	 */
	static function replace($inhalt,$temp_dat1)
	{
		#print_r($temp_dat1);
		if (is_array($temp_dat1))
		{
			foreach ($temp_dat1 as $key=>$value)
			{
				$inhalt=str_replace('{$'.$key.'}',$value,$inhalt);
			}
		}
		#echo $inhalt;
		#exit();
		return $inhalt;
	}
	
	// Erzeugt aus $name einen "sicheren" Dateinamen (ohne Umlaute etc.). $name sollte OHNE Pfad übergeben werden.
    function sicherer_dateiname( $name )
    {
        $name_org = "";
        $name_neu = "";
        $endung = "";

        $ersetzungen = array (
            array ( "ä",
                "ae"
                ),
            array ( "Ä",
                "Ae"
                ),
            array ( "ö",
                "oe"
                ),
            array ( "Ö",
                "Oe"
                ),
            array ( "ü",
                "ue"
                ),
            array ( "Ü",
                "Ue"
                ),
            array ( "ß",
                "ss"
                ),
            array ( " ",
                "_"
                )
            );
        // Trennung in Name $name_org und Endung $endung
        if ( strpos( $name, "." ) )
        {
            for ( $i = ( strlen( $name ) - 1 ); $i >= 0; $i-- )
            {
                if ( $name
                    {
                        $i } != "." )
                $endung = $name
                {
                    $i }
                . $endung;
                else
                {
                    $endung = "." . $endung;
                    $name_org = substr( $name, 0, $i );
                    $i = -1;
                }
            }
        }
        else
            $name_org = $name;
        // Ersetzungen im Namen $name_org durchführen
        foreach ( $ersetzungen as $ersetzung )
        {
            $name_org = preg_replace( "/" . preg_quote( $ersetzung[0] ) . "/", $ersetzung[1], $name_org );
        }
        // Restliche "seltsame" Zeichen aus dem Namen $name_org entfernen und in neuem Namen $name_neu speichern
        for ( $i = 0; $i < strlen( $name_org ); $i++ )
        {
            if ( preg_match( '/[a-zA-Z0-9_]/', $name_org
                    {
                        $i }
                    ) )
            $name_neu .= $name_org
            {
                $i } ;
        }
        // Neuer Name mit Endung zusammensetzen und zurückgeben
        $name_neu .= $endung;
        return $name_neu;
    }
	
	/**
	 * class_divers::get_time()
	 * 
	 * @param integer $date
	 * @return
	 */
	function get_time($date=0)
	{
		$date_ar=explode(".",$date);
		return $time=@mktime(0, 0, 0, $date_ar['1'], $date_ar['0'], $date_ar['2']);
	}
	
	function make_Date($time=0)
	{
		
		return date("d.m.Y",$time);
	}
	
	
	/**
	 * class_divers::lese_dir()
	 * 
	 * @param string $dir
	 * @param string $ext
	 * @return
	 */
	static function lese_dir( $dir = "", $ext = "" )
    {
        #echo PAPOO_ABS_PFAD .$dir;
				if ( empty ( $dir ) )
        {
            return false;
        }
        else
        {
            if (is_dir(ABS_PFAD . $dir))
            {
	            $handle = @opendir( ABS_PFAD . $dir );
	            $i = 0;
	            while ( false !== ( $file = @readdir( $handle ) ) )
	            {
	                if ( $file == '.' or $file == '..'  or $file == '.DS_Store'  or $file == '.svn')
	                {
	                    continue;
	                }
	                if ( !empty ( $ext ) )
	                {
	                    #echo "-".$file."x";
											if ( !stristr(  $file,$ext ) )
	                    {
	                      # echo "NO";echo "<br />";
												  continue;
	                    }
	                }
	                $result[$i]['name'] = $file;
	                // if (empty($file))echo "e";
	                // if (is_writeable(PAPOO_ABS_PFAD.$dir."/".$file))echo "W";
	                $result[$i]['schreib'] = is_writeable( ABS_PFAD . $dir . "/" . $file );
	
	                $i++;
	            }
	            // print_r($result);
							@usort($result, array ("diverse_class", "cmp"));
	            return $result;
            }
        }
    }
    
    
    /**
     * class_divers::lese_dir_rec()
     * 
     * @param string $dir
     * @param string $ext
     * @return
     */
    function lese_dir_rec( $dir = "", $ext = "" )
    {
        #echo PAPOO_ABS_PFAD .$dir;
				if ( empty ( $dir ) )
        {
            return false;
        }
        else
        {
            if (is_dir(ABS_PFAD . $dir))
            {
	            $handle = @opendir( ABS_PFAD . $dir );
	            $i = 0;
	            while ( false !== ( $file = @readdir( $handle ) ) )
	            {
	                if ( $file == '.' or $file == '..'  or $file == '.DS_Store'  or $file == '.svn')
	                {
	                    continue;
	                }
	                if ( !empty ( $ext ) )
	                {
	                    #echo "-".$file."x";
											if ( !stristr(  $file,$ext ) )
	                    {
	                      # echo "NO";echo "<br />";
												  continue;
	                    }
	                }
	                echo $file;
	                if (is_dir(ABS_PFAD . $dir."/".$file))
	                {
	                	$result[$file][$i] = class_divers::lese_dir_rec($file);
	                }
	                else
	                {
	                	$result[$i]['name'] = $file;
	                }
	                // if (empty($file))echo "e";
	                // if (is_writeable(PAPOO_ABS_PFAD.$dir."/".$file))echo "W";
	                $result[$i]['schreib'] = is_writeable( ABS_PFAD . $dir . "/" . $file );
	
	                $i++;
	            }
	            // print_r($result);
							@usort($result, array ("diverse_class", "cmp"));
	            return $result;
            }
        }
    }
  
     /**
      * class_divers::write_to_file()
      * 
      * @param string $file
      * @param string $inhalt
      * @param string $open
      * @return
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
     * class_divers::do_log()
     * 
     * @param mixed $logart
     * @param mixed $logdetails
     * @return void
     */
    function do_log( $logart, $logdetails )
    {
        // check ob Dateien ok
        //$logok = $this->check_log();
        // Dateien sind ok, dann loggen
        if ( $logok )
        {
            // Die Logarten durchgehen
            switch ( $logart )
            {
                // Email
                case "email" :
                    // Log erzeugen
                    #$this->log_email( $logdetails );
                    break;
                // Login
                case "login" :
                    // Log erzeugen
                   // $this->log_login( $logdetails );
                    break;
            }
        }
    }
    /**
     * class_divers::log_login()
     * 
     * @param mixed $logdetails
     * @return void
     */
    function log_login( $logdetails )
    {
        // Logfile erstellen
        //$this->check_filesize($this->filename. "login_log.log");
        $erstellen = fopen( ABS_PFAD_ROOT . "/mylogs/login_log.log", "a" );
        $filename2 = ABS_PFAD_ROOT . "/mylogs/login_log.log";
        // Zuweisen der HTTP Header Daten IP etc.
        $inhalt = date( "d.m.Y - H:i:s; " );
        $inhalt .= $_SERVER['REMOTE_ADDR'];
        $inhalt .= "; ";
        foreach ( $logdetails as $detail )
        {
            $inhalt .= "" . $detail['username'];
            $inhalt .= "; ";
            $inhalt .= "" . $detail['userok'];
            $inhalt .= "; ";
            $inhalt .= "" . $detail['exist'];
            $inhalt .= "; ";
        }
        $inhalt .= "; ";
        $inhalt .= $_SERVER['HTTP_USER_AGENT'];
        $inhalt .= "\r";
        // Inhalte aus den Logdetails zuweisen
        $file = fopen( $filename2, "a" );
        @fwrite( $file, $inhalt );
        @fclose( $file );
    }
    
    /**
     * class_divers::make_log()
     * 
     * @param mixed $filename
     * @param mixed $content
     * @return void
     */
    function make_log($filename,$content)
    {
    	$filename   = ABS_PFAD. "/mylogs/".$filename;
    	$inhalt = date( "d.m.Y - H:i:s; " );
    	$inhalt .= $_SERVER['REMOTE_ADDR'];
      $inhalt .= "; ";
      $inhalt .= $content;
      $inhalt .= "; ";
      $inhalt .= $_SERVER['HTTP_USER_AGENT'];
      $inhalt .= "\r";
      $file = fopen( $filename, "a" );
      @fwrite( $file, $inhalt );
      @fclose( $file );
    }
    
  /**
   * class_divers::create_agent()
   * 
   * @return
   */
  function create_agent()
	{
		//USer Agent Switcher
    	$ua = array('Mozilla','Opera','Microsoft Internet Explorer','ia_archiver');
    
    	//BS System Switcher
    	$op = array('Windows','Windows XP','Linux','Windows NT','Windows 2000','OSX');
    
    	//UA Versionsnummer
    	$agent  = $ua[rand(0,3)].'/'.rand(1,8).'.'.rand(0,9).' ('.$op[rand(0,5)].' '.rand(1,7).'.'.rand(0,9).'; de-DE;)';
    	
    	return $agent;
	}
	
	
	
	/**
	 * class_divers::http_request_open()
	 * 
	 * @param mixed $url
	 * @return void
	 */
	function http_request_open($url,$timeout=10)
	{
		$ch = curl_init( $url );
		#echo $url;
		curl_setopt( $ch, CURLOPT_TIMEOUT, $timeout );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_HEADER, 0 );
		curl_setopt( $ch, CURLOPT_USERPWD,"openboris:openboris2012!!"); 	
		##curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1 );
		curl_setopt( $ch, CURLOPT_USERAGENT,
			"Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.8.1.4) Gecko/20070515 Firefox/2.0.0.4" );
	
    $curl_ret = curl_exec( $ch );
		curl_close( $ch );
		#print_r($curl_ret);
		return $curl_ret;
	}
	
	function https_request_open($url,$timeout=10)
	{
		$ch = curl_init( $url );
		curl_setopt( $ch, CURLOPT_TIMEOUT, $timeout );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_HEADER, 1 );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
		#curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1 );
		curl_setopt( $ch, CURLOPT_USERAGENT,
			"Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.8.1.4) Gecko/20070515 Firefox/2.0.0.4" );
	
    $curl_ret = curl_exec( $ch );
		curl_close( $ch );
		return $curl_ret;
	}
	
	/**
	 * class_domain::isValidURL()
	 * 
	 * @param mixed $url
	 * @return
	 */
	function isValidURL($url)
	{
		/**
		$urlregex = "^(https?|ftp)\:\/\/([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?[a-z0-9+\$_-]+(\.[a-z0-9+\$_-]+)*(\:[0-9]{2,5})?(\/([a-z0-9+\$_-]\.?)+)*\/?(\?[a-z+&\$_.-][a-z0-9;:@/&%=+\$_.-]*)?(#[a-z_.-][a-z0-9+\$_.-]*)?\$";
		if (eregi($urlregex, $url)) 
		{
			echo "good";
		} 
		else 
		{
			echo "bad";
			}
		exit();
		*/
		#debug::print_d(filter_var($url, FILTER_VALIDATE_URL));
		#exit();
		if (filter_var($url, FILTER_VALIDATE_URL) !== false) {
			return true;
			// $url contains a valid URL
			}
		return false;
		if(preg_match("/^(http(s?):\\/\\/|ftp:\\/\\/{1})((\w+\.)+)\w{2,}(\/?)$/i", $url))
		{
			return true;
		}
		return false;
	}
	
}


?>