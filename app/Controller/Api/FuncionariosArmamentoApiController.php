<?php
    namespace App\Controller\Api;

    use App\Model\Entity\FuncionarioArrecadacaoEntity;


    class FuncionariosArmamentoApiController extends Api{
        public static function getFuncionarios($request) {
            $itens = [];

            $results = FuncionarioArrecadacaoEntity::getFuncionario(null, 'codigo_funcionario DESC', null);

            while ($objFuncionario = $results->fetchObject(FuncionarioArrecadacaoEntity::class)) {
                $itens[] = [
                    'id'                    => $objFuncionario->codigo_funcionario,
                    'name'                  => $objFuncionario->nome_completo,
                    'subunidade'            => $objFuncionario->subunidade,
                    'genero'                => $objFuncionario->genero,
                    'patente'               => $objFuncionario->patente,
                    'codigo_departamento'   => $objFuncionario->codigo_departamento,
                    'departamento'          => $objFuncionario->nome_departamento,
                    'celular'               => $objFuncionario->celular,
                    'fotografia'            => $objFuncionario->fotografia,
                    'criado_em'             => $objFuncionario->criado_em
                ];
            }
        
            return $itens;
        }
    }
?>