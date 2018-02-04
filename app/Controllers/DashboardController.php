<?php

namespace Carbon\Controllers;
use \Slim\Views\Twig as View;
use Carbon\Models\User;
use Carbon\Models\Bundle;
use Carbon\Models\Following;

use Carbon\Models\Followers;
use Carbon\Models\SocialMedia;
use Carbon\Models\Software;

use Carbon\Models\SocialMedia;
use Carbon\Models\Software;

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

        $SocialMedia = SocialMedia::where('user', $user->username)->first();
        $Software = Software::where('user', $user->username)->first();


        return $this->view->render($response, 'dashboard/user/userRepositories.twig', [
            'userBundles' => $bundles,
            'profileUser' => $user, 
            'profileSocial' => $SocialMedia,
            'profileSoftware'=>$Software
        ]);
        
    }

    public function getProfileStars($request, $response, $args){
        $user = User::where('username',$args['username'])->first();

        if (!$user){
            echo('Username not found');
            exit();
        }

        return $this->view->render($response, 'dashboard/user/userStars.twig', [
            'user'=>$user
        ]);
    }


    public function getProfileFollowing($request, $response, $args){
        $user = User::where('username', $args['username'])->first();
        
        if (!$user){
            echo('Username not found');
            exit();
        }
        return $this->view->render($response, 'dashboard/user/userFollowing.twig', [
            'user'=>$user, 
            ]);
    }

    public function getProfileFollowers($request, $response, $args){
        $user = User::where('username', $args['username'])->first();
        
        if (!$user){
            echo('Username not found');
            exit();
        }
        return $this->view->render($response, 'dashboard/user/userFollowers.twig', [
            'user'=>$user,
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
            'version' => "1"
        ]);
        return $response->withRedirect($this->router->pathFor('dashboard.user.uploadasset'));
    }
}