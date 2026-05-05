<?php
    namespace App\Model\Entity;
    use App\DatabaseManager\Database;

    class UtilizadorEntity{
        public $codigo_utilizador;
        public $patente;
        public $nome_utilizador;
        public $subunidade;
        public $genero;
        public $numero_de_celular;
        public $celular_alternativo;
        public $codigo_departamento;
        public $utilizador;
        public $palavra_passe;
        public $criado_em;
        public $atualizado_em;
        public $apagado_em;                    
        public $grupos; 


        public  function cadastrar(){
            $this->codigo_utilizador = (new Database('utilizadores'))->insert([
                'patente'             => $this->patente,
                'nome_utilizador'     => $this->nome_utilizador,
                'subunidade'          => $this->subunidade,
                'genero'              => $this->genero,
                'numero_de_celular'   => $this->numero_de_celular,
                'celular_alternativo' => $this->celular_alternativo,
                'codigo_departamento' => $this->codigo_departamento,
                'utilizador'          => $this->utilizador,
                'palavra_passe'       => $this->palavra_passe,
                'criado_em'           => $this->criado_em,
                'atualizado_em'       => $this->atualizado_em,
                'apagado_em'          => $this->apagado_em,
                'grupos'              => $this->grupos
            ]);
            return $this->codigo_utilizador;
        }

        public static function getUtilizadores($where = null, $order = null, $limit = null, $fields = "*"){
            return (new Database('utilizadores'))->select($where, $order, $limit, $fields);
        }

        public static function getUtilizadorById($id){
            return self::getUtilizadores('codigo_utilizador = '.$id)->fetchObject(self::class);
        }

        public  function actualizar(){
            return (new Database('utilizadores'))->update('codigo_utilizador = '.$this->codigo_utilizador, [
                'patente'             => $this->patente,
                'nome_utilizador'     => $this->nome_utilizador,
                'subunidade'          => $this->subunidade,
                'genero'              => $this->genero,
                'numero_de_celular'   => $this->numero_de_celular,
                'celular_alternativo' => $this->celular_alternativo,
                'codigo_departamento' => $this->codigo_departamento,
                'utilizador'          => $this->utilizador,
                'palavra_passe'       => $this->palavra_passe,
                'criado_em'           => $this->criado_em,
                'atualizado_em'       => $this->atualizado_em,
                'apagado_em'          => $this->apagado_em,
                'grupos'              => $this->grupos
            ]);
        }

    }

?>