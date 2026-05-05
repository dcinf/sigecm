<?php
    namespace App\Model\Entity;
    use App\DatabaseManager\Database;

    class ArrecadacaoEquipmentEntity{
        public $codigo_equipamento_arrecadacao;
        public $codigo_arrecadacao;
        public $codigo_equipamento;
        public $nome_equipamentos;
        public $quantidade_levantar; 
        public $quantidade_devolver; 
        public $criado_em;
        public $atualizado_em;
        public $apagado_em; 
        


            
        public  function cadastrar(){
            $this->codigo_equipamento_arrecadacao = (new Database('arrecadacao_equipamentos'))->insert([
                'codigo_arrecadacao'           => $this->codigo_arrecadacao,
                'codigo_equipamento'           => $this->codigo_equipamento,
                'nome_equipamentos'            => $this->nome_equipamentos,
                'quantidade_levantar'          => $this->quantidade_levantar,
                'quantidade_devolver'          => $this->quantidade_devolver,
                'criado_em'                    => $this->criado_em,
                'atualizado_em'                => $this->atualizado_em
            ]);
            return $this->codigo_equipamento_arrecadacao;
        }

        public static function getEquipmentArrecadacao($where = null, $order = null, $limit = null, $fields = "*"){
            return (new Database('arrecadacao_equipamentos'))->select($where, $order, $limit, $fields);
        }

        public static function getEquipmentArrecadacaoById($id){
            return self::getEquipmentArrecadacao('codigo_equipamento_arrecadacao = '.$id)->fetchObject(self::class);
        }

        public  function actualizar(){
            return (new Database('arrecadacao_equipamentos'))->update('codigo_equipamento_arrecadacao = '.$this->codigo_equipamento_arrecadacao, [
                'codigo_arrecadacao'           => $this->codigo_arrecadacao,
                'quantidade_devolver'          => $this->quantidade_devolver,
                'atualizado_em'                => $this->atualizado_em
            ]);
        }

    }
?>