<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$this->group('', ['namespace' => 'Koru\Discussions\Controllers'], static function (RouteCollection $routes) {
    // Tags
    $routes->get('t/(:segment)', 'DiscussionController::tag/$1', ['as' => 'tag']);

    // Discussions
    $routes->get('discussions', 'DiscussionController::list', ['as' => 'discussions']);

    // Threads
    $routes->match(['GET', 'POST'], 'discussions/new', 'ThreadController::create', ['as' => 'thread-create']);
    $routes->match(['GET', 'PUT'], 'discussions/(:num)/edit', 'ThreadController::edit/$1', ['as' => 'thread-edit']);
    $routes->post('discussions/preview', 'ThreadController::preview', ['as' => 'thread-preview']);
    $routes->get('discussions/(:num)/show', 'ThreadController::show/$1', ['as' => 'thread-show']);
    $routes->get('discussions/(:num)/delete', 'ThreadController::delete/$1', ['as' => 'thread-delete']);
    $routes->get('discussions/(:segment)/(:segment)?post_id=(:num)', 'DiscussionController::thread/$2', ['as' => 'post']);
    $routes->get('discussions/(:segment)/(:segment)', 'DiscussionController::thread/$2', ['as' => 'thread']);

    $routes->post('thread/(:num)/set-answer', 'ThreadController::manageAnswer/$1/set', ['as' => 'thread-set-answer']);
    $routes->post('thread/(:num)/unset-answer', 'ThreadController::manageAnswer/$1/unset', ['as' => 'thread-unset-answer']);

    // Posts
    $routes->match(['GET', 'POST'], 'posts/(:num)', 'PostController::create/$1', ['as' => 'post-create']);
    $routes->match(['GET', 'POST'], 'posts/(:num)/(:num)', 'PostController::create/$1/$2', ['as' => 'post-create-reply']);
    $routes->match(['GET', 'PUT'], 'posts/(:num)/edit', 'PostController::edit/$1', ['as' => 'post-edit']);
    $routes->post('posts/preview', 'PostController::preview', ['as' => 'post-preview']);
    $routes->get('posts/(:num)/show', 'PostController::show/$1', ['as' => 'post-show']);
    $routes->get('posts/replies/(:num)', 'PostController::allReplies/$1', ['as' => 'post-replies-load']);
    $routes->get('posts/(:num)/delete', 'PostController::delete/$1', ['as' => 'post-delete']);


    // Account routes
    $routes->group('account', ['filter'], static function (RouteCollection $routes) {
        $routes->get('posts', 'Account\AccountController::posts', ['as' => 'account-posts']);
        $routes->get('threads', 'Account\AccountController::threads', ['as' => 'account-threads']);
    });
});
