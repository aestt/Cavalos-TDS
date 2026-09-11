<?php

    require_once "Conexao/Conexao.php";
    require_once "Funcoes/Funcoes.php";

    class Cidade{

        private $id;
        private $nome;

      
        /**
         * Get the value of id
         */
        public function getId()
        {
                return $this->id;
        }

        /**
         * Set the value of id
         */
        public function setId($id): self
        {
                $this->id = $id;

                return $this;
        }

        /**
         * Get the value of nome
         */
        public function getNome()
        {
                return $this->nome;
        }

        /**
         * Set the value of nome
         */
        public function setNome($nome): self
        {
                $this->nome = $nome;

                return $this;
        }

        function __construct(){
            $this->con = new Conexao();
            $this->objfcn = new Funcoes();
        }

        //Métodos para comunicação do banco de dado
        function gravarCidade($dado){
            try{

                $this->nome = $dado['nome'];


                $inserir = $this->con->conectar()->prepare("INSERT INTO cidade (nome) VALUES(:nome)");    
                $inserir->bindParam(":nome" , $this->nome, PDO::PARAM_STR);


                if($inserir->execute()){
                    return 'ok';
                }else{
                    return 'deu erro';
                }


            }catch(PDOException $ex){
                    echo $ex;
            }
        }

        function selecionarId($dado){

          try{
           $this->id = $this->objfcn->base64($dado, 2);
           $selecionar = $this->con->conectar()->prepare("SELECT id, nome FROM cidade WHERE id = :idCliente");
           $selecionar->bindParam(':idCliente', $this->id, PDO::PARAM_INT);
           $selecionar->execute();
           return $selecionar->fetch(PDO::FETCH_ASSOC);
          }catch(PDOException $ex){
            echo $ex->getMessage();
          }

        }

        function editarCliente($dado){
          try{

            $this->id = $this->objfcn->base64($dado['func'], 2);

            $this->nome = $dado['nome'];

            $inserir = $this->con->conectar()->prepare("UPDATE cidade SET nome = :nome WHERE id = :idCliente");
            $inserir->bindParam(":idCliente" , $this->id, PDO::PARAM_INT);
            $inserir->bindParam(":nome" , $this->nome, PDO::PARAM_STR);

            if($inserir->execute()){
                return 'ok';
            }else{
                return 'deu erro';
            }

          }catch(PDOException $ex){
            echo $ex;
          }
        }

        function deletarCliente($dado){

          try{
           $this->id = $this->objfcn->base64($dado, 2);
           $selecionar = $this->con->conectar()->prepare("DELETE FROM cidade WHERE id = :idCliente");
           $selecionar->bindParam(':idCliente', $this->id, PDO::PARAM_INT);
           if($selecionar->execute()){
            return 'ok';
           }else{
            return 'Deu ruim';
           }
          }catch(PDOException $ex){
            echo $ex;
          }

        }

        function selecionarCidade(){
        try{

                $selecionar = $this->con->conectar()->prepare("SELECT * FROM cidade");
                $selecionar->execute();

                return $selecionar->fetchAll();

        }catch(PDOException $ex){
                echo $ex->getMessage();
        }
        }


    }


?>
