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
if ( stristr( $_SERVER['PHP_SELF'],'class_debug.php') ) die( 'You are not allowed to see this page directly' );

class debug
{

	/**
	 * class_uebungen::class_uebungen()
	 * 
	 * @return void
	 */
	function debug()
	{
		//Verbindung mit DB
		global $db;
		$this->db=&$db;
	}
	
	/**
	 * debug::d_print()
	 * 
	 * @param mixed $data
	 * @return void
	 */
	public function d_print($data)
	{
		echo "<pre>";
		print_r($data);
		echo "</pre>";
	}
	
	function printddate($stamp)
	{
		self::printdate($stamp);
	}
	
	function printdate($stamp)
	{
		self::d_print(date("d.m.Y - H:i",$stamp));
	}
	
	/**
	 * debug::mail()
	 * 
	 * @param mixed $data = Inhalt der Meldung
	 * @param string $ort = Von wo kommt die Meldung
	 * @return void
	 */
	public function mail($data,$ort="")
	{
		@mail("info@myseoapp.de","Debug Meldung ".$ort,$ort."\n".$data);
		
	}
	
	public function log($data,$ort="")
	{
		$filename   = ABS_PFAD. "/mylogs/msa_error_log.log";
  	$inhalt = date( "d.m.Y - H:i:s; " );
  	$inhalt .= $_SERVER['REMOTE_ADDR'];
    $inhalt .= "; ";
    $inhalt .= $data." - ".$ort;
    $inhalt .= "; ";
    $inhalt .= $_SERVER['HTTP_USER_AGENT'];
    $inhalt .= "\r";
    $file = fopen( $filename, "a" );
    @fwrite( $file, $inhalt );
    @fclose( $file );
		
	}
	
	/**
	 * debug::print_d()
	 * 
	 * @param mixed $data
	 * @return void
	 */
	public function print_d($data)
	{
		self::d_print($data);
	}
	

}


?>