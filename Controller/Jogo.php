<?php

require_once "Conexao/Conexao.php";

class Jogo
{
    private $con;
    private $objfcn;
    private $id;
    private $id_user;
    private $jogo;
    private $valor;
    private $tempo;
    private $status;

    /**
     * Get the value of con
     */
    public function getCon()
    {
        return $this->con;
    }

    /**
     * Set the value of con
     */
    public function setCon($con): self
    {
        $this->con = $con;

        return $this;
    }

    /**
     * Get the value of objfcn
     */
    public function getObjfcn()
    {
        return $this->objfcn;
    }

    /**
     * Set the value of objfcn
     */
    public function setObjfcn($objfcn): self
    {
        $this->objfcn = $objfcn;

        return $this;
    }

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
     * Get the value of id_user
     */
    public function getIdUser()
    {
        return $this->id_user;
    }

    /**
     * Set the value of id_user
     */
    public function setIdUser($id_user): self
    {
        $this->id_user = $id_user;

        return $this;
    }

    /**
     * Get the value of jogo
     */
    public function getJogo()
    {
        return $this->jogo;
    }

    /**
     * Set the value of jogo
     */
    public function setJogo($jogo): self
    {
        $this->jogo = $jogo;

        return $this;
    }

    /**
     * Get the value of valor
     */
    public function getValor()
    {
        return $this->valor;
    }

    /**
     * Set the value of valor
     */
    public function setValor($valor): self
    {
        $this->valor = $valor;

        return $this;
    }

    /**
     * Get the value of tempo
     */
    public function getTempo()
    {
        return $this->tempo;
    }

    /**
     * Set the value of tempo
     */
    public function setTempo($tempo): self
    {
        $this->tempo = $tempo;

        return $this;
    }

    /**
     * Get the value of status
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set the value of status
     */
    public function setStatus($status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get the value of observacao
     */

    /**
     * Set the value of observacao
     */
    function __construct()
    {
        $this->con = new Conexao();
        $this->objfcn = new Funcoes();
    }

    //Métodos para comunicação do banco de dado
    function gravarJogo($dado)
    {
        try {

            $this->jogo = $dado['jogo'];
            $this->id_user = $dado['id_user'];
            $this->valor = $dado['valor'];
            $this->tempo = $dado['tempo'];
            $this->status = $dado['status'];
            

            $inserir = $this->con->conectar()->prepare("INSERT INTO jogo (id_user,jogo,valor,tempo,status) VALUES(:id_user,:jogo,:valor,:tempo,:status)");
            $inserir->bindParam(":id_user", $this->id_user, PDO::PARAM_INT);
            $inserir->bindParam(":jogo", $this->jogo, PDO::PARAM_STR);
            $inserir->bindParam(":valor", $this->valor, PDO::PARAM_STR);
            $inserir->bindParam(":tempo", $this->tempo, PDO::PARAM_STR);
            $inserir->bindParam(":status", $this->status, PDO::PARAM_STR);

            if ($inserir->execute()) {
                return 'ok';
            } else {
                return 'deu erro';
            }
        } catch (PDOException $ex) {
            echo $ex;
        }
    }

    function selecionarId($dado)
    {

        try {
            $this->id = $this->objfcn->base64($dado, 2);
            $selecionar = $this->con->conectar()->prepare("SELECT id, jogo, valor, tempo, status
             FROM jogo WHERE id = :idJogo");
            $selecionar->bindParam(':idJogo', $this->id, PDO::PARAM_INT);
            $selecionar->execute();
            return $selecionar->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $ex) {
            echo $ex->getMessage();
        }
    }

    function editarJogo($dado)
    {
        try {

            $this->id = $this->objfcn->base64($dado['func'], 2);

            $this->jogo = $dado['jogo'];
            $this->valor = $dado['valor'];
            $this->tempo = $dado['tempo'];
            $this->status = $dado['status'];
            

            $inserir = $this->con->conectar()->prepare("UPDATE Jogo SET jogo = :jogo, valor = :valor, tempo = :tempo, status = :status WHERE id = :idJogo");
            $inserir->bindParam(":idJogo", $this->id, PDO::PARAM_INT);
            $inserir->bindParam(":jogo", $this->jogo, PDO::PARAM_STR);
            $inserir->bindParam(":valor", $this->valor, PDO::PARAM_STR);
            $inserir->bindParam(":tempo", $this->tempo, PDO::PARAM_STR);
            $inserir->bindParam(":status", $this->status, PDO::PARAM_STR);
            
            if ($inserir->execute()) {
                return 'ok';
            } else {
                return 'deu erro';
            }
        } catch (PDOException $ex) {
            echo $ex;
        }
    }

    function deletarJogo($dado)
    {

        try {
            $this->id = $this->objfcn->base64($dado, 2);
            $selecionar = $this->con->conectar()->prepare("DELETE FROM jogo WHERE id = :idJogo");
            $selecionar->bindParam(':idJogo', $this->id, PDO::PARAM_INT);
            if ($selecionar->execute()) {
                return 'ok';
            } else {
                return 'Deu ruim';
            }
        } catch (PDOException $ex) {
            echo $ex;
        }
    }

    function selecionarJogo()
    {
        try {

            $selecionar = $this->con->conectar()->prepare("SELECT * FROM jogo WHERE id_user = :id_user");
            $selecionar->bindParam(":id_user", $_SESSION['id'], PDO::PARAM_INT);
            $selecionar->execute();

            return $selecionar->fetchAll();
        } catch (PDOException $ex) {
            echo $ex;
        }
    }
}