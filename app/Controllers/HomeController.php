<?php

namespace Carbon\Controllers;
use \Slim\Views\Twig as View;

class HomeController extends Controller {
	public function index($request, $response) {
		return $this->view->render($response, 'home.twig');
	}
}