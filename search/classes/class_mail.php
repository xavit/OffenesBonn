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
if ( eregi('class_mail.php', $_SERVER['PHP_SELF']) ) die( 'You are not allowed to see this page directly' );

class class_mail
{

	/**
	 * class_uebungen::class_uebungen()
	 * 
	 * @return void
	 */
	function class_mail()
	{
		//Verbindung mit DB
		global $db;
		$this->db=&$db;
		
		global $checked;
		
		$this->send_support_mail();
	}
	
	function send_support_mail()
	{
		global $checked;
		global $user;
		global $template_dat;
		if (!empty($checked->submit_message))
		{
			$user->userid;
			$sql=sprintf("SELECT * FROM %s LEFT JOIN %s
								ON userid=extended_user_user_id
								WHERE userid='%d'",
								DB_PRAEFIX."papoo_user",
								DB_PRAEFIX."plugin_shop_crm_extended_user",
								$user->userid
								);
			$result=$this->db->get_results($sql,ARRAY_A);
			$udat=$result['0'];
			
			$content="Support Anfrage von: ".$udat['extended_user_vorname']." ".$udat['extended_user_name'];
			$content.="\n\n\n";
			$content.=$checked->index_anfrage;
			$content.="\n\n";
			
			if (is_array($udat))
			{
				foreach ($udat as $key=>$value)
				{
					if (strlen($value)>2)
					{
						$content.=$key.": ".$value."\n";
					}
				}
			
			}
			
			$this->from=$udat['email'];
			$this->support_mail($content);
			
			$template_dat['wurde_verschickt']="Ihre Nachricht wurde verschickt.";
		}
	}
	
	function logmail($body)
	{
		require_once(ABS_PFAD."/classes/class.phpmailer.php");
		$mail = new PHPMailer();

		$mail->CharSet = "utf-8";
			//$mail->IsMail(); // ???
			// Daten übergeben
		$mail->AddAddress("info@myseoapp.de", "info@myseoapp.de");
            
            #foreach ($this->cc as $dat)
            #{
            #    $mail->AddCC($dat);
            #}
            
			#$mail->cc = $this->cc;
			//print_r($this->attach);
			//Wenn ein Attachment angegeben wird
			/**
			if (!empty($this->attach))
			{
				foreach ($this->attach as $attach)
				{
					$mail->AddAttachment($attach);
				}
			}
			*/
			$mail->From = "info@myseoapp.de";
			#if (!empty($this->from_text))
			#{
			#	$mail->Sender = $this->from_text;
			#}
			#else
			#{
				$mail->Sender = $this->from;
			#}

		#	if (!empty($this->from_textx))
			#{
			#	$mail->FromName = $this->from_textx;
			#}
			#else
			#{
				$mail->FromName = "Log Mail MySEOApp";
			#}
			//$mail->language=0;
			
			#$mail->SetLanguage("de", PAPOO_ABS_PFAD."/lib/classes/language/");

			$mail->IsHTML(false);
			$mail->Subject = "Log Mail MySEOApp";
			
			$mail->Encoding = '8bit';
			$mail->Body = $body;
			
			#print_r($mail);exit();$this->replayto
			#$mail->ReplyTo = $this->ReplyTo;
			
			/**if (is_array($this->ReplyTo))
			{
				foreach ($this->ReplyTo as $key=>$value)
				{
					$mail->AddReplyTo($key,$value);
				}
			}
			*/
			
			$mail->Priority = "1";
			
			// Email senden
			if (!$mail->Send())
			{
				#echo "Fehler, Mail wurde nicht verschickt. Error: ".$mail->ErrorInfo;
				echo $this->error= "Fehler, Mail wurde nicht verschickt. Error: ".$mail->ErrorInfo;
				#$this->mail_log();
			}
			else
			{
				//ok oder nicht?
				#$this->error="gesendet";
				#$this->ok = "ok";
				#$this->mail_log();
				// Alles ok, zurückgeben
				#return $this->ok;
			}
	}
	
	function support_mail($body)
	{
		require_once(ABS_PFAD."/classes/class.phpmailer.php");
		$mail = new PHPMailer();

		$mail->CharSet = "utf-8";

		$mail->AddAddress("info@myseoapp.de", "info@myseoapp.de");
         
         if (empty($this->from))
		$mail->From = "info@myseoapp.de";
		else
		$mail->From =$this->from;
		
		$mail->FromName = "Support Anfrage Mail MySEOApp";
		
		
		$mail->IsHTML(false);
		$mail->Subject = "Support Mail MySEOApp";
			
		$mail->Encoding = '8bit';
		$mail->Body = $body;
			
		$mail->Priority = "1";
			
		// Email senden
		if (!$mail->Send())
		{
			#echo "Fehler, Mail wurde nicht verschickt. Error: ".$mail->ErrorInfo;
			echo $this->error= "Fehler, Mail wurde nicht verschickt. Error: ".$mail->ErrorInfo;
				#$this->mail_log();
		}
	}

}
$mail_class= new class_mail();

?>