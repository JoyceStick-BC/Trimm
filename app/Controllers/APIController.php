<?php

namespace Carbon\Controllers;
use Carbon\Models\User;
use Carbon\Models\Bundle;
use Elasticsearch\ClientBuilder;

class APIController extends Controller {
    public function postUpload($request, $response) {
        $fileName = $_FILES["fileToUpload"]["name"];
        $name = explode('.', $fileName)[0];
        $username = $request->getParam('username');

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

        return $response->withJson([
            'success' => true,
        ]);
    }

    public function postAuth($request, $response) {
        $params = $request->getParam('params');

        $email = $params['username'];
        $password = $params['password'];

        $user = User::where('email', $email)->first();



        if (!$user) {

            $data = [
                'success' => false,
                'message' => 'User with that email does not exist',
            ];

            return $response->withJson($data);
        }

        if (password_verify($password, $user->password)) {
            $key = md5($email . $password . time());

            $data = [
                'success' => true,
                'key' => $key,
            ];

            $user->update([
                'exchangeCode' => $key,
            ]);
        } else {
            $data = [
                'success' => false,
                'message' => 'Password did not match',
            ];
        }

        return $response->withJson($data);
    }

    public function postAuthCode($request, $response) {
        $params = $request->getParam('params');

        $code = $params['code'];
        $userFirstFour = $params['userFirstFour'];

        $user = User::where('exchangeCode', $code)->first();

        $user->update([
            'exchangeCode' => null,
        ]);

        if (!$user) {
            $data = [
                'success' => false,
                'message' => 'Exchange code did not match',
            ];

            return $response->withJson($data);
        }

        $key = md5($userFirstFour . $code);

        $user->update([
            'private_key' => $key
        ]);

        $data = [
            'success' => true,
            'final_key' => $key,
        ];

        return $response->withJson($data);
    }

    public function checkAuth($request, $response) {
        $code = $request->getParam('code');
        $email = $request->getParam('email');

        $user = User::where('email', $email)->first();

        $key = PublicKey::where('privateKey', $code)->where('user_id', $user->id)->first();

        if ($key) {
            $data = [
                'success' => true,
            ];

            return $response->withJson($data);
        }

        $data = [
            'success' => false,
        ];

        return $response->withJson($data);
    }
}

 ?>
