<?php
require_once(TINYPHP_ROOT.'/vendor/stripe-php-13.11.0/init.php');

// HOWTO

// User payment page:
// 1 - Call Init() to initialize the library. Pass the configuration (See config/config.json)
// 2 - Call Start() at the earliest possibile moment in the php page to bootstrap the Stripe payment process. Pass a custom purchase payload (eg. product name and quantity)
// 3 - Call OutputJavaScript() to make available the 'launchStripePaymentProcess' JavaScript function, used to open the Stripe payment page
// 4 - In the JavaScript code, attach the checkout button to the 'launchStripePaymentProcess' function.

// Webhook
// 1 - Call Init() to initialize the library. Pass the configuration (See config/config.json)
// 2 - Call ProcessWebHook() to process the response from Stripe. "paymentSuccessCallback" is called with the user email and the purchase payload specified in Start(). Do not send any output.

/*
{
    "phpSecretKey": "sk_live_xxxxxxxxxxxxxxx",
    "jsPublicKey": "pk_live_xxxxxxxxxxxxxxx",
    "endpointSecret": "whsec_xxxxxxxxxxxxxxx",
    "version": "YYYY-MM-DD"
}
*/

class Stripe
{
    public static $config = null;
    public static $sessionId = null;

    public static function Init($config)
    {
        self::$config = $config;
    }

    public static function Start($userEmail, $productName, $description, $imageUrl, $price, $currency, $successUrl, $cancelUrl, $purchasePayload)
    {
        \Stripe\Stripe::setApiKey(self::$config->phpSecretKey);
        $res = \Stripe\Checkout\Session::create([
            'billing_address_collection' => 'auto',
            'mode' => 'payment',
            'locale' => 'auto',
            'customer_email' => $userEmail,
            'client_reference_id' => json_encode($purchasePayload),
            'payment_method_types' => ['card'],
            'line_items' => [
            [
                'quantity' =>  1,
                'price_data' => [
                    'unit_amount' => $price,
                    'currency' => $currency,
                    'product_data' => [
                        'name' => $productName,
                        'description' => $description,
                        'images' => [$imageUrl]
                    ]
                ]
            ]
            ],
            'success_url' => $successUrl.'?sid={CHECKOUT_SESSION_ID}',
            'cancel_url' => $cancelUrl,
        ]);

        self::$sessionId = $res->id;
    }

    public static function OutputJavaScript() // creates the 'launchStripePaymentProcess' JavaScript function
    {
        echo '
        <script src="https://js.stripe.com/v3"></script>
        <script>
        function launchStripePaymentProcess(onError) {
            var stripe = Stripe("'.self::$config->jsPublicKey.'");
            stripe.redirectToCheckout({ sessionId: "'.self::$sessionId.'"})
            .then(function(result){
                if (result.error) {
                    onError(result.error.message);
                }
            });
        };
        </script>
        ';
    }

    public static function ProcessWebHook($paymentSuccessCallback)
    {
        \Stripe\Stripe::setApiKey(self::$config->phpSecretKey);
        \Stripe\Stripe::setApiVersion(self::$config->version);

        if(!isset($_SERVER['HTTP_STRIPE_SIGNATURE']))
        {
            echo "No HTTP_STRIPE_SIGNATURE header found";
            http_response_code(400);
            exit();
        }
        
        try
        {
            $sigHeader = $_SERVER['HTTP_STRIPE_SIGNATURE'];
            $stripeResponse = file_get_contents('php://input');
            $event = \Stripe\Webhook::constructEvent($stripeResponse, $sigHeader, self::$config->endpointSecret);
        }
        catch(Exception $e)
        {
            echo $e->getMessage();
            http_response_code(400);
            exit();
        }

        if($event->type == "checkout.session.completed")
        {
            try
            {
                $session = $event->data->object;
                $tid = $session->id;
                $userEmail = $session->customer_email;
                $purchasePayload = json_decode($session->client_reference_id);
            }
            catch(Exception $e)
            {
                echo $e->getMessage();
                http_response_code(400);
                exit();
            }

            call_user_func($paymentSuccessCallback, $tid, $userEmail, $purchasePayload);
        }
        
        http_response_code(200);
    }
}

?>