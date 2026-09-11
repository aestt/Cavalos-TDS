<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "Includes/bootstrap.php";
require_once "Controller/Cliente.php";
require_once "Funcoes/Funcoes.php";
require_once "Controller/Usuario.php";
require_once "Controller/Volta.php";
require_once "Controller/Cidade.php";

// =========================================================
// LOGIN
// =========================================================

$usuario = new Usuario();

if (
    !isset($_SESSION['logado']) ||
    $_SESSION['logado'] !== "logar"
) {
    header("Location: index.php");
    exit;
}

$usuario->verificaLogado($_SESSION['func']);

// =========================================================
// OBJETOS
// =========================================================

$cliente = new Cliente();
$cidade = new Cidade();
$objfcn = new Funcoes();
$volta = new Volta();

// =========================================================
// CLIENTES
// =========================================================

$carregaUsuarios =
    $usuario->selecionarUsuario();

$cidades =
    $cidade->selecionarCidade();

// =========================================================
// VARIÁVEIS
// =========================================================

$func = [];

$clientesSelecionados = [];

$cavalosDaJogada = [];

$modoEdicao = false;

$mensagemVolta = '';

// =========================================================
// EDITAR
// =========================================================

if (
    isset($_GET['acao']) &&
    $_GET['acao'] === 'edit'
) {

    if (!empty($_GET['func'])) {

        $id_jogada =
            $objfcn->base64(
                $_GET['func'],
                2
            );

        $id_jogada =
            (int)$id_jogada;

        if ($id_jogada > 0) {

            $resultado =
                $volta->selecionarJogada(
                    $id_jogada
                );

            if (!empty($resultado)) {

                $modoEdicao = true;

                // ---------------------------------------------
                // DADOS DA JOGADA
                // ---------------------------------------------

                $func =
                    $resultado[0];

                // ---------------------------------------------
                // CLIENTES DA JOGADA
                // ---------------------------------------------

                $clientesSelecionados = [];

                foreach ($resultado as $item) {

                    if (
                        isset($item['cliente']) &&
                        $item['cliente'] !== null &&
                        $item['cliente'] !== ''
                    ) {

                        $clientesSelecionados[] =
                            (int)$item['cliente'];
                    }
                }

                $clientesSelecionados =
                    array_values(
                        array_unique(
                            $clientesSelecionados
                        )
                    );

                // ---------------------------------------------
                // CAVALOS DA JOGADA
                // ---------------------------------------------

                $cavalosDaJogada =
                    $volta->selecionarCavalosJogada(
                        $id_jogada
                    );
            }
        }
    }
}

// =========================================================
// FECHAR VOLTA
// =========================================================

if (
    isset($_GET['acao']) &&
    $_GET['acao'] === 'fechar'
) {

    if (!empty($_GET['func'])) {

        $resultado =
            $volta->fecharVolta([
                'func' => $_GET['func']
            ]);

        if ($resultado === "ok") {

            header("Location: volta.php");
            exit;
        }
    }
}

// =========================================================
// REABRIR VOLTA
// =========================================================

if (
    isset($_GET['acao']) &&
    $_GET['acao'] === 'reabrir'
) {

    if (!empty($_GET['func'])) {

        $resultado =
            $volta->reabrirVolta([
                'func' => $_GET['func']
            ]);

        if ($resultado === "ok") {

            header("Location: volta.php");
            exit;
        }
    }
}

// =========================================================
// DELETAR JOGADA
// =========================================================

if (
    isset($_GET['acao']) &&
    $_GET['acao'] === 'delet'
) {

    if (!empty($_GET['func'])) {

        $resultado =
            $volta->deletarVolta([
                'func' => $_GET['func']
            ]);

        if ($resultado === "ok") {

            header("Location: volta.php");
            exit;
        }
    }
}

// =========================================================
// CADASTRAR
// =========================================================

if (isset($_POST['enviar'])) {

    $_POST['id_user'] =
        $_SESSION['id'];

    $resultado =
        $volta->gravarVolta($_POST);

    if (
        $resultado === "ok"
    ) {

        header("Location: volta.php");
        exit;
    }

    if ($resultado === "fechada") {

        $mensagemVolta = "
            <div class='alert alert-danger mt-3'>
                Esta volta está fechada.
            </div>
        ";

    } else {

        $mensagemVolta = "
            <div class='alert alert-danger mt-3'>
                Não foi possível cadastrar.
            </div>
        ";
    }
}

// =========================================================
// ALTERAR
// =========================================================

if (isset($_POST['btnAlterar'])) {

    $resultado =
        $volta->editarVolta($_POST);

    if ($resultado === "ok") {

        header("Location: volta.php");
        exit;
    }

    if ($resultado === "fechada") {

        $mensagemVolta = "
            <div class='alert alert-warning mt-3'>
                Esta volta está fechada.
            </div>
        ";

    } else {

        $mensagemVolta = "
            <div class='alert alert-danger mt-3'>
                Erro ao alterar.
            </div>
        ";
    }
}

// =========================================================
// VOLTAS
// =========================================================

$voltas =
    $volta->selecionarVolta();


?>
<!doctype html>

<html lang="pt-br">

<head>

    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>Cadastro de Voltas</title>

<link
    href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css"
    rel="stylesheet"
/>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Barlow+Condensed:wght@500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>

/* =========================================================
   TOKENS — mesma paleta clara de dia de corrida do sistema
   Este bloco só adiciona/sobrescreve aparência (CSS).
   Nenhuma tag, atributo ou linha de PHP abaixo foi alterada.
========================================================= */

:root {

    --bg:           #f6f8f2;
    --panel:        #ffffff;
    --grass:        #0c7a3e;
    --grass-dark:   #085c2e;
    --grass-soft:   #e6f4e9;
    --gold:         #e0a417;
    --gold-deep:    #b9800a;
    --gold-soft:    #fdf1d3;
    --navy:         #142a4d;
    --ink:          #1c2a22;
    --ink-dim:      #5c6b62;
    --red:          #c4293a;
    --red-soft:     #fbe4e6;
    --line:         #e1e8dd;
    --shadow:       0 6px 20px rgba(20, 60, 35, 0.09);

    --font-display: 'Fraunces', Georgia, serif;
    --font-heading: 'Barlow Condensed', Arial, sans-serif;
    --font-body:    'Inter', Arial, sans-serif;

}

body {

    background: var(--bg);

    font-family: var(--font-body);

    color: var(--ink);

}

.container {
    max-width: 1220px;
}

/* status (classes já usadas no seu PHP, só troquei a aparência) */

.status-aberta {

    color: var(--grass-dark);

    font-weight: bold;

    font-family: var(--font-heading);

    letter-spacing: 0.03em;

    font-size: 12.5px;

    background: var(--grass-soft);

    padding: 3px 10px;

    border-radius: 100px;

    display: inline-block;

}

.status-fechada {

    color: var(--gold-deep);

    font-weight: bold;

    font-family: var(--font-heading);

    letter-spacing: 0.03em;

    font-size: 12.5px;

    background: var(--gold-soft);

    padding: 3px 10px;

    border-radius: 100px;

    display: inline-block;

}

.select2-container {
    width: 100% !important;
}

.btn-acao {
    min-width: 96px;
}

/* faixa de topo — envolve o <h1> original */

.turfe-hero-sm {

    position: relative;

    margin-top: 24px;

    padding: 26px 32px;

    border-radius: 10px;

    background:
        repeating-linear-gradient(
            135deg,
            rgba(255, 255, 255, 0.06) 0px,
            rgba(255, 255, 255, 0.06) 14px,
            transparent 14px,
            transparent 28px
        ),
        linear-gradient(120deg, var(--grass) 0%, var(--grass-dark) 100%);

    box-shadow: var(--shadow);

    overflow: hidden;

}

.turfe-hero-sm .rail {

    font-family: var(--font-heading);

    letter-spacing: 0.1em;

    color: var(--gold-soft);

    font-size: 12.5px;

    margin: 0 0 4px;

    display: block;

}

.turfe-hero-sm .lead {

    font-family: var(--font-body);

    color: rgba(255, 255, 255, 0.85);

    font-size: 14.5px;

    margin: 6px 0 0;

    max-width: 60ch;

}

/* card do formulário — envolve o <div class="row mt-4"> original */

.turfe-form-card {

    margin-top: 26px;

    background: var(--panel);

    border: 1px solid var(--line);

    border-top: 3px solid var(--gold);

    border-radius: 10px;

    box-shadow: var(--shadow);

    padding: 28px 32px 30px;

}

.turfe-form-card h1 {

    font-family: var(--font-display);

    font-weight: 600;

    font-size: 26px;

    color: var(--navy);

}

.turfe-form-card h3 {

    font-family: var(--font-display);

    font-weight: 600;

    font-size: 21px;

    color: var(--navy);

}

.turfe-form-card .form-label {

    font-family: var(--font-heading);

    font-size: 12.5px;

    letter-spacing: 0.03em;

    color: var(--ink-dim);

}

.turfe-form-card .form-control,
.turfe-form-card .form-select {

    border: 1px solid var(--line);

    border-radius: 8px;

    font-family: var(--font-body);

    font-size: 14.5px;

    color: var(--ink);

    background: #fbfcf9;

    padding: 10px 13px;

}

.turfe-form-card .form-control:focus,
.turfe-form-card .form-select:focus {

    border-color: var(--grass);

    box-shadow: 0 0 0 3px rgba(12, 122, 62, 0.15);

    background: #ffffff;

}

.turfe-form-card .form-control[readonly] {

    background: var(--grass-soft);

    color: var(--grass-dark);

    font-weight: 600;

}

.turfe-form-card .btn-primary {

    background: var(--navy);

    border-color: var(--navy);

    font-family: var(--font-heading);

    font-weight: 600;

    letter-spacing: 0.02em;

}

.turfe-form-card .btn-primary:hover,
.turfe-form-card .btn-primary:focus {
    background: var(--grass-dark);
    border-color: var(--grass-dark);
}

.turfe-form-card .btn-warning {

    background: var(--gold);

    border-color: var(--gold);

    color: var(--navy);

    font-family: var(--font-heading);

    font-weight: 600;

    letter-spacing: 0.02em;

}

.turfe-form-card .btn-warning:hover,
.turfe-form-card .btn-warning:focus {
    background: var(--gold-deep);
    border-color: var(--gold-deep);
    color: var(--navy);
}

.turfe-form-card .btn-secondary {

    background: #ffffff;

    border-color: var(--line);

    color: var(--ink-dim);

    font-family: var(--font-heading);

    font-weight: 600;

    letter-spacing: 0.02em;

}

.turfe-form-card .btn-secondary:hover,
.turfe-form-card .btn-secondary:focus {
    background: var(--grass-soft);
    color: var(--ink);
    border-color: var(--grass-soft);
}

.turfe-form-card .alert-danger {

    border-radius: 8px;

    border-color: #f3b7bd;

    background: var(--red-soft);

    color: var(--red);

    font-weight: 600;

    font-size: 13.5px;

}

.turfe-form-card .alert-warning {

    border-radius: 8px;

    border-color: var(--gold);

    background: var(--gold-soft);

    color: var(--gold-deep);

    font-weight: 600;

    font-size: 13.5px;

}

/* select2 ajustado ao tema */

.select2-container--default .select2-selection--single,
.select2-container--default .select2-selection--multiple {

    min-height: 42px !important;

    border: 1px solid var(--line) !important;

    border-radius: 8px !important;

    background: #fbfcf9 !important;

}

.select2-container--default .select2-selection--single .select2-selection__rendered {

    color: var(--ink) !important;

    font-family: var(--font-body);

    font-size: 14.5px;

    line-height: 40px !important;

    padding-left: 13px !important;

}

.select2-container--default .select2-selection--multiple .select2-selection__choice {

    background: var(--grass-soft) !important;

    border: 1px solid var(--grass) !important;

    color: var(--grass-dark) !important;

    border-radius: 100px !important;

    padding: 2px 10px !important;

    font-family: var(--font-body);

    font-size: 13px;

}

.select2-dropdown {

    border: 1px solid var(--line) !important;

    border-radius: 8px !important;

    font-family: var(--font-body);

    font-size: 14.5px;

}

.select2-container--default .select2-results__option--highlighted[aria-selected] {
    background: var(--grass) !important;
}

/* tabela — envolve o <div class="table-responsive mt-5"> original */

.turfe-table-wrap {

    margin-top: 12px;

    border: 1px solid var(--line);

    border-radius: 10px;

    overflow: auto;

    box-shadow: var(--shadow);

    background: var(--panel);

}

.turfe-table-wrap .table {

    margin: 0;

    font-size: 13.5px;

    color: var(--ink);

    min-width: 1500px;

}

.turfe-table-wrap .table thead th {

    background: var(--navy);

    color: #ffffff;

    font-family: var(--font-heading);

    font-weight: 600;

    letter-spacing: 0.02em;

    font-size: 12px;

    border: none;

    padding: 12px 12px;

    white-space: nowrap;

    position: sticky;

    top: 0;

}

.turfe-table-wrap .table tbody td {

    padding: 10px 12px;

    border-color: var(--line);

    vertical-align: middle;

}

.turfe-table-wrap .table-striped tbody tr:nth-of-type(odd) {
    background: #fafcf8;
}

.turfe-table-wrap .table tbody tr:hover {
    background: var(--gold-soft);
}

.turfe-table-wrap .btn-warning {

    background: var(--gold-soft);

    border-color: var(--gold);

    color: var(--gold-deep);

    font-family: var(--font-heading);

    font-weight: 600;

}

.turfe-table-wrap .btn-warning:hover {
    background: var(--gold);
    color: var(--navy);
}

.turfe-table-wrap .btn-success {

    background: var(--grass);

    border-color: var(--grass);

    font-family: var(--font-heading);

    font-weight: 600;

}

.turfe-table-wrap .btn-success:hover {
    background: var(--grass-dark);
    border-color: var(--grass-dark);
}

.turfe-table-wrap .btn-primary {

    background: var(--navy);

    border-color: var(--navy);

    font-family: var(--font-heading);

    font-weight: 600;

}

.turfe-table-wrap .btn-primary:hover {
    background: #0b1d38;
    border-color: #0b1d38;
}

.turfe-table-wrap .btn-danger {

    background: var(--red-soft);

    border-color: #f3b7bd;

    color: var(--red);

    font-family: var(--font-heading);

    font-weight: 600;

}

.turfe-table-wrap .btn-danger:hover {
    background: var(--red);
    color: #ffffff;
}

.turfe-table-wrap .btn-secondary {

    background: #eef1ec;

    border-color: var(--line);

    color: #97a199;

    font-family: var(--font-heading);

    font-weight: 600;

}

</style>

</head>

<body>

<div class="container">

<?php

require_once "Includes/menu.php";

if (!empty($mensagemVolta)) {
    echo $mensagemVolta;
}

?>

<!-- =====================================================
     FORMULÁRIO
====================================================== -->

<div class="turfe-hero-sm">
    <span class="rail">Sistema TDS &middot; páreos</span>
    <p class="lead">Cadastre novas jogadas, feche ou reabra voltas e acompanhe o andamento de cada páreo.</p>
</div>

<div class="turfe-form-card">
<div class="row mt-4">

    <div class="col-md-6">

        <h1>
            Cadastro de Voltas
        </h1>

        <!-- ID VOLTA -->

        <div class="mb-3">

            <label class="form-label">
                ID da Volta
            </label>

            <input
                type="number"
                min="1"
                step="1"
                name="id_volta"
                class="form-control"
                required
                form="formVolta"
                value="<?= htmlspecialchars(
                    $func['id_volta'] ?? '',
                    ENT_QUOTES,
                    'UTF-8'
                ); ?>"
                <?= $modoEdicao ? 'readonly' : ''; ?>
            >

        </div>

        <!-- CÓDIGO -->

        <div class="mb-3">

            <label class="form-label">
                Código
            </label>

            <input
                type="text"
                name="codigo"
                class="form-control"
                form="formVolta"
                value="<?= htmlspecialchars(
                    $func['codigo'] ?? '',
                    ENT_QUOTES,
                    'UTF-8'
                ); ?>"
            >

        </div>

        <!-- TOTAL -->

        <div class="mb-3">

            <label class="form-label">
                Total
            </label>

            <input
                type="number"
                step="0.01"
                min="0"
                name="total"
                class="form-control"
                form="formVolta"
                value="<?= htmlspecialchars(
                    $func['total'] ?? '',
                    ENT_QUOTES,
                    'UTF-8'
                ); ?>"
            >

        </div>

        <!-- COMISSÃO -->

        <div class="mb-3">

            <label class="form-label">
                Comissão (%)
            </label>

            <input
                type="number"
                step="0.01"
                min="0"
                name="comissao"
                class="form-control"
                form="formVolta"
                value="<?= htmlspecialchars(
                    $func['comissao'] ?? '',
                    ENT_QUOTES,
                    'UTF-8'
                ); ?>"
            >

        </div>

<!-- =====================================================
     VENCEDOR
====================================================== -->

<!-- =====================================================
     VENCEDOR
     MOSTRA TODOS OS CAVALOS CADASTRADOS
     SEM REPETIR
====================================================== -->

<div class="mb-3">

    <label class="form-label">
        Vencedor
    </label>

    <select
        name="vencedor"
        class="form-select"
        form="formVolta"
    >

        <option value="">
            Selecione o cavalo vencedor
        </option>

<?php

// =========================================================
// BUSCA TODOS OS CAVALOS DA TABELA VOLTA
// =========================================================

try {

    $conVencedor = (new Conexao())->conectar();

    $sqlVencedor = $conVencedor->prepare("
        SELECT DISTINCT cavalo
        FROM volta
        WHERE cavalo IS NOT NULL
        AND cavalo <> ''
        AND cavalo > 0
        ORDER BY cavalo ASC
    ");

    $sqlVencedor->execute();

    $cavalosVencedor =
        $sqlVencedor->fetchAll(PDO::FETCH_COLUMN);

} catch (PDOException $e) {

    $cavalosVencedor = [];

}

// =========================================================
// IMPRIME OS CAVALOS SEM REPETIR
// =========================================================

foreach ($cavalosVencedor as $idCavalo) {

    $idCavalo = (int)$idCavalo;

    $selecionado = (
        isset($func['vencedor']) &&
        (int)$func['vencedor'] === $idCavalo
    );

?>

        <option
            value="<?= $idCavalo; ?>"
            <?= $selecionado ? 'selected' : ''; ?>
        >
            <?= $idCavalo; ?>
        </option>

<?php

}

?>

    </select>

</div>

        <!-- OBSERVAÇÃO -->

        <div class="mb-3">

            <label class="form-label">
                Observações
            </label>

            <textarea
                name="observacao"
                class="form-control"
                rows="3"
                form="formVolta"
            ><?= htmlspecialchars(
                $func['observacao'] ?? '',
                ENT_QUOTES,
                'UTF-8'
            ); ?></textarea>

        </div>

    </div>

    <div class="col-md-6">

        <h3>
            Cadastro Volta / Competição
        </h3>

        <!-- CLIENTES -->

        <div class="mb-3">

            <label class="form-label">
                Clientes
            </label>

            <select
                name="cliente[]"
                id="nome"
                class="form-select"
                multiple
                required
                form="formVolta"
            >

<?php

foreach ($carregaUsuarios as $item) {

    $idCliente =
        (int)$item['id'];

    $selecionado =
        in_array(
            $idCliente,
            $clientesSelecionados,
            true
        );

?>

                <option
                    value="<?= $idCliente; ?>"
                    <?= $selecionado ? 'selected' : ''; ?>
                >

                    <?= htmlspecialchars(
                        $item['nome'],
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>

                </option>

<?php

}

?>

            </select>

        </div>

        <!-- CAVALO -->

        <div class="mb-3">

            <label class="form-label">
                Cavalo Competidor — ID
            </label>

            <input
                type="number"
                step="1"
                min="0"
                name="cavalo"
                class="form-control"
                form="formVolta"
                value="<?= htmlspecialchars(
                    $func['cavalo'] ?? '',
                    ENT_QUOTES,
                    'UTF-8'
                ); ?>"
            >

        </div>

        <!-- APOSTA -->

        <div class="mb-3">

            <label class="form-label">
                R$: Valor Aposta
            </label>

            <input
                type="number"
                step="0.01"
                min="0"
                name="aposta"
                class="form-control"
                form="formVolta"
                value="<?= htmlspecialchars(
                    $func['aposta'] ?? '',
                    ENT_QUOTES,
                    'UTF-8'
                ); ?>"
            >

        </div>

        <!-- PRÊMIO -->

        <div class="mb-3">

            <label class="form-label">
                R$: Prêmio
            </label>

            <input
                type="number"
                step="0.01"
                min="0"
                name="premio"
                class="form-control"
                form="formVolta"
                value="<?= htmlspecialchars(
                    $func['premio'] ?? '',
                    ENT_QUOTES,
                    'UTF-8'
                ); ?>"
            >

        </div>

        <!-- FORM -->

        <form
            action=""
            method="post"
            id="formVolta"
        >

<?php if ($modoEdicao) { ?>

            <!--
                O TOKEN AGORA É O ID_JOGADA
            -->

            <input
                type="hidden"
                name="func"
                value="<?= $objfcn->base64(
                    $func['id_jogada'],
                    1
                ); ?>"
            >

            <button
                type="submit"
                class="btn btn-warning"
                name="btnAlterar"
            >
                Alterar Jogada
            </button>

            <a
                href="volta.php"
                class="btn btn-secondary"
            >
                Cancelar
            </a>

<?php } else { ?>

            <button
                type="submit"
                class="btn btn-primary"
                name="enviar"
            >
                Cadastrar
            </button>

<?php } ?>

        </form>

    </div>

</div>
</div>


<!-- =====================================================
     TABELA
====================================================== -->

<div class="turfe-table-wrap">
<div class="table-responsive mt-5">

<table class="table table-bordered table-striped">

<thead>

<tr>

    <th>Código</th>

    <th>ID Volta</th>

    <th>ID Jogada</th>

    <th>Status</th>

    <th>Usuário</th>

    <th>Total</th>

    <th>Comissão</th>

    <th>Total-Comissão</th>

    <th>Aposta-Comissão</th>

    <th>Vencedor</th>

    <th>Observação</th>

    <th>Clientes</th>

    <th>Cavalos</th>

    <th>Aposta</th>

    <th>Prêmio</th>

    <th>Editar</th>

    <th>Status</th>

    <th>Deletar</th>

</tr>

</thead>

<tbody>

<?php

if (!empty($voltas)) {

    foreach ($voltas as $rst) {

        $fechada =
            strtoupper(
                $rst['status_volta'] ?? 'ABERTA'
            ) === 'FECHADA';

        // -------------------------------------------------
        // TOKEN DA JOGADA
        // -------------------------------------------------

        $tokenJogada =
            $objfcn->base64(
                $rst['id_jogada'],
                1
            );

        // -------------------------------------------------
        // TOKEN DA VOLTA
        // -------------------------------------------------

        $tokenVolta =
            $objfcn->base64(
                $rst['id_volta'],
                1
            );

?>

<tr>

    <!-- CÓDIGO -->

    <td>

        <?= htmlspecialchars(
            $rst['codigo'] ?? '',
            ENT_QUOTES,
            'UTF-8'
        ); ?>

    </td>

    <!-- ID VOLTA -->

    <td>

        <?= (int)$rst['id_volta']; ?>

    </td>

    <!-- ID JOGADA -->

    <td>

        <?= (int)$rst['id_jogada']; ?>

    </td>

    <!-- STATUS -->

    <td>

<?php if ($fechada) { ?>

        <span class="status-fechada">
            FECHADA
        </span>

<?php } else { ?>

        <span class="status-aberta">
            ABERTA
        </span>

<?php } ?>

    </td>

    <!-- USUÁRIO -->

    <td>

        <?= htmlspecialchars(
            $rst['usuario_nome'] ?? '',
            ENT_QUOTES,
            'UTF-8'
        ); ?>

        <?php if (!empty($rst['usuario_email'])) { ?>

            <br>

            <small>
                <?= htmlspecialchars(
                    $rst['usuario_email'],
                    ENT_QUOTES,
                    'UTF-8'
                ); ?>
            </small>

        <?php } ?>

    </td>

    <!-- TOTAL -->

    <td>

        <?= number_format(
            (float)$rst['total'],
            2,
            ',',
            '.'
        ); ?>

    </td>

    <!-- COMISSÃO -->

    <td>

        <?= number_format(
            (float)$rst['comissao'],
            2,
            ',',
            '.'
        ); ?>%

    </td>

    <!-- TOTAL COMISSÃO -->

    <td>

        <?= number_format(
            (float)$rst['totalcomissao'],
            2,
            ',',
            '.'
        ); ?>

    </td>

    <!-- TOTAL APOSTA -->

    <td>

        <?= number_format(
            (float)$rst['totalaposta'],
            2,
            ',',
            '.'
        ); ?>

    </td>

    <!-- VENCEDOR -->

    <td>

<?php

if (
    isset($rst['vencedor']) &&
    $rst['vencedor'] !== null &&
    $rst['vencedor'] !== ''
) {

    echo 'Cavalo ' .
        (int)$rst['vencedor'];

}

?>

    </td>

    <!-- OBSERVAÇÃO -->

    <td>

        <?= htmlspecialchars(
            $rst['observacao'] ?? '',
            ENT_QUOTES,
            'UTF-8'
        ); ?>

    </td>

    <!-- CLIENTES -->

    <td>

<?php if (!empty($rst['clientes_ids'])) { ?>

        <strong>
            IDs:
        </strong>

        <?= htmlspecialchars(
            $rst['clientes_ids'],
            ENT_QUOTES,
            'UTF-8'
        ); ?>

        <br>

        <?= htmlspecialchars(
            $rst['clientes_nomes'] ?? '',
            ENT_QUOTES,
            'UTF-8'
        ); ?>

<?php } ?>

    </td>

    <!-- CAVALOS -->

    <td>

        <?= htmlspecialchars(
            $rst['cavalos_ids'] ?? '',
            ENT_QUOTES,
            'UTF-8'
        ); ?>

    </td>

    <!-- APOSTA -->

    <td>

        <?= number_format(
            (float)$rst['aposta'],
            2,
            ',',
            '.'
        ); ?>

    </td>

    <!-- PRÊMIO -->

    <td>

        <?= number_format(
            (float)$rst['premio'],
            2,
            ',',
            '.'
        ); ?>

    </td>

    <!-- EDITAR -->

    <td>

<?php if (!$fechada) { ?>

        <a
            class="btn btn-warning btn-sm btn-acao"
            href="?acao=edit&func=<?= $tokenJogada; ?>"
        >
            Editar
        </a>

<?php } else { ?>

        <button
            type="button"
            class="btn btn-secondary btn-sm"
            disabled
        >
            Fechada
        </button>

<?php } ?>

    </td>

    <!-- STATUS DA VOLTA -->

    <td>

<?php if (!$fechada) { ?>

        <a
            class="btn btn-success btn-sm btn-acao"
            href="?acao=fechar&func=<?= $tokenVolta; ?>"
            onclick="return confirm(
                'Deseja fechar toda a volta <?= (int)$rst['id_volta']; ?>?'
            );"
        >
            Fechar Volta
        </a>

<?php } else { ?>

        <a
            class="btn btn-primary btn-sm btn-acao"
            href="?acao=reabrir&func=<?= $tokenVolta; ?>"
            onclick="return confirm(
                'Deseja reabrir toda a volta <?= (int)$rst['id_volta']; ?>?'
            );"
        >
            Reabrir Volta
        </a>

<?php } ?>

    </td>

    <!-- DELETAR JOGADA -->

    <td>

<?php if (!$fechada) { ?>

        <a
            class="btn btn-danger btn-sm"
            href="?acao=delet&func=<?= $tokenJogada; ?>"
            onclick="return confirm(
                'Deseja excluir esta jogada?\\n\\nSomente esta jogada será excluída. Outras jogadas da mesma volta continuarão.'
            );"
        >
            Deletar Jogada
        </a>

<?php } else { ?>

        <button
            type="button"
            class="btn btn-secondary btn-sm"
            disabled
        >
            Fechada
        </button>

<?php } ?>

    </td>

</tr>

<?php

    }

} else {

?>

<tr>

    <td
        colspan="18"
        class="text-center"
    >
        Nenhuma volta cadastrada.
    </td>

</tr>

<?php

}

?>

</tbody>

</table>

</div>
</div>


<?php

require_once "Includes/rodape.php";

?>

</div>


<?php

require_once "Includes/javascript.php";

?>

<script
    src="https://code.jquery.com/jquery-3.7.1.min.js"
></script>

<script
    src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"
></script>

<script>

$(document).ready(function () {

    $('#nome').select2({

        placeholder:
            "Digite o nome do cliente...",

        allowClear: true,

        width: '100%'

    });

});

</script>

</body>

</html>
