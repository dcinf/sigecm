<?php
    namespace App\Controller\DashboardDalog;
    use App\Controller\DalogPageController;
    use App\Controller\Dashboard\Alert;
    use App\Controller\Dashboard\ErrorController;
    use App\Model\Entity\Fardamentos\FardamentosEntity;
    use App\Utils\Funcoes;
    use App\Utils\ViewManager;

     class GestaoStoqueController extends DalogPageController{
         public static function getFardamentos() {
            $itens = '';
            $results = FardamentosEntity::getFardamentos(null, 'codigo_fardamento', null);
            // Iterate through each type
            while ($objFardamentos = $results->fetchObject(FardamentosEntity::class)) {
                $itens .= ViewManager::render('dashboardDalog/modules/estoque/selectItem', [
                    'value'                     => $objFardamentos->codigo_fardamento,
                    'descricao'                 => $objFardamentos->tipo_fardamento ." | ".$objFardamentos->cor_material . " | ".$objFardamentos->tamanho
                ]);
            }
            return $itens;
        }


        private static function getStatus($request){
            $queryParams = $request->getQueryParams();
            
            if(!isset($queryParams['status'])) return '';

            switch($queryParams['status']){
                case 'created':
                    return Alert::getSuccess('Estoque cadastrado com sucesso.');
                    break;
                case 'updated':
                    return Alert::getSuccess('Estoque actualizada com sucesso.');
                    break;
                case 'deleted':
                    return Alert::getSuccess('Estoque excluido com sucesso.');
                    break;
            }
        } 
        
        public static function getFardamentosUpdate($request){
            if(Funcoes::Permition(16)){


                $content = ViewManager::render('dashboardDalog/modules/estoque/fardamentosUpdate',[
                    'navbar'        => parent::getNavbar(),
                    'sidebar'       => parent::getMenu(),
                    'footer'        => parent::getFooter(),
                    'selectItem'    => self::getFardamentos(),
                    'status'        => self::getStatus($request)
                ]);

                return parent::getPage('SIGECM | Funcionarios', $content);
            }else{
                return ErrorController::getError($request);
            }
        }

        public static function setFardamentosUpdate($request)
        {
            $postVars = $request->getPostVars();

            //Fazemos o cadastro das Municoes 
            if(!empty($postVars['text_artigo']) && !empty($postVars['text_quantidade'])){

                $artigoID          = $postVars['text_artigo'];
                $quantidadeArtigo  = $postVars['text_quantidade'];

                foreach ($artigoID as $index => $codigo_fardamento){
                    //busca a municao pelo seu codigo
                    $objMFardamento = FardamentosEntity::getFardamentoById($codigo_fardamento);
                        
                    if(!$objMFardamento || !isset($quantidadeArtigo[$index])){
                        continue;
                    }
                    //vai fazer o cadastro das municoes
                     $objMFardamento->codigo_fardamento         = $codigo_fardamento;
                    $objMFardamento->quantidade                 = $objMFardamento->quantidade + $quantidadeArtigo[$index];
                    $objMFardamento->atualizado_em              = parent::getNowDateTime();

                    $objMFardamento->actualizar();
                    $request->getRouter()->redirect('/stoque-fardamentos?status=updated');
                }
            }else{
                $request->getRouter()->redirect('/stoque-fardamentos?status=error_update');
            }

        }


     }