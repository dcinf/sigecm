<?php
    namespace App\Model\Entity\Fardamentos;
    use App\DatabaseManager\Database;

    class FuncionarioFardamentosEntity{
        public $codigo_funcionario;
        public $nome_completo;
        public $subunidade;
        public $genero;
        public $patente; 
        public $celular; 
        public $codigo_departamento; 
        public $nome_departamento;
        public $tamanho_calca; 
        public $tamanho_camisa; 
        public $tamanho_bota; 
        public $fotografia; 
        public $criado_em;
        public $atualizado_em;
        public $apagado_em;  


        public  function cadastrar(){
            $this->codigo_funcionario = (new Database('funcionarios_fardamentos'))->insert([
                'nome_completo'             => $this->nome_completo,
                'subunidade'                => $this->subunidade,
                'genero'                    => $this->genero,
                'patente'                   => $this->patente,
                'celular'                   => $this->celular,
                'codigo_departamento'       => $this->codigo_departamento,
                'nome_departamento'         => $this->nome_departamento,
                'tamanho_calca'             => $this->tamanho_calca,
                'tamanho_camisa'            => $this->tamanho_camisa,
                'tamanho_bota'              => $this->tamanho_bota,
                'fotografia'                => $this->fotografia,
                'criado_em'                 => $this->criado_em,
                'atualizado_em'             => $this->atualizado_em
            ]);
            return $this->codigo_funcionario;
        }

        public static function getFuncionario($where = null, $order = null, $limit = null, $fields = "*"){
            return (new Database('funcionarios_fardamentos'))->select($where, $order, $limit, $fields);
        }

        public static function getFuncionarioById($id){
            return self::getFuncionario('codigo_funcionario = '.$id)->fetchObject(self::class);
        }

        public  function actualizar(){
            return (new Database('funcionarios_fardamentos'))->update('codigo_funcionario = '.$this->codigo_funcionario, [
                'nome_completo'             => $this->nome_completo,
                'subunidade'                => $this->subunidade,
                'genero'                    => $this->genero,
                'patente'                   => $this->patente,
                'celular'                   => $this->celular,
                'codigo_departamento'       => $this->codigo_departamento,
                'nome_departamento'         => $this->nome_departamento,
                'tamanho_calca'             => $this->tamanho_calca,
                'tamanho_camisa'            => $this->tamanho_camisa,
                'tamanho_bota'              => $this->tamanho_bota,
                'fotografia'                => $this->fotografia,
                'criado_em'                 => $this->criado_em,
                'atualizado_em'             => $this->atualizado_em
            ]);
        }

    }
?>