<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
//$routes->get('/', 'Home::index');

$routes->group('api', ['namespace' => 'App\Controllers'], function ($routes) 
{
    $routes->post('register', 'AuthController::register');
    $routes->post('login', 'AuthController::login');
    
    // Rutas protegidas que requieren autenticación
    $routes->group('', ['filter' => 'jwt'], function ($routes) 
    {
        $routes->get('profile', 'AuthController::profile');
        // $routes->post('logout', 'AuthController::logout');
        // Aquí puedes agregar otras rutas protegidas
    });
});