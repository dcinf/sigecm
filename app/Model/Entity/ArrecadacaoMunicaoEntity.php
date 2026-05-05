<?php
    namespace App\Model\Entity;
    use App\DatabaseManager\Database;

    class ArrecadacaoMunicaoEntity{
        public $codigo_municao_arrecadacao;
        public $codigo_arrecadacao;
        public $codigo_municao;
        public $nome_municao;
        public $quantidade_levantar; 
        public $quantidade_devolver; 
        public $criado_em;
        public $atualizado_em;
        public $apagado_em;  


            
        public  function cadastrar(){
            $this->codigo_municao_arrecadacao = (new Database('arrecadacao_municoes'))->insert([
                'codigo_arrecadacao'           => $this->codigo_arrecadacao,
                'codigo_municao'               => $this->codigo_municao,
                'nome_municao'                 => $this->nome_municao,
                'quantidade_levantar'          => $this->quantidade_levantar,
                'quantidade_devolver'          => $this->quantidade_devolver,
                'criado_em'                    => $this->criado_em,
                'atualizado_em'                => $this->atualizado_em
            ]);
            return $this->codigo_municao_arrecadacao;
        }

        public static function getMunicaoArrecadacao($where = null, $order = null, $limit = null, $fields = "*"){
            return (new Database('arrecadacao_municoes'))->select($where, $order, $limit, $fields);
        }

        public static function getMunicaoArrecadacaoById($id){
            return self::getMunicaoArrecadacao('codigo_municao_arrecadacao = '.$id)->fetchObject(self::class);
        }

        public  function actualizar(){
            return (new Database('arrecadacao_municoes'))->update('codigo_municao_arrecadacao = '.$this->codigo_municao_arrecadacao, [
                'codigo_arrecadacao'           => $this->codigo_arrecadacao,
                'quantidade_devolver'          => $this->quantidade_devolver,
                'atualizado_em'                => $this->atualizado_em
            ]);
        }

    }
?>