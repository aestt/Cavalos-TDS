<?php

    require_once "Conexao/Conexao.php";
    require_once "Funcoes/Funcoes.php";

    class Usuario{

        private $id;
        private $nome;
        private $email;
        private $senha;
        private $token;
        private $observacao;
        

        //getter e setters
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
         * Get the value of senha
         */
        public function getSenha()
        {
                return $this->senha;
        }

        /**
         * Set the value of senha
         */
        public function setSenha($senha): self
        {
                $this->senha = $senha;

                return $this;
        }

        /**
         * Get the value of token
         */
        public function getToken()
        {
                return $this->token;
        }

        /**
         * Set the value of token
         */
        public function setToken($token): self
        {
                $this->token = $token;

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
        function gravarUsuario($dado){
        try{

                $this->nome = $dado['nome'];
                $this->email = $dado['email'];
                $this->senha = password_hash($dado['senha'], PASSWORD_DEFAULT);
                $this->token = bin2hex(random_bytes(32));
               // $this->token = $dado['token'];
                $this->observacao = $dado['observacao'];

                $con = $this->con->conectar();

                $inserir = $con->prepare("INSERT INTO usuario (nome,email,senha,token,observacao)
                VALUES(:nome,:email,:senha,:token,:observacao)");

                $inserir->bindParam(":nome",$this->nome);
                $inserir->bindParam(":email",$this->email);
                $inserir->bindParam(":senha",$this->senha);
                $inserir->bindParam(":token",$this->token);
                $inserir->bindParam(":observacao",$this->observacao);

                if($inserir->execute()){

                $idUsuario = $con->lastInsertId();

                $perfil = $con->prepare("INSERT INTO perfis
                (id_user,nome,email,token,observacao)
                VALUES(:id_user,:nome,:email,:token,:observacao)");

                $perfil->bindParam(":id_user",$idUsuario);
                $perfil->bindParam(":nome",$this->nome);
                $perfil->bindParam(":email",$this->email);
                $perfil->bindParam(":token",$this->token);
                $perfil->bindParam(":observacao",$this->observacao);

                $perfil->execute();

                return "ok";
                }

                return "deu erro";

        }catch(PDOException $ex){
                echo $ex->getMessage();
        }
        }

        function selecionarId($dado){

          try{
           $this->id = $this->objfcn->base64($dado, 2);
           $selecionar = $this->con->conectar()->prepare("SELECT id, nome, email, senha, token, observacao FROM usuario WHERE id = :idusuario");
           $selecionar->bindParam(':idusuario', $this->id, PDO::PARAM_INT);
           $selecionar->execute();
           return $selecionar->fetch(PDO::FETCH_ASSOC);
          }catch(PDOException $ex){
            echo $ex->getMessage();
          }

        }

        function editarUsuario($dado){
          try{

            $this->id = $this->objfcn->base64($dado['func'], 2);

            $this->nome = $dado['nome'];
            $this->email = $dado['email'];
            $this->senha = $dado['senha'];
            $this->token = $dado['token'];
            $this->observacao = $dado['observacao'];

            $inserir = $this->con->conectar()->prepare("UPDATE usuario SET nome = :nome, email = :email, senha = :senha, token = :token, observacao = :observacao WHERE id = :idusuario");
            $inserir->bindParam(":idusuario" , $this->id, PDO::PARAM_INT);
            $inserir->bindParam(":nome" , $this->nome, PDO::PARAM_STR);
            $inserir->bindParam(":email" , $this->email, PDO::PARAM_STR);
            $inserir->bindParam(":senha" , $this->senha, PDO::PARAM_STR);
            $inserir->bindParam(":token" , $this->token, PDO::PARAM_STR);
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

        function deletarUsuario($dado){

          try{
           $this->id = $this->objfcn->base64($dado, 2);
           $selecionar = $this->con->conectar()->prepare("DELETE FROM usuario WHERE id = :idusuario");
           $selecionar->bindParam(':idusuario', $this->id, PDO::PARAM_INT);
           if($selecionar->execute()){
            return 'ok';
           }else{
            return 'Deu ruim';
           }
          }catch(PDOException $ex){
            echo $ex;
          }

        }

        function selecionarUsuario(){
          try{

            $selecionar = $this->con->conectar()->prepare("SELECT * FROM usuario");
            $selecionar->execute();

            return $selecionar->fetchAll();

          }catch(PDOException $ex){
            echo $ex;
          }
        }

        //Método para logar usuarios
        function logarUsuario($dados){

                $this->email = $dados['email'];
                $this->senha = $dados['senha'];

                try{

                        $logar = $this->con->conectar()->prepare("
                        SELECT
                                u.id,
                                u.email,
                                u.senha,
                                p.nome,
                                p.observacao
                        FROM usuario u
                        INNER JOIN perfis p
                                ON u.id = p.id_user
                        WHERE u.email = :email
                        ");

                        $logar->bindParam(":email", $this->email, PDO::PARAM_STR);
                        $logar->execute();

                        if($logar->rowCount() == 0){

                        echo "Usuário inexistente";

                        }else{

                        $verifica = $logar->fetch(PDO::FETCH_ASSOC);

                        if(password_verify($this->senha, $verifica['senha'])){

                                // Gera um novo token
                                $novoToken = bin2hex(random_bytes(32));

                                // Atualiza o token na tabela usuario
                                $usuario = $this->con->conectar()->prepare("
                                UPDATE usuario
                                SET token = :token
                                WHERE id = :id
                                ");

                                $usuario->bindParam(":token", $novoToken, PDO::PARAM_STR);
                                $usuario->bindParam(":id", $verifica['id'], PDO::PARAM_INT);
                                $usuario->execute();

                                // Atualiza o token na tabela perfis
                                $perfil = $this->con->conectar()->prepare("
                                UPDATE perfis
                                SET token = :token
                                WHERE id_user = :id
                                ");

                                $perfil->bindParam(":token", $novoToken, PDO::PARAM_STR);
                                $perfil->bindParam(":id", $verifica['id'], PDO::PARAM_INT);
                                $perfil->execute();

                                session_start();

                                $_SESSION['logado'] = "logar";
                                $_SESSION['func'] = $verifica['id'];
                                $_SESSION['id'] = $verifica['id'];
                                $_SESSION['nome'] = $verifica['nome'];
                                $_SESSION['observacao'] = $verifica['observacao'];
                                $_SESSION['token'] = $novoToken;

                                header("Location: ../sistemahorse/home.php");
                                exit;

                        }else{

                                echo "Senha Incorreta";

                        }

                        }

                }catch(PDOException $ex){

                        echo $ex->getMessage();

                }

                }

        //VERIFICA USUARIO
       function verificaLogado($dados){

        $logar = $this->con->conectar()->prepare("
                SELECT
                u.id,
                u.email,
                u.token,
                p.nome,
                p.observacao
                FROM usuario u
                INNER JOIN perfis p
                ON u.id = p.id_user
                WHERE u.id = :id
        ");

        $logar->bindParam(":id", $dados, PDO::PARAM_INT);
        $logar->execute();

        if($logar->rowCount() == 0){

                session_destroy();
                header("Location: index.php");
                exit;

        }

        $imprimi = $logar->fetch(PDO::FETCH_ASSOC);

        // Verifica se o token da sessão é igual ao token do banco
        if($_SESSION['token'] != $imprimi['token']){

                session_destroy();
                header("Location: index.php");
                exit;

        }

                $_SESSION['nome'] = $imprimi['nome'];
                $_SESSION['observacao'] = $imprimi['observacao'];
                $_SESSION['id'] = $imprimi['id'];
                $_SESSION['token'] = $imprimi['token'];

        }

       
        //Deslogar Usuarios
        function deslogar(){

                if(session_status()   !== PHP_SESSION_ACTIVE ){
                        session_start();
                } 

                session_unset();
                session_destroy();
                header("Location: ../sistemahorse");
                exit();
                

        }

    


}


?>