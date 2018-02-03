<?php

namespace Carbon\Controllers;
use \Slim\Views\Twig as View;
use Carbon\Models\Bundle;

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
                //A bundle was found. grab the hash and stream the download.
                $file = 'localhost/snatch/public/img.zip';
                $zip = $bundle->hash . ".zip";
                $path = $request->getUri()->getBasePath() . "/bundles/{$zip}";
                echo $path;
                return $response->withRedirect($path);
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
    public function getBundle($request, $response, $args) {
        $bundle = Bundle::where('user', $username)->where('bundleName', $bundlename)->first();        
        return $this->view->render($response, 'template/bundles.twig',['bundles'=> $bundle]);
    }
}