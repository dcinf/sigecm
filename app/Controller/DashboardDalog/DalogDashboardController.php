<?php

    namespace App\Controller\DashboardDalog;
    use App\Controller\DalogPageController;
    use App\Utils\ViewManager;

    class DalogDashboardController extends DalogPageController
    {


        public static function getDalogDashboard($request)
        {
            #==================================================
            # Pagina principal de dashboard
            #==================================================

            $content = ViewManager::render('dashboardDalog/painel', [
                'navbar'        => parent::getNavbar(),
                'sidebar'       => parent::getMenu(),
                'footer'        => parent::getFooter(),
                //'users'         => $quantidadeTotal,
                //'designation'   => 'Utilizadores Activos' 
            ]);

            return parent::getPage('SIGECM | Painel Incial', $content);
        }
    }
