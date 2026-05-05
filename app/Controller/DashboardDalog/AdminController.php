<?php
    namespace App\Controller\DashboardDalog;
    use App\Controller\DalogPageController;
    use App\Controller\Dashboard\Alert;
    use App\Controller\Dashboard\ErrorController;
use App\Model\Entity\DepartamentoEntity;
use App\Model\Entity\Fardamentos\FuncionarioFardamentosEntity;
use App\Model\Entity\GroupEntity;
use App\Model\Entity\UtilizadorEntity;
use App\Utils\Funcoes;
    use App\Utils\ViewManager;

     class AdminController extends DalogPageController{
        #=====================================================
        # Utilizadores do menu de fardamentos
        #=====================================================
        public static function getNewRegisterReparticao($request){
            if(Funcoes::Permition(16)){
                //Busca as infomacoes do grupo do utilizador
                $ranges = [
                    [18, 20]
                ];

                $permissoesStr = parent::geradorPermissoes($ranges, 100);

                $where = "permissoes = '{$permissoesStr}'";
                $results = GroupEntity::getGrupos($where, 'codigo_grupo DESC', 1);
                $objGrupos = $results->fetchObject(GroupEntity::class);

                $content = ViewManager::render('dashboardDalog/modules/admin/newUtilizador',[
                    'navbar'        => parent::getNavbar(),
                    'sidebar'       => parent::getMenu(),
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
                $objUtilizador->codigo_departamento     = $postVars['text_codigo_departamento'];
                $objUtilizador->utilizador              = $utilizador_final;
                $objUtilizador->palavra_passe           = md5($postVars['text_senha_acesso']);
                $objUtilizador->grupos                  = (int) $postVars['text_grupo'];
                $objUtilizador->criado_em               = parent::getNowDateTime();
                $objUtilizador->atualizado_em           = parent::getNowDateTime();

                $objUtilizador->cadastrar(); 
                $request->getRouter()->redirect('/dalog-users?status=created');
            }else{
                $request->getRouter()->redirect('/dalog-users?status=error_update');
            }
        }

        private static function getFardamentosUsersItem(){
            $itens = '';
            $supervisor = [[16, 22]];
            $user_comum = [[16, 21]];

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
                $itens .= ViewManager::render('dashboardDalog/modules/admin/item', [
                    'codigo'         => $objUtilizador->codigo_utilizador,
                    'patente'        => $objUtilizador->patente,
                    'nome'           => $objUtilizador->nome_utilizador,
                    'subunidade'     => $objUtilizador->subunidade,
                    'celular'        => $objUtilizador->numero_de_celular,
                    'utilizador'     => $objUtilizador->utilizador,
                    'estado'        => 'Activo'
                ]);
            }
            return $itens; 
        }

        public static function getRegisterReparticaoPage($request){
            if(Funcoes::Permition(16)){
                //Busca o grupo dos utilizadores comunus 
                $content = ViewManager::render('dashboardDalog/modules/admin/utilizadores',[
                    'navbar'        => parent::getNavbar(),
                    'sidebar'       => parent::getMenu(),
                    'footer'        => parent::getFooter(),
                    'itens'         => self::getFardamentosUsersItem(),
                    'status'        => self::getStatus($request)
                ]);

                return parent::getPage('SIGECM | Utilizadores', $content);
            }else{
                return ErrorController::getError($request);
            }
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



    }

