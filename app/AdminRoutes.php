<?php

use CodeIgniter\HTTP\Method;
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->group('admin', static function (RouteCollection $routes) {
    $routes->get('/', 'Admin\DashboardController::index', ['as' => 'admin-dashboard']);

    // Settings
    $routes->group('settings', static function (RouteCollection $routes) {
        $routes->match([Method::GET, Method::POST], 'users', 'Admin\Settings\UsersController::index', ['as' => 'settings-users']);
        $routes->match([Method::GET, Method::POST], 'trust-levels', 'Admin\Settings\TrustLevelsController::index', ['as' => 'settings-trust']);
    });
});
