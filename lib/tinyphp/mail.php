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

    public static function Send(
        $from,
        $fromname,
        $to,
        $subject,
        $content,
        $attachments = null,
        $ccs = null,
        $replyTo = null,
        $replyToName = null
    ) {

        $messages = [[
            'to' => $to,
            'subject' => $subject,
            'content' => $content,
            'attachments' => $attachments,
            'ccs' => $ccs,
            'replyTo' => $replyTo,
            'replyToName' => $replyToName
        ]];

        $res = self::SendBulk(
            $from,
            $fromname,
            $messages
        );

        if (!$res['success']) {
            return false;
        }

        if (!isset($res['results'][0]) || !$res['results'][0]['success']) {
            return false;
        }

        return true;
    }

    public static function SendBulk($from, $fromname, $messages)
    {
        $mail = new PHPMailer(true);

        if (self::$isSmtpEnabled) {
            $mail->isSMTP();
            $mail->Host       = self::$smtpHost;
            $mail->SMTPAuth   = true;
            $mail->Username   = self::$smtpUsername;
            $mail->Password   = self::$smtpPassword;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = self::$smtpPort;
            $mail->SMTPKeepAlive = true;
        }

        //$mail->SMTPDebug = 2;
        //$mail->Debugoutput = 'echo';

        $mail->CharSet  = 'UTF-8';
        $mail->Encoding = 'base64';

        $results = [];

        try {

            $mail->setFrom($from, $fromname);

            foreach ($messages as $message) {

                try {
                    $mail->clearAddresses();
                    $mail->clearCCs();
                    $mail->clearBCCs();
                    $mail->clearReplyTos();
                    $mail->clearAttachments();

                    $to            = $message['to'] ?? null;
                    $subject       = $message['subject'] ?? '';
                    $content       = $message['content'] ?? '';
                    $attachments   = $message['attachments'] ?? null;
                    $ccs           = $message['ccs'] ?? null;
                    $replyTo       = $message['replyTo'] ?? null;
                    $replyToName   = $message['replyToName'] ?? null;

                    if (self::$debugMode) {
                        $subject = "[TEST TO " . $to . "] " . $subject;
                        $to = self::$testEmail;
                        $ccs = null;
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

                    if ($to != null) {
                        $mail->addAddress($to);
                    }

                    if ($ccs) {
                        for ($c = 0; $c < count($ccs); $c++) {
                            $mail->addCC($ccs[$c]);
                        }
                    }

                    if ($replyTo && $replyToName) {
                        $mail->addReplyTo($replyTo, $replyToName);
                    }

                    $mail->isHTML(true);
                    $mail->Subject = $subject;
                    $mail->msgHTML($body);

                    if ($attachments) {

                        for ($a = 0; $a < count($attachments); $a++) {

                            $attachment = $attachments[$a];

                            $filepath = $attachment[0];
                            $cid      = $attachment[1];

                            $mail->addAttachment($filepath, $cid);
                        }
                    }

                    if (self::$signatureLogoPath) {
                        $mail->addEmbeddedImage(self::$signatureLogoPath, 'logo');
                    }

                    $res = $mail->send();

                    $results[] = [
                        'to' => $to,
                        'success' => $res
                    ];
                } catch (Exception $e) {

                    $results[] = [
                        'to' => $to,
                        'success' => false,
                        'error' => $e->getMessage()
                    ];
                }
            }

            $mail->smtpClose();
        } catch (Exception $e) {

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }

        return [
            'success' => true,
            'results' => $results
        ];
    }
}
