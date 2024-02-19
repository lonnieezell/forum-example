<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->group('admin', static function (RouteCollection $routes) {
    $routes->get('/', 'Admin\DashboardController::index', ['as' => 'admin-dashboard']);

    // Settings
    $routes->group('settings', static function (RouteCollection $routes) {
        $routes->match(['get', 'post'], 'users', 'Admin\Settings\UsersController::index', ['as' => 'settings-users']);
        $routes->match(['get', 'post'], 'trust-levels', 'Admin\Settings\TrustLevelsController::index', ['as' => 'settings-trust']);
    });
});
