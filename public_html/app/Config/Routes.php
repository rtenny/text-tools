<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'DashboardController::index', ['filter' => 'auth']);

/*
 * --------------------------------------------------------------------
 * Public Routes (Authentication)
 * --------------------------------------------------------------------
 */
$routes->get('login', 'Auth\LoginController::index');
$routes->post('login', 'Auth\LoginController::authenticate');
$routes->get('logout', 'Auth\LogoutController::index');
$routes->get('password-reset/(:num)/(:segment)', 'Auth\PasswordResetController::verify/$1/$2');
$routes->post('password-reset/(:num)/(:segment)', 'Auth\PasswordResetController::reset/$1/$2');

/*
 * --------------------------------------------------------------------
 * Protected Routes
 * --------------------------------------------------------------------
 */
// Role-based dashboard redirect (protected by 'auth' filter)
$routes->get('dashboard', 'DashboardController::index', ['filter' => 'auth']);

/*
 * --------------------------------------------------------------------
 * Superadmin Routes
 * --------------------------------------------------------------------
 * Protected by 'auth' and 'superadmin' filters
 */
$routes->group('superadmin', ['filter' => ['auth', 'superadmin']], function($routes) {
    // Dashboard
    $routes->get('dashboard', 'Superadmin\DashboardController::index');

    // Projects Management
    $routes->get('projects', 'Superadmin\ProjectsController::index');
    $routes->get('projects/create', 'Superadmin\ProjectsController::create');
    $routes->post('projects/create', 'Superadmin\ProjectsController::store');
    $routes->get('projects/edit/(:num)', 'Superadmin\ProjectsController::edit/$1');
    $routes->post('projects/edit/(:num)', 'Superadmin\ProjectsController::update/$1');
    $routes->post('projects/delete/(:num)', 'Superadmin\ProjectsController::delete/$1');

    // Admins Management
    $routes->get('users', 'Superadmin\UsersController::index');
    $routes->get('users/create', 'Superadmin\UsersController::create');
    $routes->post('users/create', 'Superadmin\UsersController::store');
    $routes->get('users/password-reset-link/(:num)', 'Superadmin\UsersController::generatePasswordResetLink/$1');
});

/*
 * --------------------------------------------------------------------
 * Admin Routes
 * --------------------------------------------------------------------
 * Protected by 'auth', 'admin', and 'tenant' filters
 */
$routes->group('admin', ['filter' => ['auth', 'admin', 'tenant']], function($routes) {
    // Dashboard
    $routes->get('dashboard', 'Admin\DashboardController::index');

    // Users Management (within project)
    $routes->get('users', 'Admin\UsersController::index');
    $routes->get('users/create', 'Admin\UsersController::create');
    $routes->post('users/create', 'Admin\UsersController::store');
    $routes->get('users/password-reset-link/(:num)', 'Admin\UsersController::generatePasswordResetLink/$1');
});

/*
 * --------------------------------------------------------------------
 * User / Tools Routes
 * --------------------------------------------------------------------
 * Protected by 'auth' and 'tenant' filters
 */
$routes->group('tools', ['filter' => ['auth', 'tenant']], function($routes) {
    $routes->get('/', 'Tools\TranslatorController::index');
    $routes->post('translate', 'Tools\TranslatorController::translate');
    $routes->post('rewrite', 'Tools\RewriterController::rewrite');
    $routes->post('generate', 'Tools\GeneratorController::generate');
});
