<?php
    namespace App\Controller\DashboardDalog;

    use App\Controller\DalogPageController;
use App\Controller\Dashboard\Alert;
use App\Controller\Dashboard\ErrorController;
use App\Model\Entity\Fardamentos\FardamentosEntity;
use App\Utils\Funcoes;
    use App\Utils\ViewManager;

    class FardamentosController extends DalogPageController{
        #========================================================
        # Funcao responsavel por montar a tabela de artigos
        #========================================================
         private static function getFardamentosItens(){
            $itens = '';
            $results = FardamentosEntity::getFardamentos(null, 'codigo_fardamento', null);

            While ($objFardamentos = $results->fetchObject(FardamentosEntity::class)){
                $itens .=ViewManager::render('dashboardDalog/modules/fardamentos/item', [
                    'codigo'            => $objFardamentos->codigo_fardamento,
                    'tipo'              => $objFardamentos->tipo_fardamento,
                    'cor'               => $objFardamentos->cor_material,
                    'quantidade'        => $objFardamentos->quantidade,
                    'tamanho'           => $objFardamentos->tamanho 
                ]);
            }
            return $itens;
        }

        public static function getFardamento($request){
            if(Funcoes::Permition(16)){
                $content = ViewManager::render('dashboardDalog/modules/fardamentos/fardamentos',[
                    'navbar'                => parent::getNavbar(),
                    'sidebar'               => parent::getMenu(),
                    'footer'                => parent::getFooter(),
                    'item'          => self::getFardamentosItens(),
                    'status'                => self::getStatus($request)
                ]);
                return parent::getPage('SIGECM | Novo Tipo de Fardamento', $content);
            }else{
                return ErrorController::getError($request);
            }
        }

        public static function getNewFardamento($request){
            if(Funcoes::Permition(16)){
                $content = ViewManager::render('dashboardDalog/modules/fardamentos/newFardamento',[
                    'navbar'                => parent::getNavbar(),
                    'sidebar'               => parent::getMenu(),
                    'footer'                => parent::getFooter(),
                ]);
                return parent::getPage('SIGECM | Novo Tipo de Fardamento', $content);
            }else{
                return ErrorController::getError($request);
            }
        }

         public static function setNewFardamento($request)
        {
            $postVars = $request->getPostVars();
        
            $objFardamento = new FardamentosEntity;
            $objFardamento->tipo_fardamento          = $postVars['text_tipo_fardamento'];
            $objFardamento->material_fabrico         = $postVars['text_material_fabricacao'];
            $objFardamento->cor_material             = $postVars['text_cor_material'];
            $objFardamento->finalidade               = $postVars['text_finalidade'];
            $objFardamento->durabilidade             = $postVars['text_durabilidade'];
            $objFardamento->instrucoes               = $postVars['text_lavagem_manutencao'];
            $objFardamento->tamanho                  = $postVars['text_tamanho'];
            $objFardamento->quantidade               = $postVars['text_quantidade'];
            $objFardamento->fornecedor               = $postVars['text_fornecedor'];
            $objFardamento->adquirido_em             = $postVars['text_data_aquisicao'];
            $objFardamento->criado_em                = parent::getNowDateTime();
            $objFardamento->atualizado_em            = parent::getNowDateTime();

            //Instaciacao da Funcao para fazer o cadastro
            $objFardamento->cadastrar();
            $request->getRouter()->redirect('/fardamentos?status=created');

        }

        private static function getStatus($request){
            $queryParams = $request->getQueryParams();
            
            if(!isset($queryParams['status'])) return '';

            switch($queryParams['status']){
                case 'created':
                    return Alert::getSuccess('Artigo cadastrado com sucesso.');
                    break;
                case 'updated':
                    return Alert::getSuccess('Artigo actualizada com sucesso.');
                    break;
                case 'deleted':
                    return Alert::getSuccess('Artigo excluido com sucesso.');
                    break;
            }
        } 
        
    }
?>