<!doctype html>

<html lang="pt-br">

<head>

    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>Dashboard - Sistema TDS</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Barlow+Condensed:wght@500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<?php

ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');
//error_reporting(E_ALL);


// =========================================================
// SESSÃO
// =========================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// =========================================================
// INCLUDES
// =========================================================

require_once "Includes/bootstrap.php";
require_once "Conexao/Conexao.php";
require_once "Controller/Usuario.php";

// =========================================================
// LOGIN
// =========================================================

$usuario = new Usuario();
session_start();

if ($_SESSION['logado'] == "logar") {
    $usuario->verificaLogado($_SESSION['func']);
} else {
     header("Location: index.php");
        exit;
}


// =========================================================
// CONEXÃO
// =========================================================

$conexao = new Conexao();

$con = $conexao->conectar();

$id_user = (int)$_SESSION['id'];


// =========================================================
// VOLTAS ABERTAS
// =========================================================

$sqlAbertas = $con->prepare("

    SELECT

        v.id_volta,

        MIN(v.codigo) AS codigo,

        MIN(v.status_volta) AS status_volta,

        MIN(v.vencedor) AS vencedor,

        COUNT(DISTINCT v.id_jogada) AS total_jogadas,

        COUNT(*) AS total_apostas,

        COUNT(DISTINCT v.cliente) AS total_jogadores,

        COALESCE(SUM(v.aposta), 0) AS total_apostado,

        COALESCE(SUM(v.premio), 0) AS total_premio,

        GROUP_CONCAT(
            DISTINCT v.cavalo
            ORDER BY v.cavalo
            SEPARATOR ', '
        ) AS cavalos

    FROM volta v

    WHERE v.id_user = :id_user

    AND v.status_volta = 'ABERTA'

    GROUP BY v.id_volta

    ORDER BY v.id_volta DESC

");

$sqlAbertas->bindValue(
    ':id_user',
    $id_user,
    PDO::PARAM_INT
);

$sqlAbertas->execute();

$voltasAbertas =
    $sqlAbertas->fetchAll(PDO::FETCH_ASSOC);


// =========================================================
// VOLTAS FECHADAS
// =========================================================

$sqlFechadas = $con->prepare("

    SELECT

        v.id_volta,

        MIN(v.codigo) AS codigo,

        MIN(v.status_volta) AS status_volta,

        MIN(v.vencedor) AS vencedor,

        COUNT(DISTINCT v.id_jogada) AS total_jogadas,

        COUNT(*) AS total_apostas,

        COUNT(DISTINCT v.cliente) AS total_jogadores,

        COALESCE(SUM(v.aposta), 0) AS total_apostado,

        COALESCE(SUM(v.premio), 0) AS total_premio,

        COALESCE(SUM(v.totalcomissao), 0) AS total_comissao,

        GROUP_CONCAT(
            DISTINCT v.cavalo
            ORDER BY v.cavalo
            SEPARATOR ', '
        ) AS cavalos

    FROM volta v

    WHERE v.id_user = :id_user

    AND v.status_volta = 'FECHADA'

    GROUP BY v.id_volta

    ORDER BY v.id_volta DESC

");

$sqlFechadas->bindValue(
    ':id_user',
    $id_user,
    PDO::PARAM_INT
);

$sqlFechadas->execute();

$voltasFechadas =
    $sqlFechadas->fetchAll(PDO::FETCH_ASSOC);


// =========================================================
// DETALHES DE TODAS AS VOLTAS
// =========================================================

$sqlDetalhes = $con->prepare("

    SELECT

        v.id,

        v.id_volta,

        v.id_jogada,

        v.cliente,

        v.cavalo,

        v.aposta,

        v.premio,

        v.totalcomissao,

        v.totalaposta,

        v.vencedor,

        v.observacao,

        v.status_volta,

        c.nome AS cliente_nome,

        c.email AS cliente_email

    FROM volta v

    LEFT JOIN usuario c
        ON c.id = v.cliente

    WHERE v.id_user = :id_user

    ORDER BY

        v.id_volta DESC,

        v.id_jogada ASC,

        v.id ASC

");

$sqlDetalhes->bindValue(
    ':id_user',
    $id_user,
    PDO::PARAM_INT
);

$sqlDetalhes->execute();

$detalhes =
    $sqlDetalhes->fetchAll(PDO::FETCH_ASSOC);


// =========================================================
// ORGANIZA DETALHES
// =========================================================

$detalhesPorVolta = [];

$jogadoresPorVolta = [];

foreach ($detalhes as $item) {

    $idVolta =
        (int)$item['id_volta'];


    // -------------------------------------------------------
    // DETALHES
    // -------------------------------------------------------

    if (
        !isset(
            $detalhesPorVolta[$idVolta]
        )
    ) {

        $detalhesPorVolta[$idVolta] = [];
    }

    $detalhesPorVolta[$idVolta][] =
        $item;


    // -------------------------------------------------------
    // JOGADORES
    // -------------------------------------------------------

    $idCliente =
        (int)$item['cliente'];

    if ($idCliente <= 0) {
        continue;
    }


    if (
        !isset(
            $jogadoresPorVolta[$idVolta]
        )
    ) {

        $jogadoresPorVolta[$idVolta] = [];
    }


    if (
        !isset(
            $jogadoresPorVolta[$idVolta][$idCliente]
        )
    ) {

        $jogadoresPorVolta[$idVolta][$idCliente] = [

            'id' =>
                $idCliente,

            'nome' =>
                $item['cliente_nome'],

            'email' =>
                $item['cliente_email'],

            'apostas' =>
                0,

            'total_apostado' =>
                0,

            'total_premio' =>
                0
        ];
    }


    $jogadoresPorVolta[$idVolta][$idCliente]['apostas']++;

    $jogadoresPorVolta[$idVolta][$idCliente]['total_apostado']
        += (float)$item['aposta'];

    $jogadoresPorVolta[$idVolta][$idCliente]['total_premio']
        += (float)$item['premio'];
}


// =========================================================
// TOTALIZADORES
// =========================================================

// ABERTAS

$totalAbertas = count($voltasAbertas);

$totalJogadasAbertas = 0;

$totalApostasAbertas = 0;

$totalJogadoresAbertas = 0;

$totalApostadoAbertas = 0;

$totalPremioAbertas = 0;


foreach ($voltasAbertas as $volta) {

    $totalJogadasAbertas +=
        (int)$volta['total_jogadas'];

    $totalApostasAbertas +=
        (int)$volta['total_apostas'];

    $totalJogadoresAbertas +=
        (int)$volta['total_jogadores'];

    $totalApostadoAbertas +=
        (float)$volta['total_apostado'];

    $totalPremioAbertas +=
        (float)$volta['total_premio'];
}


// FECHADAS

$totalFechadas = count($voltasFechadas);

$totalJogadasFechadas = 0;

$totalApostasFechadas = 0;

$totalJogadoresFechadas = 0;

$totalApostadoFechadas = 0;

$totalPremioFechadas = 0;


foreach ($voltasFechadas as $volta) {

    $totalJogadasFechadas +=
        (int)$volta['total_jogadas'];

    $totalApostasFechadas +=
        (int)$volta['total_apostas'];

    $totalJogadoresFechadas +=
        (int)$volta['total_jogadores'];

    $totalApostadoFechadas +=
        (float)$volta['total_apostado'];

    $totalPremioFechadas +=
        (float)$volta['total_premio'];
}


// =========================================================
// MENU
// =========================================================

require_once "Includes/menu.php";

?>

<style>

/* =========================================================
   TOKENS — Turfe / dia de corrida
   Paleta clara e vibrante: grama, placa dourada de premiação,
   azul de bandeira de largada. Nada de tema escuro.
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
    --line:         #e1e8dd;
    --shadow:       0 6px 20px rgba(20, 60, 35, 0.09);

    --font-display: 'Fraunces', Georgia, serif;
    --font-heading: 'Barlow Condensed', Arial, sans-serif;
    --font-body:    'Inter', Arial, sans-serif;

}

* {
    box-sizing: border-box;
}

body {

    background: var(--bg);

    font-family: var(--font-body);

    color: var(--ink);

}

.container {
    max-width: 1180px;
}

/* =========================================================
   HERO
========================================================= */

.turfe-hero {

    position: relative;

    margin-top: 28px;

    padding: 36px 40px;

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

.turfe-hero::after {

    content: "";

    position: absolute;

    right: -40px;

    bottom: -60px;

    width: 220px;

    height: 220px;

    border-radius: 50%;

    background: rgba(224, 164, 23, 0.18);

}

.turfe-hero .rail {

    font-family: var(--font-heading);

    letter-spacing: 0.1em;

    color: var(--gold-soft);

    font-size: 13px;

    margin: 0 0 6px;

    position: relative;

}

.turfe-hero h1 {

    font-family: var(--font-display);

    font-weight: 600;

    font-size: 42px;

    margin: 0;

    color: #ffffff;

    position: relative;

}

.turfe-hero p.subtitle {

    font-family: var(--font-body);

    color: rgba(255, 255, 255, 0.85);

    font-size: 16px;

    margin: 8px 0 0;

    max-width: 46ch;

    position: relative;

}

/* =========================================================
   SEÇÕES
========================================================= */

.turfe-section-title {

    display: flex;

    align-items: baseline;

    gap: 14px;

    margin: 46px 0 4px;

}

.turfe-section-title h2 {

    font-family: var(--font-display);

    font-weight: 600;

    font-size: 26px;

    color: var(--navy);

    margin: 0;

    white-space: nowrap;

}

.turfe-section-title .flag {

    font-family: var(--font-heading);

    font-size: 13px;

    letter-spacing: 0.06em;

    padding: 3px 12px;

    border-radius: 100px;

    font-weight: 600;

}

.flag.open {
    color: var(--grass-dark);
    background: var(--grass-soft);
}

.flag.closed {
    color: var(--red);
    background: #fbe4e6;
}

.turfe-section-title .rule {

    flex: 1;

    height: 1px;

    background: var(--line);

}

.turfe-section-lead {

    color: var(--ink-dim);

    font-size: 14px;

    margin: 0 0 18px;

}

/* =========================================================
   PLACAR / TOTE BOARD
========================================================= */

.tote-board {

    display: grid;

    grid-template-columns: repeat(6, 1fr);

    gap: 14px;

    margin-top: 14px;

}

.tote-cell {

    background: var(--panel);

    border: 1px solid var(--line);

    border-top: 3px solid var(--gold);

    border-radius: 8px;

    padding: 18px 16px;

    box-shadow: var(--shadow);

}

.tote-label {

    font-family: var(--font-heading);

    font-size: 12.5px;

    color: var(--ink-dim);

    letter-spacing: 0.02em;

}

.tote-value {

    font-family: var(--font-display);

    font-size: 27px;

    font-weight: 600;

    color: var(--navy);

    margin-top: 4px;

    font-variant-numeric: tabular-nums;

}

.tote-value .cifra {

    font-size: 16px;

    color: var(--gold-deep);

    margin-right: 2px;

}

/* =========================================================
   CARD DE VOLTA (PROGRAMA DE CORRIDA)
========================================================= */

.volta-card {

    position: relative;

    margin-top: 22px;

    border: 1px solid var(--line);

    border-radius: 10px;

    background: var(--panel);

    box-shadow: var(--shadow);

    overflow: hidden;

}

.volta-card::before {

    content: "";

    position: absolute;

    left: 0;

    top: 0;

    bottom: 0;

    width: 5px;

}

.volta-card.is-aberta::before {
    background: var(--grass);
}

.volta-card.is-fechada::before {
    background: var(--gold);
}

.volta-plate {

    padding: 18px 24px 18px 30px;

    border-bottom: 1px solid var(--line);

    background: var(--grass-soft);

    display: flex;

    flex-wrap: wrap;

    align-items: center;

    gap: 26px;

}

.is-fechada .volta-plate {
    background: var(--gold-soft);
}

.volta-plate .post {

    width: 46px;

    height: 46px;

    border-radius: 50%;

    background: var(--navy);

    color: #ffffff;

    display: flex;

    align-items: center;

    justify-content: center;

    font-family: var(--font-display);

    font-weight: 700;

    font-size: 18px;

    flex-shrink: 0;

}

.volta-plate .meta-item {

    display: flex;

    flex-direction: column;

}

.volta-plate .meta-item .k {

    font-family: var(--font-heading);

    font-size: 11.5px;

    color: var(--ink-dim);

    letter-spacing: 0.04em;

}

.volta-plate .meta-item .v {

    font-family: var(--font-body);

    font-weight: 600;

    color: var(--navy);

    font-size: 15px;

}

.volta-plate .status-pill {

    margin-left: auto;

    font-family: var(--font-heading);

    font-size: 12.5px;

    letter-spacing: 0.05em;

    padding: 5px 14px;

    border-radius: 100px;

    font-weight: 600;

}

.status-pill.aberta {

    color: #ffffff;

    background: var(--grass);

}

.status-pill.fechada {

    color: var(--navy);

    background: var(--gold);

}

.silk {

    width: 26px;

    height: 26px;

    border-radius: 50%;

    display: flex;

    align-items: center;

    justify-content: center;

    font-family: var(--font-heading);

    font-weight: 700;

    font-size: 12px;

    color: #fff;

    border: 2px solid rgba(255,255,255,0.8);

    box-shadow: 0 1px 3px rgba(0,0,0,0.25);

}

.volta-body {

    padding: 20px 24px 24px 30px;

}

.volta-stats {

    display: grid;

    grid-template-columns: repeat(4, 1fr);

    gap: 16px;

    margin-bottom: 4px;

}

.volta-stats .stat .k {

    font-family: var(--font-heading);

    font-size: 11.5px;

    color: var(--ink-dim);

    letter-spacing: 0.04em;

}

.volta-stats .stat .v {

    font-family: var(--font-display);

    font-size: 21px;

    font-weight: 600;

    color: var(--navy);

    margin-top: 2px;

}

/* =========================================================
   TABELAS
========================================================= */

.turfe-table-wrap {

    margin-top: 14px;

    border: 1px solid var(--line);

    border-radius: 8px;

    overflow: hidden;

}

table.turfe-table {

    width: 100%;

    border-collapse: collapse;

    font-size: 14px;

    margin: 0;

    color: var(--ink);

}

table.turfe-table thead th {

    background: var(--navy);

    color: #ffffff;

    font-family: var(--font-heading);

    font-weight: 600;

    letter-spacing: 0.02em;

    font-size: 12.5px;

    text-align: left;

    padding: 10px 14px;

}

table.turfe-table tbody td {

    padding: 10px 14px;

    border-bottom: 1px solid var(--line);

    vertical-align: middle;

}

table.turfe-table tbody tr:nth-child(even) {
    background: #fafcf8;
}

table.turfe-table tbody tr:hover {
    background: var(--gold-soft);
}

.vencedor-tag {

    font-family: var(--font-heading);

    font-size: 12.5px;

    color: var(--navy);

    background: var(--gold);

    padding: 3px 9px;

    border-radius: 100px;

    font-weight: 600;

}

.turfe-subheading {

    font-family: var(--font-display);

    font-weight: 600;

    font-size: 19px;

    color: var(--navy);

    margin: 26px 0 4px;

    display: flex;

    align-items: center;

    gap: 10px;

}

.turfe-subheading::before {

    content: "";

    width: 8px;

    height: 8px;

    border-radius: 50%;

    background: var(--grass);

    display: inline-block;

}

.observacao-box {

    margin-top: 16px;

    padding: 12px 16px;

    border: 1px solid var(--line);

    border-left: 3px solid var(--gold);

    background: var(--gold-soft);

    border-radius: 6px;

    font-size: 14px;

    color: var(--ink);

}

.turfe-empty {

    margin-top: 14px;

    padding: 18px 22px;

    border: 1px dashed var(--line);

    border-radius: 8px;

    background: var(--panel);

    color: var(--ink-dim);

    font-size: 14px;

}

@media (max-width: 900px) {

    .tote-board {
        grid-template-columns: repeat(3, 1fr);
    }

    .volta-stats {
        grid-template-columns: repeat(2, 1fr);
    }

    .turfe-hero {
        padding: 26px 22px;
    }

    .turfe-hero h1 {
        font-size: 32px;
    }

}

</style>


<div class="container mt-4">

    <div class="turfe-hero">

        <p class="rail">Sistema TDS &middot; Painel do Organizador</p>

        <h1>Central de apuração das voltas</h1>

        <p class="subtitle">
            Acompanhe as voltas em andamento, feche os páreos e confira o
            resultado de cada aposta, jogador e prêmio pago.
        </p>

    </div>


    <!-- =====================================================
         RESUMO ABERTAS
    ====================================================== -->

    <div class="turfe-section-title">

        <h2>Voltas abertas</h2>

        <span class="flag open">apostas em andamento</span>

        <span class="rule"></span>

    </div>

    <p class="turfe-section-lead">
        Páreos ainda não encerrados, com apostas sendo recebidas neste momento.
    </p>

    <div class="tote-board">

        <div class="tote-cell">
            <div class="tote-label">Voltas abertas</div>
            <div class="tote-value"><?= $totalAbertas; ?></div>
        </div>

        <div class="tote-cell">
            <div class="tote-label">Jogadas</div>
            <div class="tote-value"><?= $totalJogadasAbertas; ?></div>
        </div>

        <div class="tote-cell">
            <div class="tote-label">Jogadores</div>
            <div class="tote-value"><?= $totalJogadoresAbertas; ?></div>
        </div>

        <div class="tote-cell">
            <div class="tote-label">Apostas</div>
            <div class="tote-value"><?= $totalApostasAbertas; ?></div>
        </div>

        <div class="tote-cell">
            <div class="tote-label">Total apostado</div>
            <div class="tote-value">
                <span class="cifra">R$</span><?= number_format(
                    $totalApostadoAbertas,
                    2,
                    ',',
                    '.'
                ); ?>
            </div>
        </div>

        <div class="tote-cell">
            <div class="tote-label">Prêmios</div>
            <div class="tote-value">
                <span class="cifra">R$</span><?= number_format(
                    $totalPremioAbertas,
                    2,
                    ',',
                    '.'
                ); ?>
            </div>
        </div>

    </div>


    <!-- =====================================================
         LISTA VOLTAS ABERTAS
    ====================================================== -->

<?php if (!empty($voltasAbertas)): ?>

<?php foreach ($voltasAbertas as $volta): ?>

<?php

$idVolta =
    (int)$volta['id_volta'];

$detalhesVolta =
    $detalhesPorVolta[$idVolta] ?? [];

?>

<div class="volta-card is-aberta">

    <div class="volta-plate">

        <div class="post">#<?= $idVolta; ?></div>

        <div class="meta-item">
            <span class="k">Código</span>
            <span class="v"><?= htmlspecialchars(
                $volta['codigo'] ?? '',
                ENT_QUOTES,
                'UTF-8'
            ); ?></span>
        </div>

        <div class="meta-item">
            <span class="k">Cavalos</span>
            <span class="v"><?= htmlspecialchars(
                $volta['cavalos'] ?? '',
                ENT_QUOTES,
                'UTF-8'
            ); ?></span>
        </div>

        <span class="status-pill aberta">aberta</span>

    </div>

    <div class="volta-body">

        <div class="volta-stats">

            <div class="stat">
                <div class="k">Jogadas</div>
                <div class="v"><?= (int)$volta['total_jogadas']; ?></div>
            </div>

            <div class="stat">
                <div class="k">Jogadores</div>
                <div class="v"><?= (int)$volta['total_jogadores']; ?></div>
            </div>

            <div class="stat">
                <div class="k">Apostas</div>
                <div class="v"><?= (int)$volta['total_apostas']; ?></div>
            </div>

            <div class="stat">
                <div class="k">Total apostado</div>
                <div class="v">R$ <?= number_format(
                    (float)$volta['total_apostado'],
                    2,
                    ',',
                    '.'
                ); ?></div>
            </div>

        </div>

    </div>

</div>

<?php endforeach; ?>

<?php else: ?>

<div class="turfe-empty">
    Nenhuma volta aberta no momento.
</div>

<?php endif; ?>


<!-- =====================================================
     VOLTAS FECHADAS
====================================================== -->

<div class="turfe-section-title">

    <h2>Torneios / voltas fechadas</h2>

    <span class="flag closed">resultado apurado</span>

    <span class="rule"></span>

</div>

<p class="turfe-section-lead">
    Páreos já encerrados, com vencedor definido e prêmios calculados.
</p>

<div class="tote-board">

    <div class="tote-cell">
        <div class="tote-label">Voltas fechadas</div>
        <div class="tote-value"><?= $totalFechadas; ?></div>
    </div>

    <div class="tote-cell">
        <div class="tote-label">Jogadas</div>
        <div class="tote-value"><?= $totalJogadasFechadas; ?></div>
    </div>

    <div class="tote-cell">
        <div class="tote-label">Jogadores</div>
        <div class="tote-value"><?= $totalJogadoresFechadas; ?></div>
    </div>

    <div class="tote-cell">
        <div class="tote-label">Apostas</div>
        <div class="tote-value"><?= $totalApostasFechadas; ?></div>
    </div>

    <div class="tote-cell">
        <div class="tote-label">Total apostado</div>
        <div class="tote-value">
            <span class="cifra">R$</span><?= number_format(
                $totalApostadoFechadas,
                2,
                ',',
                '.'
            ); ?>
        </div>
    </div>

    <div class="tote-cell">
        <div class="tote-label">Total prêmios</div>
        <div class="tote-value">
            <span class="cifra">R$</span><?= number_format(
                $totalPremioFechadas,
                2,
                ',',
                '.'
            ); ?>
        </div>
    </div>

</div>


<!-- =====================================================
     RELATÓRIO FECHADAS
====================================================== -->

<?php if (!empty($voltasFechadas)): ?>

<?php foreach ($voltasFechadas as $volta): ?>

<?php

$idVolta =
    (int)$volta['id_volta'];

$jogadores =
    $jogadoresPorVolta[$idVolta] ?? [];

$detalhesVolta =
    $detalhesPorVolta[$idVolta] ?? [];

// paleta tradicional de sedas de páreo, usada só para colorir
// visualmente o número do cavalo — não altera nenhum dado.
$paletaSedas = [
    '#a3242f', '#f4ecd8', '#1d4e89', '#e0a638',
    '#2f6b3a', '#161616', '#d9731c', '#c65a94',
    '#2a9d8f', '#6a3fa0', '#8a8d91', '#7fb238'
];

?>

<div class="volta-card is-fechada">

    <!-- =================================================
         CABEÇALHO
    ================================================== -->

    <div class="volta-plate">

        <div class="post">#<?= $idVolta; ?></div>

        <div class="meta-item">
            <span class="k">Código</span>
            <span class="v"><?= htmlspecialchars(
                $volta['codigo'] ?? '',
                ENT_QUOTES,
                'UTF-8'
            ); ?></span>
        </div>

        <div class="meta-item">
            <span class="k">Vencedor</span>
            <span class="v">
<?php if (
    $volta['vencedor'] !== null &&
    $volta['vencedor'] !== ''
): ?>
                Cavalo <?= (int)$volta['vencedor']; ?>
<?php else: ?>
                Não informado
<?php endif; ?>
            </span>
        </div>

        <span class="status-pill fechada">fechada</span>

    </div>


    <div class="volta-body">

        <!-- =================================================
             RESUMO
        ================================================== -->

        <div class="volta-stats">

            <div class="stat">
                <div class="k">Jogadas</div>
                <div class="v"><?= (int)$volta['total_jogadas']; ?></div>
            </div>

            <div class="stat">
                <div class="k">Jogadores</div>
                <div class="v"><?= (int)$volta['total_jogadores']; ?></div>
            </div>

            <div class="stat">
                <div class="k">Apostas</div>
                <div class="v"><?= (int)$volta['total_apostas']; ?></div>
            </div>

            <div class="stat">
                <div class="k">Total apostado</div>
                <div class="v">R$ <?= number_format(
                    (float)$volta['total_apostado'],
                    2,
                    ',',
                    '.'
                ); ?></div>
            </div>

        </div>

        <div class="volta-stats" style="margin-top: 12px;">

            <div class="stat">
                <div class="k">Total prêmios</div>
                <div class="v">R$ <?= number_format(
                    (float)$volta['total_premio'],
                    2,
                    ',',
                    '.'
                ); ?></div>
            </div>

        </div>


        <!-- =================================================
             JOGADORES
        ================================================== -->

        <div class="turfe-subheading">Jogadores da rodada</div>


        <div class="turfe-table-wrap">

            <table class="turfe-table">

                <thead>

                    <tr>

                        <th>ID</th>

                        <th>Jogador</th>

                        <th>E-mail</th>

                        <th>Apostas</th>

                        <th>Total apostado</th>

                        <th>Total prêmio</th>

                    </tr>

                </thead>

                <tbody>

<?php if (!empty($jogadores)): ?>

<?php foreach ($jogadores as $jogador): ?>

                    <tr>

                        <td><?= (int)$jogador['id']; ?></td>

                        <td><?= htmlspecialchars(
                            $jogador['nome'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?></td>

                        <td><?= htmlspecialchars(
                            $jogador['email'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?></td>

                        <td><?= (int)$jogador['apostas']; ?></td>

                        <td>R$ <?= number_format(
                            $jogador['total_apostado'],
                            2,
                            ',',
                            '.'
                        ); ?></td>

                        <td>R$ <?= number_format(
                            $jogador['total_premio'],
                            2,
                            ',',
                            '.'
                        ); ?></td>

                    </tr>

<?php endforeach; ?>

<?php else: ?>

                    <tr>

                        <td colspan="6" class="text-center">
                            Nenhum jogador encontrado.
                        </td>

                    </tr>

<?php endif; ?>

                </tbody>

            </table>

        </div>


        <!-- =================================================
             APOSTAS
        ================================================== -->

        <div class="turfe-subheading">Apostas da rodada</div>


        <div class="turfe-table-wrap">

            <table class="turfe-table">

                <thead>

                    <tr>

                        <th>Jogada</th>

                        <th>Jogador</th>

                        <th>Cavalo</th>

                        <th>Aposta</th>

                        <th>Comissão</th>

                        <th>Líquido</th>

                        <th>Prêmio</th>

                        <th>Vencedor</th>

                    </tr>

                </thead>

                <tbody>

<?php if (!empty($detalhesVolta)): ?>

<?php foreach ($detalhesVolta as $item): ?>

                    <tr>

                        <td><?= (int)$item['id_jogada']; ?></td>

                        <td><?= htmlspecialchars(
                            $item['cliente_nome'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?></td>

                        <td>

<?php if (
    $item['cavalo'] !== null &&
    $item['cavalo'] !== ''
): ?>

<?php

$corSeda =
    $paletaSedas[
        ((int)$item['cavalo'] - 1) % count($paletaSedas)
    ];

?>

                            <span
                                class="silk"
                                style="background: <?= $corSeda; ?>;"
                            ><?= (int)$item['cavalo']; ?></span>

<?php else: ?>

                            -

<?php endif; ?>

                        </td>

                        <td>R$ <?= number_format(
                            (float)$item['aposta'],
                            2,
                            ',',
                            '.'
                        ); ?></td>

                        <td>R$ <?= number_format(
                            (float)$item['totalcomissao'],
                            2,
                            ',',
                            '.'
                        ); ?></td>

                        <td>R$ <?= number_format(
                            (float)$item['totalaposta'],
                            2,
                            ',',
                            '.'
                        ); ?></td>

                        <td>R$ <?= number_format(
                            (float)$item['premio'],
                            2,
                            ',',
                            '.'
                        ); ?></td>

                        <td>

<?php if (
    $item['vencedor'] !== null &&
    $item['vencedor'] !== ''
): ?>

                            <span class="vencedor-tag">Cavalo <?= (int)$item['vencedor']; ?></span>

<?php else: ?>

                            -

<?php endif; ?>

                        </td>

                    </tr>

<?php endforeach; ?>

<?php else: ?>

                    <tr>

                        <td colspan="8" class="text-center">
                            Nenhuma aposta encontrada.
                        </td>

                    </tr>

<?php endif; ?>

                </tbody>

            </table>

        </div>


<?php if (!empty($volta['observacao'])): ?>

        <div class="observacao-box">
            <strong>Observação:</strong>
            <?= htmlspecialchars(
                $volta['observacao'],
                ENT_QUOTES,
                'UTF-8'
            ); ?>
        </div>

<?php endif; ?>

    </div>

</div>

<?php endforeach; ?>

<?php else: ?>

<div class="turfe-empty">
    Nenhuma volta fechada encontrada.
</div>

<?php endif; ?>


</div>


<?php

require_once "Includes/rodape.php";
require_once "Includes/javascript.php";

?>

</body>

</html>
