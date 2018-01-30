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

    public function getBrowse($request, $response) {
        $client = $this->es;

        if (!$client) {
            echo "Unable to connect to Elasticsearch client.";
            exit();
        }

        echo "<pre>";

        $bundles = Bundle::all();

        $bundles->each(function($bundle) use ($client) {
            //create an index to be searched for every bundle
            $params = [
                'index' => 'bundles',
                'type' => 'bundle',
                'id' => $bundle->id,
                'body' => [
                    'user' => $bundle->user,
                    'bundleName' => $bundle->bundleName
                ]
            ];

            $indexed = $client->index($params);
        });

        $params = [
            'index' => 'bundles',
            'type' => 'bundle',
            'body' => [
                'query' => [
                    'match' => [
                        'bundleName' => 'car',
                    ]
                ]
            ]
        ];


        $temp = $client->search($params);

        //$temp = $client->index($params);
        var_dump($temp);

        //return $this->view->render($response, 'browse.twig');
    }
}