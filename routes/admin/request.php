<?php
    use App\Http\Response;
    use App\Controller\Dashboard\RequestWeaponsController;
    use App\Controller\Dashboard\PdfGenaratorController;

    $objRouter->get('/requests', [
        'middlewares'   => [
            'requere-admin-login'
        ],
        function ($request){
            return new Response(200, RequestWeaponsController::getRequestWeaponPage($request));
        }
    ]);

   $objRouter->get('/new-request', [
        'middlewares'   => [
            'requere-admin-login'
        ],
        function ($request){
            return new Response(200, RequestWeaponsController::getNewRequestWithdraw($request));
        }
    ]);

   $objRouter->post('/new-request', [
        'middlewares'   => [
            'requere-admin-login'
        ],
        function ($request){
            return new Response(200, RequestWeaponsController::SetNewRequestWithdraw($request));
        }
    ]);

    $objRouter->get('/request-report&id={id}', [
        'middlewares'   => [
            'requere-admin-login'
        ],
        function ($request,  $id){
            return new Response(200, PdfGenaratorController::RequestPDFGenarator($request, $id));
        }
    ]);

?>