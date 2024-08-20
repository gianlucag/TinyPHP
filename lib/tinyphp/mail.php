<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mail
{
    private static $debugMode = false;
    private static $testEmail = null;

    public static function SetDebug($testEmail)
    {
        self::$debugMode = true;
        self::$testEmail = $testEmail;
    }

    public static function Send($from, $fromname, $to, $subject, $templateFilePath, $values = null, $attachments = null, $ccs = null)
    {
        if(self::$debugMode)
        {
            $subject = "[TEST TO ".$to."] ".$subject;
            $to = self::$testEmail;
            $css = null;
        }

        $body = file_get_contents($templateFilePath);

        if($values)
        {
            for($v = 0; $v < count($values); $v++)
            {
                //$body = str_replace("%".$v."%", iconv('UTF-8', 'windows-1252', $values[$v]), $body);
                $body = str_replace("%".$v."%", $values[$v], $body);
            }
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
}

?>