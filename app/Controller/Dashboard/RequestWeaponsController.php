<?php
    namespace App\Controller\Dashboard;;
    use App\Utils\ViewManager;
    use App\Controller\Dashboard\ErrorController;
    use App\Controller\GlobalPageController;
    use App\DatabaseManager\Database;
    use App\Model\Entity\ActivosRequestEntity;
    use App\Model\Entity\AmmuniationRequestEntity;
    use App\Model\Entity\AmmunitionEntity;
    use App\Model\Entity\ArmasRequestEntity;
    use App\Model\Entity\RequestEntity;
    use App\Model\Entity\WeaponEntity;
    use App\Utils\Funcoes;

    class RequestWeaponsController extends GlobalPageController{
        #=========================================================
        # Funcoes para as requisicoes
        #=========================================================
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

        public static function getNewRequestWithdraw($request, $mensagem = ''){
            if(Funcoes::Permition(10)){
                $content = ViewManager::render('dashboard/modules/arsenalmanagement/request/newRequestWithdraw',[
                    'navbar'                => parent::getNavbar(),
                    'sidebar'               => parent::getMenu(),
                    'rightsidebar'          => parent::getRightSidebar(),
                    'footer'                => parent::getFooter(),
                    'municaoItem'           => self::getAmmuniationItens($request),
                    //'equipmentItem'         => self::getEquipmentItensWidthdraw($request),
                    //'mensagem'              => $mensagem,
                    'armamento'             => self::getArmamentos($request)
                ]);
                return parent::getPage('SIGECM | Requisições', $content);
            }else{
                return ErrorController::getError($request);
            }
        }

        public static function SetNewRequestWithdraw($request){
            if(Funcoes::Permition(10)){
                #Instanciacao da base dados para criar uma transacao
                $db = new Database();

                $receptor = $request->getFile_fiador();
                $fornecedor = $request->getFile_fiel();
                $postVars = $request->getPostVars();

                try {
                    $db->beginTransaction();
                        // Inserir a requisicao
                        $objRequisicao = new RequestEntity;

                        $objRequisicao->armazem                             = $postVars['text_armazem'];
                        $objRequisicao->nome_requerente                     = $postVars['text_nome_fornecido'];
                        $objRequisicao->numero_requisicao                   = $postVars['text_numero_requisicao'];
                        $objRequisicao->data_requisicao                     = $postVars['text_data_requisicao'];
                        $objRequisicao->assinatura_receptor_requisicao      = $receptor;
                        $objRequisicao->assinatura_fornecedor_requisicao    = $fornecedor;
                        $objRequisicao->criado_em                           = parent::getNowDateTime();
                        $objRequisicao->atualizado_em                       = parent::getNowDateTime();

                        $codigo_requisicao =  $objRequisicao->cadastrar();

                    if(isset($postVars['text_multiple_armas']) && $codigo_requisicao != null){
                        $armasId = $postVars['text_multiple_armas']; 

                        #Ciclo responsavel por iterar todas as armas submetidas e fazer o cadastro
                        foreach ($armasId as $codigo_arma){
                            #busca a arma pelo seu ID
                            $objArmamento = WeaponEntity::getWeaponById($codigo_arma); 

                            if (!$objArmamento) {
                                continue;
                            }

                            $objArmasRequisicao = new ArmasRequestEntity;

                            #===========================================
                            # Armamento
                            #===========================================
                            $objArmasRequisicao->codigo_requisicao              = $codigo_requisicao;
                            $objArmasRequisicao->codigo_armamento               = $objArmamento->codigo_armamento;
                            $objArmasRequisicao->numero_de_serie_arma           = $objArmamento->numero_serie;
                            $objArmasRequisicao->tipo_armamento                 = $objArmamento->nome_armamento;
                            $objArmasRequisicao->modelo                         = $objArmamento->modelo;
                            $objArmasRequisicao->status_operacional_arma        = $objArmamento->status_operacional;
                            $objArmasRequisicao->calibre                        = $objArmamento->calibre;
                            $objArmasRequisicao->data_ultima_inspecao_arma      = $objArmamento->data_ultima_inspecao;
                            $objArmasRequisicao->designacao                     = $postVars['text_arma_designacao'];
                            $objArmasRequisicao->criado_em                      = parent::getNowDateTime();
                            $objArmasRequisicao->atualizado_em                  = parent::getNowDateTime();

                            $objArmasRequisicao->cadastrar(); 
                        }
                    }

                    if(isset($postVars['text_municao_retirar']) && $codigo_requisicao != null){
                        #Municoes 
                        $municoesId = $postVars['text_municao_retirar'];
                        $quantidadeMunicoes = $postVars['text_municao_quantidade'];
                        $designacaoMunicoes = $postVars['text_designacao_municao'];

                        foreach ($municoesId as $index => $codigo_municoes){
                            #busca a Municao pelo seu ID
                            $objMunicao = AmmunitionEntity::getAmmuniationById($codigo_municoes); 

                            if (!$objMunicao) {
                                continue;
                            }

                            #===========================================
                            # Municoes
                            #===========================================
                            $objMunicaoRequest = new AmmuniationRequestEntity;

                            $objMunicaoRequest->codigo_requisicao               = $codigo_requisicao;
                            $objMunicaoRequest->codigo_municao                  = $objMunicao->codigo_municao;
                            $objMunicaoRequest->quantidade_municao              = $quantidadeMunicoes[$index];
                            $objMunicaoRequest->designacao                      = $designacaoMunicoes[$index];
                            $objMunicaoRequest->criado_em                       = parent::getNowDateTime();
                            $objMunicaoRequest->atualizado_em                   = parent::getNowDateTime();

                            $objMunicaoRequest->cadastrar();
                        }
                    }

                    if(isset($postVars['text_activo_quantidade']) && isset($postVars['text_outro_activo']) && $codigo_requisicao != null){
                        #Activos
                        $quantidadeActivos =  $postVars['text_activo_quantidade'];
                        $designacaoActivos =  $postVars['text_outro_activo'];

                        foreach ($quantidadeActivos as $index => $quantidades_activos){
                            #===========================================
                            # Activos
                            #===========================================
                            $objActivosRequest = new ActivosRequestEntity;

                            $objActivosRequest->codigo_requisicao               = $codigo_requisicao;
                            $objActivosRequest->quantidade_activo               = $quantidades_activos;
                            $objActivosRequest->designacao                      = $designacaoActivos[$index];
                            $objActivosRequest->criado_em                       = parent::getNowDateTime();
                            $objActivosRequest->atualizado_em                   = parent::getNowDateTime();

                            $objActivosRequest->cadastrar();
                        }

                    }

                    $db->commit();
                    $request->getRouter()->redirect('/requests?status=created');

                } catch (\Exception $e) {
                    $db->rollBack();
                    echo "❌ Erro na transação: " . $e->getMessage();
                }
            }
        }

        private static function getRequestsItem(){
            $itens = '';
            $results = RequestEntity::getRequisicao("data_devolucao IS NULL", 'codigo_requisicao DESC', null);
            While ($objRequests = $results->fetchObject(RequestEntity::class)){ 
                //Montando os itens a serem retornados
                $itens .= ViewManager::render('dashboard/modules/arsenalmanagement/request/requestItem', [
                    'codigo'                                        => $objRequests->codigo_requisicao,
                    'armazem'                                       => $objRequests->armazem,
                    'nome_requerente'                               => $objRequests->nome_requerente,
                    'numero_requisicao'                             => $objRequests->numero_requisicao,
                    'data_requisicao'                               => $objRequests->data_requisicao,
                    'assinatura_receptor_requisicao'                => $objRequests->assinatura_receptor_requisicao,
                    'assinatura_fornecedor_requisicao'              => $objRequests->assinatura_fornecedor_requisicao,
                ]);
            }
            return $itens; 
        }

        public static function getRequestWeaponPage($request){
            if(Funcoes::Permition(10)){
                $content = ViewManager::render('dashboard/modules/arsenalmanagement/request/request',[
                    'navbar'        => parent::getNavbar(),
                    'sidebar'       => parent::getMenu(),
                    'rightsidebar'  => parent::getRightSidebar(),
                    'footer'        => parent::getFooter(),
                    'requestItem'  => self::getRequestsItem(),
                ]);

                return parent::getPage('SIGECM | Requisições', $content);
            }else{
                return ErrorController::getError($request);
            }
        }
    }
?>