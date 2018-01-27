<?php

namespace Carbon\Controllers;
use \Slim\Views\Twig as View;
use Carbon\Models\Bundle;

class DashboardController extends Controller {
    public function getProfile($request, $response, $args) {
        // Todo: query for this user's bundles, pass this into the view

        //echo "<pre>";
        //var_dump($this->auth->user()->username);

        return $this->view->render($response, 'dashboard/user/profile.twig');
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
            'version' => "1"
        ]);
        return $response->withRedirect($this->router->pathFor('dashboard.user.uploadasset'));
    }
}