<?php 
/**
 * Daten in Dateien speichern
 */
class class_make_file
{


	/**
	 * class_make_file::class_make_file()
	 * 
	 * @return void
	 */
	function class_make_file()
	{}

	/**
	 * class_make_file::create_files()
	 * 
	 * Daten annehmen und Erstellung anstoßen 
	 * 
	 * @param mixed $rdata
	 * @return
	 */
	public function create_files($rdata)
	{
		//Zuerst mal die PDF Dateien erzeugen
		$rdata=$this->create_pdf_files($rdata);
		
		//Dann daraus die Screenshots erstellen
		$rdata=$this->create_screenshots($rdata);
		
		return $rdata;
	}
	
	/**
	 * class_make_file::create_screenshots()
	 * 
	 * @param mixed $rdata
	 * @return void
	 */
	private function create_screenshots($rdata)
	{
		//absoluten Pfad zum speichern der Bilder
		$pfadhier= str_replace("lib","",dirname(__FILE__));
		
		//Bilder Größe ändern
		require_once("./lib/class_simple_image.php");
		
		//Durchloopen
		if (is_array($rdata))
		{
			foreach ($rdata as $key=>$value)
			{
				if (!empty($value['pdf_file_url']))
				{
					//Unterverzeichnisname
					$value['ausschuss']=str_replace(" ","-",$value['ausschuss']);
					$value['ausschuss']=strtolower($value['ausschuss']);
					$subdir=preg_replace("/[^a-z0-9\-]/", "", $value['ausschuss']);
					
					//Checken ob vorhanden, ansonsten anlegen
					if (!is_dir('./files/images/thumbs/'.$subdir))
					{
						mkdir('./files/images/thumbs/'.$subdir);
					}
					
					//datum
					$value['datum']=str_replace(".","-",$value['datum']);
					$value['datum']=strtolower($value['datum']);
					$subdir2=preg_replace("/[^a-z0-9\-]/", "", $value['datum']);
					
					//Checken ob vorhanden, ansonsten anlegen
					if (!is_dir('./files/images/thumbs/'.$subdir."/".$subdir2))
					{
						mkdir('./files/images/thumbs/'.$subdir."/".$subdir2);
					}
					
					//TODO: Wieder aktivieren auf Server!!!
					$rdata[$key]['thumbnails']=$this->create_img_frompdf($value['pdf_file_url'],$pfadhier);
					
					//TODO: Das hier deaktivieren ...
					#$rdata[$key]['thumbnails'][]="thumbnail-1.jpg";
					#$rdata[$key]['thumbnails'][]="thumbnail-2.jpg";
				}
				//print_r($rdata[$key]['thumbnails']);
				
			}
		}
		
		return $rdata;
		
	}
	
	/**
	 * class_make_file::create_img_frompdf()
	 * 
	 * @param mixed $pdf_org
	 * @param mixed $pfadhier
	 * @return
	 */
	private	function create_img_frompdf($pdf_org,$pfadhier)
	{
		setlocale(LC_ALL, "de_DE");;
		//Klasse INI
		$im = new imagick();
		
		//Auflösung 
		$im->setResolution(60,60);
		
		//Anzahl der Seiten des PDFs
		$pages=$this->getNumPagesInPDF($pfadhier.$pdf_org);
		
		//Dann alle Seiten durchlaufen und Bilder erzeugen
		for ($i = 0; $i < $pages; $i++) {
		//Maximal 100 Seiten
		if ($i>100)
		{
			continue;
		}
		
    	//Seitenzahl festlegen
    	$pdf=$pfadhier.$pdf_org."[".$i."]";
    	//auslesen
    	if (file_exists($pfadhier.$pdf_org))
    	{
			try {
			    		$im->readImage($pdf);
						

					} catch (Exception $e) {
		  	 	 echo 'Exception abgefangen: ',  $e->getMessage(), "\n";
				}
			if (empty($e))
			{
			    //die ("NIX");
			     
			$im->setImageColorspace(255); 
						$im->setCompression(Imagick::COMPRESSION_JPEG); 
						$im->setCompressionQuality(60); 
						$im->setImageFormat('jpg'); 
						$im->setImageUnits(imagick::RESOLUTION_PIXELSPERINCH);
						
						//Damti testweise ausgeben
						#header( "Content-Type: image/png" );
						#echo $im;
						#exit();
						$pdf_img=str_replace(".pdf","",($pdf_org));
						$pdf_img=str_replace("/files/pdf/","",($pdf_img));
						$im->setImageFileName($pfadhier."files/images/thumbs/".$pdf_img."_".$i.".jpg");
						
						//Pfade saven
						echo $image_files[]=$pfadhier."files/images/thumbs/".$pdf_img."_".$i.".jpg";
						
						//Speichern
						$im->writeImage();
			ini_set(Display_errors, "1");
            }		
			//Noch verkleinern... image_magick macht die Bilder zu groß
			/**
			$image = new SimpleImage();
	   		 $image->load($pfadhier."files/images/thumbs/".$pdf_img."_".$i.".jpg");
	    	$image->resizeToHeight(300);
	    	$image->save($pfadhier."files/images/thumbs/".$pdf_img."_".$i."x.jpg");
	    	unlink($pfadhier."files/images/thumbs/".$pdf_img."_".$i.".jpg");
	    	echo ($pfadhier."files/images/thumbs/".$pdf_img."_".$i."x.jpg");
	    	*/
	   		}
		}
		
		return $image_files;
	
	}
	
	/**
	 * class_make_file::getNumPagesInPDF()
	 * 
	 * @param string $PDFPath
	 * @return
	 */
	private function getNumPagesInPDF($PDFPath="")
	{
		
		$stream = fopen($PDFPath, "r");
		$PDFContent = fread ($stream, filesize($PDFPath));
		if(!$stream || !$PDFContent)
		    return 0;
		   
		$firstValue = 0;
		$secondValue = 0;
		if(preg_match("/\/N\s+([0-9]+)/", $PDFContent, $matches)) {
		    $firstValue = $matches[1];
		}
		 
		if(preg_match_all("/\/Count\s+([0-9]+)/s", $PDFContent, $matches))
		{
		    $secondValue = max($matches[1]);
		}
		return (($secondValue != 0) ? $secondValue : max($firstValue, $secondValue));
	}
	
	private function is_utf8(&$string) {
	if ($string === mb_convert_encoding(mb_convert_encoding($string, "UTF-32", "UTF-8"), "UTF-8", "UTF-32")) {
		return true;
	} 
	else {
	  if ($this->ignore_invalid_utf8) {
		$string = mb_convert_encoding(mb_convert_encoding($string, "UTF-32", "UTF-8"), "UTF-8", "UTF-32") ;
		return true;
	  }
	  else {
		return false;
	  }
	}
} 
	
	/**
	 * class_make_file::create_pdf_files()
	 * 
	 * @return void
	 */
	private function create_pdf_files($rdata)
	{
		//Mpdf Klasse initialisieren
		require_once('./lib/mpdf50/mpdf.php');
		
		//absoluten Pfad zum speichern der Dokumente
		$pfadhier= str_replace("lib","",dirname(__FILE__));

		//Durchloopen
		if (is_array($rdata))
		{
			foreach ($rdata as $key=>$value)
			{
				//Unterverzeichnisname
				$value['ausschuss']=str_replace(" ","-",$value['ausschuss']);
				$value['ausschuss']=strtolower($value['ausschuss']);
				$subdir=preg_replace("/[^a-z0-9\-]/", "", $value['ausschuss']);
				
				//Checken ob vorhanden, ansonsten anlegen
				if (!is_dir('./files/pdf/'.$subdir))
				{
					mkdir('./files/pdf/'.$subdir);
				}
				
				//datum
				$value['datum']=str_replace(".","-",$value['datum']);
				$value['datum']=strtolower($value['datum']);
				$subdir2=preg_replace("/[^a-z0-9\-]/", "", $value['datum']);
				
				//Checken ob vorhanden, ansonsten anlegen
				if (!is_dir('./files/pdf/'.$subdir."/".$subdir2))
				{
					mkdir('./files/pdf/'.$subdir."/".$subdir2);
				}
				
				//Filename
				$value['kurz_betreff']=str_replace(" ","-",$value['kurz_betreff']);
				$value['kurz_betreff']=strtolower($value['kurz_betreff']);
				$value['id']=class_methods::get_clean_text($value['id']);
				$value['id']=preg_replace("/[^a-z0-9]/", "", $value['id']);
				
				$file_name="/files/pdf/".$subdir."/".$subdir2."/".preg_replace("/[^a-z0-9\-]/", "", substr($value['kurz_betreff'],0,40))."-".($value['id']).".pdf";
				echo "FILE:"; echo $file=$pfadhier.$file_name;
			
				
				/**
				 * Aus dem HTML Dokument ein PDF erstellen
				 * */
				if (!empty($value['id_data']['html']))
				{
					//PDF im Format A4 erstellen 
					$mpdf=new mPDF('utf-8', 'A4');
					//$mpdf->debug=true;
					$value['id_data']['html'] =(preg_replace("/(\<\!\-\-.*\-\-\>)/sU", "", $value['id_data']['html']));
					
					//Falls der Text komische utf8 Sachen enthält
					#if (!$this->is_utf8($value['id_data']['html']))
					#{
					#	$value['id_data']['html']=utf8_encode($value['id_data']['html']);
					#}
					
					$mpdf->WriteHTML($value['id_data']['html']);      
					#echo $file;
					//$mpdf->SetDisplayMode('fullpage');
					$mpdf->Output($file,"F"); //I für Displayanzeige
					//exit();	
				}
				//exit();
				/**
				 * Inhalt als PDF wegspeichern
				 * 
				 * Erstmal lassen wg. Urheberprobleme... 
				 * 
				 * In den obigen Daten ist nur Text, aber
				 * hier können auch Scans, Karten usw. drin sein
				 * Ob das alles Urherberechtsmäßig ok ist - ist fraglich
				 * 
				 * Daher kann das in der Config Datei eingestellt werden
				 * 
				 * */
				if (!empty($value['id_data']['pdf']) && SCRAPE_PDF ==1)
				{	
					//Daten speichern
					class_methods::write_to_file($file,$value['id_data']['pdf']);
				}
				
				//Nur Pfad übergeben wenn auch existiert...
				if (file_exists($file))
				{
					//Url noch übergeben
					$rdata[$key]['pdf_file_url']=$file_name;
				}
				
			}
		}
		
		return $rdata;
	}
	
}
?>