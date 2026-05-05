<?php
    namespace App\Model\Entity;
    use App\DatabaseManager\Database;

    class ArmasRequestEntity{
        public $codigo_armamento_requisitado;
        public $codigo_requisicao;  
        public $codigo_armamento; 
        public $numero_de_serie_arma; 
        public $tipo_armamento; 
        public $modelo; 
        public $status_operacional_arma; 
        public $calibre; 
        public $data_ultima_inspecao_arma;  
        public $designacao;
        public $criado_em;
        public $atualizado_em;   
        public $apagado_em;


        public  function cadastrar(){
            $this->codigo_armamento_requisitado = (new Database('arma_requisicao'))->insert([
                'codigo_requisicao'                 => $this->codigo_requisicao,
                'codigo_armamento'                  => $this->codigo_armamento,
                'numero_de_serie_arma'              => $this->numero_de_serie_arma,
                'tipo_armamento'                    => $this->tipo_armamento,
                'modelo'                            => $this->modelo,
                'status_operacional_arma'           => $this->status_operacional_arma,
                'calibre'                           => $this->calibre,
                'data_ultima_inspecao_arma'         => $this->data_ultima_inspecao_arma,
                'designacao'                        => $this->designacao,
                'criado_em'                         => $this->criado_em,
                'atualizado_em'                     => $this->atualizado_em
            ]);
            return $this->codigo_armamento_requisitado;
        }

        public static function getArmasRequest($where = null, $order = null, $limit = null, $fields = "*"){
            return (new Database('arma_requisicao'))->select($where, $order, $limit, $fields);
        }

        public static function getAmasRequestById($id){
            return self::getArmasRequest('codigo_armamento_requisitado = '.$id)->fetchObject(self::class);
        }

        public  function actualizar(){
            return (new Database('arma_requisicao'))->update('codigo_armamento_requisitado = '.$this->codigo_armamento_requisitado, [
                'codigo_requisicao'                 => $this->codigo_requisicao,
                'codigo_armamento'                  => $this->codigo_armamento,
                'numero_de_serie_arma'              => $this->numero_de_serie_arma,
                'tipo_armamento'                    => $this->tipo_armamento,
                'modelo'                            => $this->modelo,
                'status_operacional_arma'           => $this->status_operacional_arma,
                'calibre'                           => $this->calibre,
                'data_ultima_inspecao_arma'         => $this->data_ultima_inspecao_arma,
                'designacao'                        => $this->designacao,
                'criado_em'                         => $this->criado_em,
                'atualizado_em'                     => $this->atualizado_em
            ]);
        }

    }
?>