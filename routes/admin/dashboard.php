<?php
    use App\Http\Response;
    use App\Controller\Dashboard\DashboardController;

    $objRouter->get('/painel', [
        'middlewares'   => [
            'requere-admin-login'
        ],
        function ($request){
            return new Response(200, DashboardController::getDashboard($request));
        }
    ]);

    $objRouter->post('/painel', [
        'middlewares'   => [
            'requere-admin-login'
        ],
        function ($request){
            return new Response(200, DashboardController::setChangePassword($request));
        }
    ]);
?>