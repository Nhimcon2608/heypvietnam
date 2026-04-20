<?php
// app/routes.php
// Define route patterns

// Admin routes
$router->get('/admin', 'AdminController::index');
$router->post('/admin', 'AdminController::index');
$router->get('/admin/pages', 'AdminController::pages');
$router->post('/admin/pages', 'AdminController::pages');
$router->get('/admin/logout', 'AdminController::logout');

// Client/Frontend routes
$router->get('/', 'PagesController::index');
$router->get('/about', 'PagesController::about');
$router->get('/contact', 'PagesController::contact');
$router->post('/contact', 'PagesController::contact');
