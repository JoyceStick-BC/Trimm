<?php

namespace Carbon\Controllers;
use \Slim\Views\Twig as View;
use Carbon\Models\Bundle;
use Carbon\Models\User;
use Carbon\Models\PublicKey;
use Carbon\Models\Payment;
use \SendGrid\Email;
use \SendGrid\Content;
use \SendGrid\Mail;
use \RandomLib\Factory;
use \Stripe\Stripe;
use Carbon\Models\StripeDB;

class BundleController extends Controller {
    public function downloadBundle($request, $response, $args) {
        $username = $args["username"];
        $bundlename = $args["bundlename"];

        if(isset($args["version"])) {
            //version is provided
            $version = $args["version"];
            $bundle = Bundle::where('user', $username)->where('bundleName', $bundlename)->where('version', $version)->first();
        } else {
            //download latest version
            $bundle = Bundle::where('user', $username)->where('bundleName', $bundlename)->orderBy('created_at', 'desc')->first();
        }

        if(!$bundle) {
            //the bundle doesn't exist
            if(isset($args["version"])) {
                $error = array(
                    "success" => "false",
                    "message" => "Could not find an asset with that username, bundle name, and version"
                );
            } else {
                $error = array(
                    "success" => "false",
                    "message" => "Could not find an asset with that username and bundle name"
                );
            }
        } else {
            //A bundle was found. if it is paid, respond with json else respond with download.
            if ($bundle->price) {
                $json = [
                    'bundleName' => $args['bundlename'],
                    'user' => $args['username'],
                    'price' => $bundle->price,
                    'hash' => $bundle->hash,
                    'message' => "This bundle costs $bundle->price. Would you like to purchase it?(y/n)"
                ];

                return $response->withJson($json);
            } else {
                $file = 'localhost/snatch/public/img.zip';
                $zip = $bundle->hash . ".zip";
                $path = $request->getUri()->getBasePath() . "/bundles/{$zip}";
                echo $path;
                return $response->withRedirect($path);
            }
        }
    }

    public function getLatestVersion($request, $response, $args) {
        $username = $args["username"];
        $bundlename = $args["bundlename"];

        $bundle = Bundle::where('user', $username)->where('bundleName', $bundlename)->orderBy('created_at', 'desc')->first();

        if(!$bundle) {
            //the bundle doesn't exist
            $response = array(
                "success" => "false",
                "message" => "Could not find an asset with that username, bundle name"
            );
        } else {
            $response = array(
                "success" => "true",
                "username" => $username,
                "bundlename" => $bundlename,
                "latest-version" => $bundle->version,
                "created_at" => $bundle->created_at
            );
        }

        echo json_encode($response, JSON_PRETTY_PRINT);
        exit();
    }

    public function sendEmail($request, $response, $args) {
        $factory = new Factory;
        $generator = $factory->getMediumStrengthGenerator();
        $key = $generator->generateString(6, '1234567890');

        $user = User::where('username', $args['username'])->first();

        PublicKey::create(array(
            'user' => $user->username,
            'privateKey' => md5($key),
            'type' => 'charge',
            'expiration' =>  date("Y-m-d H:i:s", time() + 3600),
        ));

        //FIGURE OUT EMAIL LATER
        /*$from = new Email($user->name, $user->email);
        $subject = 'Charge code';
        $to = new Email('Trimm', 'charges@trimm3d.com');
        $content = new Content('text/plain', 'Your charge key is: '. $key);
        $mail = new Mail($from, $subject, $to, $content);

        $sg = new \SendGrid(getenv('SENDGRID_API_KEY'));

        $response = $sg->client->mail()->send()->post($mail);

        echo "<pre>";
        var_dump($response);

        echo $response->statusCode();
        print_r($response->headers());
        echo $response->body();*/

        $json = [
            'success' => 'true',
            'hash' => $args['bundleHash'],
            'username' => $args['username'],
            'message' => 'Please input the code sent to your email address to confirm payment.',
        ];

        return $response->withJson($json);

    }

    public function downloadPaid($request, $response, $args) {
        //try to grab the key from db
        $key = PublicKey::select('privateKey', 'expiration')
                        ->where('user', $args['username'])
                        ->where('type', 'charge')
                        ->where('privateKey', md5($request->getParam('key')))
                        ->orderBy('id', 'desc')
                        ->first();

        //check if the key is valid
        if (!$key) {
            $json = [
                'message' => 'This key does not exist',
            ];
            return $response->withJson($json);
        } else if (!md5($request->getParam('code')) == $key->privateKey) {
            $json = [
                'message' => 'This key is incorrect',
            ];
            return $response->withJson($json);
        } else if (strtotime($key->expiration) - time() > 3600) {
            $json = [
                'message' => 'This key has expired',
            ];
            return $response->withJson($json);
        }

        //get the bundle and seller through the provided hash
        $bundle = Bundle::where('hash', $args['bundleHash'])->first();
        $bundlePrice = $bundle->price;
        $seller = User::where('username', $bundle->user)->first()->id;
        $sellerStripeBank = StripeDB::where('user_id', $seller)->first()->acct_id;
        //get buyer through the username
        $buyer = User::where('username', $args['username'])->first()->id;
        $buyerStripeCard = StripeDB::where('user_id', $buyer)->first()->card_id;

        //make charge
        Stripe::setApiKey(getenv('STR_SEC'));
        $charge = \Stripe\Charge::create(array(
            'amount' => $bundlePrice,
            'currency' => 'usd',
            'customer' => $buyerStripeCard,
            'destination' => array(
                'account' => $sellerStripeBank,
            ),
        ));

        //make payment record
        if ($charge->status == 'succeeded') {
            Payment::create(array(
                'buyer_card_id' => $buyerStripeCard,
                'seller_acct_id' => $sellerStripeBank,
                'amount' => $bundlePrice,
                'bundleName' => $bundle->bundleName,
            ));

            //download the bundle
            /*
            $file = 'localhost/snatch/public/img.zip';
            $zip = $bundle->hash . ".zip";
            $path = $request->getUri()->getBasePath() . "/bundles/{$zip}";
            echo $path;
            return $response->withRedirect($path);
            */
        } else {
            $json = [
                'message' => 'Unable to process payment',
            ];
            return $response->withJson($json);
        }


    }
}
