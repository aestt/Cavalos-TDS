<?php

require_once "includes/bootstrap.php";
require_once "Controller/Cidade.php";
require_once "Funcoes/Funcoes.php";
require_once "Controller/Usuario.php";

session_start();

//Verifica sessao e usuario
$usuario = new Usuario();

if($_SESSION['logado'] == "logar"){
  $usuario->verificaLogado($_SESSION['func']);
  $mensagemSessao = "ok";
}else{
  $mensagemSessao = "Verificar sessao";
}


$cliente = new Cidade();
$objfcn = new Funcoes();
$func = [];
$clientes = $cliente->selecionarCidade();

$mensagemCidade = '';

// Selecionar
if(isset($_POST['enviar'])){
  
  $_POST['id_user'] = $_SESSION['id'];

  if($cliente->gravarCidade($_POST) == 'ok'){
    header("Location: cidade.php");
    exit;
  }else{
    $mensagemCidade = "Não gravou";
  }
}

// Alterar
if(isset($_POST["btnAlterar"])) {

  if($cliente->editarCliente($_POST) == "ok") {
    header("Location: cliente.php");
    exit;
  }else{
    $mensagemCidade = "<div class='alert alert-danger'>Erro ao alterar.</div>";
  }

}

// Ações + deletar
if(isset($_GET["acao"])) {
  switch($_GET["acao"]) {
    case "edit":
      $func = $cliente->selecionarId($_GET["func"]);
      break;
    case "delet":
      if($cliente->deletarCliente($_GET["func"]) == "ok") {
        header("Location: cidade.php");
        exit;
      }else{
        $mensagemCidade = "<div class='alert alert-danger'>Erro ao excluir.</div>";
      }
      break;
  }
}
?>
<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cadastro de Cidades</title>
    <?php require_once "includes/bootstrap.php"; ?>

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
    max-width: 860px;
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

.turfe-hero-sm h1 {

    font-family: var(--font-display);

    font-weight: 600;

    font-size: 30px;

    margin: 0;

    color: #ffffff;

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

    max-width: 52ch;

}

/* card do formulário — envolve o <form> original */

.turfe-form-card {

    margin-top: 26px;

    background: var(--panel);

    border: 1px solid var(--line);

    border-top: 3px solid var(--gold);

    border-radius: 10px;

    box-shadow: var(--shadow);

    padding: 28px 30px 30px;

}

.turfe-form-card .form-label {

    font-family: var(--font-heading);

    font-size: 12.5px;

    letter-spacing: 0.03em;

    color: var(--ink-dim);

}

.turfe-form-card .form-control {

    border: 1px solid var(--line);

    border-radius: 8px;

    font-family: var(--font-body);

    font-size: 14.5px;

    color: var(--ink);

    background: #fbfcf9;

    padding: 10px 13px;

}

.turfe-form-card .form-control:focus {

    border-color: var(--grass);

    box-shadow: 0 0 0 3px rgba(12, 122, 62, 0.15);

    background: #ffffff;

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

/* tabela — envolve o <table> original */

.turfe-table-wrap {

    margin-top: 36px;

    border: 1px solid var(--line);

    border-radius: 10px;

    overflow: hidden;

    box-shadow: var(--shadow);

    background: var(--panel);

}

.turfe-table-wrap .table {

    margin: 0;

    font-size: 14px;

    color: var(--ink);

}

.turfe-table-wrap .table thead th {

    background: var(--navy);

    color: #ffffff;

    font-family: var(--font-heading);

    font-weight: 600;

    letter-spacing: 0.02em;

    font-size: 12.5px;

    border: none;

    padding: 12px 14px;

}

.turfe-table-wrap .table tbody th,
.turfe-table-wrap .table tbody td {

    padding: 11px 14px;

    border-bottom: 1px solid var(--line);

    border-top: none;

    vertical-align: middle;

}

.turfe-table-wrap .table tbody tr:nth-child(even) {
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

    </style>
</head>

<body>
    <div class="container">
        <?php
          require_once "includes/menu.php";

          echo $mensagemSessao;

          if (!empty($mensagemCidade)) {
              echo $mensagemCidade;
          }
        ?>
        <div class="turfe-hero-sm">
            <span class="rail">Sistema TDS &middot; cadastro</span>
            <h1>Cadastro de Cidade:</h1>
        </div>

        <div class="turfe-form-card">
        <form action="" method="post">
            <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">Nome</label>
                <input type="text" name="nome" class="form-control" value="<?= isset($func["nome"]) ? $func["nome"] : "" ?>">
            </div>

           
            <input
            type="hidden"
            name="func"
            value="<?= isset($func["id"]) ? $objfcn->base64($func["id"],1) : "" ?>">

            <?php if (isset($_GET["acao"]) && $_GET["acao"] == "edit") { ?>

              <input
                  type="submit"
                  class="btn btn-warning"
                  name="btnAlterar"
                  value="Alterar">

              <a href="cidade.php" class="btn btn-secondary">Cancelar</a>

            <?php } else { ?>

              <input
                  type="submit"
                  class="btn btn-primary"
                  name="enviar"
                  value="Cadastrar">

            <?php } ?>

        </form>
        </div>

        <div class="turfe-table-wrap">
        <table class="table">
          <thead>
            <tr>
              <th scope="col">Código</th>
              <th scope="col">Nome</th>
              <th scope="col">Deletar</th>
              <th scope="col">Editar</th>
            </tr>
          </thead>
          <tbody>
          <?php

            foreach($clientes as $rst){

           ?>
            <tr>
              <th scope="row"><?php echo $rst['id']; ?></th>
              <td><?php echo $rst['nome']; ?></td>
              <td>
                <a
                  class="btn btn-warning btn-sm"
                  href="?acao=edit&func=<?= $objfcn->base64($rst["id"],1); ?>">
                  Editar
                </a>
              </td>
              <td>
                <a
                  class="btn btn-danger btn-sm"
                  href="?acao=delet&func=<?= $objfcn->base64($rst["id"],1); ?>"
                  onclick="return confirm('Deseja realmente excluir este cliente?')">

                  Excluir

                </a>

              </td>
            </tr>
            <?php
            }

            ?>
          </tbody>
        </table>
        </div>

        <?php require_once "includes/rodape.php"; ?>
    </div>

    <?php require_once "includes/javascript.php"; ?>
</body>

</html>
