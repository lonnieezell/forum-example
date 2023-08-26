<?php

use CodeIgniter\Router\RouteCollection;

$routes->get('/', function () {
    return redirect()->to('/categories');
});

// Discussions
$routes->get('categories', 'Discussions\DiscussionController::forums');
$routes->get('categories/(:segment)', 'Discussions\DiscussionController::forum/$1', ['as' => 'forum']);
$routes->get('discussions', 'Discussions\DiscussionController::list');
$routes->get('discussions/(:segment)/(:segment)', 'Discussions\DiscussionController::thread/$2', ['as' => 'thread']);

// Members
$routes->get('members', 'Members\MemberController::list');
$routes->get('members/(:segment)', 'Members\MemberController::profile/$1', ['as' => 'profile']);

service('auth')->routes($routes);
