<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mail
{
    private static $debugMode = false;
    private static $testEmail = null;
    private static $signature = null;

    public static function SetEmailSignature($signature)
    {
        self::$signature = $signature;
    }

    public static function SetDebug($testEmail)
    {
        self::$debugMode = true;
        self::$testEmail = $testEmail;
    }

    public static function Send($from, $fromname, $to, $subject, $content, $attachments = null, $ccs = null)
    {
        if(self::$debugMode)
        {
            $subject = "[TEST TO ".$to."] ".$subject;
            $to = self::$testEmail;
            $css = null;
        }

        $body = '
        <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
        <html>
        <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        </head>
        <body bgcolor="#ffffff" text="#000000">
        ';

        $body .= $content;
        
        $body .= '
        <br />
        <br />
        ';

        if(self::$signature)
        {
            $body .= self::$signature;
        }

        $body .= '
        </body>
        </html> 
        ';

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
            $mail->msgHTML($body);

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