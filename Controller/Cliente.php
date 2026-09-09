<?php

    require_once "Conexao/Conexao.php";
    require_once "Funcoes/Funcoes.php";

    class Cliente{

        private $id;
        private $id_user;
        private $nome;
        private $email;
        private $cpf;
        private $telefone;
        private $cidade;
        private $observacao;

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
         * Set the value of id_user
         */
        public function setIdUser($id_user): self
        {
                $this->id_user = $id_user;

                return $this;
        }

        public function getIdUser()
        {
                return $this->id_user;
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

        /**
         * Get the value of email
         */
        public function getEmail()
        {
                return $this->email;
        }

        /**
         * Set the value of email
         */
        public function setEmail($email): self
        {
                $this->email = $email;

                return $this;
        }

        /**
         * Get the value of cpf
         */
        public function getCpf()
        {
                return $this->cpf;
        }

        /**
         * Set the value of cpf
         */
        public function setCpf($cpf): self
        {
                $this->cpf = $cpf;

                return $this;
        }

        /**
         * Get the value of telefone
         */
        public function getTelefone()
        {
                return $this->telefone;
        }

        /**
         * Set the value of telefone
         */
        public function setTelefone($telefone): self
        {
                $this->telefone = $telefone;

                return $this;
        }

        /**
         * Get the value of cidade
         */
        public function getCidade()
        {
                return $this->cidade;
        }

        /**
         * Set the value of cidade
         */
        public function setCidade($cidade): self
        {
                $this->cidade = $cidade;

                return $this;
        }

        /**
         * Get the value of observacao
         */
        public function getObservacao()
        {
                return $this->observacao;
        }

        /**
         * Set the value of observacao
         */
        public function setObservacao($observacao): self
        {
                $this->observacao = $observacao;

                return $this;
        }

        function __construct(){
            $this->con = new Conexao();
            $this->objfcn = new Funcoes();
        }

        //Métodos para comunicação do banco de dado
        function gravarCliente($dado){
            try{
                $this->id_user = $dado['id_user'];
                $this->nome = $dado['nome'];
                $this->email = $dado['email'];
                $this->cpf = $dado['cpf'];
                $this->telefone = $dado['telefone'];
                $this->cidade = $dado['cidade'];
                $this->observacao = $dado['observacao'];
                

                $inserir = $this->con->conectar()->prepare("INSERT INTO cliente (id_user,nome,email,cpf,telefone,cidade,observacao) VALUES(:id_user,:nome,:email,:cpf,:telefone,:cidade,:observacao)");
                $inserir->bindParam(":id_user" , $this->id_user, PDO::PARAM_INT);
                $inserir->bindParam(":nome" , $this->nome, PDO::PARAM_STR);
                $inserir->bindParam(":email" , $this->email, PDO::PARAM_STR);
                $inserir->bindParam(":cpf" , $this->cpf, PDO::PARAM_STR);
                $inserir->bindParam(":telefone" , $this->telefone, PDO::PARAM_STR);
                $inserir->bindParam(":cidade" , $this->cidade, PDO::PARAM_STR);
                $inserir->bindParam(":observacao" , $this->observacao, PDO::PARAM_STR);

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
           $selecionar = $this->con->conectar()->prepare("SELECT id, nome, email, cpf, telefone, observacao FROM cliente WHERE id = :idCliente");
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
            $this->email = $dado['email'];
            $this->cpf = $dado['cpf'];
            $this->telefone = $dado['telefone'];
            $this->observacao = $dado['observacao'];

            $inserir = $this->con->conectar()->prepare("UPDATE cliente SET nome = :nome, email = :email, cpf = :cpf, telefone = :telefone, observacao = :observacao WHERE id = :idCliente");
            $inserir->bindParam(":idCliente" , $this->id, PDO::PARAM_INT);
            $inserir->bindParam(":nome" , $this->nome, PDO::PARAM_STR);
            $inserir->bindParam(":email" , $this->email, PDO::PARAM_STR);
            $inserir->bindParam(":cpf" , $this->cpf, PDO::PARAM_STR);
            $inserir->bindParam(":telefone" , $this->telefone, PDO::PARAM_STR);
            $inserir->bindParam(":observacao" , $this->observacao, PDO::PARAM_STR);

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
           $selecionar = $this->con->conectar()->prepare("DELETE FROM cliente WHERE id = :idCliente");
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

        function selecionarCliente(){

                try {

                        $selecionar = $this->con->conectar()->prepare("
                        SELECT cliente.*, 
                                cidade.nome AS cidade 
                        FROM cliente 
                        INNER JOIN cidade ON cliente.cidade = cidade.id
                        ");

                        $selecionar->execute();

                        return $selecionar->fetchAll(PDO::FETCH_ASSOC);

                } catch(PDOException $ex){

                        echo "Erro ao listar clientes: " . $ex->getMessage();
                        return [];

                }
                }

    }


?>