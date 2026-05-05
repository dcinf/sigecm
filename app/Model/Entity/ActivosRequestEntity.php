<?php
    namespace App\Model\Entity;
    use App\DatabaseManager\Database;

    class ActivosRequestEntity{
        public $codigo_activo_requisitado;
        public $codigo_requisicao;  
        public $quantidade_activo;  
        public $designacao;
        public $criado_em;
        public $atualizado_em;   
        public $apagado_em;

        public  function cadastrar(){
            $this->codigo_activo_requisitado = (new Database('activos_requisicao'))->insert([
                'codigo_requisicao'                 => $this->codigo_requisicao,
                'quantidade_activo'                 => $this->quantidade_activo,
                'designacao'                        => $this->designacao,
                'criado_em'                         => $this->criado_em,
                'atualizado_em'                     => $this->atualizado_em
            ]);
            return $this->codigo_activo_requisitado;
        }

        public static function getActivosRequest($where = null, $order = null, $limit = null, $fields = "*"){
            return (new Database('activos_requisicao'))->select($where, $order, $limit, $fields);
        }

        public static function getActivosRequestById($id){
            return self::getActivosRequest('codigo_activo_requisitado = '.$id)->fetchObject(self::class);
        }

        public  function actualizar(){
            return (new Database('activos_requisicao'))->update('codigo_activo_requisitado = '.$this->codigo_activo_requisitado, [
                'codigo_requisicao'                 => $this->codigo_requisicao,
                'quantidade_activo'                 => $this->quantidade_activo,
                'designacao'                        => $this->designacao,
                'criado_em'                         => $this->criado_em,
                'atualizado_em'                     => $this->atualizado_em
            ]);
        }

    }
?>