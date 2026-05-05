<?php
    namespace App\Model\Entity;
    use App\DatabaseManager\Database;

    class RequestEntity{
        public $codigo_requisicao;
        public $armazem;                    
        public $nome_requerente;                    
        public $numero_requisicao;
        public $data_requisicao;
        public $data_devolucao;
        public $assinatura_receptor_requisicao;
        public $assinatura_fornecedor_requisicao;
        public $assinatura_receptor_devolucao;
        public $assinatura_devolvedor;
        public $criado_em;
        public $atualizado_em;
        public $apagado_em;
        
        public  function cadastrar(){
            $this->codigo_requisicao = (new Database('requisicao'))->insert([
                'armazem'                               => $this->armazem,
                'nome_requerente'                       => $this->nome_requerente,
                'numero_requisicao'                     => $this->numero_requisicao,
                'data_requisicao'                       => $this->data_requisicao,
                'data_devolucao'                        => $this->data_devolucao,
                'assinatura_receptor_requisicao'        => $this->assinatura_receptor_requisicao,
                'assinatura_fornecedor_requisicao'      => $this->assinatura_fornecedor_requisicao,
                'assinatura_receptor_devolucao'         => $this->assinatura_receptor_devolucao,
                'assinatura_devolvedor'                 => $this->assinatura_devolvedor,
                'criado_em'                             => $this->criado_em,
                'atualizado_em'                         => $this->atualizado_em
            ]);
            return $this->codigo_requisicao;
        }

        public static function getRequisicao($where = null, $order = null, $limit = null, $fields = "*"){
            return (new Database('requisicao'))->select($where, $order, $limit, $fields);
        }

        public static function getRequisicaoById($id){
            return self::getRequisicao('codigo_requisicao = '.$id)->fetchObject(self::class);
        }

        public  function actualizar(){
            return (new Database('requisicao'))->update('codigo_requisicao = '.$this->codigo_requisicao, [
                'armazem'                               => $this->armazem,
                'nome_requerente'                       => $this->nome_requerente,
                'numero_requisicao'                     => $this->numero_requisicao,
                'data_requisicao'                       => $this->data_requisicao,
                'data_devolucao'                        => $this->data_devolucao,
                'assinatura_receptor_requisicao'        => $this->assinatura_receptor_requisicao,
                'assinatura_fornecedor_requisicao'      => $this->assinatura_fornecedor_requisicao,
                'assinatura_receptor_devolucao'         => $this->assinatura_receptor_devolucao,
                'assinatura_devolvedor'                 => $this->assinatura_devolvedor,
                'criado_em'                             => $this->criado_em,
                'atualizado_em'                         => $this->atualizado_em
            ]);
        }

    }
?>