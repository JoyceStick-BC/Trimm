<?php

namespace Carbon\Controllers;
use \Slim\Views\Twig as View;
use Carbon\Models\User;
use Carbon\Models\Bundle;
use Carbon\Models\BundleComponent;
use Elasticsearch\ClientBuilder;

class DashboardController extends Controller {
    public function getProfile($request, $response, $args) {
        if (isset($args['username'])) {
            $username = $args["username"];
        } else {
            $username = $this->auth->user()->username;
        }
        $user = User::where('username', $username)->first();
        
        if(!$user) {
            //404
            echo "user not found";
            exit();
        }

        $bundles = Bundle::where('user', $username)->get();

        return $this->view->render($response, 'dashboard/user/profile.twig', [
            'userBundles' => $bundles,
            'user' => $user
        ]);
        
    }

    public function getUpload($request, $response) {
        return $this->view->render($response, 'dashboard/user/upload.twig');
    }

    public function postUpload($request, $response) {
        $fileName = $_FILES["fileToUpload"]["name"];
        $name = explode('.', $fileName)[0];
        $username = $this->container->auth->user()->username;

        $identifier = time() . $username . $name;

        $target_file = getenv('PUBLIC_PATH')."bundles/temp/asset/". $identifier . '.zip';
        move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file);

        $infoJson = [
            "type" => "asset",
            "version" => "1",
            "username" => $username,
            "name" => $name,
            "bundlename" => $username . "/" .$name
        ];
        $jsonFile = fopen(getenv('PUBLIC_PATH')."bundles/temp/json/{$identifier}.txt", "w");
        fwrite($jsonFile, json_encode($infoJson));
        fclose($jsonFile);

        $hash = md5($identifier);

        $bundle = Bundle::create([
            'user' => $username,
            'bundleName' => $name,
            'hash' => $hash,
            'version' => "1",
            'description' => $request->getParam('description'),
        ]);

        $zip = new \ZipArchive();
        $uploadedZip = new \ZipArchive();
        $uploadedZip->open($target_file);
        if ($zip->open(getenv('PUBLIC_PATH')."bundles/{$hash}.zip", \ZipArchive::CREATE) === TRUE)
        {
            echo getenv('PUBLIC_PATH')."bundles/{$hash}.zip";
            $zipOpen = zip_open(getenv('PUBLIC_PATH')."bundles/temp/asset/". $identifier . '.zip'); 
            echo "<pre>";

            $files = array();

            while ($zip_entry = zip_read($zipOpen)) {
                $filePathElements= array_filter(explode('/', zip_entry_name($zip_entry)));
                foreach($filePathElements as $filePathElement){
                    if ($filePathElement == '__MACOSX' || $filePathElement == '.DS_Store'){
                        continue 2;
                    }
                }
                if(count($filePathElements) <= 1){
                    $destination = '';
                }
                else{
                    $destination = array_slice($filePathElements, 0, count($filePathElements)-1);
                    $destination = implode('/', $destination).'/';
                }

                $metaFilename = $filePathElements[count($filePathElements)-1].'.meta';
                $guid = $this->getGUID();
                $files[]= [
                    'hash'=>$guid, 
                    'bundle_id'=>$bundle->id,
                    'path'=>$destination,
                    'name'=>$metaFilename
                ];

                $bundleComponent = BundleComponent::create([
                    'hash'=>$guid, 
                    'bundle_id'=>$bundle->id,
                    'path'=>$destination,
                    'name'=>$metaFilename
                ]);

                $uploadedZip->addFromString($destination.$metaFilename, "fileFormatVersion: 2\nguid: ".$guid);
                echo $destination.'<br>';
            }
            zip_close($zipOpen);
            $uploadedZip->close();
            $zip->addFile(getenv('PUBLIC_PATH')."bundles/temp/json/{$identifier}.txt", "info.json");
            $zip->addFile(getenv('PUBLIC_PATH')."bundles/temp/asset/". $identifier . '.zip', "{$name}.zip");
            $zip->close();
        }

        //add file to elasticsearch server
        $client = new ClientBuilder;
        $client = $client->create()->build();

        $params = [
            'index' => 'bundles',
            'type' => 'bundle',
            'body' => [
                'user' => $username,
                'bundleName' => $name,
                'hash' => $hash,
                'description' => $request->getParam('description'),
            ],
        ];

        $indexed = $client->index($params);

        return $response->withRedirect($this->router->pathFor('dashboard.user.uploadasset'));
    }
    public function getGUID(){
       mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
       $charid = strtoupper(md5(uniqid(rand(), true)));
       $uuid = substr($charid, 0, 8)
           .substr($charid, 8, 4)
           .substr($charid,12, 4)
           .substr($charid,16, 4)
           .substr($charid,20,12);
       return $uuid;
    }
}