<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->group('admin', static function (RouteCollection $routes) {
    $routes->get('/', 'Admin\DashboardController::index', ['as' => 'admin-dashboard']);
});
