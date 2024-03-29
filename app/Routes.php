<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', static fn () => redirect()->to('/discussions'));

$routes->get('terms-of-service', 'LegalController::terms', ['as' => 'terms']);
$routes->get('privacy-policy', 'LegalController::privacy', ['as' => 'privacy']);

// Categories
$routes->get('c/(:segment)', 'Discussions\DiscussionController::category/$1', ['as' => 'category']);

// Tags
$routes->get('t/(:segment)', 'Discussions\DiscussionController::tag/$1', ['as' => 'tag']);

// Discussions
$routes->get('discussions', 'Discussions\DiscussionController::list', ['as' => 'discussions']);

// Threads
$routes->match(['get', 'post'], 'discussions/new', 'Discussions\ThreadController::create', ['as' => 'thread-create']);
$routes->match(['get', 'put'], 'discussions/(:num)/edit', 'Discussions\ThreadController::edit/$1', ['as' => 'thread-edit']);
$routes->post('discussions/preview', 'Discussions\ThreadController::preview', ['as' => 'thread-preview']);
$routes->get('discussions/(:num)/show', 'Discussions\ThreadController::show/$1', ['as' => 'thread-show']);
$routes->get('discussions/(:num)/delete', 'Discussions\ThreadController::delete/$1', ['as' => 'thread-delete']);
$routes->get('discussions/(:segment)/(:segment)?post_id=(:num)', 'Discussions\DiscussionController::thread/$2', ['as' => 'post']);
$routes->get('discussions/(:segment)/(:segment)', 'Discussions\DiscussionController::thread/$2', ['as' => 'thread']);

$routes->post('thread/(:num)/set-answer', 'Discussions\ThreadController::manageAnswer/$1/set', ['as' => 'thread-set-answer']);
$routes->post('thread/(:num)/unset-answer', 'Discussions\ThreadController::manageAnswer/$1/unset', ['as' => 'thread-unset-answer']);

// Posts
$routes->match(['get', 'post'], 'posts/(:num)', 'Discussions\PostController::create/$1', ['as' => 'post-create']);
$routes->match(['get', 'post'], 'posts/(:num)/(:num)', 'Discussions\PostController::create/$1/$2', ['as' => 'post-create-reply']);
$routes->match(['get', 'put'], 'posts/(:num)/edit', 'Discussions\PostController::edit/$1', ['as' => 'post-edit']);
$routes->post('posts/preview', 'Discussions\PostController::preview', ['as' => 'post-preview']);
$routes->get('posts/(:num)/show', 'Discussions\PostController::show/$1', ['as' => 'post-show']);
$routes->get('posts/replies/(:num)', 'Discussions\PostController::allReplies/$1', ['as' => 'post-replies-load']);
$routes->get('posts/(:num)/delete', 'Discussions\PostController::delete/$1', ['as' => 'post-delete']);

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
    $routes->get('threads', 'Account\AccountController::threads', ['as' => 'account-threads']);
    $routes->match(['get', 'post'], 'notifications', 'Account\AccountController::notifications', ['as' => 'account-notifications']);
    $routes->get('security', 'Account\SecurityController::index', ['as' => 'account-security']);
    $routes->post('security/logout-all', 'Account\SecurityController::logoutAll', ['as' => 'account-security-logout-all']);
    $routes->post('security/delete', 'Account\SecurityController::deleteAccount', ['as' => 'account-security-delete']);
    $routes->post('security/change-password', 'Account\SecurityController::changePassword', ['as' => 'account-change-password']);
    $routes->post('security/two-factor-auth-email', 'Account\SecurityController::twoFactorAuthEmail', ['as' => 'account-two-factor-auth-email']);
    $routes->match(['get', 'post'], 'profile', 'Account\AccountController::profile', ['as' => 'account-profile']);
    $routes->post('avatar', 'Account\AccountController::deleteAvatar', ['as' => 'account.avatar.delete']);
});

// Actions
$routes->get('thread-notifications/(:num)/(:num)/(:segment)', 'ActionsController::notifications/$1/$2/$3', ['as' => 'action-thread-notifications']);
$routes->get('cancel-account-delete/(:num)', 'ActionsController::cancelAccountDelete/$1', ['as' => 'action-cancel-account-delete']);

// Help section
$routes->match(['get', 'post'], 'help', 'HelpController::index', ['as' => 'pages']);
$routes->match(['get', 'post'], 'help/(:any)', 'HelpController::show/$1', ['as' => 'page']);

// Report
$routes->match(['get', 'post'], 'report/(:num)/thread', 'Discussions\ReportController::index/$1/thread', ['as' => 'thread-report']);
$routes->match(['get', 'post'], 'report/(:num)/post', 'Discussions\ReportController::index/$1/post', ['as' => 'post-report']);

// Moderation area
$routes->group('moderation', ['filter'], static function (RouteCollection $routes) {
    $routes->get('/', static fn () => redirect()->to('moderation/reports/threads'));
    $routes->get('reports/threads', 'Moderation\ReportsController::list/thread', ['as' => 'moderate-threads']);
    $routes->get('reports/posts', 'Moderation\ReportsController::list/post', ['as' => 'moderate-posts']);
    $routes->get('reports/logs', 'Moderation\ReportsController::logs', ['as' => 'moderate-logs']);
    $routes->post('action/(:segment)', 'Moderation\ReportsController::action/$1', ['as' => 'moderate-action']);
});

// Content utilities
$routes->group('content', ['filter'], static function (RouteCollection $routes) {
    $routes->post('react/(:num)/(:segment)', 'Discussions\ReactionController::toggleReaction/$1/$2', ['as' => 'react-to']);
});

// Shield Auth routes
service('auth')->routes($routes, ['namespace' => 'App\Controllers\Auth']);
