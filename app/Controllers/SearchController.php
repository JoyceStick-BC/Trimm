<?php

namespace Carbon\Controllers;
use Elasticsearch\ClientBuilder;
use Carbon\Models\User;
use \Slim\Views\Twig as View;

class SearchController extends Controller {
	public function getBrowseUsers($request, $response, $args) {
		$client = new ClientBuilder;
        $client = $client->create()->build();

        if (!$client->ping()) {
            echo "Unable to connect to Elasticsearch server.";
            exit();
    	}

    	$params = [
            'index' => 'users',
            'type' => 'user',
            'body' => [
                'query' => [
                    'multi_match' => [
                        'query' => $args['query'],
                        //will find best matches, can add more columns (like description)
                        'fields' => ['username', 'name'],
                        'fuzziness' => '5'
                    ]
                ]
            ]
        ];

        $users = $client->search($params);
        $users = $users['hits']['hits'];

        return $response->withJson($users);
	}

	public function getBrowse($request, $response) {
        return $this->view->render($response, 'browse.twig');
    }

    public function getBrowseWithQuery($request, $response, $args) {
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

        return $response->withJson($bundles);
    }
}