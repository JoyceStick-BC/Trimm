<?php

namespace Carbon\Controllers;
use \Slim\Views\Twig as View;
use Carbon\Models\Bundle;
use Elasticsearch\ClientBuilder;

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
        return $this->view->render($response, 'browse.twig');
    }

    public function getBrowseWithQuery($request, $response, $args) {
        if(!isset($args['query'])) {
            echo "need a query bitch";
            exit();
        }
        $client = new ClientBuilder;
        $client = $client->create()->build();

        if (!$client->ping()) {
            echo "Unable to connect to Elasticsearch server.";
            exit();
        }

        //EDIT THE FOLLOWING TO IMPROVE SEARCH QUERIES
        $params = [
            'index' => 'bundles',
            'type' => 'bundle',
            'body' => [
                'query' => [
                    'multi_match' => [
                        'query' => $args['query'],
                        //will find best matches, can add more columns (like description)
                        'fields' => ['bundleName', 'user', 'description'],
                        'fuzziness' => '5'
                    ]
                ]
            ]
        ];

        $bundles = $client->search($params);
        $bundles = $bundles['hits']['hits'];

        echo json_encode($bundles);

        /*return $this->view->render($response, 'browse.twig', [
            'query' => $request->getParam('query'),
            'results' => $bundles,
        ]);*/
    }
}