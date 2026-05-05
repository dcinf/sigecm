<?php
    namespace App\Model\Entity\Fardamentos;
    use App\DatabaseManager\Database;

    class FardamentosEntity{
        public $codigo_fardamento;
        public $tipo_fardamento;
        public $material_fabrico;
        public $cor_material;
        public $finalidade; 
        public $durabilidade; 
        public $instrucoes; 
        public $tamanho;
        public $quantidade; 
        public $fornecedor; 
        public $adquirido_em;  
        public $criado_em;
        public $atualizado_em;
        public $apagado_em;  


        public  function cadastrar(){
            $this->codigo_fardamento = (new Database('fardamentos'))->insert([
                'tipo_fardamento'           => $this->tipo_fardamento,
                'material_fabrico'          => $this->material_fabrico,
                'cor_material'              => $this->cor_material,
                'finalidade'                => $this->finalidade,
                'durabilidade'              => $this->durabilidade,
                'instrucoes'                => $this->instrucoes,
                'tamanho'                   => $this->tamanho,
                'quantidade'                => $this->quantidade,
                'fornecedor'                => $this->fornecedor,
                'adquirido_em'              => $this->adquirido_em,
                'criado_em'                 => $this->criado_em,
                'atualizado_em'             => $this->atualizado_em
            ]);
            return $this->codigo_fardamento;
        }

        public static function getFardamentos($where = null, $order = null, $limit = null, $fields = "*"){
            return (new Database('fardamentos'))->select($where, $order, $limit, $fields);
        }

        public static function getFardamentoById($id){
            return self::getFardamentos('codigo_fardamento = '.$id)->fetchObject(self::class);
        }

        public  function actualizar(){
            return (new Database('fardamentos'))->update('codigo_fardamento = '.$this->codigo_fardamento, [
                'tipo_fardamento'           => $this->tipo_fardamento,
                'material_fabrico'          => $this->material_fabrico,
                'cor_material'              => $this->cor_material,
                'finalidade'                => $this->finalidade,
                'durabilidade'              => $this->durabilidade,
                'instrucoes'                => $this->instrucoes,
                'tamanho'                   => $this->tamanho,
                'quantidade'                => $this->quantidade,
                'fornecedor'                => $this->fornecedor,
                'adquirido_em'              => $this->adquirido_em,
                'criado_em'                 => $this->criado_em,
                'atualizado_em'             => $this->atualizado_em
            ]);
        }

    }
?>