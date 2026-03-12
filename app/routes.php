<?php
// app/routes.php
// Define route patterns

// Admin routes
$router->get('/admin', 'AdminController::index');
$router->post('/admin', 'AdminController::index');
$router->get('/admin/dashboard', 'AdminController::dashboard');
$router->get('/admin/logout', 'AdminController::logout');

// Admin Categories
$router->get('/admin/categories', 'AdminController::categories');
$router->get('/admin/categories/add', 'AdminController::addCategory');
$router->post('/admin/categories/add', 'AdminController::addCategory');
$router->get('/admin/categories/edit/{id}', 'AdminController::editCategory');
$router->post('/admin/categories/edit/{id}', 'AdminController::editCategory');
$router->get('/admin/categories/delete/{id}', 'AdminController::deleteCategory');

// Admin Products
$router->get('/admin/products', 'AdminController::products');
$router->get('/admin/products/add', 'AdminController::addProduct');
$router->post('/admin/products/add', 'AdminController::addProduct');
$router->get('/admin/products/addproduct', 'AdminController::addProduct');
$router->post('/admin/products/addproduct', 'AdminController::addProduct');
$router->get('/admin/products/edit/{id}', 'AdminController::editProduct');
$router->post('/admin/products/edit/{id}', 'AdminController::editProduct');
$router->get('/admin/products/delete/{id}', 'AdminController::deleteProduct');

// Admin Settings
$router->get('/admin/settings', 'AdminController::settings');
$router->post('/admin/settings', 'AdminController::settings');

// Client/Frontend routes
$router->get('/', 'PagesController::index');
$router->get('/about', 'PagesController::about');
$router->get('/products', 'ProductsController::index');
$router->get('/products/category/{id}', 'ProductsController::category');
$router->get('/products/{id}', 'ProductsController::show');
$router->get('/contact', 'PagesController::contact');
$router->post('/contact', 'PagesController::contact'); 