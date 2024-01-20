<?php

class Captcha
{
    public static $pubKey = null;
    public static $priKey = null;

    public static function Init($pubKey, $priKey)
    {
        self::$pubKey = $pubKey;
        self::$priKey = $priKey;
    }

    public static function InjectReCaptchaOnClick()
    {
        echo '<script src="https://www.google.com/recaptcha/api.js?render='.self::$pubKey.'"></script>';
        echo '
        <script>
        function submitFormWithCaptchaCheck(buttonElem) {
            var element = buttonElem;
            while (element && element.tagName !== "FORM") element = element.parentNode;
            var form = element;
            grecaptcha.ready(function() {
                grecaptcha.execute("'.self::$pubKey.'", {action: "submit"}).then(function(token) {
                    var input = document.createElement("input");
                    input.type = "hidden";
                    input.name = "g-recaptcha-response";
                    input.value = token;
                    form.appendChild(input);
                    form.submit();
                });
            });
        }
        </script>
        ';
    }

    public static function IsHuman($score = 0.5)
    {
        $recaptcha = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.self::$priKey.'&response='.$_POST['g-recaptcha-response']);
        $recaptcha = json_decode($recaptcha);
        if ($recaptcha->success == true && $recaptcha->score >= $score)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}

?>