<?php
    use App\Http\Response;
    use App\Controller\Dashboard\ProfileController;

    #======================================================
    # Rotas referentes a classificacao
    #======================================================
    $objRouter->get('/profile', [
        'middlewares'   => [
            'requere-admin-login'
        ],
        function ($request){
            return new Response(200, ProfileController::getProfilePage($request));
        }
    ]);

    $objRouter->post('/profile', [
        'middlewares'   => [
            'requere-admin-login'
        ],
        function ($request){
            return new Response(200, ProfileController::updateUserPassword($request));
        }
    ]); 


?>