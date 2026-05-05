<?php
    namespace App\Controller;

use App\Model\Entity\DepartamentoEntity;
use App\Model\Entity\GroupEntity;
use App\Utils\Funcoes;
    use App\Utils\ViewManager;
    use DateTime;
    use DateTimeZone;

    class GlobalPageController{
        public static function getPage($title, $content){
            return ViewManager::render('page', [
                'title'          => $title,
                'content'        => $content
            ]);
        }


        public static function getNavbar(){
            return ViewManager::render('dashboard/navbar', [
                'name'              => $_SESSION['admin']['utilizador']['nome_utilizador'],
                'profile_pic'       => ''
            ]);
        }

        public static function getRightSidebar(){
            return ViewManager::render('dashboard/menu/rightsidebar', [
            ]);
        }
        
        public static function getFooter(){
            return ViewManager::render('dashboard/footer', []);
        }


        //===============================================
        // Permissoes
        //===============================================
        private static function getAdminMenu(){
            $itens = '';
            $itens .= ViewManager::render('dashboard/menu/administration/admin', []);   
            return $itens;
        }

        private static function getVisitsMenu(){
            $itens = '';
            $itens .= ViewManager::render('dashboard/menu/visits/visits', []);   
            return $itens;
        }

        private static function getArsenal(){
            $itens = '';
            $itens .= ViewManager::render('dashboard/menu/arsenal/arsenal', [
                //falta a verificacao do supervisor ou nao
            ]);   
            return $itens;
        }

        private static function getArsenalUsers(){
            $itens = '';
            $itens .= ViewManager::render('dashboard/menu/arsenal/arsenalUser', [
                //falta a verificacao do supervisor ou nao
            ]);   
            return $itens;
        }


        public static function getMenu(){
            if(Funcoes::Permition(0)){
                return ViewManager::render('dashboard/menu/box', [
                    'admin'               => self::getAdminMenu(),
                    'visits'              => self::getVisitsMenu(),
                    'arsenal'             => self::getArsenal(),
                ]);
            }elseif(Funcoes::Permition(5)){
                return ViewManager::render('dashboard/menu/box', [
                    'admin'               => '',
                    'visits'              => self::getVisitsMenu(),
                    'arsenal'             => '',
                ]);
            }elseif(Funcoes::Permition(9)){
                return ViewManager::render('dashboard/menu/box', [
                    'admin'               => '',
                    'visits'              => '',
                    'arsenal'             => self::getArsenal(),
                ]);
            }elseif(Funcoes::Permition(10)){
                return ViewManager::render('dashboard/menu/box', [
                    'admin'               => '',
                    'visits'              => '',
                    'arsenal'             => self::getArsenalUsers(),
                ]);
            }


            /*if(Funcoes::Permition(0)){
                return ViewManager::render('dashboard/menu/box', [
                    'administracao'     => self::getAdmin(),
                    'clinica'           => self::getClinica(),
                    'farmacia'          => self::getFarmacia(),
                ]);
            }elseif(Funcoes::Permition(5)){
                return ViewManager::render('dashboard/menu/box', [
                    'administracao'     => '',
                    'clinica'           => self::getClinica(),
                    'farmacia'          => '',
                ]);
            }elseif(Funcoes::Permition(9)){
                return ViewManager::render('dashboard/menu/box', [
                    'administracao'     => '',
                    'clinica'           => '',
                    'farmacia'          => self::getFarmacia(),
                ]);
            }*/
        }

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
                    'descricao'                => $objDepartamento->nome_departamento
                ]);
            }
            
            return $itens;
        }


             public static function getGruposItems() {
            $itens = '';
            $results = GroupEntity::getGrupos(null, 'codigo_grupo', null);
            //Iterate through each type
            while ($objGrupo = $results->fetchObject(GroupEntity::class)) {
                $itens .= ViewManager::render('dashboard/modules/groupmanagement/groupItem', [
                    'codigo'                   => $objGrupo->codigo_grupo,
                    'descricao'                => $objGrupo->nome_grupo
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

    }
?>