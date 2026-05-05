<?php
    namespace App\Controller\Dashboard;
    use App\Utils\ViewManager;
    use App\DatabaseManager\Pagination;
    use App\Model\Entity\LoginEntity\UtilizadorPermissoes as EntityUtilizador;
    use App\Controller\Dashboard\ErrorController;
    use App\Controller\GlobalPageController;
use App\Model\Entity\DepartamentoEntity;
use App\Model\Entity\UtilizadorEntity;
use App\Utils\Funcoes;

    class ProfileController extends GlobalPageController{
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

        public static function updateUserPassword($request){
            if(Funcoes::Permition(10)){
                $postVars = $request->getPostVars();

                $codigo_utilizador = $_SESSION['admin']['utilizador']['id'];
                $objUtilizador = UtilizadorEntity::getUtilizadorById($codigo_utilizador);

                if(md5($postVars['text_old_password']) != $objUtilizador->palavra_passe){
                    $request->getRouter()->redirect('/profile?status=error_old_password');
                }else{
                    if($postVars['text_new_password'] != $postVars['text_new_password_repeat']){
                        $request->getRouter()->redirect('/profile?status=error_new_password');
                    }else{
                        $objUtilizador->palavra_passe       = md5($postVars['text_new_password']);
                        $objUtilizador->atualizado_em       = parent::getNowDateTime();

                        $objUtilizador->actualizar();
                        $request->getRouter()->redirect('/profile?status=updated');  
                    }
                }
            }
        } 


        private static function getStatus($request){
            $queryParams = $request->getQueryParams();
            
            if(!isset($queryParams['status'])) return '';

            switch($queryParams['status']){
                case 'error_old_password':
                    return Alert::getError('Senha actual incorreta.');
                    break;
                case 'updated':
                    return Alert::getSuccess('A sua senha actualizada com sucesso.');
                    break;
                case 'error_new_password':
                    return Alert::getError('A nova senha e a sua repetição não correspondem.');
                    break;
            }
        } 
        
        public static function getProfilePage($request){
            if(Funcoes::Permition(10)){
                $objUtilizador = UtilizadorEntity::getUtilizadorById($_SESSION['admin']['utilizador']['id']);
                $objDepartamento = DepartamentoEntity::getEDepartamentoById($objUtilizador->codigo_departamento);
                $content = ViewManager::render('dashboard/profile',[
                    'navbar'        => parent::getNavbar(),
                    'sidebar'       => parent::getMenu(),
                    'rightsidebar'  => parent::getRightSidebar(),
                    'footer'        => parent::getFooter(),
                    'status'        => self::getStatus($request),
                    'utilizador'    => $objUtilizador->nome_utilizador,
                    'user_name'     => $objUtilizador->nome_utilizador,
                    'celular'       => $objUtilizador->numero_de_celular,
                    'celular_alt'   => $objUtilizador->celular_alternativo,
                    'departamento'  => $objDepartamento->nome_departamento,
                    'subunidade'    => $objUtilizador->subunidade,

                ]);

                return parent::getPage('SIGECM | Utilizadores', $content);
            }else{
                return ErrorController::getError($request);
            }
        }

    }
?>