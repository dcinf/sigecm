<?php
    namespace App\Controller\Dashboard;
    use App\Controller\GlobalPageController;
    use App\Model\Entity\ArrecadacaoMunicaoEntity;
    use App\Model\Entity\ArrecadacaoRegisterEntity;
    use App\Utils\ViewManager;
    use App\Model\Entity\LoginEntity\UtilizadorPermissoes as EntityUtilizador;
    use App\Model\Entity\EnterManagementEntity;
    use App\Model\Entity\GroupEntity;
    use App\Model\Entity\UtilizadorEntity;
    use App\Model\Entity\WeaponEntity;
    use App\Utils\Funcoes;
    use App\Controller\DashboardDalog\DalogDashboardController;

    class DashboardController extends GlobalPageController
    {

        #========================================================
        # Dados para apresentacao no painel de visitantes
        #========================================================
        private static function gerarPermissoes($posicaoInicial, $posicaoFinal, $tamanhoTotal = 100) {
            $permissoes = str_repeat('0', $tamanhoTotal);
            $posicaoFinal = min($posicaoFinal, $tamanhoTotal - 1);
            $permissoes = substr_replace($permissoes, str_repeat('1', $posicaoFinal - $posicaoInicial + 1), $posicaoInicial, $posicaoFinal - $posicaoInicial + 1);
            
            return $permissoes;
        }  


        //funcao que busca os tipos das armas
        private static function getWeaponType(){
            $itens = '';
            $where = "data_levantamento < NOW() - INTERVAL 1 DAY AND data_devolucao is null";
            $results = ArrecadacaoRegisterEntity::getArrecadacao($where, 'codigo_arrecadacao DESC', null);

            $mostrar_modal = false;
            $script_modal = '';

            // Verifica se há resultados
            if ($results != null && $results->rowCount() > 0) {
                $mostrar_modal = true; // Definir como true se houver itens expirados
            }
            
            while ($objArrecadacao = $results->fetchObject(ArrecadacaoRegisterEntity::class)){ 
                $whereMunicao = "codigo_arrecadacao = '{$objArrecadacao->codigo_arrecadacao}'";
                $resultsMunicao = ArrecadacaoMunicaoEntity::getMunicaoArrecadacao($whereMunicao, 'codigo_arrecadacao DESC', null);
                $objArrecadacaoMunicoes = $resultsMunicao->fetchObject(ArrecadacaoMunicaoEntity::class);

                //Montando os itens a serem retornados
                $itens .= ViewManager::render('dashboard/expiredWithdrawsItem', [
                    'codigo'                => $objArrecadacao->codigo_arrecadacao,
                    'nome'                  => $objArrecadacao->nome_funcionario,
                    'tipo'                  => $objArrecadacao->tipo_armamento,
                    'numero'                => $objArrecadacao->numero_de_serie_arma,
                    'municoes'              => $objArrecadacaoMunicoes ? $objArrecadacaoMunicoes->quantidade_levantar : '0',
                    'patente'               => $objArrecadacao->patente_funcionario,
                    'subunidade'            => $objArrecadacao->subunidade,
                    'data_devolucao'        => $objArrecadacao->data_devolucao,
                    'contacto'              => $objArrecadacao->celular_funcionario,
                    'transacao'             => 'Levantamento',
                    'cor'                   => 'bg-danger-lighten text-danger',
                    'assinatura'            => $objArrecadacao->assinatura_arrecadacao
                ]);
                
            }
            
            return $itens;
        }



        private static function getUtilizadorItens($request){
            $contador = 0;
            $supervisor = [[9, 15]];
            $user_comum = [[10, 15]];

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
                $contador++;
            }
            return $contador;
        }
        #========================================================
        # Fim Dados para apresentacao no painel de visitantes
        #========================================================


        #========================================================
        #  Dados para apresentacao no painel de Armamentos
        #========================================================
        private static function getWithdrawItemReturnReport(){
            $itens = '';
            $results = ArrecadacaoRegisterEntity::getArrecadacao(null, 'codigo_arrecadacao DESC', null);
            While ($objArrecadacao = $results->fetchObject(ArrecadacaoRegisterEntity::class)){ 
                // Busca a quantidade de munições da retirada
                $whereMunicao = "codigo_arrecadacao = '{$objArrecadacao->codigo_arrecadacao}'";
                $resultsMunicao = ArrecadacaoMunicaoEntity::getMunicaoArrecadacao($whereMunicao, 'codigo_arrecadacao DESC', null);
                $objArrecadacaoMunicoes = $resultsMunicao->fetchObject(ArrecadacaoMunicaoEntity::class);

                if($objArrecadacao->data_devolucao == NULL){
                    $itens .= ViewManager::render('dashboard/painelArmamentoItem', [
                        'codigo'                => $objArrecadacao->codigo_arrecadacao,
                        'nome'                  => $objArrecadacao->nome_funcionario,
                        'tipo'                  => $objArrecadacao->tipo_armamento,
                        'numero'                => $objArrecadacao->numero_de_serie_arma,
                        'municoes'              => $objArrecadacaoMunicoes ? $objArrecadacaoMunicoes->quantidade_levantar : '0',
                        'patente'               => $objArrecadacao->patente_funcionario,
                        'subunidade'            => $objArrecadacao->subunidade,
                        'data_devolucao'        => $objArrecadacao->data_devolucao,
                        'transacao'             => 'Levantamento',
                        'cor'                   => 'bg-danger-lighten text-danger',
                        'assinatura'            => $objArrecadacao->assinatura_arrecadacao
                    ]);
                }else{
                    $itens .= ViewManager::render('dashboard/painelArmamentoItem', [
                        'codigo'                => $objArrecadacao->codigo_arrecadacao,
                        'nome'                  => $objArrecadacao->nome_funcionario,
                        'tipo'                  => $objArrecadacao->tipo_armamento,
                        'numero'                => $objArrecadacao->numero_de_serie_arma,
                        'municoes'              => $objArrecadacaoMunicoes ? $objArrecadacaoMunicoes->quantidade_levantar : '0',
                        'patente'               => $objArrecadacao->patente_funcionario,
                        'subunidade'            => $objArrecadacao->subunidade,
                        'data_devolucao'        => $objArrecadacao->data_devolucao,
                        'transacao'             => 'Devolução',
                        'cor'                   => 'bg-success-lighten text-success',
                        'assinatura'            => $objArrecadacao->assinatura_devolucao
                    ]); 
                }
                
            }
            return $itens; 
        }



        //funcao que busca a senha do utilizador
        private static function getNewPassword(){
            $senha_padrao = md5('123456789');
            $codigo_utilizador = (int) $_SESSION['admin']['utilizador']['id'];
            $where = "codigo_utilizador = '{$codigo_utilizador}' AND palavra_passe = '{$senha_padrao}'";
            $results = UtilizadorEntity::getUtilizadores($where, 'codigo_utilizador DESC', null);

            $mostrar_modal = false;
            $senha_modal = '';

            if($results != null && $results->rowCount() > 0){
                $mostrar_modal = true;
            }

             // Gera o script apenas se for necessário
             if ($mostrar_modal) {
                $senha_modal = <<<JS
                    <script>
                        document.addEventListener("DOMContentLoaded", function () {
                            var myModal = new bootstrap.Modal(document.getElementById('login-modal'), {
                                backdrop: 'static',
                                keyboard: false
                            });
                            myModal.show();
                        });
                    </script>
                JS;
            }

            return [
                'mostrar_modal' => $mostrar_modal,
                'senha_modal'   => $senha_modal
            ];

        }


        private static function getExpiredWithdrawReport(){
            $itens = '';
            $where = "data_levantamento < NOW() - INTERVAL 7 DAY AND data_devolucao is null";
            $results = ArrecadacaoRegisterEntity::getArrecadacao($where, 'codigo_arrecadacao DESC', null);

            $mostrar_modal = false;
            $script_modal = '';

            // Verifica se há resultados
            if ($results != null && $results->rowCount() > 0) {
                $mostrar_modal = true; // Definir como true se houver itens expirados
            }
            
            While ($objArrecadacao = $results->fetchObject(ArrecadacaoRegisterEntity::class)){ 
                $whereMunicao = "codigo_arrecadacao = '{$objArrecadacao->codigo_arrecadacao}'";
                $resultsMunicao = ArrecadacaoMunicaoEntity::getMunicaoArrecadacao($whereMunicao, 'codigo_arrecadacao DESC', null);
                $objArrecadacaoMunicoes = $resultsMunicao->fetchObject(ArrecadacaoMunicaoEntity::class);

                //Montando os itens a serem retornados
                $itens .= ViewManager::render('dashboard/expiredWithdrawsItem', [
                    'codigo'                => $objArrecadacao->codigo_arrecadacao,
                    'nome'                  => $objArrecadacao->nome_funcionario,
                    'tipo'                  => $objArrecadacao->tipo_armamento,
                    'numero'                => $objArrecadacao->numero_de_serie_arma,
                    'municoes'              => $objArrecadacaoMunicoes ? $objArrecadacaoMunicoes->quantidade_levantar : '0',
                    'patente'               => $objArrecadacao->patente_funcionario,
                    'subunidade'            => $objArrecadacao->subunidade,
                    'data_devolucao'        => $objArrecadacao->data_devolucao,
                    'contacto'              => $objArrecadacao->celular_funcionario,
                    'transacao'             => 'Levantamento',
                    'cor'                   => 'bg-danger-lighten text-danger',
                    'assinatura'            => $objArrecadacao->assinatura_arrecadacao
                ]);
                
            }
            
            // Gera o script apenas se for necessário
            if ($mostrar_modal) {
                $script_modal = <<<JS
                    <script>
                        document.addEventListener("DOMContentLoaded", function () {
                            var myModal = new bootstrap.Modal(document.getElementById('scrollable-modal'), {
                                backdrop: 'static',
                                keyboard: false
                            });
                            myModal.show();
                        });
                    </script>
                JS;
            }

            return [
                'itens' => $itens,
                'mostrar_modal' => $mostrar_modal,
                'script_modal' => $script_modal
            ];
        }
        #========================================================
        # Fim Dados para apresentacao no painel de Armamento
        #========================================================



        #========================================================
        # Alteracao da palavra passe do utilizador
        #========================================================
        public static function getLoginPage($request, $errorMessage = null){
            $status = !is_null($errorMessage) ? Alert::getError($errorMessage) : '';

            $content = ViewManager::render('login/index', [
                'status'  => $status
            ]);

            return parent::getPage('SIGECM | Acessar', $content);
        }

        public static function setChangePassword($request){
            $postVars = $request->getPostVars();

            if($postVars['text_password']  != $postVars['text_password_repeat']){
                $request->getRouter()->redirect('/painel?status=error');
            }else{
                $codigo_utilizador = $postVars['text_codigo_utilizador'];
                $objUtilizador = UtilizadorEntity::getUtilizadorById($codigo_utilizador);

                if(!$objUtilizador){
                    $request->getRouter()->redirect('/painel?status=user_not_found');
                }else{
                    $objUtilizador->palavra_passe       = md5($postVars['text_password']);
                    $objUtilizador->atualizado_em       = parent::getNowDateTime();

                    $objUtilizador->actualizar();
                    $request->getRouter()->redirect('/painel?status=updated');  
                }
            }
        }


        private static function getStatus($request){
            $queryParams = $request->getQueryParams();
            if(!isset($queryParams['status'])) return '';
            switch($queryParams['status']){
                case 'user_not_found':
                    return Alert::getError('Utilizador não encontrado.');
                    break;
                case 'updated':
                    return Alert::getSuccess('A sua senha foi actualizada com sucesso.');
                    break;
                case 'error':
                    return Alert::getError('A nova senha e a sua repetição não correspondem.');
                    break;
            }
        }

        #========================================================
        # Fim alteracao da palavra passe do utilizador
        #========================================================
        
        //Verificacacao das permissoes
        public static function getDashboard($request)
        {
            if (Funcoes::Permition(0)) {

                #==================================================
                # Pagina principal de dashboard
                #==================================================

                //$quantidadeTotal = EntityUtilizador::getUtilizadores(null, null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

                $content = ViewManager::render('dashboard/painel', [
                    'navbar'        => parent::getNavbar(),
                    'sidebar'       => parent::getMenu(),
                    'rightsidebar'  => parent::getRightSidebar(),
                    'footer'        => parent::getFooter(),
                    //'users'         => $quantidadeTotal,
                    //'designation'   => 'Utilizadores Activos' 
                ]);

                return parent::getPage('SIGECM | Painel Inicial', $content);

            } elseif (Funcoes::Permition(5)) {

                #===========================================
                # informacao das entradas
                #===========================================
                $data_hoje = date('Y-m-d');
                $where = "data_saida IS NULL AND DATE(data_entrada) = '$data_hoje'";
                $quantidadeTotalEntradas = EnterManagementEntity::getMovimentacoes($where, 'codigo_movimentacoes DESC', null, 'COUNT(*) as qtd')->fetchObject()->qtd;

                #===========================================
                # informacao das Saidas
                #===========================================
                $where = "data_saida IS NOT NULL AND DATE(data_saida) = '$data_hoje'";
                $quantidadeTotalsaidas = EnterManagementEntity::getMovimentacoes($where, 'codigo_movimentacoes DESC', null, 'COUNT(*) as qtd')->fetchObject()->qtd;

                #===========================================
                # informacao das entras e saidas
                #===========================================
                $quantidadeTotal = EnterManagementEntity::getMovimentacoes(null, 'codigo_movimentacoes', null, 'COUNT(*) as qtd')->fetchObject()->qtd;
                $content = ViewManager::render('dashboard/painelVisitas', [
                    'navbar'        => parent::getNavbar(),
                    'sidebar'       => parent::getMenu(),
                    'rightsidebar'  => parent::getRightSidebar(),
                    'footer'        => parent::getFooter(),
                    'utilizadores'  => self::getUtilizadorItens($request),
                    'entradas'      => $quantidadeTotalEntradas,
                    'saidas'        => $quantidadeTotalsaidas,
                    'movimentacoes' => $quantidadeTotal,
                    //'designation'   => 'Utilizadores Activos'
                ]);
                return parent::getPage('SIGECM | Painel Incial', $content);
            }elseif(Funcoes::Permition(9)){
                #===========================================
                # Total de armas no arsenal
                #===========================================
                $quantidadeTotalArmas = WeaponEntity::getWeapons(null, 'codigo_armamento DESC', null, 'COUNT(*) as qtd')->fetchObject()->qtd;

                //disponiveis
                $whereArmas = "disponibilidade = ". 1;
                $quantidadeArmasDisponivies = WeaponEntity::getWeapons($whereArmas, 'codigo_armamento DESC', null, 'COUNT(*) as qtd')->fetchObject()->qtd;

                #===========================================
                # informacoes das retiradas
                #===========================================
                $data_hoje = date('Y-m-d');
                $where = "data_devolucao IS NULL AND DATE(data_levantamento) = '$data_hoje'";
                $quantidadeTotalLevantamentos = ArrecadacaoRegisterEntity::getArrecadacao($where, 'codigo_arrecadacao DESC', null, 'COUNT(*) as qtd')->fetchObject()->qtd;

                #===========================================
                # informacao das devolucoes
                #===========================================
                $where = "data_devolucao IS NOT NULL AND DATE(data_devolucao) = '$data_hoje'";
                $quantidadeTotalDevolucoes = ArrecadacaoRegisterEntity::getArrecadacao($where, 'codigo_arrecadacao DESC', null, 'COUNT(*) as qtd')->fetchObject()->qtd;

                #===========================================
                # Arsenal em circulacao 
                #===========================================
                $where = "data_devolucao IS NULL";
                $quantidadeTotalCirculacao = ArrecadacaoRegisterEntity::getArrecadacao($where, 'codigo_arrecadacao DESC', null, 'COUNT(*) as qtd')->fetchObject()->qtd;

                //busca a lista dos funcionarios com armas em atraso
                $expiredData = self::getExpiredWithdrawReport();
                $senha_modal = self::getNewPassword();
                $content = ViewManager::render('dashboard/modules/arsenalmanagement/reparticao/painelreparticao',[
                    'navbar'            => parent::getNavbar(),
                    'sidebar'           => parent::getMenu(),
                    'rightsidebar'      => parent::getRightSidebar(),
                    'footer'            => parent::getFooter(),
                    'utilizadores'      => self::getUtilizadorItens($request),
                    'resumo'            => self::getWithdrawItemReturnReport(),
                    'expiredWithdraws'  => $expiredData['itens'],
                    'scriptModal'       => $expiredData['script_modal'],
                    'modalSenha'        => $senha_modal['senha_modal'],
                    'retiradas'         => $quantidadeTotalLevantamentos,
                    'saidas'            => $quantidadeTotalDevolucoes,
                    'report'            => $quantidadeTotalCirculacao,
                    'totalArmas'        => $quantidadeTotalArmas,
                    'TotalDisponivel'   => $quantidadeArmasDisponivies,
                ]);
    
                return parent::getPage('SIGECM | Painel Incial', $content);

            }elseif(Funcoes::Permition(10)){
                #===========================================
                # informacoes das retiradas
                #===========================================
                $data_hoje = date('Y-m-d');
                $where = "data_devolucao IS NULL AND DATE(data_levantamento) = '$data_hoje'";
                $quantidadeTotalLevantamentos = ArrecadacaoRegisterEntity::getArrecadacao($where, 'codigo_arrecadacao DESC', null, 'COUNT(*) as qtd')->fetchObject()->qtd;

                #===========================================
                # informacao das devolucoes
                #===========================================
                $where = "data_devolucao IS NOT NULL AND DATE(data_devolucao) = '$data_hoje'";
                $quantidadeTotalDevolucoes = ArrecadacaoRegisterEntity::getArrecadacao($where, 'codigo_arrecadacao DESC', null, 'COUNT(*) as qtd')->fetchObject()->qtd;

                #===========================================
                # Arsenal em circulacao 
                #===========================================
                $where = "data_devolucao IS NULL";
                $quantidadeTotalCirculacao = ArrecadacaoRegisterEntity::getArrecadacao($where, 'codigo_arrecadacao DESC', null, 'COUNT(*) as qtd')->fetchObject()->qtd;

                #total de armas
                $quantidadeTotalArmas = WeaponEntity::getWeapons(null, 'codigo_armamento DESC', null, 'COUNT(*) as qtd')->fetchObject()->qtd;

                $senha_modal = self::getNewPassword();

                if (!empty($senha_modal['senha_modal'])) {
                    $expiredItens = '';
                    $expiredScript = '';
                } else {
                    $expiredData = self::getExpiredWithdrawReport();
                    $expiredItens = $expiredData['itens']; 
                    $expiredScript = $expiredData['script_modal'];
                }

                $content = ViewManager::render('dashboard/painelArmamento',[
                    'navbar'            => parent::getNavbar(),
                    'sidebar'           => parent::getMenu(),
                    'rightsidebar'      => parent::getRightSidebar(),
                    'footer'            => parent::getFooter(),
                    'armas'             => $quantidadeTotalArmas,
                    'resumo'            => self::getWithdrawItemReturnReport(),
                    'expiredWithdraws'  => $expiredItens,
                    'scriptModal'       => $expiredScript,
                    'modalSenha'        => $senha_modal['senha_modal'],
                    'codigo_utilizador' => $_SESSION['admin']['utilizador']['id'],
                    'name_utilizador'   => $_SESSION['admin']['utilizador']['nome_utilizador'],
                    'retiradas'         => $quantidadeTotalLevantamentos,
                    'saidas'            => $quantidadeTotalDevolucoes,
                    'report'            => $quantidadeTotalCirculacao,
                    'status'            => self::getStatus($request),
                ]);
    
                return parent::getPage('SIGECM | Painel Incial', $content);
            }elseif(Funcoes::Permition(16)){
                return DalogDashboardController::getDalogDashboard($request);
            }
            else {
                echo ErrorController::getError($request);
            }
        }
    }

?>