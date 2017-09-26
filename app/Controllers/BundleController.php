<?php

namespace Carbon\Controllers;
use \Slim\Views\Twig as View;
use Carbon\Models\Bundle;

class BundleController extends Controller {
    public function downloadBundle($request, $response, $args) {
        $username = $args["username"];
        $bundlename = $args["bundlename"];

        $bundle = Bundle::where('user', $username)->where('bundleName', $bundlename)->first();

        if(!$bundle) {
            //the bundle doesnt exist
        } else {
                $file = 'localhost/snatch/public/img.zip';
                $path = $request->getUri()->getBasePath() . "/bundles/$username-$bundlename.zip";
                return $response->withRedirect($path);
        }
    }
}