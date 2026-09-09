<?php

require_once "Conexao/Conexao.php";
require_once "Funcoes/Funcoes.php";

class Volta
{
    private $con;
    private $objfcn;

    public function __construct()
    {
        $this->con = new Conexao();
        $this->objfcn = new Funcoes();
    }

    // =========================================================
    // USUÁRIO LOGADO
    // =========================================================

    private function usuarioLogado()
    {
        return (int)($_SESSION['id'] ?? 0);
    }

    // =========================================================
    // DECODIFICAR TOKEN
    // =========================================================

    private function decodificar($valor)
    {
        if (empty($valor)) {
            return 0;
        }

        return (int)$this->objfcn->base64($valor, 2);
    }

    // =========================================================
    // GERAR NOVO ID_JOGADA
    //
    // O ID_JOGADA pertence ao cadastro/grupo.
    // =========================================================

    private function gerarIdJogada($con)
    {
        $sql = $con->query("
            SELECT COALESCE(MAX(id_jogada), 0) + 1 AS nova_jogada
            FROM volta
        ");

        $resultado = $sql->fetch(PDO::FETCH_ASSOC);

        return (int)($resultado['nova_jogada'] ?? 1);
    }

    // =========================================================
    // GRAVAR NOVA JOGADA
    // =========================================================

    public function gravarVolta($dado)
    {
        try {

            $con = $this->con->conectar();

            $id_user = $this->usuarioLogado();

            if ($id_user <= 0) {
                return false;
            }

            // =================================================
            // ID DA VOLTA
            // =================================================

            if (
                !isset($dado['id_volta']) ||
                trim($dado['id_volta']) === ''
            ) {
                return false;
            }

            $id_volta = (int)$dado['id_volta'];

            if ($id_volta <= 0) {
                return false;
            }

            // =================================================
            // CLIENTES
            // =================================================

            if (
                !isset($dado['cliente']) ||
                !is_array($dado['cliente']) ||
                empty($dado['cliente'])
            ) {
                return false;
            }

            $clientes = [];

            foreach ($dado['cliente'] as $cliente) {

                $cliente = (int)$cliente;

                if ($cliente > 0) {
                    $clientes[] = $cliente;
                }
            }

            $clientes = array_values(
                array_unique($clientes)
            );

            if (empty($clientes)) {
                return false;
            }

            // =================================================
            // VERIFICA STATUS DA VOLTA
            // =================================================

            $verifica = $con->prepare("
                SELECT status_volta
                FROM volta
                WHERE id_volta = :id_volta
                AND id_user = :id_user
                LIMIT 1
            ");

            $verifica->bindValue(
                ':id_volta',
                $id_volta,
                PDO::PARAM_INT
            );

            $verifica->bindValue(
                ':id_user',
                $id_user,
                PDO::PARAM_INT
            );

            $verifica->execute();

            $voltaExistente = $verifica->fetch(PDO::FETCH_ASSOC);

            if (
                $voltaExistente &&
                strtoupper($voltaExistente['status_volta']) === 'FECHADA'
            ) {
                return "fechada";
            }

            // =================================================
            // NOVO ID_JOGADA
            // =================================================

            $id_jogada = $this->gerarIdJogada($con);

            // =================================================
            // DADOS
            // =================================================

            $codigo = trim(
                $dado['codigo'] ?? ''
            );

            $total = (float)(
                $dado['total'] ?? 0
            );

            $comissao = (float)(
                $dado['comissao'] ?? 0
            );

            $vencedor = !empty($dado['vencedor'])
                ? (int)$dado['vencedor']
                : null;

            $observacao = trim(
                $dado['observacao'] ?? ''
            );

            $cavalo = !empty($dado['cavalo'])
                ? (int)$dado['cavalo']
                : null;

            $aposta = (float)(
                $dado['aposta'] ?? 0
            );

            $premio = (float)(
                $dado['premio'] ?? 0
            );

            // =================================================
            // CÁLCULOS
            // =================================================

            $totalcomissao =
                $total * ($comissao / 100);

            $totalaposta =
                $aposta - $totalcomissao;

            // =================================================
            // INSERT
            // =================================================

            $sql = $con->prepare("
                INSERT INTO volta
                (
                    id_user,
                    id_jogada,
                    id_volta,
                    codigo,
                    total,
                    comissao,
                    totalcomissao,
                    totalaposta,
                    vencedor,
                    observacao,
                    cliente,
                    cavalo,
                    aposta,
                    premio,
                    status_volta
                )
                VALUES
                (
                    :id_user,
                    :id_jogada,
                    :id_volta,
                    :codigo,
                    :total,
                    :comissao,
                    :totalcomissao,
                    :totalaposta,
                    :vencedor,
                    :observacao,
                    :cliente,
                    :cavalo,
                    :aposta,
                    :premio,
                    'ABERTA'
                )
            ");

            // =================================================
            // UM REGISTRO POR CLIENTE
            // MESMO ID_JOGADA
            // =================================================

            foreach ($clientes as $cliente) {

                $sql->bindValue(
                    ':id_user',
                    $id_user,
                    PDO::PARAM_INT
                );

                $sql->bindValue(
                    ':id_jogada',
                    $id_jogada,
                    PDO::PARAM_INT
                );

                $sql->bindValue(
                    ':id_volta',
                    $id_volta,
                    PDO::PARAM_INT
                );

                $sql->bindValue(
                    ':codigo',
                    $codigo,
                    PDO::PARAM_STR
                );

                $sql->bindValue(
                    ':total',
                    $total
                );

                $sql->bindValue(
                    ':comissao',
                    $comissao
                );

                $sql->bindValue(
                    ':totalcomissao',
                    $totalcomissao
                );

                $sql->bindValue(
                    ':totalaposta',
                    $totalaposta
                );

                $sql->bindValue(
                    ':vencedor',
                    $vencedor,
                    $vencedor === null
                        ? PDO::PARAM_NULL
                        : PDO::PARAM_INT
                );

                $sql->bindValue(
                    ':observacao',
                    $observacao,
                    PDO::PARAM_STR
                );

                $sql->bindValue(
                    ':cliente',
                    $cliente,
                    PDO::PARAM_INT
                );

                $sql->bindValue(
                    ':cavalo',
                    $cavalo,
                    $cavalo === null
                        ? PDO::PARAM_NULL
                        : PDO::PARAM_INT
                );

                $sql->bindValue(
                    ':aposta',
                    $aposta
                );

                $sql->bindValue(
                    ':premio',
                    $premio
                );

                $sql->execute();
            }

            return "ok";

        } catch (PDOException $e) {

            echo "Erro ao gravar: " .
                $e->getMessage();

            return false;
        }
    }

    // =========================================================
    // BUSCAR UMA JOGADA COMPLETA
    //
    // USA id_jogada
    // =========================================================

    public function selecionarJogada($id_jogada)
    {
        try {

            $con = $this->con->conectar();

            $id_jogada = (int)$id_jogada;

            if ($id_jogada <= 0) {
                return [];
            }

            $sql = $con->prepare("
                SELECT
                    v.id,
                    v.id_jogada,
                    v.id_volta,
                    v.id_user,
                    v.codigo,
                    v.total,
                    v.comissao,
                    v.totalcomissao,
                    v.totalaposta,
                    v.vencedor,
                    v.observacao,
                    v.cliente,
                    v.cavalo,
                    v.aposta,
                    v.premio,
                    v.status_volta,

                    c.nome AS cliente_nome,
                    c.email AS cliente_email

                FROM volta v

                LEFT JOIN usuario c
                    ON c.id = v.cliente

                WHERE v.id_jogada = :id_jogada
                AND v.id_user = :id_user

                ORDER BY v.id ASC
            ");

            $sql->bindValue(
                ':id_jogada',
                $id_jogada,
                PDO::PARAM_INT
            );

            $sql->bindValue(
                ':id_user',
                $this->usuarioLogado(),
                PDO::PARAM_INT
            );

            $sql->execute();

            return $sql->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {

            echo "Erro ao selecionar jogada: " .
                $e->getMessage();

            return [];
        }
    }

    // =========================================================
    // EDITAR JOGADA
    //
    // Edita todos os clientes que pertencem
    // ao mesmo id_jogada.
    // =========================================================

    public function editarVolta($dado)
    {
        try {

            $con = $this->con->conectar();

            $id_user = $this->usuarioLogado();

            if ($id_user <= 0) {
                return false;
            }

            if (empty($dado['func'])) {
                return false;
            }

            // -------------------------------------------------
            // FUNC = ID_JOGADA
            // -------------------------------------------------

            $id_jogada =
                $this->decodificar($dado['func']);

            if ($id_jogada <= 0) {
                return false;
            }

            // -------------------------------------------------
            // CLIENTES
            // -------------------------------------------------

            if (
                !isset($dado['cliente']) ||
                !is_array($dado['cliente']) ||
                empty($dado['cliente'])
            ) {
                return false;
            }

            $clientes = [];

            foreach ($dado['cliente'] as $cliente) {

                $cliente = (int)$cliente;

                if ($cliente > 0) {
                    $clientes[] = $cliente;
                }
            }

            $clientes = array_values(
                array_unique($clientes)
            );

            if (empty($clientes)) {
                return false;
            }

            // -------------------------------------------------
            // PEGA A VOLTA
            // -------------------------------------------------

            $verifica = $con->prepare("
                SELECT
                    id_volta,
                    status_volta
                FROM volta

                WHERE id_jogada = :id_jogada
                AND id_user = :id_user

                LIMIT 1
            ");

            $verifica->bindValue(
                ':id_jogada',
                $id_jogada,
                PDO::PARAM_INT
            );

            $verifica->bindValue(
                ':id_user',
                $id_user,
                PDO::PARAM_INT
            );

            $verifica->execute();

            $jogada = $verifica->fetch(PDO::FETCH_ASSOC);

            if (!$jogada) {
                return false;
            }

            // -------------------------------------------------
            // NÃO EDITA FECHADA
            // -------------------------------------------------

            if (
                strtoupper(
                    $jogada['status_volta']
                ) === 'FECHADA'
            ) {
                return "fechada";
            }

            $id_volta =
                (int)$jogada['id_volta'];

            // -------------------------------------------------
            // DADOS
            // -------------------------------------------------

            $codigo = trim(
                $dado['codigo'] ?? ''
            );

            $total = (float)(
                $dado['total'] ?? 0
            );

            $comissao = (float)(
                $dado['comissao'] ?? 0
            );

            $vencedor = !empty($dado['vencedor'])
                ? (int)$dado['vencedor']
                : null;

            $observacao = trim(
                $dado['observacao'] ?? ''
            );

            $cavalo = !empty($dado['cavalo'])
                ? (int)$dado['cavalo']
                : null;

            $aposta = (float)(
                $dado['aposta'] ?? 0
            );

            $premio = (float)(
                $dado['premio'] ?? 0
            );

            // -------------------------------------------------
            // CÁLCULOS
            // -------------------------------------------------

            $totalcomissao =
                $total * ($comissao / 100);

            $totalaposta =
                $aposta - $totalcomissao;

            // -------------------------------------------------
            // TRANSAÇÃO
            // -------------------------------------------------

            $con->beginTransaction();

            // -------------------------------------------------
            // REMOVE SOMENTE ESTA JOGADA
            //
            // OUTRAS JOGADAS DA MESMA VOLTA
            // CONTINUAM EXISTINDO.
            // -------------------------------------------------

            $delete = $con->prepare("
                DELETE FROM volta

                WHERE id_jogada = :id_jogada
                AND id_user = :id_user
            ");

            $delete->bindValue(
                ':id_jogada',
                $id_jogada,
                PDO::PARAM_INT
            );

            $delete->bindValue(
                ':id_user',
                $id_user,
                PDO::PARAM_INT
            );

            $delete->execute();

            // -------------------------------------------------
            // INSERE NOVAMENTE
            // COM O MESMO ID_JOGADA
            // -------------------------------------------------

            $insert = $con->prepare("
                INSERT INTO volta
                (
                    id_user,
                    id_jogada,
                    id_volta,
                    codigo,
                    total,
                    comissao,
                    totalcomissao,
                    totalaposta,
                    vencedor,
                    observacao,
                    cliente,
                    cavalo,
                    aposta,
                    premio,
                    status_volta
                )
                VALUES
                (
                    :id_user,
                    :id_jogada,
                    :id_volta,
                    :codigo,
                    :total,
                    :comissao,
                    :totalcomissao,
                    :totalaposta,
                    :vencedor,
                    :observacao,
                    :cliente,
                    :cavalo,
                    :aposta,
                    :premio,
                    'ABERTA'
                )
            ");

            foreach ($clientes as $cliente) {

                $insert->bindValue(
                    ':id_user',
                    $id_user,
                    PDO::PARAM_INT
                );

                $insert->bindValue(
                    ':id_jogada',
                    $id_jogada,
                    PDO::PARAM_INT
                );

                $insert->bindValue(
                    ':id_volta',
                    $id_volta,
                    PDO::PARAM_INT
                );

                $insert->bindValue(
                    ':codigo',
                    $codigo,
                    PDO::PARAM_STR
                );

                $insert->bindValue(
                    ':total',
                    $total
                );

                $insert->bindValue(
                    ':comissao',
                    $comissao
                );

                $insert->bindValue(
                    ':totalcomissao',
                    $totalcomissao
                );

                $insert->bindValue(
                    ':totalaposta',
                    $totalaposta
                );

                $insert->bindValue(
                    ':vencedor',
                    $vencedor,
                    $vencedor === null
                        ? PDO::PARAM_NULL
                        : PDO::PARAM_INT
                );

                $insert->bindValue(
                    ':observacao',
                    $observacao,
                    PDO::PARAM_STR
                );

                $insert->bindValue(
                    ':cliente',
                    $cliente,
                    PDO::PARAM_INT
                );

                $insert->bindValue(
                    ':cavalo',
                    $cavalo,
                    $cavalo === null
                        ? PDO::PARAM_NULL
                        : PDO::PARAM_INT
                );

                $insert->bindValue(
                    ':aposta',
                    $aposta
                );

                $insert->bindValue(
                    ':premio',
                    $premio
                );

                $insert->execute();
            }

            $con->commit();

            return "ok";

        } catch (PDOException $e) {

            if (
                isset($con) &&
                $con->inTransaction()
            ) {
                $con->rollBack();
            }

            echo "Erro ao alterar: " .
                $e->getMessage();

            return false;
        }
    }

    // =========================================================
    // FECHAR VOLTA INTEIRA
    //
    // USA id_volta
    // =========================================================

    public function fecharVolta($dado)
    {
        try {

            $con = $this->con->conectar();

            if (empty($dado['func'])) {
                return false;
            }

            $id_volta =
                $this->decodificar(
                    $dado['func']
                );

            if ($id_volta <= 0) {
                return false;
            }

            $sql = $con->prepare("
                UPDATE volta

                SET status_volta = 'FECHADA'

                WHERE id_volta = :id_volta
                AND id_user = :id_user
            ");

            $sql->bindValue(
                ':id_volta',
                $id_volta,
                PDO::PARAM_INT
            );

            $sql->bindValue(
                ':id_user',
                $this->usuarioLogado(),
                PDO::PARAM_INT
            );

            $sql->execute();

            return "ok";

        } catch (PDOException $e) {

            echo "Erro ao fechar volta: " .
                $e->getMessage();

            return false;
        }
    }

    // =========================================================
    // REABRIR VOLTA INTEIRA
    // =========================================================

    public function reabrirVolta($dado)
    {
        try {

            $con = $this->con->conectar();

            if (empty($dado['func'])) {
                return false;
            }

            $id_volta =
                $this->decodificar(
                    $dado['func']
                );

            if ($id_volta <= 0) {
                return false;
            }

            $sql = $con->prepare("
                UPDATE volta

                SET status_volta = 'ABERTA'

                WHERE id_volta = :id_volta
                AND id_user = :id_user
            ");

            $sql->bindValue(
                ':id_volta',
                $id_volta,
                PDO::PARAM_INT
            );

            $sql->bindValue(
                ':id_user',
                $this->usuarioLogado(),
                PDO::PARAM_INT
            );

            $sql->execute();

            return "ok";

        } catch (PDOException $e) {

            echo "Erro ao reabrir volta: " .
                $e->getMessage();

            return false;
        }
    }

    // =========================================================
    // DELETAR JOGADA
    //
    // USA id_jogada
    //
    // SOMENTE AQUELA JOGADA É DELETADA.
    // =========================================================

    public function deletarVolta($dado)
    {
        try {

            $con = $this->con->conectar();

            if (empty($dado['func'])) {
                return false;
            }

            $id_jogada =
                $this->decodificar(
                    $dado['func']
                );

            if ($id_jogada <= 0) {
                return false;
            }

            // -------------------------------------------------
            // STATUS
            // -------------------------------------------------

            $verifica = $con->prepare("
                SELECT status_volta
                FROM volta

                WHERE id_jogada = :id_jogada
                AND id_user = :id_user

                LIMIT 1
            ");

            $verifica->bindValue(
                ':id_jogada',
                $id_jogada,
                PDO::PARAM_INT
            );

            $verifica->bindValue(
                ':id_user',
                $this->usuarioLogado(),
                PDO::PARAM_INT
            );

            $verifica->execute();

            $jogada = $verifica->fetch(PDO::FETCH_ASSOC);

            if (!$jogada) {
                return false;
            }

            if (
                strtoupper(
                    $jogada['status_volta']
                ) === 'FECHADA'
            ) {
                return "fechada";
            }

            // -------------------------------------------------
            // DELETE
            // -------------------------------------------------

            $delete = $con->prepare("
                DELETE FROM volta

                WHERE id_jogada = :id_jogada
                AND id_user = :id_user
            ");

            $delete->bindValue(
                ':id_jogada',
                $id_jogada,
                PDO::PARAM_INT
            );

            $delete->bindValue(
                ':id_user',
                $this->usuarioLogado(),
                PDO::PARAM_INT
            );

            $delete->execute();

            return "ok";

        } catch (PDOException $e) {

            echo "Erro ao excluir jogada: " .
                $e->getMessage();

            return false;
        }
    }

    // =========================================================
    // LISTAR VOLTAS
    //
    // UMA LINHA POR id_jogada.
    //
    // Assim:
    //
    // Jogada 1 = 1 cliente
    // Jogada 2 = 2 clientes
    // Jogada 3 = 5 clientes
    //
    // Mesmo id_volta.
    // =========================================================

    public function selecionarVolta()
    {
        try {

            $con = $this->con->conectar();

            $sql = $con->prepare("
                SELECT

                    MIN(v.id) AS id,

                    v.id_jogada,
                    v.id_volta,
                    v.id_user,

                    MIN(v.codigo) AS codigo,
                    MIN(v.total) AS total,
                    MIN(v.comissao) AS comissao,
                    MIN(v.totalcomissao) AS totalcomissao,
                    MIN(v.totalaposta) AS totalaposta,

                    MIN(v.vencedor) AS vencedor,

                    MIN(v.observacao) AS observacao,

                    MIN(v.aposta) AS aposta,
                    MIN(v.premio) AS premio,

                    MIN(v.status_volta) AS status_volta,

                    GROUP_CONCAT(
                        DISTINCT v.cliente
                        ORDER BY v.cliente
                        SEPARATOR ','
                    ) AS clientes_ids,

                    GROUP_CONCAT(
                        DISTINCT c.nome
                        ORDER BY c.nome
                        SEPARATOR ', '
                    ) AS clientes_nomes,

                    GROUP_CONCAT(
                        DISTINCT v.cavalo
                        ORDER BY v.cavalo
                        SEPARATOR ','
                    ) AS cavalos_ids,

                    u.nome AS usuario_nome,
                    u.email AS usuario_email

                FROM volta v

                INNER JOIN usuario u
                    ON u.id = v.id_user

                LEFT JOIN usuario c
                    ON c.id = v.cliente

                WHERE v.id_user = :id_user

                GROUP BY
                    v.id_jogada,
                    v.id_volta,
                    v.id_user,
                    u.nome,
                    u.email

                ORDER BY
                    v.id_volta DESC,
                    v.id_jogada DESC
            ");

            $sql->bindValue(
                ':id_user',
                $this->usuarioLogado(),
                PDO::PARAM_INT
            );

            $sql->execute();

            return $sql->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {

            echo "Erro ao listar voltas: " .
                $e->getMessage();

            return [];
        }
    }

    // =========================================================
    // BUSCAR CAVALOS DE UMA JOGADA
    // =========================================================

    public function selecionarCavalosJogada($id_jogada)
    {
        try {

            $con = $this->con->conectar();

            $sql = $con->prepare("
                SELECT DISTINCT cavalo

                FROM volta

                WHERE id_jogada = :id_jogada
                AND id_user = :id_user
                AND cavalo IS NOT NULL
                AND cavalo > 0

                ORDER BY cavalo
            ");

            $sql->bindValue(
                ':id_jogada',
                (int)$id_jogada,
                PDO::PARAM_INT
            );

            $sql->bindValue(
                ':id_user',
                $this->usuarioLogado(),
                PDO::PARAM_INT
            );

            $sql->execute();

            return $sql->fetchAll(PDO::FETCH_COLUMN);

        } catch (PDOException $e) {

            return [];
        }
    }
}
?>