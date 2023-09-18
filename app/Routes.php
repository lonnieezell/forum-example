<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', static fn () => redirect()->to('/discussions'));

// Categories
$routes->get('c/(:segment)', 'Discussions\DiscussionController::category/$1', ['as' => 'category']);

// Tags
$routes->get('t/(:segment)', 'Discussions\TagController::get/$1', ['as' => 'tag']);

// Discussions
$routes->get('discussions', 'Discussions\DiscussionController::list');

// Threads
$routes->match(['get', 'post'], 'discussions/new', 'Discussions\ThreadController::create', ['as' => 'thread-create']);
$routes->match(['get', 'put'], 'discussions/(:num)/edit', 'Discussions\ThreadController::edit/$1', ['as' => 'thread-edit']);
$routes->post('discussions/preview', 'Discussions\ThreadController::preview', ['as' => 'thread-preview']);
$routes->get('discussions/(:segment)/(:segment)', 'Discussions\DiscussionController::thread/$2', ['as' => 'thread']);

// Posts
$routes->match(['get', 'post'], 'posts/(:num)', 'Discussions\PostController::create/$1', ['as' => 'post-create']);
$routes->match(['get', 'post'], 'posts/(:num)/(:num)', 'Discussions\PostController::create/$1/$2', ['as' => 'post-create-reply']);
$routes->match(['get', 'put'], 'posts/(:num)/edit', 'Discussions\PostController::edit/$1', ['as' => 'post-edit']);
$routes->post('post/preview', 'Discussions\PostController::preview', ['as' => 'post-preview']);
$routes->get('post/replies/(:num)', 'Discussions\PostController::allReplies/$1', ['as' => 'post-replies-load']);

// Members
$routes->get('members', 'Members\MemberController::list');
$routes->get('members/(:segment)', 'Members\MemberController::profile/$1', ['as' => 'profile']);

// General Error Page.
$routes->get('display-error', 'ErrorController::general', ['as' => 'general-error']);

// Shield Auth routes
service('auth')->routes($routes);
