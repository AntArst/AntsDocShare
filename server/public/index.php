<?php

// Error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Load Composer autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Start session for web routes
session_start();

// Initialize router
$router = new \App\Api\Router();
$router->route();

