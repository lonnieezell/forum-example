<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', static fn () => redirect()->to('/discussions'));

// Categories
$routes->get('c/(:segment)', 'Discussions\DiscussionController::category/$1', ['as' => 'category']);

// Tags
$routes->get('t/(:segment)', 'Discussions\DiscussionController::tag/$1', ['as' => 'tag']);

// Discussions
$routes->get('discussions', 'Discussions\DiscussionController::list');

// Threads
$routes->match(['get', 'post'], 'discussions/new', 'Discussions\ThreadController::create', ['as' => 'thread-create']);
$routes->match(['get', 'put'], 'discussions/(:num)/edit', 'Discussions\ThreadController::edit/$1', ['as' => 'thread-edit']);
$routes->post('discussions/preview', 'Discussions\ThreadController::preview', ['as' => 'thread-preview']);
$routes->get('discussions/(:num)/show', 'Discussions\ThreadController::show/$1', ['as' => 'thread-show']);
$routes->get('discussions/(:segment)/(:segment)?post_id=(:num)', 'Discussions\DiscussionController::thread/$2', ['as' => 'post']);
$routes->get('discussions/(:segment)/(:segment)', 'Discussions\DiscussionController::thread/$2', ['as' => 'thread']);

// Posts
$routes->match(['get', 'post'], 'posts/(:num)', 'Discussions\PostController::create/$1', ['as' => 'post-create']);
$routes->match(['get', 'post'], 'posts/(:num)/(:num)', 'Discussions\PostController::create/$1/$2', ['as' => 'post-create-reply']);
$routes->match(['get', 'put'], 'posts/(:num)/edit', 'Discussions\PostController::edit/$1', ['as' => 'post-edit']);
$routes->post('posts/preview', 'Discussions\PostController::preview', ['as' => 'post-preview']);
$routes->get('posts/(:num)/show', 'Discussions\PostController::show/$1', ['as' => 'post-show']);
$routes->get('posts/replies/(:num)', 'Discussions\PostController::allReplies/$1', ['as' => 'post-replies-load']);

// Image upload
$routes->post('images/upload', 'Discussions\ImageController::upload');

// Members
$routes->get('members', 'Members\MemberController::list');
$routes->get('members/(:segment)', 'Members\MemberController::profile/$1', ['as' => 'profile']);

// General Error Page.
$routes->get('display-error', 'ErrorController::general', ['as' => 'general-error']);

// Account routes
$routes->group('account', ['filter'], static function (RouteCollection $routes) {
    $routes->get('/', 'Account\AccountController::index', ['as' => 'account']);
    $routes->get('posts', 'Account\AccountController::posts', ['as' => 'account-posts']);
    $routes->match(['get', 'post'], 'notifications', 'Account\AccountController::notifications', ['as' => 'account-notifications']);
    $routes->get('security', 'Account\SecurityController::index', ['as' => 'account-security']);
    $routes->post('security/logout-all', 'Account\SecurityController::logoutAll', ['as' => 'account-security-logout-all']);
    $routes->post('security/delete', 'Account\SecurityController::deleteAccount', ['as' => 'account-security-delete']);
    $routes->post('security/change-password', 'Account\SecurityController::changePassword', ['as' => 'account-change-password']);
});

// Actions
$routes->get('thread-notifications/(:num)/(:num)/(:segment)', 'ActionsController::notifications/$1/$2/$3', ['as' => 'action-thread-notifications']);

// Shield Auth routes
service('auth')->routes($routes);
