<?php

namespace Carbon\Controllers;
use \Slim\Views\Twig as View;
use Carbon\Models\User;
use Carbon\Models\Bundle;
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

        $target_file = "/var/www/trimm3d.com/public_html/public/bundles/temp/asset/". $identifier . '.zip';
        move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file);

        $infoJson = [
            "type" => "asset",
            "version" => "1",
            "username" => $username,
            "name" => $name,
            "bundlename" => $username . "/" .$name
        ];
        $jsonFile = fopen("/var/www/trimm3d.com/public_html/public/bundles/temp/json/{$identifier}.txt", "w");
        fwrite($jsonFile, json_encode($infoJson));
        fclose($jsonFile);

        $hash = md5($identifier);

        $zip = new \ZipArchive();
        if ($zip->open("/var/www/trimm3d.com/public_html/public/bundles/{$hash}.zip", \ZipArchive::CREATE) === TRUE)
        {
            $zip->addFile("/var/www/trimm3d.com/public_html/public/bundles/temp/json/{$identifier}.txt", "info.json");
            $zip->addFile("/var/www/trimm3d.com/public_html/public/bundles/temp/asset/". $identifier . '.zip', "{$name}.zip");
            $zip->close();
        }

        $bundle = Bundle::create([
            'user' => $username,
            'bundleName' => $name,
            'hash' => $hash,
            'version' => "1",
            'description' => $request->getParam('description'),
        ]);

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
}