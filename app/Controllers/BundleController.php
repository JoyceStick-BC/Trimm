<?php

namespace Carbon\Controllers;
use \Slim\Views\Twig as View;
use Carbon\Models\Bundle;

class BundleController extends Controller {
    public function downloadBundle($request, $response, $args) {
        if(isset($args["version"])) {
            //version is provided
        } else {
            //download latest version
        }

        $username = $args["username"];
        $bundlename = $args["bundlename"];

        $bundle = Bundle::where('user', $username)->where('bundleName', $bundlename)->first();

        if(!$bundle) {
            //the bundle doesn't exist
            $error = array(
                "success" => "false",
                "message" => "Could not find a bundle with that username and bundle name"
            );

            echo json_encode($error);
            exit();
        } else {
                //A bundle was found. grab the hash and stream the download.
                $file = 'localhost/snatch/public/img.zip';
                $zip = $bundle->hash . ".zip";
                $path = $request->getUri()->getBasePath() . "/bundles/{$zip}";
                return $response->withRedirect($path);
        }
    }
}