<?php

namespace Carbon\Controllers;
use Elasticsearch\ClientBuilder;
use Carbon\Models\User;

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
}