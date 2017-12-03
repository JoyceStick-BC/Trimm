<?php

namespace Carbon\Controllers;
use \Slim\Views\Twig as View;
use Carbon\Models\Bundle;

class DashboardController extends Controller {
    public function getProfile($request, $response, $args) {
        return $this->view->render($response, 'dashboard/user/profile.twig');
    }
    public function getUpload($request, $response) {
        return $this->view->render($response, 'dashboard/user/upload.twig');
    }

    public function postUpload($request, $response) {
        $fileName = $_FILES["fileToUpload"]["name"];
        $name = explode('.', $fileName)[0];
        $username = $this->container->auth->user()->username;

        $hash = md5($username . time() . $name);

        $target_dir = "/var/www/trimm3d.com/public_html/public/bundles/";
        $target_file = $target_dir . basename($hash . '.zip');



        $infoJson = [
            "type" => "asset",
            "version" => "1",
            "username" => $username,
            "name" => $name,
            "bundlename" => $username . "/" .$name
        ];

        move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file);

        $bundle = Bundle::create([
            'user' => $username,
            'bundleName' => $name,
            'hash' => $hash,
            'version' => "1"
        ]);
    }
}