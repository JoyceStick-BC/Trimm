<?php
/* structure of a route
$app->get('/home', function ($request, $response) {
	return 'Home';
});
*/

use Carbon\Middleware\AuthMiddleware;
use Carbon\Middleware\GuestMiddleware;
use Carbon\Middleware\StripeMiddleware;

$app->get('/', 'HomeController:index')->setName('home');

$app->group('', function () {
    $this->get('/auth/signup', 'AuthController:getSignUp')->setName('auth.signup');
    $this->post('/auth/signup', 'AuthController:postSignUp');

    $this->get('/auth/signin', 'AuthController:getSignIn')->setName('auth.signin');
    $this->post('/auth/signin', 'AuthController:postSignIn');
})->add(new GuestMiddleware($container));

$app->group('', function () use ($container) {
    $this->get('/auth/signout', 'AuthController:getSignOut')->setName('auth.signout');

    $this->get('/auth/password/change', 'PasswordController:getChangePassword')->setName('auth.password.change');
    $this->post('/auth/password/change', 'PasswordController:postChangePassword');

    $this->get('/browse', 'SearchController:getBrowse')->setName('bundles.browse');

    $this->group('/dashboard', function () {
        $this->get('/profile[/{username}]', 'DashboardController:getProfile')->setName('dashboard.user.profile');
        $this->get('/upload', 'DashboardController:getUpload')->setName('dashboard.user.uploadasset');
        $this->post('/upload', 'DashboardController:postUpload');
    });

    $this->group('/account', function() {
        $this->get('/payment', 'AccountController:getPayment')->setName('account.getPayment');
        $this->post('/payment', 'AccountController:postPayment')->setName('account.postPayment');
        $this->get('/bankInfo', 'AccountController:getBankInfo')->setName('account.getBankInfo');
        $this->post('/bankInfo', 'AccountController:postBankInfo')->setName('account.postBankInfo');
        $this->get('/help', 'AccountController:getBankHelp')->setName('account.help');
        $this->get('/confirmCharge/{user}/{bundleName}', 'AccountController:getCharge')->setName('account.confirmCharge');
    })->add(new StripeMiddleware($container));

})->add(new AuthMiddleware($container));

$app->group('/api', function() {
    $this->get('/browse[/{query}]', 'SearchController:getBrowseWithQuery')->setName('bundles.getBrowse');
    $this->post('/charge', 'AccountController:postCharge')->setName('account.postCharge');
    $this->get('/users/{query}', 'SearchController:getBrowseUsers')->setName('users.getBrowse');
});

$app->get('/download/{username}/{bundlename}[/{version}]', 'BundleController:downloadBundle')->setName('bundle.download');

$app->get('/latest/{username}/{bundlename}', 'BundleController:getLatestVersion')->setName('bundle.latestVersion');