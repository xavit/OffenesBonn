<?php 
/**
 * class_methods class.
 * Diverse Methoden
 */
class class_methods
{

	/**
	 * class_methods function.
	 * 
	 * @access public
	 * @return void
	 */
	function class_methods()
	{}
	
	
	/**
	 * get_site function.
	 * Wird rausgeholt und direkt utf8 kodiert
	 * @access public
	 * @param string $url. (default: "")
	 * @param string $url. (default: "60")
	 * @return void
	 */
	public function get_site($url="", $timeout="60")
	{
		$ch = curl_init( $url );
		curl_setopt( $ch, CURLOPT_TIMEOUT, $timeout );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_HEADER, 0 );
		//curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1 );
		curl_setopt( $ch, CURLOPT_USERAGENT,
			"Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.8.1.4) Gecko/20070515 Firefox/2.0.0.4" );
	
    	$curl_ret = (curl_exec( $ch ));
		curl_close( $ch );
		return $curl_ret;
	
	}
	/**
	 * get_clean_text function.
	 * 
	 * @access public
	 * @param string $string. (default: "")
	 * @return void
	 */
	public function get_clean_text($string="")
	{
		$string=strip_tags($string);
		$string=html_entity_decode($string);
		$string=str_replace("\n"," " ,$string);
		$string=str_replace("\r"," ",$string);
		$string=str_replace("\t"," ",$string);
		$string=str_replace("  "," ",$string);
		$string=str_replace("  "," ",$string);
		$string=str_replace("  "," ",$string);
		$string=str_replace("  "," ",$string);
		$string=str_replace("  "," ",$string);
		
		$string=trim($string);
		
		return $string;
	}
	/**
     * Inhalt in eine Datei schreiben
     *
     * VORSICHT KEINE ÜBERPRÜFUNG
     * @param Dateiname rel zu PAPOO_ABS_PFAD $file
     * @param text $inhalt
     * @param name $name
     * @return true or false
     */
   public function write_to_file( $file = "", $inhalt = "",$open="w+" )
   {
        if ( empty ( $inhalt ) or empty ( $file ) )
        {
            return false;
        }
        else
        { 
          $filex =  $file;
          $filex=str_replace("//","/",$filex);
          $file = fopen( $filex, $open );
          @fwrite( $file, $inhalt );
          @fclose( $file );
          return true;
        }
    }
	
}
?>