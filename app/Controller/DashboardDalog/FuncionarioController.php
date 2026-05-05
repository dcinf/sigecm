<?php
    namespace App\Controller\DashboardDalog;
    use App\Controller\DalogPageController;
    use App\Controller\Dashboard\Alert;
    use App\Controller\Dashboard\ErrorController;
use App\Model\Entity\DepartamentoEntity;
use App\Model\Entity\Fardamentos\FuncionarioFardamentosEntity;
use App\Utils\Funcoes;
    use App\Utils\ViewManager;

     class FuncionarioController extends DalogPageController{

        #=========================================================
        # funcoes responsaveis por montar os elementos das tebelas
        #=========================================================

        private static function getFuncionariosItens(){
            $itens = '';
            $results = FuncionarioFardamentosEntity::getFuncionario(null, 'codigo_funcionario', null);

            While ($objFuncionario = $results->fetchObject(FuncionarioFardamentosEntity::class)){
                $itens .=ViewManager::render('dashboardDalog/modules/funcionario/item', [
                    'codigo'            => $objFuncionario->codigo_funcionario,
                    'patente'           => $objFuncionario->patente,
                    'name'              => $objFuncionario->nome_completo,
                    'celular'           => $objFuncionario->celular,
                    'departamento'      => $objFuncionario->nome_departamento 
                ]);
            }
            return $itens;
        }


        #============================================================================
        # Funcao para cadastrar novo Funcionario
        #============================================================================
        public static function getNewFuncionario($request){
            if(Funcoes::Permition(16)){
                $content = ViewManager::render('dashboardDalog/modules/funcionario/newFuncionario',[
                    'navbar'                => parent::getNavbar(),
                    'sidebar'               => parent::getMenu(),
                    'footer'                => parent::getFooter(),
                    'departamento'          => parent::getDepartamentos()
                ]);
                return parent::getPage('SIGECM | Novo Funcionario', $content);
            }else{
                return ErrorController::getError($request);
            }
        }


        public static function setNewFuncionario($request)
        {
            $postVars = $request->getPostVars();
        
            # Busca o nome do departamento;
            $where =  "codigo_departamento = " . $postVars['text_codigo_departamento'];
            $objDepartamento = DepartamentoEntity::getEDepartamentoById($where, 'codigo_departamento DESC', null);

            $objFuncionario = new FuncionarioFardamentosEntity;
            $objFuncionario->nome_completo              = $postVars['text_nome_completo'];
            $objFuncionario->subunidade                 = $postVars['text_subunidade'];
            $objFuncionario->genero                     = $postVars['text_genero'];
            $objFuncionario->patente                    = $postVars['text_patente'];
            $objFuncionario->codigo_departamento        = $objDepartamento->codigo_departamento;
            $objFuncionario->nome_departamento          = $objDepartamento->nome_departamento;
            $objFuncionario->tamanho_calca              = $postVars['text_tamanho_calca'];
            $objFuncionario->tamanho_camisa             = $postVars['text_tamanho_camisa'];
            $objFuncionario->tamanho_bota               = $postVars['text_tamanho_Bota'];
            $objFuncionario->celular                    = $postVars['text_contacto'];
            $objFuncionario->criado_em                  = parent::getNowDateTime();
            $objFuncionario->atualizado_em              = parent::getNowDateTime();

            //Instaciacao da Funcao para fazer o cadastro
            $objFuncionario->cadastrar();
            $request->getRouter()->redirect('/funcionarios?status=created');

        }


         private static function getStatus($request){
            $queryParams = $request->getQueryParams();
            
            if(!isset($queryParams['status'])) return '';

            switch($queryParams['status']){
                case 'created':
                    return Alert::getSuccess('Funcionario cadastrado com sucesso.');
                    break;
                case 'updated':
                    return Alert::getSuccess('Funcionario actualizada com sucesso.');
                    break;
                case 'deleted':
                    return Alert::getSuccess('Funcionario excluido com sucesso.');
                    break;
            }
        } 
        
        public static function getFuncionarios($request){
            if(Funcoes::Permition(16)){
                $content = ViewManager::render('dashboardDalog/modules/funcionario/funcionario',[
                    'navbar'        => parent::getNavbar(),
                    'sidebar'       => parent::getMenu(),
                    'footer'        => parent::getFooter(),
                    'item'          => self::getFuncionariosItens(),
                    'status'        => self::getStatus($request)
                ]);

                return parent::getPage('SIGECM | Funcionarios', $content);
            }else{
                return ErrorController::getError($request);
            }
        }

     }
?>