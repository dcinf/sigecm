<?php
    namespace App\Controller;

use App\Model\Entity\DepartamentoEntity;
use App\Utils\Funcoes;
    use App\Utils\ViewManager;
    use DateTime;
    use DateTimeZone;

    class DalogPageController{
        public static function getPage($title, $content){
            return ViewManager::render('pageDalog', [
                'title'          => $title,
                'content'        => $content
            ]);
        }

         public static function getNavbar(){
            return ViewManager::render('dashboardDalog/navbar', [
                'name'              => $_SESSION['admin']['utilizador']['nome_utilizador'],
                'profile_pic'       => ''
            ]);
        }

        public static function getFooter(){
            return ViewManager::render('dashboardDalog/footer', []);
        }

         private static function getAdminMenu(){
            $itens = '';
            $itens .= ViewManager::render('dashboardDalog/menu/administration/admin', []);   
            return $itens;
        }


        public static function getMenu(){
            if(Funcoes::Permition(16)){
                return ViewManager::render('dashboardDalog/menu/box', [
                    'admin'               => self::getAdminMenu(),
                ]);
            }elseif(Funcoes::Permition(5)){
                return ViewManager::render('dashboardDalog/menu/box', [
                    'admin'               => '',
                ]);
            }
        }


        #=========================================================================
        # FUNCOES REUTILIZAVEIS
        #=========================================================================
        //Funcao que busca os departamentos para um select
        public static function getDepartamentos() {
            $itens = '';
            $results = DepartamentoEntity::getDepartamentos(null, 'codigo_departamento', null);
            // Iterate through each type
            while ($objDepartamento = $results->fetchObject(DepartamentoEntity::class)) {
                $itens .= ViewManager::render('dashboard/modules/global/globalItem', [
                    'codigo'                      => $objDepartamento->codigo_departamento,
                    'descricao'                   => $objDepartamento->nome_departamento
                ]);
            }
            
            return $itens;
        }


        //funcao que gera 0 e 1 
        public static function geradorPermissoes(array $ranges, int $comprimento = 100): string {
            $permissoes = array_fill(0, $comprimento, '0');
        
            foreach ($ranges as $range) {
                $inicio = $range[0];
                $fim = $range[1];
        
                for ($i = $inicio; $i <= $fim && $i < $comprimento; $i++) {
                    $permissoes[$i] = '1';
                }
            }
        
            return implode('', $permissoes);
        }

        #=========================================================================
        # FIM FUNCOES REUTILIZAVEIS
        #=========================================================================

         #==========================================================================
        # Funcoes que lidam com as formatacoes das datas
        #==========================================================================
        public static function getNowDateTime(){
            $date = new DateTime('now', new DateTimeZone('Africa/Maputo')); 
            return $date->format('Y-m-d H:i:s');
        }

        public static function getNowDate(){
            $date = new DateTime('now', new DateTimeZone('Africa/Maputo')); 
            return $date->format('Y-m-d');
        }

        public static function getFormattedData($data){
            $date = new DateTime($data, new DateTimeZone('UTC'));

            $fmtDate = new \IntlDateFormatter(
                'pt_MZ', \IntlDateFormatter::LONG, \IntlDateFormatter::NONE, null, \IntlDateFormatter::GREGORIAN
            );

            $fmtTime = new \IntlDateFormatter(
                'pt_MZ', \IntlDateFormatter::NONE, \IntlDateFormatter::SHORT,null, \IntlDateFormatter::GREGORIAN 
            );

            $formattedDate = $fmtDate->format($date); 
            $formattedTime = $fmtTime->format($date);

            $formattedDateTime = $formattedDate . ' às ' . $formattedTime;

            return $formattedDateTime;
        }

        public static function getFormattedDataOnly($data){
            $date = new DateTime($data, new DateTimeZone('UTC'));

            $fmtDate = new \IntlDateFormatter(
                'pt_MZ', \IntlDateFormatter::LONG, \IntlDateFormatter::NONE, null, \IntlDateFormatter::GREGORIAN
            );

            $formattedDate = $fmtDate->format($date); 
            return $formattedDate;
        }
        #==========================================================================
        # Fim das funcoes que lidam com as formatacoes das datas
        #==========================================================================

    }


?>