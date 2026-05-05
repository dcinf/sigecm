<?php
    namespace App\Controller\Dashboard;
    use App\Utils\ViewManager;
    use App\Controller\Dashboard\ErrorController;
    use App\Controller\GlobalPageController;
    use App\DatabaseManager\Database;
    use App\Model\Entity\AmmunitionEntity;
    use App\Model\Entity\ArrecadacaoEntity;
    use App\Model\Entity\ArrecadacaoMunicaoEntity;
    use App\Model\Entity\ArrecadacaoRegisterEntity;
    use App\Model\Entity\ArrecadacaoEquipmentEntity;
    use App\Model\Entity\DepartamentoEntity;
    use App\Model\Entity\EquipamentosQuantEntity;
    use App\Model\Entity\EquipmentEntity;
    use App\Model\Entity\FuncionarioArrecadacaoEntity;
    use App\Model\Entity\GroupEntity;
    use App\Model\Entity\StatusArmamentoEntity;
    use App\Model\Entity\UtilizadorEntity;
    use App\Model\Entity\WeaponEntity;
    use App\Model\Entity\WeaponTypeEntity;
    use App\Utils\Funcoes;
    use DateTime;

    class ArsenalManagementController extends GlobalPageController{
        #==========================================================================
        # funcoes que lidam com as formatacoes das datas
        #==========================================================================
        private static function date_format($data_original) {
            $date = DateTime::createFromFormat('m/d/Y', $data_original);
        
            if ($date) {
                return $date->format('Y-m-d');
            } else {
                return false; 
            }
        }
        #==========================================================================
        # Fim das funcoes que lidam com as formatacoes das datas
        #==========================================================================

        #============================================================================
        # Funcoes relacionadas as classificacoes das armas
        #============================================================================
        public static function getNewWeaponType($request){
            if(Funcoes::Permition(11)){
                $content = ViewManager::render('dashboard/modules/arsenalmanagement/weapon_types/newWeaponType',[
                    'navbar'        => parent::getNavbar(),
                    'sidebar'       => parent::getMenu(),
                    'rightsidebar'  => parent::getRightSidebar(),
                    'footer'        => parent::getFooter(),
                ]);

                return parent::getPage('SIGECM | Nova Tipo de Armamento', $content);
            }else{
                return ErrorController::getError($request);
            }
        }

        public static function setNewWeaponType($request){
            $postVars = $request->getPostVars();

            $objWeaponType = new WeaponTypeEntity;
            $objWeaponType->classificacao                   = $postVars['text_classificacao'];
            $objWeaponType->tipo_armamento                  = $postVars['text_tipo_armamento'];
            $objWeaponType->tipo_uso                        = $postVars['text_tipo_uso'];
            $objWeaponType->potencia                        = $postVars['text_potencia'];
            $objWeaponType->alcance_eficaz                  = $postVars['text_alcance_eficaz'];
            $objWeaponType->tipo_municao                    = $postVars['text_tipo_municao'];
            $objWeaponType->calibre_municao                 = $postVars['text_calibre_municao'];
            $objWeaponType->pais_origem                     = $postVars['text_pais_origem'];
            $objWeaponType->finalidade                      = $postVars['text_finalidade'];
            $objWeaponType->categoria_perigo                = $postVars['text_categoria_perigo'];
            $objWeaponType->descricao                       = $postVars['text_descricao'];
            $objWeaponType->criado_em                       = parent::getNowDateTime();
            $objWeaponType->atualizado_em                   = parent::getNowDateTime();

            $objWeaponType->cadastrar();

            $request->getRouter()->redirect('/weapontype?status=created');
        }


        private static function getWeaponTypeItens($request){
            $itens = '';
            $results = WeaponTypeEntity::getWeaponTypes(null, 'codigo_tipo_armamento DESC', null);
            While ($objWeaponType = $results->fetchObject(WeaponTypeEntity::class)){
                // Montando os itens a serem retornados
                $itens .= ViewManager::render('dashboard/modules/arsenalmanagement/weapon_types/weaponTypeItem', [
                    'codigo'                => $objWeaponType->codigo_tipo_armamento,
                    'categoria'             => $objWeaponType->classificacao,
                    'subcategoria'          => $objWeaponType->tipo_armamento,
                    'finalidade'            => $objWeaponType->finalidade,
                    'calibre'               => $objWeaponType->calibre_municao,
                    'pais_origem'           => $objWeaponType->pais_origem,
                ]);
            }
            return $itens;
        }


        public static function getWeaponTypesPage($request){
            if(Funcoes::Permition(11)){
                $content = ViewManager::render('dashboard/modules/arsenalmanagement/weapon_types/weaponTypes',[
                    'navbar'        => parent::getNavbar(),
                    'sidebar'       => parent::getMenu(),
                    'rightsidebar'  => parent::getRightSidebar(),
                    'footer'        => parent::getFooter(),
                    'itens'         => self::getWeaponTypeItens($request),
                ]);

                return parent::getPage('SIGECM | Armamento', $content);
            }else{
                return ErrorController::getError($request);
            }
        }
        #============================================================================
        # Fim Funcoes relacionadas as classificacoes das armas
        #============================================================================


        #============================================================================
        # Funcoes relacionadas ao Armamento
        #============================================================================
        private static function getStatusWeapon($request){
            $itens = '';
            $results = StatusArmamentoEntity::getStatus(null, 'codigo_status', null);
            while ($objStatus = $results->fetchObject(StatusArmamentoEntity::class)){
                // Montando os itens a serem retornados
                $itens .= ViewManager::render('dashboard/modules/arsenalmanagement/weapons/statusItem', [
                    'codigo'        => $objStatus->codigo_status,
                    'status'        => $objStatus->status,
                ]);
            }
            return $itens;
        }

        private static function getWeaponTypeForNewWeapon($request){
            $itens = '';
            $results = WeaponTypeEntity::getWeaponTypes(null, 'codigo_tipo_armamento', null);
            while ($objType = $results->fetchObject(WeaponTypeEntity::class)){
                // Montando os itens a serem retornados
                $itens .= ViewManager::render('dashboard/modules/arsenalmanagement/weapons/typeItem', [
                    'codigo_type' => $objType->codigo_tipo_armamento,
                    'type'        => $objType->tipo_armamento,
                ]);
            }
            return $itens;
        }

        public static function getNewWeapon($request){
            if(Funcoes::Permition(11)){
                $content = ViewManager::render('dashboard/modules/arsenalmanagement/weapons/newWeapon',[
                    'navbar'                => parent::getNavbar(),
                    'sidebar'               => parent::getMenu(),
                    'rightsidebar'          => parent::getRightSidebar(),
                    'footer'                => parent::getFooter(),
                    'typeItem'              => self::getWeaponTypeForNewWeapon($request),
                    'status'                => self::getStatusWeapon($request)
                ]);
                return parent::getPage('SIGECM | Nova Arma', $content);
            }else{
                return ErrorController::getError($request);
            }
        }

        public static function setNewWeapon($request){
            $postVars = $request->getPostVars();

            $weapon_type_id = (int) $postVars['tipo_armamento'];
            $objWeaponType = WeaponTypeEntity::getWeaponTypeById($weapon_type_id);

            $objArsenal = new WeaponEntity;
            $objArsenal->codigo_tipo_armamento           = $postVars['tipo_armamento'];
            $objArsenal->nome_armamento                  = $objWeaponType->tipo_armamento;
            $objArsenal->numero_serie                    = $postVars['text_numero_serie'];
            $objArsenal->marca                           = $postVars['text_marca'];
            $objArsenal->modelo                          = $postVars['text_modelo'];
            $objArsenal->calibre                         = $objWeaponType->calibre_municao;
            $objArsenal->peso                            = $postVars['text_peso_arma'];
            $objArsenal->local_armazenamento             = $postVars['text_local_armazenamento'];
            $objArsenal->status_operacional              = $postVars['text_status'];
            $objArsenal->disponibilidade                 = 1;
            $objArsenal->data_aquisicao                  = self::date_format($postVars['text_data_aquisicao']);
            $objArsenal->data_ultima_inspecao            = self::date_format($postVars['text_data_inspencao']);
            $objArsenal->data_ultimo_uso                 = self::date_format($postVars['text_data_uso']);
            $objArsenal->observacoes                     = $postVars['text_obs'];
            $objArsenal->cadastrado_por                  = $_SESSION['admin']['utilizador']['nome_utilizador'];;
            $objArsenal->criado_em                       = parent::getNowDateTime();
            $objArsenal->atualizado_em                   = parent::getNowDateTime();

            $objArsenal->cadastrar();
            $request->getRouter()->redirect('/weapons?status=created');
        }



        private static function getWeaponsItens($request) {
            $itens = '';
            $results = WeaponEntity::getWeapons(null, 'codigo_armamento DESC', null);
            
            // Iterate through each type
            while ($objArmamento = $results->fetchObject(WeaponEntity::class)) {
                $itens .= ViewManager::render('dashboard/modules/arsenalmanagement/weapons/weaponItem', [
                    'codigo'                => $objArmamento->codigo_tipo_armamento,
                    'serie'                 => $objArmamento->numero_serie,
                    'tipo'                  => $objArmamento->nome_armamento,
                    'status'                => $objArmamento->status_operacional,
                    'calibre'               => $objArmamento->calibre,
                    'inspensao'             => $objArmamento->data_ultima_inspecao
                ]);
            }
            
            return $itens;
        }


        public static function getWeaponsPage($request){
            if(Funcoes::Permition(11)){
                $content = ViewManager::render('dashboard/modules/arsenalmanagement/weapons/weapons',[
                    'navbar'        => parent::getNavbar(),
                    'sidebar'       => parent::getMenu(),
                    'rightsidebar'  => parent::getRightSidebar(),
                    'footer'        => parent::getFooter(),
                    'itens'         => self::getWeaponsItens($request),
                ]);

                return parent::getPage('SIGECM | Armamento', $content);
            }else{
                return ErrorController::getError($request);
            }
        }
        #============================================================================
        # Fim Funcoes relacionadas ao Armamento
        #============================================================================

        #============================================================================
        # Funcoes relacionadas as Municoes
        #============================================================================
        private static function getWeaponsItensForAmmunition($request) {
            $itens = '';
            $results = WeaponEntity::getWeapons(null, 'codigo_armamento', null);
            // Iterate through each type
            while ($objWeapon = $results->fetchObject(WeaponEntity::class)) {
                $itens .= ViewManager::render('dashboard/modules/arsenalmanagement/ammunition/weaponTypeItem', [
                    'codigo'                      => $objWeapon->codigo_tipo_armamento,
                    'WeaponType'                  => $objWeapon->nome_armamento
                ]);
            }
            
            return $itens;
        }
        public static function getNewAmmunition($request){
            if(Funcoes::Permition(12)){
                $content = ViewManager::render('dashboard/modules/arsenalmanagement/ammunition/newAmmunition',[
                    'navbar'                => parent::getNavbar(),
                    'sidebar'               => parent::getMenu(),
                    'rightsidebar'          => parent::getRightSidebar(),
                    'footer'                => parent::getFooter(),
                    'typeItem'              => self::getWeaponsItensForAmmunition($request),
                ]);
                return parent::getPage('SIGECM | Nova Municao', $content);
            }else{
                return ErrorController::getError($request);
            }
        }


        public static function setNewAmmunition($request){
            if(Funcoes::Permition(12)){
                $postVars = $request->getPostVars();

                $cod_type = (int) $postVars['text_type'];
                $quantinty = (int) $postVars['text_quantidade'];

                $objAmmunition = new AmmunitionEntity;
                $objAmmunition->nome                   = $postVars['text_calibre_municao'];
                $objAmmunition->calibre                = $postVars['text_calibre_municao'];
                $objAmmunition->tipo                   = $postVars['text_tipo_municao'];
                $objAmmunition->peso                   = $postVars['text_peso_municao'];
                $objAmmunition->velocidade_inicial     = $postVars['text_velocidade_inicial'];
                $objAmmunition->capacidade_penetracao  = $postVars['text_capacidade_penetracao'];
                $objAmmunition->fabricante             = $postVars['text_fabriante'];
                $objAmmunition->data_fabricacao        = self::date_format($postVars['text_data_fabricacao']);
                $objAmmunition->quantidade_estoque     = $quantinty;
                $objAmmunition->arma_compativel        = $cod_type;
                $objAmmunition->observacoes            = $postVars['text_descricao'];
                $objAmmunition->criado_em              = parent::getNowDateTime();
                $objAmmunition->atualizado_em          = parent::getNowDateTime();

                $objAmmunition->cadastrar();

                $request->getRouter()->redirect('/ammunition?status=created');
            }else{
                return ErrorController::getError($request);
            }
        }

        private static function getAmmunitionItens($request){
            $itens = '';
            $results = AmmunitionEntity::getAmmunition(null, 'codigo_municao DESC', null);
            While ($objAmmunition = $results->fetchObject(AmmunitionEntity::class)){
                //Montando os itens a serem retornados
                $itens .= ViewManager::render('dashboard/modules/arsenalmanagement/ammunition/ammunitionItem', [
                    'codigo'                => $objAmmunition->codigo_municao,
                    'calibre'               => $objAmmunition->nome,
                    'tipo'                  => $objAmmunition->tipo,
                    'quantidade'            => $objAmmunition->quantidade_estoque,
                    'velocidade'            => $objAmmunition->velocidade_inicial,
                ]);
            }
            return $itens;
        }

        public static function getAmmunitionPage($request){
            if(Funcoes::Permition(12)){
                $content = ViewManager::render('dashboard/modules/arsenalmanagement/ammunition/ammunition',[
                    'navbar'        => parent::getNavbar(),
                    'sidebar'       => parent::getMenu(),
                    'rightsidebar'  => parent::getRightSidebar(),
                    'footer'        => parent::getFooter(),
                    'itens'         => self::getAmmunitionItens($request),
                ]);

                return parent::getPage('SIGECM | Armamento', $content);
            }else{
                return ErrorController::getError($request);
            }
        }



        # Funcao para reabastecer as municoes 

        public static function setRefillAmmunition($request){
            if(Funcoes::Permition(15)){
                $postVars = $request->getPostVars();


                //Fazemos o cadastro das Municoes 
                if(!empty($postVars['text_municao_actualizar']) && !empty($postVars['text_municao_quantidade_actualizar'])){

                    $municoesID          = $postVars['text_municao_actualizar'];
                    $quantidadeMunicoes  = $postVars['text_municao_quantidade_actualizar'];

                    foreach ($municoesID as $index => $codigo_municao){
                        //busca a municao pelo seu codigo
                        $objMunicao = AmmunitionEntity::getAmmuniationById($codigo_municao);
                        
                        if(!$objMunicao || !isset($quantidadeMunicoes[$index])){
                            continue;
                        }
                        //vai fazer o cadastro das municoes
                        $objMunicao->codigo_municao             = $codigo_municao;
                        $objMunicao->quantidade_estoque         = $objMunicao->quantidade_estoque + $quantidadeMunicoes[$index];
                        $objMunicao->atualizado_em              = parent::getNowDateTime();

                        $objMunicao->actualizar();
                    }
                }

                $request->getRouter()->redirect('/refill-ammunition?status=updated');
            }else{
                $request->getRouter()->redirect('/refill-ammunition?status=error_update');
            }
        }


        public static function getRefillAmmunitionPage($request){
            if(Funcoes::Permition(15)){
                $content = ViewManager::render('dashboard/modules/arsenalmanagement/ammunition/addAmmunition',[
                    'navbar'        => parent::getNavbar(),
                    'sidebar'       => parent::getMenu(),
                    'rightsidebar'  => parent::getRightSidebar(),
                    'footer'        => parent::getFooter(),
                    'municaoItem'   => self::getAmmuniationItens($request),
                ]);

                return parent::getPage('SIGECM | Abastecer', $content);
            }else{
                return ErrorController::getError($request);
            }
        }

        #============================================================================
        # Funcoes relacionadas as Municoes
        #============================================================================

        #============================================================================
        # Funcoes relacionadas aos equipamentos
        #============================================================================
        public static function getNewEquipment($request){
            if(Funcoes::Permition(13)){
                $content = ViewManager::render('dashboard/modules/arsenalmanagement/equipment/newEquipment',[
                    'navbar'                => parent::getNavbar(),
                    'sidebar'               => parent::getMenu(),
                    'rightsidebar'          => parent::getRightSidebar(),
                    'footer'                => parent::getFooter(),
                ]);
                return parent::getPage('SIGECM | Nova Equipamento', $content);
            }else{
                return ErrorController::getError($request);
            }
        }

        public static function SetNewEquipment($request){
            if(Funcoes::Permition(13)){
                $postVars = $request->getPostVars();

                $quantinty = (int) $postVars['text_quantidade'];

                $objEquipment = new EquipmentEntity;
                $objEquipment->tipo                   = $postVars['text_tipo_equipamento'];
                $objEquipment->nome                   = $postVars['text_nome_equipamento'];
                $objEquipment->material               = $postVars['text_material_fabricacao'];
                $objEquipment->capacidade             = $postVars['capacidade_carga_protecao'];
                $objEquipment->peso                   = $postVars['text_peso'];
                $objEquipment->cor                    = $postVars['text_cor_equipamento'];
                $objEquipment->compatibilidade        = $postVars['text_compatibilidade'];
                $objEquipment->finalidade             = $postVars['text_finalidade'];
                $objEquipment->fabricante             = $postVars['text_fabricante'];
                $objEquipment->pais_origem            = $postVars['text_pais_origem'];
                $objEquipment->data_fabricacao        = self::date_format($postVars['text_data_fabricacao']);
                $objEquipment->estado                 = $postVars['text_estado_equipamento'];
                $objEquipment->quantidade             = $quantinty;
                $objEquipment->descricao              = $postVars['text_descricao'];
                $objEquipment->criado_em              = parent::getNowDateTime();
                $objEquipment->atualizado_em          = parent::getNowDateTime();

                $objEquipment->cadastrar();

                $request->getRouter()->redirect('/equipment?status=created');
            }else{
                return ErrorController::getError($request);
            }
        }

        private static function getEquipmentItens($request){
            $itens = '';
            $results = EquipmentEntity::getEquipments(null, 'codigo_equipamento DESC', null);
            While ($objEquipment = $results->fetchObject(EquipmentEntity::class)){
                //Montando os itens a serem retornados
                $itens .= ViewManager::render('dashboard/modules/arsenalmanagement/equipment/equipmentItem', [
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

        public static function getEquipmentPage($request){
            if(Funcoes::Permition(13)){
                $content = ViewManager::render('dashboard/modules/arsenalmanagement/equipment/equipment',[
                    'navbar'        => parent::getNavbar(),
                    'sidebar'       => parent::getMenu(),
                    'rightsidebar'  => parent::getRightSidebar(),
                    'footer'        => parent::getFooter(),
                    'itens'         => self::getEquipmentItens($request),
                ]);

                return parent::getPage('SIGECM | Armamento', $content);
            }else{
                return ErrorController::getError($request);
            }
        }



        public static function SetNewEquipmentWithdraw($request){
            if(Funcoes::Permition(13)){
                $file = $request->getFile();
                $postVars = $request->getPostVars();

                $objQuantidades = new EquipamentosQuantEntity;

                #===========================================
                # Funcionario
                #===========================================
                $objQuantidades->codigo_funcionario             = $postVars['text_codigo'];
                $objQuantidades->nome_funcionario               = $postVars['text_full_name'];
                $objQuantidades->patente_funcionario            = $postVars['text_patente'];
                $objQuantidades->departamento                   = $postVars['text_departamento'];
                $objQuantidades->cargo                          = $postVars['text_cargo'];
                $objQuantidades->documento_identidade           = $postVars['text_documento'];
                $objQuantidades->celular_funcionario            = $postVars['text_celular'];
                $objQuantidades->celular_alt                    = $postVars['text_celular_alt'];
                $objQuantidades->fotografia                     = $postVars['text_fotografia'];


                #===========================================
                # Assinaturas
                #===========================================
                $objQuantidades->assinatura_levantamento        = $file;
                $objQuantidades->data_levantamento              = parent::getNowDate();;
                $objQuantidades->criado_em                      = parent::getNowDateTime();
                $objQuantidades->atualizado_em                  = parent::getNowDateTime();



                #=============================================
                # Logica para guardar os equipamentos
                #=============================================
                $equipamentos               = $postVars['text_equipamentos'];
                $quantidades_equipamentos   = json_decode($postVars['text_quantidades_equipamentos'], true);

                // Laço para iterar pelos arrays
                for ($i = 0; $i < count($equipamentos); $i++) { 
                    $codigo_equipamento = $equipamentos[$i]; 
                    $quantidade = $quantidades_equipamentos[$i]; 

                    $objEquipment = EquipmentEntity::getEquipmentById($codigo_equipamento);
                            
                    $objQuantidades->codigo_equipamento   = $codigo_equipamento;
                    $objQuantidades->nome_equipamento     = $objEquipment->nome;
                    $objQuantidades->quantidade           = $quantidade;
                    $objQuantidades->criado_em            = parent::getNowDateTime();
                    $objQuantidades->atualizado_em        = parent::getNowDateTime();

                    $codigo_quant_equipamento = $objQuantidades->cadastrar();

                    #=============================================
                    # Faz a reducao do estoque
                    #=============================================
                    if($codigo_quant_equipamento != NULL){
                        $objEquipmentQuant = EquipamentosQuantEntity::getQuantitiesById($codigo_quant_equipamento);
                        $QntEquipmet = $objEquipmentQuant->quantidade;

                        $totalEquipamento = $objEquipment->quantidade;

                        $nova_quantidade = (int)$totalEquipamento - (int) $QntEquipmet;
                                
                        $objEquipment->quantidade = $nova_quantidade;
                        $objEquipment->actualizar();
                    }
                }
                
            }else{
                return ErrorController::getError($request);
            }
        }




        public static function getNewEquipmentWithdraw($request, $mensagem = ''){
            if(Funcoes::Permition(13)){
                $content = ViewManager::render('dashboard/modules/arsenalmanagement/equipment/newEquipmentWithdraw',[
                    'navbar'                => parent::getNavbar(),
                    'sidebar'               => parent::getMenu(),
                    'rightsidebar'          => parent::getRightSidebar(),
                    'footer'                => parent::getFooter(),
                    'municaoItem'           => self::getAmmuniationItens($request),
                    'equipmentItem'         => self::getEquipmentItensWidthdraw($request),
                    'mensagem'              => $mensagem
                ]);
                return parent::getPage('SIGECM | Nova Retirada', $content);
            }else{
                return ErrorController::getError($request);
            }
        }


        # Funcoes de reabaster equipamentos
        public static function setRefillEquipments($request){
            if(Funcoes::Permition(15)){
                $postVars = $request->getPostVars();


                //Fazemos o cadastro das Municoes 
                if(!empty($postVars['text_equipament_actualizar']) && !empty($postVars['text_equipamento_quantidade_actualizar'])){

                    $equipamentosID          = $postVars['text_equipament_actualizar'];
                    $quantidadeEquipamentos  = $postVars['text_equipamento_quantidade_actualizar'];

                    foreach ($equipamentosID as $index => $codigo_equipamento){
                        //busca a municao pelo seu codigo
                        $objEquipamento = EquipmentEntity::getEquipmentById($codigo_equipamento);
                        
                        if(!$objEquipamento || !isset($quantidadeEquipamentos[$index])){
                            continue;
                        }
                        //vai fazer o cadastro das municoes
                        $objEquipamento->codigo_municao             = $codigo_equipamento;
                        $objEquipamento->quantidade         = $objEquipamento->quantidade + $quantidadeEquipamentos[$index];
                        $objEquipamento->atualizado_em              = parent::getNowDateTime();

                        $objEquipamento->actualizar();
                    }
                }

                $request->getRouter()->redirect('/refill-equipments?status=updated');
            }else{
                $request->getRouter()->redirect('/refill-equipments?status=error_update');
            }
        }


        public static function getRefillEquipmentsPage($request){
            if(Funcoes::Permition(15)){
                $content = ViewManager::render('dashboard/modules/arsenalmanagement/equipment/addEquipments',[
                    'navbar'        => parent::getNavbar(),
                    'sidebar'       => parent::getMenu(),
                    'rightsidebar'  => parent::getRightSidebar(),
                    'footer'        => parent::getFooter(),
                    'equipmentItem' => self::getEquipmentItensWidthdraw($request)
                ]);

                return parent::getPage('SIGECM | Abastecer', $content);
            }else{
                return ErrorController::getError($request);
            }
        }





        #============================================================================
        # Funcoes relacionadas aos equipamentos
        #============================================================================



        #============================================================================
        # Funcoes relacionadas a ARECADACAO
        #============================================================================

        //Levantamento do armamento
        private static function getAmmuniationItens($request) {
            $itens = '';
            $results = AmmunitionEntity::getAmmunition(null, 'codigo_municao', null);
            // Iterate through each type
            while ($objAmmunition = $results->fetchObject(AmmunitionEntity::class)) {
                $itens .= ViewManager::render('dashboard/modules/arsenalmanagement/weapon_inventory/ammuniationItem', [
                    'codigo'                   => $objAmmunition->codigo_municao,
                    'municao'                  => $objAmmunition->nome
                ]);
            }
            
            return $itens;
        }

        private static function getEquipmentItensWidthdraw($request) {
            $itens = '';
            $results = EquipmentEntity::getEquipments(null, 'codigo_equipamento DESC', null);
            While ($objEquipment = $results->fetchObject(EquipmentEntity::class)){
                //Montando os itens a serem retornados
                $itens .= ViewManager::render('dashboard/modules/arsenalmanagement/weapon_inventory/equipmentItem', [
                    'codigo'                => $objEquipment->codigo_equipamento,
                    'equipamento'           => $objEquipment->nome,
                ]);
            }
            return $itens;
        }

        public static function getNewWithdraw($request, $mensagem = ''){
            if(Funcoes::Permition(10)){
                $content = ViewManager::render('dashboard/modules/arsenalmanagement/weapon_inventory/newWithdraw',[
                    'navbar'                => parent::getNavbar(),
                    'sidebar'               => parent::getMenu(),
                    'rightsidebar'          => parent::getRightSidebar(),
                    'footer'                => parent::getFooter(),
                    'municaoItem'           => self::getAmmuniationItens($request),
                    'equipmentItem'         => self::getEquipmentItensWidthdraw($request),
                    'mensagem'              => $mensagem
                ]);
                return parent::getPage('SIGECM | Nova Retirada', $content);
            }else{
                return ErrorController::getError($request);
            }
        }

        public static function SetNewWithdraw($request){
            if(Funcoes::Permition(10)){
                $file = $request->getFile();
                $postVars = $request->getPostVars();

                $objArrecadacao = new ArrecadacaoEntity;

                #========================================
                # Verificacoes
                #========================================
                # Verificacao se numero de serie nao foi atribuida a outro funcionario
                $where =  "numero_de_serie_arma = ".$postVars['text_numero_serie']." AND data_devolucao is null";
                $results = ArrecadacaoEntity::getArrecadacao($where, 'codigo_arrecadacao DESC', null);
                $objArrecadacaoVerificacao = $results->fetchObject(ArrecadacaoEntity::class);

                if(!empty($objArrecadacaoVerificacao)){
                    $request->getRouter()->redirect('/new-withdraw?status=error_serie');
                }else{
                    #===========================================
                    # Funcionario
                    #===========================================
                    $objArrecadacao->codigo_funcionario             = $postVars['text_codigo'];
                    $objArrecadacao->nome_funcionario               = $postVars['text_full_name'];
                    $objArrecadacao->patente_funcionario            = $postVars['text_patente'];
                    $objArrecadacao->departamento                   = $postVars['text_departamento'];
                    $objArrecadacao->cargo                          = $postVars['text_cargo'];
                    $objArrecadacao->documento_identidade           = $postVars['text_documento'];
                    $objArrecadacao->celular_funcionario            = $postVars['text_celular'];
                    $objArrecadacao->celular_alt                    = $postVars['text_celular_alt'];
                    $objArrecadacao->fotografia                     = $postVars['text_fotografia'];

                    #===========================================
                    # Armamento
                    #===========================================
                    $objArrecadacao->codigo_armamento               = $postVars['text_codigo_armamento'];
                    $objArrecadacao->numero_de_serie_arma           = $postVars['text_numero_serie'];
                    $objArrecadacao->tipo_armamento                 = $postVars['text_tipo_arma'];
                    $objArrecadacao->status_operacional_arma        = $postVars['text_estado'];
                    $objArrecadacao->calibre_municao_arma           = $postVars['text_calibre_municao'];
                    $objArrecadacao->data_ultima_inspecao_arma      = $postVars['text_data_inspencao'];
                    $objArrecadacao->status_operacional_arma        = $postVars['text_estado'];

                    #===========================================
                    # Municoes
                    #===========================================
                    $objArrecadacao->codigo_municao                 = $postVars['text_municao_retirar'];
                    $objArrecadacao->quantidade_municao             = $postVars['text_municao_quantidade'];
                    $objArrecadacao->tipo_armamento                 = $postVars['text_tipo_arma'];


                    #===========================================
                    # Assinaturas
                    #===========================================
                    $objArrecadacao->assinatura_arrecadacao         = $file;
                    $objArrecadacao->data_levantamento              = parent::getNowDate();;
                    $objArrecadacao->criado_em                      = parent::getNowDateTime();
                    $objArrecadacao->atualizado_em                  = parent::getNowDateTime();

                
                    $objArrecadacao->cadastrar();
                    $request->getRouter()->redirect('/withdraw?status=created');
        
                }
                
            }else{
                return ErrorController::getError($request);
            }
        }


        private static function getWithdrawItem($request){
            $itens = '';
            $results = ArrecadacaoEntity::getArrecadacao(null, 'codigo_arrecadacao DESC', null);
            While ($objArrecacao = $results->fetchObject(ArrecadacaoEntity::class)){ 
                //Montando os itens a serem retornados
                $itens .= ViewManager::render('dashboard/modules/arsenalmanagement/weapon_inventory/withdrawItem', [
                    'codigo'                => $objArrecacao->codigo_arrecadacao,
                    'nome'                  => $objArrecacao->nome_funcionario,
                    'imagem'                => $objArrecacao->fotografia,
                    'tipo_armamento'        => $objArrecacao->tipo_armamento,
                    'numero'                => $objArrecacao->numero_de_serie_arma,
                    'municoes'              => $objArrecacao->quantidade_municao,
                    'patente'               => $objArrecacao->patente_funcionario,
                    'assinatura'            => $objArrecacao->assinatura_arrecadacao,
                    'telefone'              => $objArrecacao->celular_funcionario,
                    'data_retirada'         => $objArrecacao->data_levantamento
                ]);
            }
            return $itens; 
        }



        public static function getWeaponsInventoryPage($request){
            if(Funcoes::Permition(10)){
                $content = ViewManager::render('dashboard/modules/arsenalmanagement/weapon_inventory/weaponWithdraw',[
                    'navbar'        => parent::getNavbar(),
                    'sidebar'       => parent::getMenu(),
                    'rightsidebar'  => parent::getRightSidebar(),
                    'footer'        => parent::getFooter(),
                    'withdrawItem'  => self::getWithdrawItem($request),
                ]);

                return parent::getPage('SIGECM | Armamento', $content);
            }else{
                return ErrorController::getError($request);
            }
        }
        #============================================================================
        # fIM Funcoes relacionadas a ARECADACAO
        #============================================================================






























        #============================================================================
        # Registar funcionario e Arecadar
        #============================================================================

        // Devolucao do armamento
        private static function getEquipmentItensReturn($request, $id) {
            $itens = '';
            $results = EquipamentosQuantEntity::getQuantities('codigo_arrecadacao = '.$id, 'codigo_quantidade DESC', null);
            While ($objEquipment = $results->fetchObject(EquipmentEntity::class)){
                $objStatusEquipment = EquipmentEntity::getEquipmentById($objEquipment->codigo_equipamento);
                //Montando os itens a serem retornados
                $itens .= ViewManager::render('dashboard/modules/arsenalmanagement/weapon_inventory/equipmentReturnItem', [
                    'codigo'                => $objEquipment->codigo_equipamento,
                    'equipamento'           => $objEquipment->nome_equipamento,
                    'quantidade'            => $objEquipment->quantidade,
                    'estado'                => $objStatusEquipment->estado,
                    'data_levantamento'     => $objEquipment->criado_em,
                ]);
            }
            return $itens;
        }

        private static function getWithdrawItemReturnReport(){
            $itens = '';
            $results = ArrecadacaoRegisterEntity::getArrecadacao("data_devolucao IS NOT NULL", 'codigo_arrecadacao DESC', null);
            While ($objArrecadacao = $results->fetchObject(ArrecadacaoRegisterEntity::class)){ 
                   // Busca a quantidade de munições da retirada
                $whereMunicao = "codigo_arrecadacao = '{$objArrecadacao->codigo_arrecadacao}'";
                $resultsMunicao = ArrecadacaoMunicaoEntity::getMunicaoArrecadacao($whereMunicao, 'codigo_arrecadacao DESC', null);
                $objArrecadacaoMunicoes = $resultsMunicao->fetchObject(ArrecadacaoMunicaoEntity::class);

                //Montando os itens a serem retornados
                $itens .= ViewManager::render('dashboard/modules/arsenalmanagement/weapon_inventory/weaponReturnItemReport', [
                    'codigo'                => $objArrecadacao->codigo_arrecadacao,
                    'nome'                  => $objArrecadacao->nome_funcionario,
                    'tipo_armamento'        => $objArrecadacao->tipo_armamento,
                    'numero'                => $objArrecadacao->numero_de_serie_arma,
                    'municoes'              => $objArrecadacaoMunicoes ? $objArrecadacaoMunicoes->quantidade_levantar : '0',
                    'patente'               => $objArrecadacao->patente_funcionario,
                    'data_retirada'         => $objArrecadacao->data_levantamento,
                    'data_devolucao'        => $objArrecadacao->data_devolucao,
                    'assinatura_devolucao'  => $objArrecadacao->assinatura_devolucao,
                    'assinatura_receptor'   => $objArrecadacao->assinatura_fiel
                ]);
            }
            return $itens; 
        }

          
        
        public static function getWeaponsReturnReport($request){
            if(Funcoes::Permition(10)){
                $content = ViewManager::render('dashboard/modules/arsenalmanagement/weapon_inventory/weaponReturnReport',[
                    'navbar'        => parent::getNavbar(),
                    'sidebar'       => parent::getMenu(),
                    'rightsidebar'  => parent::getRightSidebar(),
                    'footer'        => parent::getFooter(),
                    'withdrawItemReturn'  => self::getWithdrawItemReturnReport(),
                ]);

                return parent::getPage('SIGECM | Armamento', $content);
            }else{
                return ErrorController::getError($request);
            }
        }

        //=============================================================================
        //Funcoes que fazem a devolucao do armamento
        //=============================================================================
        public static function getNewWeaponReturn($request, $id){
            if(Funcoes::Permition(10)){
                $objArrecadacao = ArrecadacaoRegisterEntity::getArrecadacaoById($id);

                $objStatus = StatusArmamentoEntity::getStatusById($objArrecadacao->status_operacional_arma);

                //Busca as municoes na tabela de municoes 
                $whereMunicao = "codigo_arrecadacao = '{$objArrecadacao->codigo_arrecadacao}'";
                $resultsMunicao = ArrecadacaoMunicaoEntity::getMunicaoArrecadacao($whereMunicao, 'codigo_arrecadacao DESC', null);
                $objArrecadacaoMunicoes = $resultsMunicao->fetchObject(ArrecadacaoMunicaoEntity::class);

                $content = ViewManager::render('dashboard/modules/arsenalmanagement/weapon_inventory/newWeaponReturn',[
                    'navbar'                => parent::getNavbar(),
                    'sidebar'               => parent::getMenu(),
                    'rightsidebar'          => parent::getRightSidebar(),
                    'footer'                => parent::getFooter(),
                    'fullname'              => $objArrecadacao->nome_funcionario,
                    'patente'               => $objArrecadacao->patente_funcionario,
                    'subunidade'            => $objArrecadacao->subunidade,
                    'assinatura'            => $objArrecadacao->assinatura_arrecadacao,
                    'data_levantamento'     => parent::getFormattedDataOnly($objArrecadacao->data_levantamento),
                    'gun_code'              => $objArrecadacao->codigo_armamento,
                    'gun_serie'             => $objArrecadacao->numero_de_serie_arma,
                    'tipo'                  => $objArrecadacao->tipo_armamento,
                    'equipamento'           => self::getEquipmentReturnItem($request, $id),
                    'gun_status'            => $objStatus->status,
                    'gun_calibre'           => $objArrecadacao->calibre_municao_arma,
                    'gun_data_inspencao'    => $objArrecadacao->data_ultima_inspecao_arma,
                    'gun_ammuniation'       => $objArrecadacaoMunicoes ? $objArrecadacaoMunicoes->quantidade_levantar : '0',
                ]);
                return parent::getPage('SIGECM | Devolucao', $content);
            }else{
                return ErrorController::getError($request);
            }
        }  
        
        private static function getEquipmentReturnItem($request, $id) {
            $itens = '';
            $whereMunicao = "codigo_arrecadacao = '{$id}'";
            $results = ArrecadacaoEquipmentEntity::getEquipmentArrecadacao($whereMunicao, 'codigo_equipamento_arrecadacao DESC', null);
        
            $hasResults = false;
            while ($objEquipment = $results->fetchObject(ArrecadacaoEquipmentEntity::class)) {
                $hasResults = true;
                $itens .= ViewManager::render('dashboard/modules/arsenalmanagement/weapon_inventory/equipamentoItem', [
                    'acessorio'  => $objEquipment->nome_equipamentos,
                    'quantidade' => $objEquipment->quantidade_levantar,
                ]);
            }
        
            if (!$hasResults) {
                // Linha de fallback
                $itens .= '
                    <tr>
                        <td colspan="2" style="text-align: center;">Nenhum acessorio foi retirado</td>
                    </tr>
                ';
            }
        
            return $itens;
        }


        #======================================================
        # Funcao que faz a devolucao do armamento
        #======================================================

        public static function SetNewReturn($request, $id){
            if(Funcoes::Permition(10)){
                $file_fiador = $request->getFile_fiador();
                $file_fiel = $request->getFile_fiel();
                $postVars = $request->getPostVars();

                #=====================================
                # codigo que faz a actualizacao das 
                # municoes
                #=====================================
                if(!empty($postVars['text_quantidade_devolucao'])){
                    $whereMunicoes = "codigo_arrecadacao = '{$id}'";
                    $resultMunicoes = ArrecadacaoMunicaoEntity::getMunicaoArrecadacao($whereMunicoes, 'codigo_municao_arrecadacao DESC');

                    while ($objMunicao = $resultMunicoes->fetchObject(ArrecadacaoMunicaoEntity::class)) {
                        $objMunicao->quantidade_devolver = $postVars['text_quantidade_devolucao'];
                        $objMunicao->atualizado_em = date('Y-m-d H:i:s');
                        $objMunicao->actualizar();
                    }
                }

                //actualizacao da tabela de equipamentos
                $whereEquipamentos = "codigo_arrecadacao = '{$id}'";
                $resultsEquipamentos = ArrecadacaoEquipmentEntity::getEquipmentArrecadacao($whereEquipamentos, 'codigo_arrecadacao DESC', null);

                while ($objEquipamento = $resultsEquipamentos->fetchObject(ArrecadacaoEquipmentEntity::class)){
                    // Atualiza os campos desejados
                    $objEquipamento->codigo_arrecadacao     = $id;
                    $objEquipamento->quantidade_devolver    = $objEquipamento->quantidade_levantar;
                    $objEquipamento->atualizado_em          = parent::getNowDateTime();

                    // Atualiza no banco de dados
                    $objEquipamento->actualizar();
                }

                $whereArrecadacao = "codigo_arrecadacao = '{$id}'";
                $resultsArrecadacao = ArrecadacaoRegisterEntity::getArrecadacao($whereArrecadacao, 'codigo_arrecadacao DESC', null);

                while($objArrecadacao = $resultsArrecadacao->fetchObject(ArrecadacaoRegisterEntity::class)){
                    $objArrecadacao->data_devolucao                 = parent::getNowDate();
                    $objArrecadacao->assinatura_devolucao           = $file_fiel;
                    $objArrecadacao->assinatura_fiel                = $file_fiador ;
                    $objArrecadacao->atualizado_em                  = parent::getNowDateTime();

                    $objArrecadacao->actualizar();
                    $request->getRouter()->redirect('/return-weapon?status=updated');
                }
                
            }else{
                return ErrorController::getError($request);
            }
        }


        private static function getWithdrawItemReturn(){
            $itens = '';
            $results = ArrecadacaoRegisterEntity::getArrecadacao("data_devolucao IS NULL", 'codigo_arrecadacao DESC', null);
            While ($objArrecadacao = $results->fetchObject(ArrecadacaoRegisterEntity::class)){ 
                $whereMunicao = "codigo_arrecadacao = '{$objArrecadacao->codigo_arrecadacao}'";
                $resultsMunicao = ArrecadacaoMunicaoEntity::getMunicaoArrecadacao($whereMunicao, 'codigo_arrecadacao DESC', null);
                $objArrecadacaoMunicoes = $resultsMunicao->fetchObject(ArrecadacaoMunicaoEntity::class);

                //Montando os itens a serem retornados
                $itens .= ViewManager::render('dashboard/modules/arsenalmanagement/weapon_inventory/weaponReturnItem', [
                    'codigo'                => $objArrecadacao->codigo_arrecadacao,
                    'nome'                  => $objArrecadacao->nome_funcionario,
                    'tipo_armamento'        => $objArrecadacao->tipo_armamento,
                    'numero'                => $objArrecadacao->numero_de_serie_arma,
                    'municoes'              => $objArrecadacaoMunicoes ? $objArrecadacaoMunicoes->quantidade_levantar : '0',
                    'patente'               => $objArrecadacao->patente_funcionario,
                    'assinatura'            => $objArrecadacao->assinatura_arrecadacao,
                    'telefone'              => $objArrecadacao->celular_funcionario,
                    'data_retirada'         => $objArrecadacao->data_levantamento,
                ]);
            }
            return $itens; 
        }

        public static function getWeaponsReturnPage($request){
            if(Funcoes::Permition(10)){
                $content = ViewManager::render('dashboard/modules/arsenalmanagement/weapon_inventory/weaponReturn',[
                    'navbar'        => parent::getNavbar(),
                    'sidebar'       => parent::getMenu(),
                    'rightsidebar'  => parent::getRightSidebar(),
                    'footer'        => parent::getFooter(),
                    'withdrawItemReturn'  => self::getWithdrawItemReturn(),
                ]);

                return parent::getPage('SIGECM | Armamento', $content);
            }else{
                return ErrorController::getError($request);
            }
        }

        #============================================================================
        # funcoes auxiliares do levantamento e devolucao de armamento
        #============================================================================
        public static function getArmamentos($request) {
            $itens = '';

            $where = "disponibilidade = 1";
            $results = WeaponEntity::getWeapons($where, 'codigo_armamento DESC', null);

            while ($objWeapons = $results->fetchObject(WeaponEntity::class)) {
                //$objStatus = StatusArmamentoEntity::getStatusById($objWeapons->codigo_armamento);
                $itens .= ViewManager::render('dashboard/modules/arsenalmanagement/registerWithdraw/weaponsItem',[
                    'id'                => $objWeapons->codigo_armamento,
                    'numero_serie'      => $objWeapons->numero_serie,
                ]);
            }
            return $itens;
        }
        
     
        private static function getRegisterWithdrawItem($request){
            $itens = '';
            $results = ArrecadacaoRegisterEntity::getArrecadacao('data_devolucao is NULL', 'codigo_arrecadacao DESC', null);
            while ($objArrecadacao = $results->fetchObject(ArrecadacaoRegisterEntity::class)){ 
                // Busca a quantidade de munições da retirada
                $whereMunicao = "codigo_arrecadacao = '{$objArrecadacao->codigo_arrecadacao}'";
                $resultsMunicao = ArrecadacaoMunicaoEntity::getMunicaoArrecadacao($whereMunicao, 'codigo_arrecadacao DESC', null);
                $objArrecadacaoMunicoes = $resultsMunicao->fetchObject(ArrecadacaoMunicaoEntity::class);

                $itens .= ViewManager::render('dashboard/modules/arsenalmanagement/registerWithdraw/withdrawItem', [
                    'codigo'         => $objArrecadacao->codigo_arrecadacao,
                    'nome'           => $objArrecadacao->nome_funcionario,
                    'tipo_armamento' => $objArrecadacao->tipo_armamento,
                    'numero'         => $objArrecadacao->numero_de_serie_arma,
                    'municoes'       => $objArrecadacaoMunicoes ? $objArrecadacaoMunicoes->quantidade_levantar : '0',
                    'patente'        => $objArrecadacao->patente_funcionario,
                    'assinatura'     => $objArrecadacao->assinatura_arrecadacao,
                    'telefone'       => $objArrecadacao->celular_funcionario,
                    'data_retirada'  => $objArrecadacao->data_levantamento
                ]);
            }
            return $itens; 
        }

        public static function getNewRegisterWithdraw($request, $mensagem = ''){
            if(Funcoes::Permition(10)){
                $content = ViewManager::render('dashboard/modules/arsenalmanagement/registerWithdraw/newRegisterWithdraw',[
                    'navbar'                => parent::getNavbar(),
                    'sidebar'               => parent::getMenu(),
                    'rightsidebar'          => parent::getRightSidebar(),
                    'footer'                => parent::getFooter(),
                    'municaoItem'           => self::getAmmuniationItens($request),
                    'equipmentItem'         => self::getEquipmentItensWidthdraw($request),
                    'mensagem'              => $mensagem,
                    'armamento'             => self::getArmamentos($request),
                    'departamento'          => parent::getDepartamentos()
                ]);
                return parent::getPage('SIGECM | Nova Retirada', $content);
            }else{
                return ErrorController::getError($request);
            }
        }


        //================================================================
        // funcao que faz o registo de uma arrecadacao 
        //================================================================
        public static function SetNewRegisterWithdraw($request){
            if(Funcoes::Permition(10)){
                 #Instanciacao da base dados para criar uma transacao
                $db = new Database();

                $file = $request->getFile();
                $postVars = $request->getPostVars();

                try {
                    $db->beginTransaction();
                    //Verifica se o numero da arma nao foi atribuida a outra pessoa
                    if(!empty($postVars['text_numero_serie'])){
                        $objArrecadacao = new ArrecadacaoRegisterEntity;
                        $objFuncionario = new FuncionarioArrecadacaoEntity;
    
                        #========================================
                        # Verificacoes
                        #========================================
                        # Verificacao se numero de serie nao foi atribuida a outro funcionario
                        if (!empty($postVars['text_codigo_funcionario'])) {
                            $codigoFuncionario = (int)$postVars['text_codigo_funcionario']; // Elimina risco de SQL injection se for usado corretamente
                            $where = "codigo_funcionario = $codigoFuncionario AND data_devolucao IS NULL";
                            $results = ArrecadacaoRegisterEntity::getArrecadacao($where, 'codigo_arrecadacao DESC', null);
                            $objArrecadacaoVerificacao = $results->fetchObject(ArrecadacaoRegisterEntity::class);
                        } else {
                            $objArrecadacaoVerificacao = null;
                            // Você pode também adicionar um log ou mensagem aqui, se necessário
                        }
        
    
                        if(!empty($objArrecadacaoVerificacao)){
                            $request->getRouter()->redirect('/new-register-withdraw?status=error_funcionario');
                        }else{

                            if (!empty($postVars['text_numero_serie'])) {
                                // Escapar aspas simples para evitar quebra de SQL (recomendado: usar prepared statements, mas abaixo é uma alternativa básica)
                                $numeroSerie = addslashes($postVars['text_numero_serie']); 
                                $where = "numero_de_serie_arma = '$numeroSerie' AND data_devolucao IS NULL";
                                $results = ArrecadacaoRegisterEntity::getArrecadacao($where, 'codigo_arrecadacao DESC', null);
                                $objArrecadacaoVerificacao = $results->fetchObject(ArrecadacaoRegisterEntity::class);
                            } else {
                                $objArrecadacaoVerificacao = null;
                                // Opcional: definir uma mensagem de aviso ou erro
                            }

                           
                            if(!empty($objArrecadacaoVerificacao)){
                                $request->getRouter()->redirect('/new-register-withdraw?status=error_serie');
                            }else{
                                //verifica se o funcionario ja foi cadastrado antes
                                if(empty($postVars['text_codigo_funcionario'])){

                                    //Busca o departamento
                                    $codigo_departamento = $postVars['text_depertamento'];
                                    $objDepartamento = DepartamentoEntity::getEDepartamentoById($codigo_departamento);

                                    if(!empty($objDepartamento)){
                                        $nome_departamento = $objDepartamento->nome_departamento;
                                    }else{
                                        echo "❌ Departamento nao existente: ";
                                    }

                                    #===========================================
                                    # Cadastra o Funcionario
                                    #===========================================
                                    $objFuncionario->nome_completo                  = $postVars['text_nome_completo'];
                                    $objFuncionario->patente                        = $postVars['text_patente'];
                                    $objFuncionario->subunidade                     = $postVars['text_subunidade'];
                                    $objFuncionario->genero                         = $postVars['text_genero'];
                                    $objFuncionario->celular                        = $postVars['text_contacto_fiel'];
                                    $objFuncionario->codigo_departamento            = $postVars['text_depertamento'];
                                    $objFuncionario->nome_departamento              = $nome_departamento;
                                    $objFuncionario->fotografia                     = "usermilitary.png";
                                    $objFuncionario->criado_em                      = parent::getNowDateTime();
                                    $objFuncionario->atualizado_em                  = parent::getNowDateTime();

                                    $codigo_novo_funcionario = $objFuncionario->cadastrar();

                                    //Verifica se o funcionario foi cadastrado
                                    if($codigo_novo_funcionario != null){
                                        //Passo a seguir cadastrar a arrecadacao
                                        #===========================================
                                        # Funcionario Armamento
                                        #===========================================
                                        $objArrecadacao->codigo_funcionario             = $codigo_novo_funcionario;
                                        $objArrecadacao->nome_funcionario               = $postVars['text_nome_completo'];
                                        $objArrecadacao->patente_funcionario            = $postVars['text_patente'];
                                        $objArrecadacao->subunidade                     = $postVars['text_subunidade'];
                                        $objArrecadacao->celular_funcionario            = $postVars['text_contacto_fiel'];
                                        $objArrecadacao->departamento                   = $postVars['text_depertamento'];

                                        #===========================================
                                        # Armamento
                                        #===========================================
                                        $objArrecadacao->codigo_armamento               = $postVars['text_codigo_armamento'];
                                        $objArrecadacao->numero_de_serie_arma           = $postVars['text_numero_serie'];
                                        $objArrecadacao->tipo_armamento                 = $postVars['text_tipo_arma'];
                                        $objArrecadacao->modelo                         = $postVars['text_modelo'];
                                        $objArrecadacao->status_operacional_arma        = $postVars['text_estado'];
                                        $objArrecadacao->calibre_municao_arma           = $postVars['text_calibre_municao'];
                                        $objArrecadacao->data_ultima_inspecao_arma      = $postVars['text_data_inspencao'];

                                        $objArrecadacao->assinatura_arrecadacao         = $file;
                                        $objArrecadacao->data_levantamento              = parent::getNowDate();
                                        $objArrecadacao->criado_em                      = parent::getNowDateTime();
                                        $objArrecadacao->atualizado_em                  = parent::getNowDateTime();

                                        $codigo_arrecadacao  = $objArrecadacao->cadastrar();

                                        //Fazemos o cadastro das Municoes 
                                        if($codigo_arrecadacao != null && !empty($postVars['text_municao_retirar'])){

                                            $municoesID          = $postVars['text_municao_retirar'];
                                            $quantidadeMunicoes  = $postVars['text_municao_quantidade'];

                                            foreach ($municoesID as $index => $codigo_municao){
                                                //busca a municao pelo seu codigo
                                                $objMunicao = AmmunitionEntity::getAmmuniationById($codigo_municao);
                                                
                                                if(!$objMunicao){
                                                    continue;
                                                }
                                                //vai fazer o cadastro das municoes 
                                                $objMunicoes = new ArrecadacaoMunicaoEntity;

                                                $objMunicoes->codigo_arrecadacao         = $codigo_arrecadacao;
                                                $objMunicoes->codigo_municao             = $codigo_municao;
                                                $objMunicoes->nome_municao               = $objMunicao->nome;
                                                $objMunicoes->quantidade_levantar        = $quantidadeMunicoes[$index];
                                                $objMunicoes->criado_em                  = parent::getNowDateTime();
                                                $objMunicoes->atualizado_em              = parent::getNowDateTime();

                                                $objMunicoes->cadastrar();
                                            }
                                        }

                                        //Fazendoo cadastro dos equipamentos 
                                        if($codigo_arrecadacao != null && !empty($postVars['text_equipament'])){

                                            $equipamentosID          = $postVars['text_equipament'];
                                            $quantidadeEquipamentos  = $postVars['text_equipamento_quantidade'];

                                            foreach ($equipamentosID as $index => $codigo_equipamento){
                                                //busca a municao pelo seu codigo
                                                $objEquipamento = EquipmentEntity::getEquipmentById($codigo_equipamento);
                                                
                                                if(!$objEquipamento){
                                                    continue;
                                                }
                                                //vai fazer o cadastro das municoes 
                                                $objEquipametoArrecadacao = new ArrecadacaoEquipmentEntity;

                                                $objEquipametoArrecadacao->codigo_arrecadacao         = $codigo_arrecadacao;
                                                $objEquipametoArrecadacao->codigo_equipamento         = $codigo_equipamento;
                                                $objEquipametoArrecadacao->nome_equipamentos          = $objEquipamento->nome;
                                                $objEquipametoArrecadacao->quantidade_levantar        = $quantidadeEquipamentos[$index];
                                                $objEquipametoArrecadacao->criado_em                  = parent::getNowDateTime();
                                                $objEquipametoArrecadacao->atualizado_em              = parent::getNowDateTime();

                                                $objEquipametoArrecadacao->cadastrar();
                                            }
                                        }

                                    }else{
                                        //Deve imprimir erro de falha ao cadastrar o funcionario
                                        $request->getRouter()->redirect('/new-register-withdraw?status=error_funcionario');
                                    }

                                }else{
                                    //Caso o funcionario ja tenha sido cadastrado
                                    #===========================================
                                    # Funcionario Armamento
                                    #===========================================
                                    $objArrecadacao->codigo_funcionario             = $postVars['text_codigo_funcionario'];
                                    $objArrecadacao->nome_funcionario               = $postVars['text_nome_completo'];
                                    $objArrecadacao->patente_funcionario            = $postVars['text_patente'];
                                    $objArrecadacao->subunidade                     = $postVars['text_subunidade'];
                                    $objArrecadacao->celular_funcionario            = $postVars['text_contacto_fiel'];
                                    $objArrecadacao->departamento                   = $postVars['text_depertamento'];

                                    #===========================================
                                    # Armamento
                                    #===========================================
                                    $objArrecadacao->codigo_armamento               = $postVars['text_codigo_armamento'];
                                    $objArrecadacao->numero_de_serie_arma           = $postVars['text_numero_serie'];
                                    $objArrecadacao->tipo_armamento                 = $postVars['text_tipo_arma'];
                                    $objArrecadacao->modelo                         = $postVars['text_modelo'];
                                    $objArrecadacao->status_operacional_arma        = $postVars['text_estado'];
                                    $objArrecadacao->calibre_municao_arma           = $postVars['text_calibre_municao'];
                                    $objArrecadacao->data_ultima_inspecao_arma      = $postVars['text_data_inspencao'];

                                    $objArrecadacao->assinatura_arrecadacao         = $file;
                                    $objArrecadacao->data_levantamento              = parent::getNowDate();
                                    $objArrecadacao->criado_em                      = parent::getNowDateTime();
                                    $objArrecadacao->atualizado_em                  = parent::getNowDateTime();

                                    $codigo_arrecadacao  = $objArrecadacao->cadastrar();

                                    //Fazemos o cadastro das Municoes 
                                    if($codigo_arrecadacao != null && !empty($postVars['text_municao_retirar'])){

                                        $municoesID          = $postVars['text_municao_retirar'];
                                        $quantidadeMunicoes  = $postVars['text_municao_quantidade'];

                                        foreach ($municoesID as $index => $codigo_municao){
                                            //busca a municao pelo seu codigo
                                            $objMunicao = AmmunitionEntity::getAmmuniationById($codigo_municao);
                                                
                                            if(!$objMunicao){
                                                continue;
                                            }
                                            //vai fazer o cadastro das municoes 
                                            $objMunicoes = new ArrecadacaoMunicaoEntity;

                                            $objMunicoes->codigo_arrecadacao         = $codigo_arrecadacao;
                                            $objMunicoes->codigo_municao             = $codigo_municao;
                                            $objMunicoes->nome_municao               = $objMunicao->nome;
                                            $objMunicoes->quantidade_levantar        = $quantidadeMunicoes[$index];
                                            $objMunicoes->criado_em                  = parent::getNowDateTime();
                                            $objMunicoes->atualizado_em              = parent::getNowDateTime();

                                            $objMunicoes->cadastrar();
                                        }
                                    }

                                    //Fazendoo cadastro dos equipamentos 
                                    if($codigo_arrecadacao != null && !empty($postVars['text_equipament'])){
                                        $equipamentosID          = $postVars['text_equipament'];
                                        $quantidadeEquipamentos  = $postVars['text_equipamento_quantidade'];

                                        foreach ($equipamentosID as $index => $codigo_equipamento){
                                            //busca a municao pelo seu codigo
                                            $objEquipamento = EquipmentEntity::getEquipmentById($codigo_equipamento);
                                                
                                            if(!$objEquipamento){
                                                continue;
                                            }
                                            //vai fazer o cadastro das municoes 
                                            $objEquipametoArrecadacao = new ArrecadacaoEquipmentEntity;

                                            $objEquipametoArrecadacao->codigo_arrecadacao         = $codigo_arrecadacao;
                                            $objEquipametoArrecadacao->codigo_equipamento         = $codigo_equipamento;
                                            $objEquipametoArrecadacao->nome_equipamentos          = $objEquipamento->nome;
                                            $objEquipametoArrecadacao->quantidade_levantar        = $quantidadeEquipamentos[$index];
                                            $objEquipametoArrecadacao->criado_em                  = parent::getNowDateTime();
                                            $objEquipametoArrecadacao->atualizado_em              = parent::getNowDateTime();

                                            $objEquipametoArrecadacao->cadastrar();
                                        }
                                    }
                                }
                            }   
                        }

                    }else if(!empty($postVars['text_multiple_armas'])){
                        //=================================================
                        // condicao para arrecadar carias armas
                        //=================================================
                        $armasId = $postVars['text_multiple_armas'];

                        $objArrecadacao = new ArrecadacaoRegisterEntity;
                        $objFuncionario = new FuncionarioArrecadacaoEntity;
            
                        #========================================
                        # Verificacoes
                        #========================================
                        # Verificacao se numero de serie nao foi atribuida a outro funcionario

                        if (!empty($postVars['text_codigo_funcionario'])) {
                            $codigoFuncionario = (int)$postVars['text_codigo_funcionario']; // Elimina risco de SQL injection se for usado corretamente
                            $where = "codigo_funcionario = $codigoFuncionario AND data_devolucao IS NULL";
                            $results = ArrecadacaoRegisterEntity::getArrecadacao($where, 'codigo_arrecadacao DESC', null);
                            $objArrecadacaoVerificacao = $results->fetchObject(ArrecadacaoRegisterEntity::class);
                        } else {
                            $objArrecadacaoVerificacao = null;
                            // Você pode também adicionar um log ou mensagem aqui, se necessário
                        }
        

                        if(!empty($objArrecadacaoVerificacao)){
                            $request->getRouter()->redirect('/new-register-withdraw?status=error_funcionario');
                        }else{
                            //verifica se o funcionario ja foi cadastrado antes
                            if(empty($postVars['text_codigo_funcionario'])){

                                //Busca o departamento
                                $codigo_departamento = $postVars['text_depertamento'];
                                $objDepartamento = DepartamentoEntity::getEDepartamentoById($codigo_departamento);

                                if(!empty($objDepartamento)){
                                    $nome_departamento = $objDepartamento->nome_departamento;
                                }else{
                                    echo "❌ Departamento nao existente: ";
                                }

                                #===========================================
                                # Cadastra o Funcionario
                                #===========================================
                                $objFuncionario->nome_completo                  = $postVars['text_nome_completo'];
                                $objFuncionario->patente                        = $postVars['text_patente'];
                                $objFuncionario->subunidade                     = $postVars['text_subunidade'];
                                $objFuncionario->genero                         = $postVars['text_genero'];
                                $objFuncionario->celular                        = $postVars['text_contacto_fiel'];
                                $objFuncionario->codigo_departamento            = $postVars['text_depertamento'];
                                $objFuncionario->nome_departamento              = $nome_departamento;
                                $objFuncionario->fotografia                     = "usermilitary.png";
                                $objFuncionario->criado_em                      = parent::getNowDateTime();
                                $objFuncionario->atualizado_em                  = parent::getNowDateTime();

                                $codigo_novo_funcionario = $objFuncionario->cadastrar();

                                //depois de registar o funcionario comeca a criar as requisicoes 
                                foreach ($armasId as $codigo_arma){
                                    //Verifica se o funcionario foi cadastrado
                                    if($codigo_novo_funcionario != null){
                                        //Passo a seguir cadastrar a arrecadacao
                                        #===========================================
                                        # Funcionario Armamento
                                        #===========================================
                                        $objArrecadacao->codigo_funcionario             = $codigo_novo_funcionario;
                                        $objArrecadacao->nome_funcionario               = $postVars['text_nome_completo'];
                                        $objArrecadacao->patente_funcionario            = $postVars['text_patente'];
                                        $objArrecadacao->subunidade                     = $postVars['text_subunidade'];
                                        $objArrecadacao->celular_funcionario            = $postVars['text_contacto_fiel'];
                                        $objArrecadacao->departamento                   = $postVars['text_depertamento'];

                                        #===========================================
                                        # Armamento
                                        #===========================================
                                        $objArmamento = WeaponEntity::getWeaponById($codigo_arma); 

                                        $objArrecadacao->codigo_armamento               = $codigo_arma;
                                        $objArrecadacao->numero_de_serie_arma           = $objArmamento->numero_serie;
                                        $objArrecadacao->tipo_armamento                 = $objArmamento->nome_armamento;
                                        $objArrecadacao->modelo                         = $objArmamento->modelo;
                                        $objArrecadacao->status_operacional_arma        = $objArmamento->status_operacional;
                                        $objArrecadacao->calibre_municao_arma           = $objArmamento->calibre;
                                        $objArrecadacao->data_ultima_inspecao_arma      = $objArmamento->data_ultima_inspecao;


                                        $objArrecadacao->assinatura_arrecadacao         = $file;
                                        $objArrecadacao->data_levantamento              = parent::getNowDate();
                                        $objArrecadacao->criado_em                      = parent::getNowDateTime();
                                        $objArrecadacao->atualizado_em                  = parent::getNowDateTime();

                                        $codigo_arrecadacao  = $objArrecadacao->cadastrar();

                                        //Fazemos o cadastro das Municoes 
                                        if($codigo_arrecadacao != null && !empty($postVars['text_municao_retirar'])){

                                            $municoesID          = $postVars['text_municao_retirar'];
                                            $quantidadeMunicoes  = $postVars['text_municao_quantidade'];

                                            foreach ($municoesID as $index => $codigo_municao){
                                                //busca a municao pelo seu codigo
                                                $objMunicao = AmmunitionEntity::getAmmuniationById($codigo_municao);
                                                
                                                if(!$objMunicao){
                                                    continue;
                                                }
                                                //vai fazer o cadastro das municoes 
                                                $objMunicoes = new ArrecadacaoMunicaoEntity;

                                                $objMunicoes->codigo_arrecadacao         = $codigo_arrecadacao;
                                                $objMunicoes->codigo_municao             = $codigo_municao;
                                                $objMunicoes->nome_municao               = $objMunicao->nome;
                                                $objMunicoes->quantidade_levantar        = $quantidadeMunicoes[$index];
                                                $objMunicoes->criado_em                  = parent::getNowDateTime();
                                                $objMunicoes->atualizado_em              = parent::getNowDateTime();

                                                $objMunicoes->cadastrar();
                                            }
                                        }

                                        //Fazendoo cadastro dos equipamentos 
                                        if($codigo_arrecadacao != null && !empty($postVars['text_equipament'])){

                                            $equipamentosID          = $postVars['text_equipament'];
                                            $quantidadeEquipamentos  = $postVars['text_equipamento_quantidade'];

                                            foreach ($equipamentosID as $index => $codigo_equipamento){
                                                //busca a municao pelo seu codigo
                                                $objEquipamento = EquipmentEntity::getEquipmentById($codigo_equipamento);
                                                
                                                if(!$objEquipamento){
                                                    continue;
                                                }
                                                //vai fazer o cadastro das municoes 
                                                $objEquipametoArrecadacao = new ArrecadacaoEquipmentEntity;

                                                $objEquipametoArrecadacao->codigo_arrecadacao         = $codigo_arrecadacao;
                                                $objEquipametoArrecadacao->codigo_equipamento         = $codigo_equipamento;
                                                $objEquipametoArrecadacao->nome_equipamentos          = $objEquipamento->nome;
                                                $objEquipametoArrecadacao->quantidade_levantar        = $quantidadeEquipamentos[$index];
                                                $objEquipametoArrecadacao->criado_em                  = parent::getNowDateTime();
                                                $objEquipametoArrecadacao->atualizado_em              = parent::getNowDateTime();

                                                $objEquipametoArrecadacao->cadastrar();
                                            }
                                        }

                                    }else{
                                        //Deve imprimir erro de falha ao cadastrar o funcionario
                                        $request->getRouter()->redirect('/new-register-withdraw?status=error_funcionario');
                                    }
                                }

                            }else{

                                foreach ($armasId as $codigo_arma){
                                    //Caso o funcionario ja tenha sido cadastrado
                                    #===========================================
                                    # Funcionario Armamento
                                    #===========================================

                                    $objArrecadacao->codigo_funcionario             = $postVars['text_codigo_funcionario'];
                                    $objArrecadacao->nome_funcionario               = $postVars['text_nome_completo'];
                                    $objArrecadacao->patente_funcionario            = $postVars['text_patente'];
                                    $objArrecadacao->subunidade                     = $postVars['text_subunidade'];
                                    $objArrecadacao->celular_funcionario            = $postVars['text_contacto_fiel'];
                                    $objArrecadacao->departamento                   = $postVars['text_depertamento'];

                                    #===========================================
                                    # Armamento
                                    #===========================================

                                    $objArmamento = WeaponEntity::getWeaponById($codigo_arma); 

                                    $objArrecadacao->codigo_armamento               = $codigo_arma;
                                    $objArrecadacao->numero_de_serie_arma           = $objArmamento->numero_serie;
                                    $objArrecadacao->tipo_armamento                 = $objArmamento->nome_armamento;
                                    $objArrecadacao->modelo                         = $objArmamento->modelo;
                                    $objArrecadacao->status_operacional_arma        = $objArmamento->status_operacional;
                                    $objArrecadacao->calibre_municao_arma           = $objArmamento->calibre;
                                    $objArrecadacao->data_ultima_inspecao_arma      = $objArmamento->data_ultima_inspecao;


                                    $objArrecadacao->assinatura_arrecadacao         = $file;
                                    $objArrecadacao->data_levantamento              = parent::getNowDate();
                                    $objArrecadacao->criado_em                      = parent::getNowDateTime();
                                    $objArrecadacao->atualizado_em                  = parent::getNowDateTime();

                                    $codigo_arrecadacao  = $objArrecadacao->cadastrar();

                                    //Fazemos o cadastro das Municoes 
                                    if($codigo_arrecadacao != null && !empty($postVars['text_municao_retirar'])){

                                        $municoesID          = $postVars['text_municao_retirar'];
                                        $quantidadeMunicoes  = $postVars['text_municao_quantidade'];

                                        foreach ($municoesID as $index => $codigo_municao){
                                            //busca a municao pelo seu codigo
                                            $objMunicao = AmmunitionEntity::getAmmuniationById($codigo_municao);
                                                
                                            if(!$objMunicao){
                                                continue;
                                            }
                                            //vai fazer o cadastro das municoes 
                                            $objMunicoes = new ArrecadacaoMunicaoEntity;

                                            $objMunicoes->codigo_arrecadacao         = $codigo_arrecadacao;
                                            $objMunicoes->codigo_municao             = $codigo_municao;
                                            $objMunicoes->nome_municao               = $objMunicao->nome;
                                            $objMunicoes->quantidade_levantar        = $quantidadeMunicoes[$index];
                                            $objMunicoes->criado_em                  = parent::getNowDateTime();
                                            $objMunicoes->atualizado_em              = parent::getNowDateTime();

                                            $objMunicoes->cadastrar();
                                        }
                                    }

                                    //Fazendoo cadastro dos equipamentos 
                                    if($codigo_arrecadacao != null && !empty($postVars['text_equipament'])){
                                        $equipamentosID          = $postVars['text_equipament'];
                                        $quantidadeEquipamentos  = $postVars['text_equipamento_quantidade'];

                                        foreach ($equipamentosID as $index => $codigo_equipamento){
                                            //busca a municao pelo seu codigo
                                            $objEquipamento = EquipmentEntity::getEquipmentById($codigo_equipamento);
                                                
                                            if(!$objEquipamento){
                                                continue;
                                            }
                                            //vai fazer o cadastro das municoes 
                                            $objEquipametoArrecadacao = new ArrecadacaoEquipmentEntity;

                                            $objEquipametoArrecadacao->codigo_arrecadacao         = $codigo_arrecadacao;
                                            $objEquipametoArrecadacao->codigo_equipamento         = $codigo_equipamento;
                                            $objEquipametoArrecadacao->nome_equipamentos          = $objEquipamento->nome;
                                            $objEquipametoArrecadacao->quantidade_levantar        = $quantidadeEquipamentos[$index];
                                            $objEquipametoArrecadacao->criado_em                  = parent::getNowDateTime();
                                            $objEquipametoArrecadacao->atualizado_em              = parent::getNowDateTime();

                                            $objEquipametoArrecadacao->cadastrar();
                                        }
                                    }
                                }
                            }
                        }
            
                    }
                    $db->commit();
                    $request->getRouter()->redirect('/register-withdraw?status=created');

                }catch (\Exception $e) {
                    $db->rollBack();
                    echo "❌ Erro na transação: " . $e->getMessage();
                }  
            }else{
                return ErrorController::getError($request);
            }
        }
        
        public static function getRegisterWitrhdrawPage($request){
            if(Funcoes::Permition(10)){
                $content = ViewManager::render('dashboard/modules/arsenalmanagement/registerWithdraw/withdrawRegister',[
                    'navbar'        => parent::getNavbar(),
                    'sidebar'       => parent::getMenu(),
                    'rightsidebar'  => parent::getRightSidebar(),
                    'footer'        => parent::getFooter(),
                    'withdrawItem'  => self::getRegisterWithdrawItem($request),
                ]);

                return parent::getPage('SIGECM | Armamento', $content);
            }else{
                return ErrorController::getError($request);
            }
        }
        #============================================================================
        # Fim Registar funcionario e Arecadar
        #============================================================================

        public static function getNewRegisterReparticao($request){
            if(Funcoes::Permition(9)){
                //Busca as infomacoes do grupo do utilizador
                /*$ranges = [
                    [10, 13],
                    [15, 15]
                ];*/
                $ranges = [
                    [10, 14]
                ];

                $permissoesStr = parent::geradorPermissoes($ranges, 100);

                $where = "permissoes = '{$permissoesStr}'";
                $results = GroupEntity::getGrupos($where, 'codigo_grupo DESC', 1);
                $objGrupos = $results->fetchObject(GroupEntity::class);

                $content = ViewManager::render('dashboard/modules/arsenalmanagement/reparticao/newUserRegister',[
                    'navbar'        => parent::getNavbar(),
                    'sidebar'       => parent::getMenu(),
                    'rightsidebar'  => parent::getRightSidebar(),
                    'footer'        => parent::getFooter(),
                    'codigo_grupo'  => $objGrupos->codigo_grupo,
                    'grupo'         => $objGrupos->nome_grupo,
                    'departamento'  => parent::getDepartamentos()
                ]);

                return parent::getPage('SIGECM |Novo Utilizador', $content);
            }else{
                return ErrorController::getError($request);
            }
        }


        public static function setNewRegisterReparticao($request){
            if(Funcoes::Permition(9)){
                $postVars = $request->getPostVars();

                //concatena nome e apelido e com ponto john.doe
                // Sanitização e preparação de dados
                $nome       = filter_var(trim($postVars['text_nome']), FILTER_SANITIZE_STRING);
                $apelido    = filter_var(trim($postVars['text_apelido']), FILTER_SANITIZE_STRING);
                $utilizador_final = strtolower($nome . '.' . $apelido);

                $objUtilizador = new UtilizadorEntity;
                $objUtilizador->patente                 = $postVars['text_patente'];
                $objUtilizador->nome_utilizador         = $postVars['text_nome_completo'];
                $objUtilizador->subunidade              = $postVars['text_subunidade'];
                $objUtilizador->genero                  = $postVars['text_genero'];
                $objUtilizador->numero_de_celular       = $postVars['text_celular'];
                $objUtilizador->celular_alternativo     = $postVars['text_celular_alt'];
                $objUtilizador->codigo_departamento     = $postVars['text_depertamento'];
                $objUtilizador->utilizador              = $utilizador_final;
                $objUtilizador->palavra_passe           = md5($postVars['text_senha_acesso']);
                $objUtilizador->grupos                  = (int) $postVars['text_grupo'];
                $objUtilizador->criado_em               = parent::getNowDateTime();
                $objUtilizador->atualizado_em           = parent::getNowDateTime();

                $objUtilizador->cadastrar(); 
                $request->getRouter()->redirect('/users-armamento?status=created');
            }else{
                $request->getRouter()->redirect('/refill-equipments?status=error_update');
            }
        }

        // Lista dos utlizadores do armamento
        private static function getArmamentosUsersItem(){
            $itens = '';
            $supervisor = [[9, 15]];
            $user_comum = [[10, 14]];

            // Geração segura das permissões
            $permissoesSuper = parent::geradorPermissoes($supervisor, 100);
            $permissoesUser  = parent::geradorPermissoes($user_comum, 100);

            // Busca dos grupos com essas permissões
            $whereGrupo = "permissoes = '{$permissoesSuper}' OR permissoes = '{$permissoesUser}'";
            $resultsGrupo = GroupEntity::getGrupos($whereGrupo, 'codigo_grupo DESC', null);

            $gruposIds = [];
            while ($grupo = $resultsGrupo->fetchObject(GroupEntity::class)) {
                $gruposIds[] = $grupo->codigo_grupo;
            }

            // Verificação se encontrou grupo
            // Busca dos utilizadores do grupo encontrado
            if (empty($gruposIds)) {
                return '';
            }
            
            $where = 'grupos IN (' . implode(',', $gruposIds) . ')';
            $resultsUsers = UtilizadorEntity::getUtilizadores($where, 'codigo_utilizador DESC', null);
            while ($objUtilizador = $resultsUsers->fetchObject(UtilizadorEntity::class)){
                $itens .= ViewManager::render('dashboard/modules/arsenalmanagement/reparticao/userItem', [
                    'codigo'         => $objUtilizador->codigo_utilizador,
                    'patente'        => $objUtilizador->patente,
                    'nome'           => $objUtilizador->nome_utilizador,
                    'subunidade'     => $objUtilizador->subunidade,
                    'celular'        => $objUtilizador->numero_de_celular,
                    'estado'        => 'Activo'
                ]);
            }
            return $itens; 
        }


        public static function getRegisterReparticaoPage($request){
            if(Funcoes::Permition(9)){
                //Busca o grupo dos utilizadores comunus 
                $content = ViewManager::render('dashboard/modules/arsenalmanagement/reparticao/usersRegister',[
                    'navbar'        => parent::getNavbar(),
                    'sidebar'       => parent::getMenu(),
                    'rightsidebar'  => parent::getRightSidebar(),
                    'footer'        => parent::getFooter(),
                    'itens'         => self::getArmamentosUsersItem()
                ]);

                return parent::getPage('SIGECM | Utilizadores', $content);
            }else{
                return ErrorController::getError($request);
            }
        }
        #============================================================================
        # Funcoes da reparticao
        #============================================================================

        #============================================================================
        # Funcoes de geracao de  relatorios
        #============================================================================
        


        #============================================================================
        # Fim Funcoes de geracao de  relatorios
        #============================================================================






        #============================================================================
        # Fim das Funcoes da reparticao
        #============================================================================
    }
?>