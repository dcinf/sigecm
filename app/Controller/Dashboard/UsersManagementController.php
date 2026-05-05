<?php
    namespace App\Controller\Dashboard;
    use App\Utils\ViewManager;
    use App\DatabaseManager\Pagination;
    use App\Model\Entity\LoginEntity\UtilizadorPermissoes as EntityUtilizador;
    use App\Controller\Dashboard\ErrorController;
    use App\Controller\GlobalPageController;
use App\Model\Entity\UtilizadorEntity;
use App\Utils\Funcoes;

    class UsersManagementController extends GlobalPageController{
        private static function getUtilizadorItens($request, &$objPagination){
            $itens = '';
            $quantidadeTotal = EntityUtilizador::getUsers(null, null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

            $queryParams = $request->getQueryParams();
            $paginaActual = $queryParams['page'] ?? 1;

            $objPagination = new Pagination($quantidadeTotal, $paginaActual, 8);

            $results = EntityUtilizador::getUsers(null, 'codigo_utilizador', $objPagination->getLimit());

            While ($objUtilizador = $results->fetchObject(EntityUtilizador::class)){
                $itens .=ViewManager::render('dashboard/modules/usersmanagment/itens', [
                    'codigo'            => $objUtilizador->codigo_utilizador,
                    'user'              => $objUtilizador->utilizador,
                    'name'              => $objUtilizador->nome_utilizador,
                    'grupo'             => $objUtilizador->descricao_grupo,
                    'departamento'      =>  $objUtilizador->departamento 
                ]);
            }
            return $itens;
        }


        
        
        private static function getStatus($request){
            $queryParams = $request->getQueryParams();
            
            if(!isset($queryParams['status'])) return '';

            switch($queryParams['status']){
                case 'created':
                    return Alert::getSuccess('Utilizador cadastrado com sucesso.');
                    break;
                case 'updated':
                    return Alert::getSuccess('Utilizador actualizada com sucesso.');
                    break;
                case 'deleted':
                    return Alert::getSuccess('Utilizador excluido com sucesso.');
                    break;
            }
        } 
        
        public static function getUtilizadores($request){
            if(Funcoes::Permition(1)){
                $content = ViewManager::render('dashboard/modules/usersmanagment/users',[
                    'navbar'        => parent::getNavbar(),
                    'sidebar'       => parent::getMenu(),
                    'rightsidebar'  => parent::getRightSidebar(),
                    'footer'        => parent::getFooter(),
                    'itens'         => self::getUtilizadorItens($request, $objPagination),
                    'status'        => self::getStatus($request)
                ]);

                return parent::getPage('SIGECM | Utilizadores', $content);
            }else{
                return ErrorController::getError($request);
            }
        }

        #===========================================================
        # cadastro de um novo utilizador
        #===========================================================
        public static function setNewUtilizador($request){
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
                $request->getRouter()->redirect('/usersmanagement?status=created');
            }else{
                $request->getRouter()->redirect('/usersmanagement?status=error_update');
            }
        }


        public static function getNewUtilizador($request){
            if(Funcoes::Permition(1)){
                $content = ViewManager::render('dashboard/modules/usersmanagment/newuser',[
                    'navbar'            => parent::getNavbar(),
                    'sidebar'           => parent::getMenu(),
                    'rightsidebar'      => parent::getRightSidebar(),
                    'footer'            => parent::getFooter(),
                    'itens'             => self::getUtilizadorItens($request, $objPagination),
                    'departamenentos'   => parent::getDepartamentos(),
                    'grupos'            => parent::getGruposItems(),
                    'status'            => self::getStatus($request)
                ]);

                return parent::getPage('SIGECM | Utilizadores', $content);
            }else{
                return ErrorController::getError($request);
            }
        }

    }
?>