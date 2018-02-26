<?php
/* structure of a route
$app->get('/home', function ($request, $response) {
	return 'Home';
});
*/

use Carbon\Middleware\AuthMiddleware;
use Carbon\Middleware\GuestMiddleware;

$app->get('/', 'HomeController:index')->setName('home');

$app->group('', function () {
    $this->get('/auth/signup', 'AuthController:getSignUp')->setName('auth.signup');
    $this->post('/auth/signup', 'AuthController:postSignUp');

    $this->get('/auth/signin', 'AuthController:getSignIn')->setName('auth.signin');
    $this->post('/auth/signin', 'AuthController:postSignIn');
})->add(new GuestMiddleware($container));

$app->group('', function () {
    $this->get('/auth/signout', 'AuthController:getSignOut')->setName('auth.signout');

    $this->get('/auth/password/change', 'PasswordController:getChangePassword')->setName('auth.password.change');
    $this->post('/auth/password/change', 'PasswordController:postChangePassword');

    $this->get('/browse', 'BundleController:getBrowse')->setName('bundles.browse');

    $this->group('/dashboard', function () {
        $this->get('/profile[/{username}]', 'DashboardController:getProfile')->setName('dashboard.user.profile');
        $this->get('/upload', 'DashboardController:getUpload')->setName('dashboard.user.uploadasset');
        $this->post('/upload', 'DashboardController:postUpload');
    });

})->add(new AuthMiddleware($container));

$app->group('/api', function() {
    $this->get('/browse[/{query}]', 'BundleController:getBrowseWithQuery')->setName('bundles.getBrowse');
    $this->post('/upload-asset', 'APIController:postUpload');
    $this->post('/auth', 'APIController:postAuth');
    $this->post('/verify-code', 'APIController:postAuthCode');
});

$app->get('/bundle/{username}/{bundlename}', 'BundleController:downloadBundle')->setName('bundle.downloadzip');

$app->get('/download/{username}/{bundlename}[/{version}]', 'BundleController:downloadBundle')->setName('bundle.download');

$app->get('/latest/{username}/{bundlename}', 'BundleController:getLatestVersion')->setName('bundle.latestVersion');
