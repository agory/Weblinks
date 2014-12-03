<?php

// Home page
$app->get('/', "Weblinks\Controller\HomeController::indexAction");

// Detailed info about an link
$app->match('/link/submit', "Weblinks\Controller\HomeController::AddLink");

// Login form
$app->get('/login', "Weblinks\Controller\HomeController::loginAction")
->bind('login');  // named route so that path('login') works in Twig templates

// Admin zone
$app->get('/admin', "Weblinks\Controller\AdminController::indexAction");

// Edit an existing link
$app->match('/admin/link/{id}/edit', "Weblinks\Controller\AdminController::editLink");

// Remove an link
$app->get('/admin/link/{id}/delete', "Weblinks\Controller\AdminController::deleteLink");

// Add a user
$app->match('/admin/user/add', "Weblinks\Controller\AdminController::addUser");

// Edit an existing user
$app->match('/admin/user/{id}/edit', "Weblinks\Controller\AdminController::editUser");

// Remove a user
$app->get('/admin/user/{id}/delete', "Weblinks\Controller\AdminController::deleteUser");



