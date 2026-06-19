<?php

require_once __DIR__ . '/../app/config/bootstrap.php';

use App\Controllers\AuthController;
use App\Controllers\MessageController;
use App\Controllers\SessionController;
use App\Controllers\ConvocationController;
use App\Controllers\ValveController;
use App\Controllers\FileController;
use App\Controllers\UserController;
use App\Controllers\CourseController;

session_start();

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// simple router for API endpoints
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

switch ($uri) {
    case '/':
    case '/login':
        $controller = new AuthController();
        $controller->showLogin();
        break;
    case '/api/login':
        $controller = new AuthController();
        $controller->login();
        break;
    case '/api/logout':
        $controller = new AuthController();
        $controller->logout();
        break;
    case '/api/messages/private':
        header('Content-Type: application/json');
        $c = new MessageController();
        $c->sendPrivate();
        break;
    case '/api/messages/public':
        header('Content-Type: application/json');
        $c = new MessageController();
        $c->sendPublic();
        break;
    case '/api/messages/inbox':
        header('Content-Type: application/json');
        $c = new MessageController();
        $c->inbox();
        break;
    case '/api/messages/conversation':
        header('Content-Type: application/json');
        $c = new MessageController();
        $c->conversation();
        break;
    case '/api/messages/sent':
        header('Content-Type: application/json');
        $c = new MessageController();
        $c->sent();
        break;
    case '/api/messages/delete':
        header('Content-Type: application/json');
        $c = new MessageController();
        $c->deleteMessage();
        break;
    case '/api/convocations/send':
        header('Content-Type: application/json');
        $c = new ConvocationController();
        $c->send();
        break;
    case '/api/convocations/list':
        header('Content-Type: application/json');
        $c = new ConvocationController();
        $c->list();
        break;
    case '/api/valve/create':
        header('Content-Type: application/json');
        $c = new ValveController();
        $c->create();
        break;
    case '/api/valve/list':
        header('Content-Type: application/json');
        $c = new ValveController();
        $c->list();
        break;
    case '/api/users/create':
        header('Content-Type: application/json');
        $c = new UserController();
        $c->create();
        break;
    case '/api/users/list':
        header('Content-Type: application/json');
        $c = new UserController();
        $c->list();
        break;
    case '/api/session/profile':
        header('Content-Type: application/json');
        $c = new SessionController();
        $c->profile();
        break;
    case '/api/courses/create':
        header('Content-Type: application/json');
        $c = new CourseController();
        $c->create();
        break;
    case '/api/courses/list':
        header('Content-Type: application/json');
        $c = new CourseController();
        $c->list();
        break;
    case '/api/upload':
        header('Content-Type: application/json');
        $uploadPath = 'C:\\Users\\HP\\Pictures';
        $c = new FileController($uploadPath);
        $c->upload();
        break;
    default:
        http_response_code(404);
        echo 'Page not found';
        break;
}



//cd "c:\Users\HP\Desktop\Fasi chat"
//php -S 127.0.0.1:8000 -t public public/router.php
//http://127.0.0.1:8000/login.html