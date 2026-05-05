<?php
    namespace App\Controller\Dashboard;

    use App\Controller\GlobalPageController;
use App\Model\Entity\AmmunitionEntity;
use App\Model\Entity\DisponibilidadeArmamentoEntity;
use App\Model\Entity\EquipmentEntity;
use App\Model\Entity\StatusArmamentoEntity;
use App\Model\Entity\WeaponEntity;
use App\Model\Entity\WeaponTypeEntity;
use App\Utils\Funcoes;
    use App\Utils\ViewManager;

    class ReportsArsenalController extends GlobalPageController{

        #====================================================
        # Reports of weapons
        #====================================================

         private static function getWeaponType(){
            $itens = '';
            $results = WeaponTypeEntity::getWeaponTypes(null, 'codigo_tipo_armamento', null);
            while ($objType = $results->fetchObject(WeaponTypeEntity::class)){
                // Montando os itens a serem retornados
                $itens .= ViewManager::render('dashboard/modules/arsenalmanagement/reports/weapons/typeItem', [
                    'codigo_type' => $objType->codigo_tipo_armamento,
                    'type'        => $objType->tipo_armamento,
                ]);
            }
            return $itens;
        }

         private static function getWeaponModel(){
            $itens = '';
            $results = WeaponEntity::getWeapons(null, 'codigo_armamento', null);
            while ($objType = $results->fetchObject(WeaponTypeEntity::class)){
                // Montando os itens a serem retornados
                $itens .= ViewManager::render('dashboard/modules/arsenalmanagement/reports/weapons/typeItem', [
                    'codigo_type' => $objType->codigo_tipo_armamento,
                    'type'        => $objType->tipo_armamento,
                ]);
            }
            return $itens;
        }

         private static function getWeaponStatus(){
            $itens = '';
            $results = DisponibilidadeArmamentoEntity::getDisponibilidade(null, 'codigo_disponibilidade', null);
            while ($objStatus = $results->fetchObject(DisponibilidadeArmamentoEntity::class)){
                // Montando os itens a serem retornados
                $itens .= ViewManager::render('dashboard/modules/arsenalmanagement/reports/weapons/statusItem', [
                    'codigo_status' => $objStatus->codigo_disponibilidade,
                    'status'        => $objStatus->disponibilidade,
                ]);
            }
            return $itens;
        }

        private static function getWeaponOperacionalStatus(){
            $itens = '';
            $results = StatusArmamentoEntity::getStatus(null, 'codigo_status', null);
            while ($objStatus = $results->fetchObject(StatusArmamentoEntity::class)){
                // Montando os itens a serem retornados
                $itens .= ViewManager::render('dashboard/modules/arsenalmanagement/reports/weapons/statusItem', [
                    'codigo_status' => $objStatus->codigo_status,
                    'status'        => $objStatus->status,
                ]);
            }
            return $itens;
        }
        
        private static function getWeaponsItens($request) {
            $itens = '';
            $results = WeaponEntity::getWeapons(null, 'codigo_armamento DESC', null);
            
            // Iterate through each type
            while ($objArmamento = $results->fetchObject(WeaponEntity::class)) {
                $objsituacao      = StatusArmamentoEntity::getStatusById($objArmamento->status_operacional);
                $itens .= ViewManager::render('dashboard/modules/arsenalmanagement/reports/weapons/weaponItem', [
                    'codigo'                => $objArmamento->codigo_tipo_armamento,
                    'serie'                 => $objArmamento->numero_serie,
                    'tipo'                  => $objArmamento->nome_armamento,
                    'status'                => $objsituacao->status,
                    'calibre'               => $objArmamento->calibre,
                    'inspensao'             => $objArmamento->data_ultima_inspecao,
                    'responsavel'           => $objArmamento->responsavel_atual
                ]);
            }
            
            return $itens;
        }


        public static function getWeaponsReportPage($request){
            if(Funcoes::Permition(11)){
                $content = ViewManager::render('dashboard/modules/arsenalmanagement/reports/weapons/weaponsreport',[
                    'navbar'             => parent::getNavbar(),
                    'sidebar'            => parent::getMenu(),
                    'rightsidebar'       => parent::getRightSidebar(),
                    'footer'             => parent::getFooter(),
                    'weaponType'         => self::getWeaponType(),
                    'statusWeapon'       => self::getWeaponStatus(),
                    'statusOperacional'  => self::getWeaponOperacionalStatus(),
                    'itens'              => self::getWeaponsItens($request),
                ]);

                return parent::getPage('SIGECM | Relatorio Armamento', $content);
            }else{
                return ErrorController::getError($request);
            }
        }


        #=========================================================
        # Funcoes referentes ao relatorio das Municoes
        #=========================================================

         private static function getAmmuniationItens($request){
            $itens = '';
            $results = AmmunitionEntity::getAmmunition(null, 'codigo_municao DESC', null);
            While ($objAmmunition = $results->fetchObject(AmmunitionEntity::class)){
                //Montando os itens a serem retornados
                $objArmamento      = WeaponEntity::getWeaponById($objAmmunition->arma_compativel);
                $itens .= ViewManager::render('dashboard/modules/arsenalmanagement/reports/ammuniation/ammuniationItem', [
                    'codigo'                => $objAmmunition->codigo_municao,
                    'calibre'               => $objAmmunition->nome,
                    'tipo'                  => $objAmmunition->tipo,
                    'quantidade'            => $objAmmunition->quantidade_estoque,
                    'arma_compativel'       => $objArmamento->nome_armamento,
                    'velocidade'            => $objAmmunition->velocidade_inicial,
                ]);
            }
            return $itens;
        }

        public static function getAmmuniationReportPage($request){
            if(Funcoes::Permition(11)){
                $content = ViewManager::render('dashboard/modules/arsenalmanagement/reports/ammuniation/ammuniationsreport',[
                    'navbar'             => parent::getNavbar(),
                    'sidebar'            => parent::getMenu(),
                    'rightsidebar'       => parent::getRightSidebar(),
                    'footer'             => parent::getFooter(),
                    'itens'              => self::getAmmuniationItens($request),
                ]);

                return parent::getPage('SIGECM | Relatorio Munições', $content);
            }else{
                return ErrorController::getError($request);
            }
        }



        #=========================================================
        # Funcoes referentes ao relatorio das Equipamento
        #=========================================================

         private static function getEquipmentsItens($request){
            $itens = '';
             $results = EquipmentEntity::getEquipments(null, 'codigo_equipamento DESC', null);
            While ($objEquipment = $results->fetchObject(EquipmentEntity::class)){
                //Montando os itens a serem retornados
                #$objArmamento      = WeaponEntity::getWeaponById($objAmmunition->arma_compativel);
                $itens .= ViewManager::render('dashboard/modules/arsenalmanagement/reports/equipments/equipmentsItem', [
                    'codigo'                => $objEquipment->codigo_equipamento,
                    'tipo'                  => $objEquipment->tipo,
                    'nome'                  => $objEquipment->nome,
                    'quantidade'            => $objEquipment->quantidade,
                    'cor'                   => $objEquipment->cor,
                    'finalidade'            => $objEquipment->finalidade,
                ]);
            }
            return $itens;
        }

        public static function getEquipmentsReportPage($request){
            if(Funcoes::Permition(11)){
                $content = ViewManager::render('dashboard/modules/arsenalmanagement/reports/equipments/equipmentsreport',[
                    'navbar'             => parent::getNavbar(),
                    'sidebar'            => parent::getMenu(),
                    'rightsidebar'       => parent::getRightSidebar(),
                    'footer'             => parent::getFooter(),
                    'itens'              => self::getEquipmentsItens($request),
                ]);

                return parent::getPage('SIGECM | Relatorio Equipamentos', $content);
            }else{
                return ErrorController::getError($request);
            }
        }

        #=========================================================
        # FIM funcoes referentes ao relatorio das Municoes
        #=========================================================

    }
?>