<?php
require 'PHPMailer-6.9.1/src/Exception.php';
require 'PHPMailer-6.9.1/src/PHPMailer.php';
require 'PHPMailer-6.9.1/src/SMTP.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendemail($from, $fromname, $to, $subject, $templateFilePath, $values, $attachments, $ccs = null)
{
	$body = file_get_contents($templateFilePath);
	for($v = 0; $v < count($values); $v++)
	{
		$body = str_replace("%".$v."%", iconv('UTF-8', 'windows-1252', $values[$v]), $body);
	}

    $mail = new PHPMailer(true);
    try
    {
        $mail->setFrom($from, $fromname);
        if($to != null) $mail->addAddress($to);

        if($ccs)
        {
            for($c = 0; $c < count($ccs); $c++)
            {
                $mail->addCC($ccs[$c]);
            }
        }

        //if($bccs != null) $mail->addBCC($bccs);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;

        if($attachments)
        {
            for($a = 0; $a < count($attachments); $a++)
            {
                $attachment = $attachments[$a];
                $filepath = $attachment[0];
                $cid = $attachment[1];
                $mail->AddAttachment($filepath, $cid); 
            }
        }
        $res = $mail->send();
        return $res;
    }
    catch(Exception $e)
    {
        return false;
    }
}

?>