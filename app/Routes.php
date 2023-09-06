<?php

use CodeIgniter\Router\RouteCollection;

$routes->get('/', function () {
    return redirect()->to('/categories');
});

// Discussions
$routes->get('categories', 'Discussions\DiscussionController::forums');
$routes->get('categories/(:segment)', 'Discussions\DiscussionController::forum/$1', ['as' => 'forum']);
$routes->get('discussions', 'Discussions\DiscussionController::list');
$routes->match(['get', 'post'], 'discussions/new', 'Discussions\ThreadController::create', ['as' => 'thread-create']);
$routes->post('discussions/preview', 'Discussions\ThreadController::preview', ['as' => 'thread-preview']);
$routes->get('discussions/(:segment)/(:segment)', 'Discussions\DiscussionController::thread/$2', ['as' => 'thread']);
$routes->match(['get', 'post'], 'posts/(:segment)', 'Discussions\PostController::create/$1', ['as' => 'post-create']);
$routes->post('post/preview', 'Discussions\PostController::preview', ['as' => 'post-preview']);

// Members
$routes->get('members', 'Members\MemberController::list');
$routes->get('members/(:segment)', 'Members\MemberController::profile/$1', ['as' => 'profile']);

service('auth')->routes($routes);
