<?php
    namespace App\Model\Entity;
    use App\DatabaseManager\Database;

    class AmmuniationRequestEntity{
        public $codigo_municao_requisitado;
        public $codigo_requisicao;  
        public $codigo_municao; 
        public $quantidade_municao;  
        public $designacao;
        public $criado_em;
        public $atualizado_em;   
        public $apagado_em;


        public  function cadastrar(){
            $this->codigo_municao_requisitado = (new Database('municao_requisicao'))->insert([
                'codigo_requisicao'                 => $this->codigo_requisicao,
                'codigo_municao'                    => $this->codigo_municao,
                'quantidade_municao'                => $this->quantidade_municao,
                'designacao'                        => $this->designacao,
                'criado_em'                         => $this->criado_em,
                'atualizado_em'                     => $this->atualizado_em
            ]);
            return $this->codigo_municao_requisitado;
        }

        public static function getMunicaoRequest($where = null, $order = null, $limit = null, $fields = "*"){
            return (new Database('municao_requisicao'))->select($where, $order, $limit, $fields);
        }

        public static function getMunicaoRequestById($id){
            return self::getMunicaoRequest('codigo_municao_requisitado = '.$id)->fetchObject(self::class);
        }

        public  function actualizar(){
            return (new Database('municao_requisicao'))->update('codigo_municao_requisitado = '.$this->codigo_municao_requisitado, [
                'codigo_requisicao'                 => $this->codigo_requisicao,
                'codigo_municao'                    => $this->codigo_municao,
                'quantidade_municao'                => $this->quantidade_municao,
                'designacao'                        => $this->designacao,
                'criado_em'                         => $this->criado_em,
                'atualizado_em'                     => $this->atualizado_em
            ]);
        }

    }
?>