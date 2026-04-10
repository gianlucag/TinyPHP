<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mail
{
    private static $debugMode = false;
    private static $testEmail = null;
    private static $signature = null;
    private static $signatureLogoPath = null;
    private static $isSmtpEnabled = false;
    private static $smtpHost = null;
    private static $smtpUsername = null;
    private static $smtpPassword = null;
    private static $smtpPort = null;

    public static function SetSMTPParams($host, $username, $password, $port)
    {
        self::$isSmtpEnabled = true;
        self::$smtpHost = $host;
        self::$smtpUsername = $username;
        self::$smtpPassword = $password;
        self::$smtpPort = $port;
    }

    public static function SetEmailSignature($signature)
    {
        self::$signature = $signature;
    }

    public static function SetSignatureLogo($path)
    {
        self::$signatureLogoPath = $path;
    }

    public static function SetDebug($testEmail)
    {
        self::$debugMode = true;
        self::$testEmail = $testEmail;
    }

    public static function Send($from, $fromname, $to, $subject, $content, $attachments = null, $ccs = null, $replyTo = null, $replyToName = null)
    {
        if (self::$debugMode) {
            $subject = "[TEST TO " . $to . "] " . $subject;
            $to = self::$testEmail;
            $css = null;
        }

        $body = '
        <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
        <html>
        <body bgcolor="#ffffff" text="#000000">
        ';

        $body .= $content;

        if (self::$signature) {
            $body .= "<br><br>--<br>";
            $body .= self::$signature;

            if (self::$signatureLogoPath) {
                $body .= '<br><br><img alt="logo" width="80" src="cid:logo">';
            }
        }

        $body .= '
        </body>
        </html> 
        ';

        $mail = new PHPMailer(true);

        if (self::$isSmtpEnabled) {
            $mail->isSMTP();
            $mail->Host       = self::$smtpHost;
            $mail->SMTPAuth   = true;
            $mail->Username   = self::$smtpUsername;
            $mail->Password   = self::$smtpPassword;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = self::$smtpPort;
        }

        $mail->CharSet  = 'UTF-8';
        $mail->Encoding = 'base64';

        try {
            $mail->setFrom($from, $fromname);
            if ($to != null) $mail->addAddress($to);

            if ($ccs) {
                for ($c = 0; $c < count($ccs); $c++) {
                    $mail->addCC($ccs[$c]);
                }
            }

            if ($replyTo && $replyToName) $mail->addReplyTo($replyTo, $replyToName);

            //if($bccs != null) $mail->addBCC($bccs);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->msgHTML($body);

            if ($attachments) {
                for ($a = 0; $a < count($attachments); $a++) {
                    $attachment = $attachments[$a];
                    $filepath = $attachment[0];
                    $cid = $attachment[1];
                    $mail->AddAttachment($filepath, $cid);
                }
            }

            if (self::$signatureLogoPath) {
                $mail->addEmbeddedImage(self::$signatureLogoPath, 'logo');
            }

            $res = $mail->send();
            return $res;
        } catch (Exception $e) {
            return false;
        }
    }
}
