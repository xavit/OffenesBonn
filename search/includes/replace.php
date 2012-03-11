<?php 

//EintrŠge aus Sprachdaten ersetzen
foreach ($lang_dat as $key => $var) 
{
	$rapidform=str_replace('{$'.$key.'}',$var,$rapidform);
}

//Sonstige Daten 
foreach ($template_dat as $key => $var) 
{
	$rapidform=str_replace('{$'.$key.'}',$var,$rapidform);
}

//Rapidform setzen
$template_dat['rapid_form']=$rapidform;
#echo $inhalt;
#print_r($template_dat);

//Sonstige Daten 
foreach ($template_dat as $key => $var) 
{
	$inhalt=str_replace('{$'.$key.'}',$var,$inhalt);
}

//Sonstige Daten in Subtemplates die jetzt erst eingebunden sind.
foreach ($template_dat as $key => $var) 
{
	#echo $key;
	
	//Sub Arrays durchgehen
	if (is_array($var))
	{
		foreach ($var as $key2=>$value2)
		{
			//Inhalte ersetzen
			$inhalt=str_replace('{$'.$key.'.'.$key2.'}',$value2,$inhalt);
			
			//Arrays 3. Ebene durchgehen
			if (is_array($value2))
			{
				foreach ($value2 as $key3=>$value3)
				{
					//Inhalte ersetzen
					$inhalt=str_replace('{$'.$key.'.'.$key2.'.'.$key3.'}',$value3,$inhalt);
				}
			}
		}
	}
}

//EintrŠge aus Sprachdaten ersetzen
foreach ($lang_dat as $key => $var) 
{
	$inhalt=str_replace('{$'.$key.'}',$var,$inhalt);
}

//If Konstrukte
#$inhalt2=str_replace("\n","",$inhalt);
#$inhalt2=str_replace("\t","",$inhalt2);
$ifs=preg_match_all('/\{if \$(.*?)\}(.*?)\{\/if\}/sm', $inhalt, $matches);
//print_r($matches);
if (is_array($matches))
{
	foreach ($matches['1'] as $key=>$value)
	{
		if (!empty($template_dat[$value]))
		{
			$ok[]=$key;
		}
	}
}
//print_r($ok);
if (!empty($ok))
{
	if (is_array($ok))
	{
		foreach ($ok as $key=>$value)
		{
			$inhalt=str_replace($matches['0'][$value],$matches['2'][$value],$inhalt);
		}
	}
}
//Nicht belegte Variablen ersetzen
$inhalt=preg_replace('/\{\$(.*?)\}/',"",$inhalt);

//NIcht belegte ifs lšschen
$inhalt=preg_replace('/\{if \$(.*?)\}(.*?)\{\/if\}/sm',"",$inhalt);
if (IS_LIFE!="staging")
{
	$inhalt=str_replace("#start#","",$inhalt);
}



?>