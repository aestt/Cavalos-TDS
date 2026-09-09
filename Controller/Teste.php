<?php

    require_once "Conexao/Conexao.php";

    class Teste{

        private $id;
        private $nome;
        private $email;
        private $cpf;
        private $telefone;
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
        }

        //Métodos para comunicação do banco de dados
        function gravarCliente($dados){
            try{

                $this->nome = $dados['nome'];
                $this->email = $dados['email'];
                $this->cpf = $dados['cpf'];
                $this->telefone = $dados['telefone'];
                $this->observacao = $dados['observacao'];

                $insirir = $this->con->conectar()->prepare("INSERT INTO teste (nome,email,cpf,telefone,observacao) VALUES(:nome,:email,:cpf,:telefone,:observacao)");
                $insirir->bindParam(":nome" , $this->nome, PDO::PARAM_STR);
                $insirir->bindParam(":email" , $this->email, PDO::PARAM_STR);
                $insirir->bindParam(":cpf" , $this->cpf, PDO::PARAM_STR);
                $insirir->bindParam(":telefone" , $this->telefone, PDO::PARAM_STR);
                $insirir->bindParam(":observacao" , $this->observacao, PDO::PARAM_STR);

                if($insirir->execute()){
                    return 'ok';
                }else{
                    return 'deu erro';
                }


            }catch(PDOExcpetion $ex){
                    echo $ex;
            }
        }

        function editarCliente(){}

        function deletarCliente(){}

        function selecionarCliente(){
                try{

                        $selecionar =  $this->con->conectar()->prepare("SELECT * FROM teste");
                        $selecionar->execute();

                        return $selecionar->fetchAll();


                 }catch(PDOExcpetion $ex){
                    echo $ex;
                }
        }

    }


?>