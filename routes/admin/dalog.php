<?php

use App\Controller\DashboardDalog\AdminController;
use App\Controller\DashboardDalog\FardamentosController;
use App\Http\Response;
use App\Controller\DashboardDalog\FuncionarioController;
use App\Controller\DashboardDalog\GestaoStoqueController;

#============================================================================
# Rotas de funcionarios
#============================================================================
$objRouter->get('/funcionarios', [
    'middlewares'   => [
        'requere-admin-login'
    ],
    function ($request) {
        return new Response(200, FuncionarioController::getFuncionarios($request));
    }
]);

$objRouter->get('/new-funcionario', [
    'middlewares'   => [
        'requere-admin-login'
    ],
    function ($request) {
        return new Response(200, FuncionarioController::getNewFuncionario($request));
    }
]);

$objRouter->post('/new-funcionario', [
    'middlewares'   => [
        'requere-admin-login'
    ],
    function ($request) {
        return new Response(200, FuncionarioController::setNewFuncionario($request));
    }
]);


#============================================================================
# Rotas de funcionarios
#============================================================================


$objRouter->get('/fardamentos', [
    'middlewares'   => [
        'requere-admin-login'
    ],
    function ($request) {
        return new Response(200, FardamentosController::getFardamento($request));
    }
]);

$objRouter->get('/new-fardamentos', [
    'middlewares'   => [
        'requere-admin-login'
    ],
    function ($request) {
        return new Response(200, FardamentosController::getNewFardamento($request));
    }
]);

$objRouter->post('/new-fardamentos', [
    'middlewares'   => [
        'requere-admin-login'
    ],
    function ($request) {
        return new Response(200, FardamentosController::setNewFardamento($request));
    }
]);


#============================================================================
# Rotas de Estoque
#============================================================================

$objRouter->get('/stoque-fardamentos', [
    'middlewares'   => [
        'requere-admin-login'
    ],
    function ($request) {
        return new Response(200, GestaoStoqueController::getFardamentosUpdate($request));
    }
]);


$objRouter->post('/stoque-fardamentos', [
    'middlewares'   => [
        'requere-admin-login'
    ],
    function ($request) {
        return new Response(200, GestaoStoqueController::setFardamentosUpdate($request));
    }
]);



#============================================================================
# Rotas de Administracao do chefe da reparticao
#============================================================================

$objRouter->get('/dalog-users', [
    'middlewares'   => [
        'requere-admin-login'
    ],
    function ($request) {
        return new Response(200, AdminController::getRegisterReparticaoPage($request));
    }
]);

$objRouter->get('/new-dalog-user', [
    'middlewares'   => [
        'requere-admin-login'
    ],
    function ($request) {
        return new Response(200, AdminController::getNewRegisterReparticao($request));
    }
]);



