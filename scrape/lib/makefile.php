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
					//$rdata[$key]['thumbnails']=$this->create_img_frompdf($value['pdf_file_url'],$pfadhier);
					
					//TODO: Das hier deaktivieren ...
					$rdata[$key]['thumbnails'][]="thumbnail-1.jpg";
					$rdata[$key]['thumbnails'][]="thumbnail-2.jpg";
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
		$im->setResolution(30,30);
		
		//Anzahl der Seiten des PDFs
		$pages=$this->getNumPagesInPDF($pfadhier.$pdf_org);
		
		//Dann alle Seiten durchlaufen und Bilder erzeugen
		for ($i = 0; $i < $pages; $i++) {
    	//Seitenzahl festlegen
    	$pdf=$pfadhier.$pdf_org."[".$i."]";
    	//auslesen
			$im->readImage($pdf);
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
			$image_files[]=$pfadhier."files/images/thumbs/".$pdf_img."_".$i.".jpg";
			
			//Speichern
			$im->writeImage();
			
			//Noch verkleinern... image_magick macht die Bilder zu groß
			$image = new SimpleImage();
	    $image->load($pfadhier."files/images/thumbs/".$pdf_img."_".$i.".jpg");
	    $image->resizeToHeight(300);
	    $image->save($pfadhier."files/images/thumbs/".$pdf_img."_".$i."x.jpg");
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
				$file_name="/files/pdf/".$subdir."/".$subdir2."/".preg_replace("/[^a-z0-9\-]/", "", substr($value['kurz_betreff'],0,40))."-".$value['id'].".pdf";
				$file=$pfadhier.$file_name;
			
				
				/**
				 * Aus dem HTML Dokument ein PDF erstellen
				 * */
				if (!empty($value['id_data']['html']))
				{
					//PDF im Format A4 erstellen 
					$mpdf=new mPDF('utf-8', 'A4');
					$mpdf->debug=true;
					$value['id_data']['html'] = preg_replace("/(\<\!\-\-.*\-\-\>)/sU", "", $value['id_data']['html']);
					$mpdf->WriteHTML($value['id_data']['html']);      
					echo $file;
					//$mpdf->SetDisplayMode('fullpage');
					$mpdf->Output($file,"F"); //I für Displayanzeige
					//exit();	
				}
				
				/**
				 * Inhalt als PDF wegspeichern
				 * 
				 * Erstmal lassen wg. Urheberprobleme... 
				 * 
				 * In den obigen Daten ist nur Text, aber
				 * hier können auch Scans, Karten usw. drin sein
				 * Ob das alles Urherberechtsmäßig ok ist - ist fraglich
				 * 
				 * Das macht daher keinen Sinn.
				 * 
				 * */
				if (!empty($value['id_data']['pdf']))
				{
					/**
					//Daten speichern
					* # /////class  _  methods::  write_to_file   ($file,$value['id_data']['pdf']);
					
					*/
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