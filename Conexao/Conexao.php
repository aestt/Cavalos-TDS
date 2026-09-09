<?php

    class Conexao{

        private $usuario;
        private $senha;
        private $banco;
        private $servidor;
        private static $pdo;

        function __construct(){
            $this->servidor = "localhost";
            $this->banco = "sistema";
            $this->senha = "";
            $this->usuario = "root";
        }

        //Função para chamar nas outras classes
        public function conectar(){
            try{

                if(is_null(self::$pdo)){

                   self::$pdo = new PDO(
                            "mysql:host=" . $this->servidor . ";dbname=" . $this->banco,$this->usuario,$this->senha
                        );

                }

                return self::$pdo;

            }catch(PDOExcpetion $ex){
                echo $ex;
            }

        }

    }