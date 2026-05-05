<?php

namespace App\Controller\Dashboard;

use App\Utils\ViewManager;
use App\Controller\GlobalPageController;
use App\Model\Entity\ActivosRequestEntity;
use App\Model\Entity\AmmuniationRequestEntity;
use App\Model\Entity\AmmunitionEntity;
use App\Model\Entity\ArmasRequestEntity;
use App\Model\Entity\ArrecadacaoEntity;
use App\Model\Entity\ArrecadacaoRegisterEntity;
use App\Model\Entity\DepartamentoEntity;
use App\Model\Entity\DisponibilidadeArmamentoEntity;
use App\Model\Entity\EquipmentEntity;
use App\Model\Entity\RequestEntity;
use App\Model\Entity\StatusArmamentoEntity;
use App\Model\Entity\WeaponEntity;
use App\Model\Entity\WeaponTypeEntity;
use DateTime;
use Dompdf\Dompdf;
use Dompdf\Options;

class PdfGenaratorController extends GlobalPageController
{
    private static function changeDateType($data)
    {
        // Divida a string nas duas datas
        $dates = explode(' - ', $data);

        // Converta a primeira data
        $start_date = DateTime::createFromFormat('F j, Y', $dates[0])->format('Y-m-d');

        // Converta a segunda data
        $end_date = DateTime::createFromFormat('F j, Y', $dates[1])->format('Y-m-d');

        return "'" . $start_date . "' AND '" . $end_date . "'";
    }



    //==================================================
    # tabela de armamentos
    #===================================================
    private static function getWeaponsItem($where)
    {
        $itens = '';
        $results = WeaponEntity::getWeapons($where, 'codigo_armamento', null);

        $contador = 1;
        while ($objArmamento = $results->fetchObject(WeaponEntity::class)) {
            $objTipoArmamento = WeaponTypeEntity::getweaponTypeById($objArmamento->codigo_tipo_armamento);
            $objDisponibidade = DisponibilidadeArmamentoEntity::getDisponibilidadeById($objArmamento->disponibilidade);
            $objsituacao      = StatusArmamentoEntity::getStatusById($objArmamento->status_operacional);
            //Montando os itens a serem retornados
            $itens .= ViewManager::render('pdfreports/weaponsItem', [
                'contador'              => $contador,
                'numero_serie'          => $objArmamento->numero_serie,
                'tipo'                  => $objTipoArmamento->tipo_armamento,
                'modelo'                => $objArmamento->modelo,
                'calibre'               => $objArmamento->calibre,
                'local_guarda'          => $objArmamento->local_armazenamento,
                'situacao'              => $objDisponibidade->disponibilidade,
                'estado'                => $objsituacao->status,
                'fiador'                => $objArmamento->responsavel_atual
            ]);
            $contador++;
        }

        // Caso nenhum dado tenha sido encontrado
        if (trim($itens) === '') {
            $itens = '
                    <tr>
                        <td colspan="9" style="text-align: center; font-weight: bold; padding: 10px;">
                            Nenhum dado encontrado.
                        </td>
                    </tr>
                ';
            $contador = 0;
        }

        return [
            'html'      => $itens,
            'contador' => $contador > 0 ? $contador - 1 : 0 // subtrai 1 pois o último incremento é após o último item
        ];
    }
    //==================================================
    # tabela de armamentos
    #===================================================

    #===================================================
    # tabela de municoes
    #===================================================
    private static function getAmmuniationItem($where)
    {
        $itens = '';
        $results = AmmunitionEntity::getAmmunition($where, 'codigo_municao', null);

        $contador = 1;
        while ($objMunicoes = $results->fetchObject(AmmunitionEntity::class)) {
            $objArmamento = WeaponEntity::getweaponById($objMunicoes->arma_compativel);
            //Montando os itens a serem retornados
            $itens .= ViewManager::render('pdfreports/ammuniationItem', [
                'contador'              => $contador,
                'calibre'               => $objMunicoes->calibre,
                'tipo'                  => $objMunicoes->tipo,
                'arma_compativel'       => $objArmamento->nome_armamento,
                'data'                  => $objMunicoes->data_fabricacao,
                'quantidade'            => $objMunicoes->quantidade_estoque,
                'armazem'               => "Comando da Casa Militar"
            ]);
            $contador++;
        }

        // Caso nenhum dado tenha sido encontrado
        if (trim($itens) === '') {
            $itens = '
                    <tr>
                        <td colspan="7" style="text-align: center; font-weight: bold; padding: 10px;">
                            Nenhum dado encontrado.
                        </td>
                    </tr>
                ';
            $contador = 0;
        }

        return [
            'html'      => $itens,
            'contador' => $contador > 0 ? $contador - 1 : 0 // subtrai 1 pois o último incremento é após o último item
        ];
    }
    #===================================================
    # Fim tabela de municoes
    #===================================================

    #===================================================
    # Tabela de Equipamentos
    #===================================================
     private static function getEquipmentsItem($where)
    {
        $itens = '';
        $results = EquipmentEntity::getEquipments($where, 'codigo_equipamento', null);

        $contador = 1;
        while ($objEquipamentos = $results->fetchObject(EquipmentEntity::class)) {
            //Montando os itens a serem retornados
            $itens .= ViewManager::render('pdfreports/equipmentItem', [
                'contador'              => $contador,
                'nome_equipamento'      => $objEquipamentos->nome,
                'tipo'                  => $objEquipamentos->tipo,
                'cor'                   => $objEquipamentos->cor,
                'finalidade'            => $objEquipamentos->finalidade,
                'data'                  => $objEquipamentos->data_fabricacao,
                'quantidade'            => $objEquipamentos->quantidade,
                'armazem'               => "Comando da Casa Militar"
            ]);
            $contador++;
        }

        // Caso nenhum dado tenha sido encontrado
        if (trim($itens) === '') {
            $itens = '
                    <tr>
                        <td colspan="8" style="text-align: center; font-weight: bold; padding: 10px;">
                            Nenhum dado encontrado.
                        </td>
                    </tr>
                ';
            $contador = 0;
        }

        return [
            'html'      => $itens,
            'contador' => $contador > 0 ? $contador - 1 : 0 // subtrai 1 pois o último incremento é após o último item
        ];
    }

    #===================================================
    # Fim tabela de Equipamentos
    #===================================================


    private static function getWithdrawItemReturn($where)
    {
        $itens = '';
        $results = ArrecadacaoRegisterEntity::getArrecadacao($where, 'codigo_arrecadacao ASC', null);
        $contador = 1;
        while ($objArrecacao = $results->fetchObject(ArrecadacaoRegisterEntity::class)) {
            //Montando os itens a serem retornados
            $itens .= ViewManager::render('pdfreports/withdrawItem', [
                'contador'              => $contador,
                'codigo'                => $objArrecacao->codigo_arrecadacao,
                'nome_completo'         => $objArrecacao->nome_funcionario,
                'imagem'                => $objArrecacao->fotografia,
                'tipo_armamento'        => $objArrecacao->tipo_armamento,
                'numero'                => $objArrecacao->numero_de_serie_arma,
                'municoes'              => $objArrecacao->quantidade_municao,
                'patente'               => $objArrecacao->patente_funcionario,
                'assinatura'            => $objArrecacao->assinatura_arrecadacao,
                'telefone'              => $objArrecacao->celular_funcionario,
                'data_retirada'         => $objArrecacao->data_levantamento,
            ]);
            $contador++;
        }

        return $itens;
    }


    private static function getReturnItemReturn($where)
    {
        $itens = '';
        $results = ArrecadacaoRegisterEntity::getArrecadacao($where, 'codigo_arrecadacao ASC', null);
        $contador = 1;
        while ($objArrecacao = $results->fetchObject(ArrecadacaoRegisterEntity::class)) {
            //Montando os itens a serem retornados
            $itens .= ViewManager::render('pdfreports/returnItem', [
                'contador'              => $contador,
                'codigo'                => $objArrecacao->codigo_arrecadacao,
                'nome_completo'         => $objArrecacao->nome_funcionario,
                'tipo_armamento'        => $objArrecacao->tipo_armamento,
                'numero'                => $objArrecacao->numero_de_serie_arma,
                'municoes'              => $objArrecacao->quantidade_municao,
                'patente'               => $objArrecacao->patente_funcionario,
                'data_retirada'         => $objArrecacao->data_levantamento,
                'data_devolucao'        => $objArrecacao->data_devolucao,
                'assinaturaFiel'        => $objArrecacao->assinatura_devolucao,
                'assinaturaReceptor'    => $objArrecacao->assinatura_fiel
            ]);
            $contador++;
        }

        return $itens;
    }


    private static function getArmaRequestsItem($id)
    {
        $itens = '';
        $numerosArmas = [];
        $designacao = '';
        $results = ArmasRequestEntity::getArmasRequest("codigo_requisicao = " . (int)$id, 'codigo_armamento_requisitado DESC', null);

        while ($objArmas = $results->fetchObject(ArmasRequestEntity::class)) {
            // Supondo que o campo do número da arma seja 'numero_arma'
            $numerosArmas[] = $objArmas->numero_de_serie_arma;
            // Pega a designação só uma vez (na primeira iteração)
            if (empty($designacao)) {
                $designacao = $objArmas->designacao;
            }
        }

        $quantidade = count($numerosArmas);

        if ($quantidade > 0) {
            // Monta a frase final
            $itens = ViewManager::render('pdfreports/requestsItem', [
                'quantidade'  => $quantidade,
                'designacao'  => $designacao . implode(', ', $numerosArmas)
            ]);
        } else {
            $itens = ''; // Nenhuma arma encontrada
        }



        #Dados Referentes as Municoes
        $results = AmmuniationRequestEntity::getMunicaoRequest("codigo_requisicao = " . (int)$id, 'codigo_municao_requisitado DESC', null);
        if ($results && $results->rowCount() > 0) {
            while ($objMunicoesRequests = $results->fetchObject(AmmuniationRequestEntity::class)) {
                //Montando os itens a serem retornados
                $itens .= ViewManager::render('pdfreports/requestsItem', [
                    'quantidade'                                    => $objMunicoesRequests->quantidade_municao,
                    'designacao'                                    => $objMunicoesRequests->designacao,
                ]);
            }
        }


        #Dados referentes aos Outros Activos
        $results = ActivosRequestEntity::getActivosRequest("codigo_requisicao = " . (int)$id, 'codigo_activo_requisitado DESC', null);
        if ($results && $results->rowCount() > 0) {
            while ($objActivosRequests = $results->fetchObject(ActivosRequestEntity::class)) {
                //Montando os itens a serem retornados
                $itens .= ViewManager::render('pdfreports/requestsItem', [
                    'quantidade'                                    => $objActivosRequests->quantidade_activo,
                    'designacao'                                    => $objActivosRequests->designacao,
                ]);
            }
        }
        return $itens;
    }


    #============================================================================
    # Funcoes relacionadas a geracao do PDF
    #============================================================================
    public static function withdrawPDFGenarator($request)
    {
        try {

            #=============================================================
            # Verificações das datas
            #=============================================================
            $postVars = $request->getPostVars();

            $specificDate   = $postVars['selectedDateRange'];
            $allDates       = $postVars['text_data_checkbox'] ?? '';

            $tableItems = '';

            if ($specificDate != '' && $allDates == '' || $specificDate != '' && $allDates != '') {
                $data_buscar = self::changeDateType($specificDate);

                // Pegando os itens para a tabela dinamicamente
                $tableItems = self::getWithdrawItemReturn("data_levantamento BETWEEN " . $data_buscar . " AND data_devolucao is NULL");

                // Remover as aspas
                $date_range = str_replace("'", "", $data_buscar);

                // Separar as datas
                $dates = explode(" AND ", $date_range);

                // Criar objetos DateTime e formatar as datas para o formato dd-mm-yyyy
                $start_date = DateTime::createFromFormat('Y-m-d', $dates[0])->format('d-m-Y');
                $end_date = DateTime::createFromFormat('Y-m-d', $dates[1])->format('d-m-Y');


                if ($start_date == $end_date) {
                    // Concatenar as datas novamente
                    $titulo = 'Relatório dos levantamentos do dia   ' . parent::getFormattedDataOnly($start_date);
                } else {
                    // Concatenar as datas novamente
                    $formatted_date_range = parent::getFormattedDataOnly($start_date) . ' até ' . parent::getFormattedDataOnly($end_date);
                    $titulo = 'Relatório dos levantamentos que compreende o periodo de  ' . $formatted_date_range;
                }
            }

            if ($allDates != '' && $specificDate == '') {
                // Pegando os itens para a tabela dinamicamente
                $tableItems = self::getWithdrawItemReturn("data_devolucao is NULL");
                $titulo = 'Relatório Geral de Leventamentos do Arsenal';
            }

            //Caso nao se especifique algum periodo para imprimir
            if ($specificDate == '' && $allDates == '')  $request->getRouter()->redirect('/report-withdraw?status=dataerror');

            #=============================================================
            # Verificações das datas
            #=============================================================

            // Inicializa o Dompdf
            $dompdf = new Dompdf();
            $options = new Options();
            $options->setIsRemoteEnabled(true); // Habilitar carregamento remoto de recursos

            // Configuração do caminho correto
            $chrootPath = realpath(__DIR__);
            if ($chrootPath === false) {
                throw new \Exception("Erro: o diretório de chroot não pôde ser encontrado.");
            }

            $options->setChroot($chrootPath);
            $dompdf->setOptions($options);

            // Corrigindo o caminho para o arquivo HTML
            $htmlPath = realpath($chrootPath . '/../../../resources/views/pdfreports/withdraw.html');
            if ($htmlPath === false) {
                throw new \Exception("Erro: o arquivo withdraw.html não pôde ser encontrado.");
            }

            // Carregar o conteúdo do arquivo HTML
            $html = file_get_contents($htmlPath);
            // Titulo
            $html = str_replace('{{titulo}}', $titulo, $html);

            // Data atual (dia, mês e ano)
            $dataAtual = parent::getNowDateTime();
            $html = str_replace('{{data_atual}}', $dataAtual, $html);

            $utilizador = $_SESSION['admin']['utilizador']['nome_utilizador'];
            $html = str_replace('{{utilizador}}', $utilizador, $html);

            #============================================================
            # Carregando informações e mandando para tabela
            #============================================================
            // Aqui vamos substituir {{tableItem}} com o conteúdo gerado pela função
            $html = str_replace('{{tableItem}}', $tableItems, $html);

            // Carregar HTML com o Dompdf
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'landscape'); // Configurar o formato da página
            $dompdf->render();

            // Enviar cabeçalhos para exibir o PDF
            $output = $dompdf->output();
            header('Content-Type: application/pdf');
            echo $output;
        } catch (\Exception $e) {
            echo "Erro ao gerar o PDF: " . $e->getMessage();
        }
    }


    public static function ReturnPDFGenarator($request)
    {
        try {

            #=============================================================
            # Verificações das datas
            #=============================================================
            $postVars = $request->getPostVars();

            $specificDate   = $postVars['selectedDateRange'];
            $allDates       = $postVars['text_data_checkbox'] ?? '';

            $tableItems = '';

            if ($specificDate != '' && $allDates == '' || $specificDate != '' && $allDates != '') {
                $data_buscar = self::changeDateType($specificDate);


                // Pegando os itens para a tabela dinamicamente
                $tableItems = self::getReturnItemReturn("data_devolucao BETWEEN " . $data_buscar . " AND data_devolucao is not NULL");

                // Remover as aspas
                $date_range = str_replace("'", "", $data_buscar);

                // Separar as datas
                $dates = explode(" AND ", $date_range);

                // Criar objetos DateTime e formatar as datas para o formato dd-mm-yyyy
                $start_date = DateTime::createFromFormat('Y-m-d', $dates[0])->format('d-m-Y');
                $end_date = DateTime::createFromFormat('Y-m-d', $dates[1])->format('d-m-Y');

                if ($start_date == $end_date) {
                    // Concatenar as datas novamente
                    $titulo = 'Relatório das devoluções do dia   ' . parent::getFormattedDataOnly($start_date);
                } else {
                    // Concatenar as datas novamente
                    $formatted_date_range = parent::getFormattedDataOnly($start_date) . ' até ' . parent::getFormattedDataOnly($end_date);
                    $titulo = 'Relatório das devoluções que compreende o periodo de  ' . $formatted_date_range;
                }
            }

            if ($allDates != '' && $specificDate == '') {
                // Pegando os itens para a tabela dinamicamente
                $tableItems = self::getReturnItemReturn("data_devolucao is not NULL");
                $titulo = 'Relatório Geral das devoluções';
            }

            //Caso nao se especifique algum periodo para imprimir
            if ($specificDate == '' && $allDates == '')  $request->getRouter()->redirect('/report-return?status=dataerror');

            #=============================================================
            # Verificações das datas
            #=============================================================

            // Inicializa o Dompdf
            $dompdf = new Dompdf();
            $options = new Options();
            $options->setIsRemoteEnabled(true); // Habilitar carregamento remoto de recursos

            // Configuração do caminho correto
            $chrootPath = realpath(__DIR__);
            if ($chrootPath === false) {
                throw new \Exception("Erro: o diretório de chroot não pôde ser encontrado.");
            }

            $options->setChroot($chrootPath);
            $dompdf->setOptions($options);

            // Corrigindo o caminho para o arquivo HTML
            $htmlPath = realpath($chrootPath . '/../../../resources/views/pdfreports/return.html');
            if ($htmlPath === false) {
                throw new \Exception("Erro: o arquivo withdraw.html não pôde ser encontrado.");
            }

            // Carregar o conteúdo do arquivo HTML
            $html = file_get_contents($htmlPath);
            // Titulo
            $html = str_replace('{{titulo}}', $titulo, $html);

            // Data atual (dia, mês e ano)
            $dataAtual = parent::getNowDateTime();
            $html = str_replace('{{data_atual}}', $dataAtual, $html);

            $utilizador = $_SESSION['admin']['utilizador']['nome_utilizador'];
            $html = str_replace('{{utilizador}}', $utilizador, $html);

            #============================================================
            # Carregando informações e mandando para tabela
            #============================================================
            // Aqui vamos substituir {{tableItem}} com o conteúdo gerado pela função
            $html = str_replace('{{tableItem}}', $tableItems, $html);

            // Carregar HTML com o Dompdf
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'landscape'); // Configurar o formato da página
            $dompdf->render();

            // Enviar cabeçalhos para exibir o PDF
            $output = $dompdf->output();
            header('Content-Type: application/pdf');
            echo $output;
        } catch (\Exception $e) {
            echo "Erro ao gerar o PDF: " . $e->getMessage();
        }
    }



    public static function RequestPDFGenarator($request, $id)
    {
        try {
            #=============================================================
            # Informacoes das Requisicoes
            #=============================================================
            $objRequests = RequestEntity::getRequisicaoById($id);

            #=============================================================
            # Verificações das datas
            #=============================================================
            $tableItems = self::getArmaRequestsItem($id);

            // Inicializa o Dompdf
            $dompdf = new Dompdf();
            $options = new Options();
            $options->setIsRemoteEnabled(true); // Habilitar carregamento remoto de recursos

            // Configuração do caminho correto
            $chrootPath = realpath(__DIR__);
            if ($chrootPath === false) {
                throw new \Exception("Erro: o diretório de chroot não pôde ser encontrado.");
            }

            $options->setChroot($chrootPath);
            $dompdf->setOptions($options);

            // Corrigindo o caminho para o arquivo HTML
            $htmlPath = realpath($chrootPath . '/../../../resources/views/pdfreports/requests.html');
            if ($htmlPath === false) {
                throw new \Exception("Erro: o arquivo withdraw.html não pôde ser encontrado.");
            }

            // Carregar o conteúdo do arquivo HTML
            $html = file_get_contents($htmlPath);

            // Armazem
            $html = str_replace('{{nome_armazem}}', $objRequests->armazem, $html);

            // Fornecido
            $html = str_replace('{{nome_fornecido}}', $objRequests->nome_requerente, $html);

            //Numero de requisicao 
            $html = str_replace('{{numero_requisicao}}', $objRequests->numero_requisicao, $html);

            //Data da requisicao
            $html = str_replace('{{data_request}}', parent::getFormattedDataOnly($objRequests->data_requisicao), $html);

            // Data atual (dia, mês e ano)
            $dataAtual = parent::getNowDateTime();
            $html = str_replace('{{data_atual}}', $dataAtual, $html);

            //Assinatura do receptor
            $assinaturaReceptor = $objRequests->assinatura_receptor_requisicao; // exemplo: 'assinatura.png'
            $assinaturaUrlReceptor = "images/Signatures/" . $assinaturaReceptor;
            $html = str_replace('{{assinatura_receptor}}', $assinaturaUrlReceptor, $html);

            //Assinatura do fornecedor
            $assinaturaFornecedor = $objRequests->assinatura_fornecedor_requisicao; // exemplo: 'assinatura.png'
            $assinaturaUrlFornecedor = "images/Signatures/" . $assinaturaFornecedor;
            $html = str_replace('{{assinatura_fornecedor}}', $assinaturaUrlFornecedor, $html);



            $html = str_replace('{{data}}', parent::getFormattedDataOnly($objRequests->criado_em), $html);


            $utilizador = $_SESSION['admin']['utilizador']['nome_utilizador'];
            $html = str_replace('{{utilizador}}', $utilizador, $html);

            #============================================================
            # Carregando informações e mandando para tabela
            #============================================================
            // Aqui vamos substituir {{tableItem}} com o conteúdo gerado pela função
            $html = str_replace('{{requestsItem}}', $tableItems, $html);

            // Carregar HTML com o Dompdf
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait'); // Configurar o formato da página
            $dompdf->render();

            // Enviar cabeçalhos para exibir o PDF
            $output = $dompdf->output();
            header('Content-Type: application/pdf');
            echo $output;
        } catch (\Exception $e) {
            echo "Erro ao gerar o PDF: " . $e->getMessage();
        }
    }


    #=================================================================
    # PDF de uma arma apenas
    #=================================================================
    public static function WeaponPDFGenarator($request)
    {
        try {

            $postVars = $request->getPostVars();

            /*echo '<pre>';
                print_r($postVars);
                echo '</pre>';
                exit;*/

            if (!empty($postVars['text_numero_serie'])) {
                $serie = $postVars['text_numero_serie'];
                #=============================================================
                # Informacoes das Requisicoes
                #=============================================================
                $objArmamento = WeaponEntity::getWeaponBySerieNumber($serie);
                $tipo_arma = WeaponTypeEntity::getweaponTypeById($objArmamento->codigo_tipo_armamento);
                $estado_arma = StatusArmamentoEntity::getStatusById($objArmamento->status_operacional);

                $results = ArrecadacaoRegisterEntity::getArrecadacao("numero_de_serie_arma = '{$serie}' AND data_devolucao IS NULL", 'codigo_arrecadacao DESC', null);
                $objArrecadacao = $results->fetchObject(ArrecadacaoRegisterEntity::class);

                if (!empty($objArrecadacao)) {
                    $objDepartamento = DepartamentoEntity::getEDepartamentoById($objArrecadacao->departamento);
                }

                #=============================================================
                # Verificações das datas
                #=============================================================
                //$tableItems = self::getArmaRequestsItem($id);

                // Inicializa o Dompdf
                $dompdf = new Dompdf();
                $options = new Options();
                $options->setIsRemoteEnabled(true); // Habilitar carregamento remoto de recursos

                // Configuração do caminho correto
                $chrootPath = realpath(__DIR__);
                if ($chrootPath === false) {
                    throw new \Exception("Erro: o diretório de chroot não pôde ser encontrado.");
                }

                $options->setChroot($chrootPath);
                $dompdf->setOptions($options);

                // Corrigindo o caminho para o arquivo HTML
                $htmlPath = realpath($chrootPath . '/../../../resources/views/pdfreports/weaponsReportSerie.html');
                if ($htmlPath === false) {
                    throw new \Exception("Erro: o arquivo withdraw.html não pôde ser encontrado.");
                }

                // Carregar o conteúdo do arquivo HTML
                $html = file_get_contents($htmlPath);

                #=====================================================
                # Preenche os dados da arma
                #=====================================================
                $placeholders = [
                    '{{serie}}'        => $objArmamento->numero_serie,
                    '{{calibre}}'      => $objArmamento->calibre,
                    '{{modelo}}'       => $objArmamento->modelo,
                    '{{marca}}'        => $objArmamento->marca,
                    '{{tipo}}'         => $tipo_arma->tipo_armamento,
                    '{{data_fabrico}}' => $objArmamento->data_aquisicao,
                    '{{estado}}'       => $estado_arma->status,
                    '{{armazem}}'      => $objArmamento->local_armazenamento,
                ];

                $html = str_replace(array_keys($placeholders), array_values($placeholders), $html);

                if (!empty($objArrecadacao)) {
                    $placeholders_user = [
                        '{{nome_completo}}'     => $objArrecadacao->nome_funcionario,
                        '{{subunidade}}'        => $objArrecadacao->subunidade,
                        '{{departamento}}'      => $objDepartamento->nome_departamento,
                        '{{patente}}'           => $objArrecadacao->patente_funcionario,
                        '{{contacto}}'          => $objArrecadacao->celular_funcionario,
                        '{{data_levantamento}}' => $objArrecadacao->data_levantamento,
                    ];
                } else {
                    $placeholders_user = [
                        '{{nome_completo}}'     => "Armazem da Casa Militar",
                        '{{subunidade}}'        => $objArmamento->local_armazenamento,
                        '{{departamento}}'      => "Departamento de Administração Logística",
                        '{{patente}}'           => "Arma no Estoque",
                        '{{contacto}}'          => "Casa Militar",
                        '{{data_levantamento}}' => "Arma no Estoque",
                    ];
                }

                $html = str_replace(array_keys($placeholders_user), array_values($placeholders_user), $html);

                $placeholders_gun = [
                    '{{inspencao}}'         => $objArmamento->data_ultima_inspecao,
                    '{{observacoes}}'       => $objArmamento->observacoes
                ];

                $html = str_replace(array_keys($placeholders_gun), array_values($placeholders_gun), $html);


                // Data atual (dia, mês e ano)
                $dataAtual = parent::getNowDateTime();
                $html = str_replace('{{data_atual}}', $dataAtual, $html);

                $utilizador = $_SESSION['admin']['utilizador']['nome_utilizador'];
                $html = str_replace('{{utilizador}}', $utilizador, $html);

                // Carregar HTML com o Dompdf
                $dompdf->loadHtml($html);
                $dompdf->setPaper('A4', 'portrait'); // Configurar o formato da página
                $dompdf->render();

                // Enviar cabeçalhos para exibir o PDF
                $output = $dompdf->output();
                header('Content-Type: application/pdf');
                echo $output;
            } else if (!empty($postVars['text_tipo_armamento']) || !empty($postVars['text_estado_uso']) || !empty($postVars['text_estado_operacional'])) {
                $tipo_armamento = $postVars['text_tipo_armamento'];

                $tableItems = '';

                // Mais específico: tipo + estado operacional + uso
                if (!empty($postVars['text_tipo_armamento']) && !empty($postVars['text_estado_operacional']) && !empty($postVars['text_estado_uso'])) {
                    $tipo_armamento = $postVars['text_tipo_armamento'];
                    $disponibilidade = $postVars['text_estado_uso'];
                    $estado = $postVars['text_estado_operacional'];
                    $resultados_tabela = self::getWeaponsItem("codigo_tipo_armamento = {$tipo_armamento} AND disponibilidade = {$disponibilidade} AND status_operacional = {$estado}");
                    $tableItems = $resultados_tabela['html'];
                    $contador_total = $resultados_tabela['contador'];
                }
                // tipo + operacional (sem uso)
                elseif (!empty($postVars['text_tipo_armamento']) && !empty($postVars['text_estado_operacional']) && empty($postVars['text_estado_uso'])) {
                    $tipo_armamento = $postVars['text_tipo_armamento'];
                    $estado = $postVars['text_estado_operacional'];
                    $resultados_tabela = self::getWeaponsItem("codigo_tipo_armamento = {$tipo_armamento} AND status_operacional = {$estado}");
                    $tableItems = $resultados_tabela['html'];
                    $contador_total = $resultados_tabela['contador'];
                }
                // tipo + uso (sem operacional)
                elseif (!empty($postVars['text_tipo_armamento']) && !empty($postVars['text_estado_uso'])) {
                    $tipo_armamento = $postVars['text_tipo_armamento'];
                    $disponibilidade = $postVars['text_estado_uso'];
                    $resultados_tabela = self::getWeaponsItem("codigo_tipo_armamento = '{$tipo_armamento}' AND disponibilidade = {$disponibilidade}");
                    $tableItems = $resultados_tabela['html'];
                    $contador_total = $resultados_tabela['contador'];
                }
                // só tipo
                elseif (!empty($postVars['text_tipo_armamento']) && empty($postVars['text_estado_uso'])) {
                    $tipo_armamento = $postVars['text_tipo_armamento'];
                    $resultados_tabela = self::getWeaponsItem("codigo_tipo_armamento = '{$tipo_armamento}'");
                    $tableItems = $resultados_tabela['html'];
                    $contador_total = $resultados_tabela['contador'];
                }
                // só uso
                else if (!empty($postVars['text_estado_uso']) && empty($postVars['text_estado_operacional'])) {
                    $disponibilidade = $postVars['text_estado_uso'];
                    $resultados_tabela = self::getWeaponsItem("disponibilidade = '{$disponibilidade}'");
                    $tableItems = $resultados_tabela['html'];
                    $contador_total = $resultados_tabela['contador'];
                }

                // só uso + estado
                else if (!empty($postVars['text_estado_uso']) && !empty($postVars['text_estado_operacional'])) {
                    $disponibilidade = $postVars['text_estado_uso'];
                    $estado = $postVars['text_estado_operacional'];
                    $resultados_tabela = self::getWeaponsItem("disponibilidade = {$disponibilidade} AND status_operacional = {$estado}");
                    $tableItems = $resultados_tabela['html'];
                    $contador_total = $resultados_tabela['contador'];
                }

                // só estado
                else if (empty($postVars['text_estado_uso']) && !empty($postVars['text_estado_operacional'])) {
                    $estado = $postVars['text_estado_operacional'];
                    $resultados_tabela = self::getWeaponsItem("status_operacional = '{$estado}'");
                    $tableItems = $resultados_tabela['html'];
                    $contador_total = $resultados_tabela['contador'];
                }



                #=============================================================
                # Verificações das datas
                #=============================================================
                //$tableItems = self::getArmaRequestsItem($id);

                // Inicializa o Dompdf
                $dompdf = new Dompdf();
                $options = new Options();
                $options->setIsRemoteEnabled(true); // Habilitar carregamento remoto de recursos

                // Configuração do caminho correto
                $chrootPath = realpath(__DIR__);
                if ($chrootPath === false) {
                    throw new \Exception("Erro: o diretório de chroot não pôde ser encontrado.");
                }

                $options->setChroot($chrootPath);
                $dompdf->setOptions($options);

                // Corrigindo o caminho para o arquivo HTML
                $htmlPath = realpath($chrootPath . '/../../../resources/views/pdfreports/weaponsReport.html');
                if ($htmlPath === false) {
                    throw new \Exception("Erro: o arquivo withdraw.html não pôde ser encontrado.");
                }

                // Carregar o conteúdo do arquivo HTML
                $html = file_get_contents($htmlPath);

                // Aqui vamos substituir {{tableItem}} com o conteúdo gerado pela função
                $html = str_replace('{{tableItem}}', $tableItems, $html);
                $html = str_replace('{{total}}', $contador_total, $html);

                // Data atual (dia, mês e ano)
                $dataAtual = parent::getNowDateTime();
                $html = str_replace('{{data_atual}}', $dataAtual, $html);

                $utilizador = $_SESSION['admin']['utilizador']['nome_utilizador'];
                $html = str_replace('{{utilizador}}', $utilizador, $html);

                // Carregar HTML com o Dompdf
                $dompdf->loadHtml($html);
                $dompdf->setPaper('A4', 'landscape'); // Configurar o formato da página
                $dompdf->render();

                // Enviar cabeçalhos para exibir o PDF
                $output = $dompdf->output();
                header('Content-Type: application/pdf');
                echo $output;
            } else {
                $tipo_armamento = $postVars['text_tipo_armamento'];

                $tableItems = '';

                $resultados_tabela = self::getWeaponsItem(null);
                $tableItems = $resultados_tabela['html'];
                $contador_total = $resultados_tabela['contador'];

                #=============================================================
                # Verificações das datas
                #=============================================================
                //$tableItems = self::getArmaRequestsItem($id);

                // Inicializa o Dompdf
                $dompdf = new Dompdf();
                $options = new Options();
                $options->setIsRemoteEnabled(true); // Habilitar carregamento remoto de recursos

                // Configuração do caminho correto
                $chrootPath = realpath(__DIR__);
                if ($chrootPath === false) {
                    throw new \Exception("Erro: o diretório de chroot não pôde ser encontrado.");
                }

                $options->setChroot($chrootPath);
                $dompdf->setOptions($options);

                // Corrigindo o caminho para o arquivo HTML
                $htmlPath = realpath($chrootPath . '/../../../resources/views/pdfreports/weaponsReport.html');
                if ($htmlPath === false) {
                    throw new \Exception("Erro: o arquivo withdraw.html não pôde ser encontrado.");
                }

                // Carregar o conteúdo do arquivo HTML
                $html = file_get_contents($htmlPath);

                // Aqui vamos substituir {{tableItem}} com o conteúdo gerado pela função
                $html = str_replace('{{tableItem}}', $tableItems, $html);
                $html = str_replace('{{total}}', $contador_total, $html);

                // Data atual (dia, mês e ano)
                $dataAtual = parent::getNowDateTime();
                $html = str_replace('{{data_atual}}', $dataAtual, $html);

                $utilizador = $_SESSION['admin']['utilizador']['nome_utilizador'];
                $html = str_replace('{{utilizador}}', $utilizador, $html);

                // Carregar HTML com o Dompdf
                $dompdf->loadHtml($html);
                $dompdf->setPaper('A4', 'landscape'); // Configurar o formato da página
                $dompdf->render();

                // Enviar cabeçalhos para exibir o PDF
                $output = $dompdf->output();
                header('Content-Type: application/pdf');
                echo $output;
            }
        } catch (\Exception $e) {
            echo "Erro ao gerar o PDF: " . $e->getMessage();
        }
    }


    #==================================================================
    # PDF das Municoes
    #==================================================================

    public static function AmmuniationPDFGenarator($request)
    {
        try {

            $postVars = $request->getPostVars();
            $tableItems = '';

            if (!empty($postVars['text_calibre_municao']) && empty($postVars['text_tipo_municao'])) {
                $calibre_municao = $postVars['text_calibre_municao'];
                $resultados_tabela = self::getAmmuniationItem("calibre = '{$calibre_municao}'");
                $tableItems = $resultados_tabela['html'];
                $contador_total = $resultados_tabela['contador'];
            }else if(empty($postVars['text_calibre_municao']) && !empty($postVars['text_tipo_municao'])){
                $tipo_municao  = $postVars['text_tipo_municao'];
                $resultados_tabela = self::getAmmuniationItem("tipo = '{$tipo_municao}'");
                $tableItems = $resultados_tabela['html'];
                $contador_total = $resultados_tabela['contador'];
            } else if(!empty($postVars['text_calibre_municao']) && !empty($postVars['text_tipo_municao'])){
                $calibre_municao = $postVars['text_calibre_municao'];
                $tipo_municao  = $postVars['text_tipo_municao'];
                $resultados_tabela = self::getAmmuniationItem("calibre = '{$calibre_municao}' AND tipo = '{$tipo_municao}'");
                $tableItems = $resultados_tabela['html'];
                $contador_total = $resultados_tabela['contador'];
            }else {
                $resultados_tabela = self::getAmmuniationItem(null);
                $tableItems = $resultados_tabela['html'];
                $contador_total = $resultados_tabela['contador'];
            }

            // Inicializa o Dompdf
            $dompdf = new Dompdf();
            $options = new Options();
            $options->setIsRemoteEnabled(true); // Habilitar carregamento remoto de recursos

            // Configuração do caminho correto
            $chrootPath = realpath(__DIR__);
            if ($chrootPath === false) {
                throw new \Exception("Erro: o diretório de chroot não pôde ser encontrado.");
            }

            $options->setChroot($chrootPath);
            $dompdf->setOptions($options);

            // Corrigindo o caminho para o arquivo HTML
            $htmlPath = realpath($chrootPath . '/../../../resources/views/pdfreports/ammuniationReport.html');
            if ($htmlPath === false) {
                throw new \Exception("Erro: o arquivo withdraw.html não pôde ser encontrado.");
            }

            // Carregar o conteúdo do arquivo HTML
            $html = file_get_contents($htmlPath);

            // Aqui vamos substituir {{tableItem}} com o conteúdo gerado pela função
            $html = str_replace('{{tableItem}}', $tableItems, $html);
            $html = str_replace('{{total}}', $contador_total, $html);

            // Data atual (dia, mês e ano)
            $dataAtual = parent::getNowDateTime();
            $html = str_replace('{{data_atual}}', $dataAtual, $html);

            $utilizador = $_SESSION['admin']['utilizador']['nome_utilizador'];
            $html = str_replace('{{utilizador}}', $utilizador, $html);

            // Carregar HTML com o Dompdf
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'landscape'); // Configurar o formato da página
            $dompdf->render();

            // Enviar cabeçalhos para exibir o PDF
            $output = $dompdf->output();
            header('Content-Type: application/pdf');
            echo $output;
        } catch (\Exception $e) {
            echo "Erro ao gerar o PDF: " . $e->getMessage();
        }
    }

    #==================================================================
    # PDF dos Equipamentos
    #==================================================================

     public static function EquipmentsPDFGenarator($request)
    {
        try {

            $postVars = $request->getPostVars();
            $tableItems = '';

            if (!empty($postVars['text_tipo_equipamento']) && empty($postVars['text_nome_equipamento'])) {
                $tipo_equipamento = $postVars['text_tipo_equipamento'];
                $resultados_tabela = self::getEquipmentsItem("tipo = '{$tipo_equipamento}'");
                $tableItems = $resultados_tabela['html'];
                $contador_total = $resultados_tabela['contador'];
            }else if(empty($postVars['text_tipo_equipamento']) && !empty($postVars['text_nome_equipamento'])){
                $nome_equipamento = $postVars['text_nome_equipamento'];
                $resultados_tabela = self::getEquipmentsItem("nome = '{$nome_equipamento}'");
                $tableItems = $resultados_tabela['html'];
                $contador_total = $resultados_tabela['contador'];
            } else if(!empty($postVars['text_tipo_equipamento']) && !empty($postVars['text_nome_equipamento'])){
                $tipo_equipamento = $postVars['text_tipo_equipamento'];
                $nome_equipamento  = $postVars['text_nome_equipamento'];
                $resultados_tabela = self::getEquipmentsItem("tipo = '{$tipo_equipamento}' AND nome = '{$nome_equipamento}'");
                $tableItems = $resultados_tabela['html'];
                $contador_total = $resultados_tabela['contador'];
            }else {
                $resultados_tabela = self::getEquipmentsItem(null);
                $tableItems = $resultados_tabela['html'];
                $contador_total = $resultados_tabela['contador'];
            }

            // Inicializa o Dompdf
            $dompdf = new Dompdf();
            $options = new Options();
            $options->setIsRemoteEnabled(true); // Habilitar carregamento remoto de recursos

            // Configuração do caminho correto
            $chrootPath = realpath(__DIR__);
            if ($chrootPath === false) {
                throw new \Exception("Erro: o diretório de chroot não pôde ser encontrado.");
            }

            $options->setChroot($chrootPath);
            $dompdf->setOptions($options);

            // Corrigindo o caminho para o arquivo HTML
            $htmlPath = realpath($chrootPath . '/../../../resources/views/pdfreports/equipmentReport.html');
            if ($htmlPath === false) {
                throw new \Exception("Erro: o arquivo withdraw.html não pôde ser encontrado.");
            }

            // Carregar o conteúdo do arquivo HTML
            $html = file_get_contents($htmlPath);

            // Aqui vamos substituir {{tableItem}} com o conteúdo gerado pela função
            $html = str_replace('{{tableItem}}', $tableItems, $html);
            $html = str_replace('{{total}}', $contador_total, $html);

            // Data atual (dia, mês e ano)
            $dataAtual = parent::getNowDateTime();
            $html = str_replace('{{data_atual}}', $dataAtual, $html);

            $utilizador = $_SESSION['admin']['utilizador']['nome_utilizador'];
            $html = str_replace('{{utilizador}}', $utilizador, $html);

            // Carregar HTML com o Dompdf
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'landscape'); // Configurar o formato da página
            $dompdf->render();

            // Enviar cabeçalhos para exibir o PDF
            $output = $dompdf->output();
            header('Content-Type: application/pdf');
            echo $output;
        } catch (\Exception $e) {
            echo "Erro ao gerar o PDF: " . $e->getMessage();
        }
    }

    #============================================================================
    # FIM Funcoes relacionadas a geracao do PDF
    #============================================================================
}
