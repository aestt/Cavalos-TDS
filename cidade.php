<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cadastro de cidades</title>
    <?php require_once "includes/bootstrap.php"; ?>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Barlow+Condensed:wght@500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>

/* =========================================================
   TOKENS — mesma paleta clara de dia de corrida do sistema
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

* {
    box-sizing: border-box;
}

body {

    background: var(--bg);

    font-family: var(--font-body);

    color: var(--ink);

}

.container {
    max-width: 860px;
}

/* =========================================================
   CABEÇALHO
========================================================= */

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

}

.turfe-hero-sm h1 {

    font-family: var(--font-display);

    font-weight: 600;

    font-size: 30px;

    margin: 0;

    color: #ffffff;

}

.turfe-hero-sm p {

    font-family: var(--font-body);

    color: rgba(255, 255, 255, 0.85);

    font-size: 14.5px;

    margin: 6px 0 0;

    max-width: 52ch;

}

/* =========================================================
   AVISOS
========================================================= */

.turfe-alert {

    margin-top: 20px;

    padding: 12px 16px;

    border-radius: 8px;

    font-size: 13.5px;

    font-weight: 600;

}

.turfe-alert.danger {
    background: var(--red-soft);
    border: 1px solid #f3b7bd;
    color: var(--red);
}

/* =========================================================
   CARD DE FORMULÁRIO
========================================================= */

.turfe-form-card {

    margin-top: 26px;

    background: var(--panel);

    border: 1px solid var(--line);

    border-top: 3px solid var(--gold);

    border-radius: 10px;

    box-shadow: var(--shadow);

    padding: 28px 30px 30px;

}

.turfe-form-card .form-eyebrow {

    font-family: var(--font-heading);

    font-size: 12.5px;

    letter-spacing: 0.05em;

    color: var(--grass-dark);

    background: var(--grass-soft);

    display: inline-block;

    padding: 4px 12px;

    border-radius: 100px;

    margin-bottom: 12px;

}

.turfe-form-card h2 {

    font-family: var(--font-display);

    font-weight: 600;

    font-size: 22px;

    color: var(--navy);

    margin: 0 0 20px;

}

.turfe-field {
    margin-bottom: 18px;
}

.turfe-field label,
.turfe-form-card .form-label {

    display: block;

    font-family: var(--font-heading);

    font-size: 12.5px;

    letter-spacing: 0.03em;

    color: var(--ink-dim);

    margin-bottom: 6px;

}

.turfe-field .form-control,
.turfe-form-card .form-control {

    width: 100%;

    padding: 10px 13px;

    border: 1px solid var(--line);

    border-radius: 8px;

    font-family: var(--font-body);

    font-size: 14.5px;

    color: var(--ink);

    background: #fbfcf9;

    transition: border-color 0.15s ease, box-shadow 0.15s ease;

}

.turfe-form-card .form-control:focus {

    outline: none;

    border-color: var(--grass);

    box-shadow: 0 0 0 3px rgba(12, 122, 62, 0.15);

    background: #ffffff;

}

.turfe-actions {

    display: flex;

    gap: 10px;

    margin-top: 6px;

}

.btn-turfe-primary {

    padding: 11px 22px;

    border: none;

    border-radius: 8px;

    background: var(--navy);

    color: #ffffff;

    font-family: var(--font-heading);

    font-size: 15px;

    letter-spacing: 0.02em;

    font-weight: 600;

    cursor: pointer;

    transition: background 0.15s ease;

}

.btn-turfe-primary:hover {
    background: var(--grass-dark);
    color: #ffffff;
}

.btn-turfe-warning {

    padding: 11px 22px;

    border: none;

    border-radius: 8px;

    background: var(--gold);

    color: var(--navy);

    font-family: var(--font-heading);

    font-size: 15px;

    letter-spacing: 0.02em;

    font-weight: 600;

    cursor: pointer;

    transition: background 0.15s ease;

}

.btn-turfe-warning:hover {
    background: var(--gold-deep);
    color: var(--navy);
}

.btn-turfe-secondary {

    padding: 11px 22px;

    border: 1px solid var(--line);

    border-radius: 8px;

    background: #ffffff;

    color: var(--ink-dim);

    font-family: var(--font-heading);

    font-size: 15px;

    letter-spacing: 0.02em;

    font-weight: 600;

    text-decoration: none;

    display: inline-flex;

    align-items: center;

    transition: background 0.15s ease;

}

.btn-turfe-secondary:hover {
    background: var(--grass-soft);
    color: var(--ink);
}

/* =========================================================
   TABELA
========================================================= */

.turfe-section-title {

    display: flex;

    align-items: baseline;

    gap: 14px;

    margin: 42px 0 16px;

}

.turfe-section-title h2 {

    font-family: var(--font-display);

    font-weight: 600;

    font-size: 24px;

    color: var(--navy);

    margin: 0;

    white-space: nowrap;

}

.turfe-section-title .rule {

    flex: 1;

    height: 1px;

    background: var(--line);

}

.turfe-section-title .count-tag {

    font-family: var(--font-heading);

    font-size: 12.5px;

    letter-spacing: 0.05em;

    color: var(--grass-dark);

    background: var(--grass-soft);

    padding: 3px 12px;

    border-radius: 100px;

    font-weight: 600;

    white-space: nowrap;

}

.turfe-table-wrap {

    border: 1px solid var(--line);

    border-radius: 10px;

    overflow: hidden;

    box-shadow: var(--shadow);

    background: var(--panel);

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

    padding: 12px 14px;

    white-space: nowrap;

}

table.turfe-table tbody td,
table.turfe-table tbody th {

    padding: 11px 14px;

    border-bottom: 1px solid var(--line);

    vertical-align: middle;

    font-weight: 400;

}

table.turfe-table tbody tr:nth-child(even) {
    background: #fafcf8;
}

table.turfe-table tbody tr:hover {
    background: var(--gold-soft);
}

.id-chip {

    display: inline-flex;

    align-items: center;

    justify-content: center;

    min-width: 30px;

    height: 26px;

    padding: 0 8px;

    border-radius: 100px;

    background: var(--grass-soft);

    color: var(--grass-dark);

    font-family: var(--font-heading);

    font-weight: 600;

    font-size: 13px;

}

.row-actions {

    display: flex;

    gap: 8px;

    white-space: nowrap;

}

.btn-chip {

    padding: 6px 13px;

    border-radius: 100px;

    font-family: var(--font-heading);

    font-size: 12.5px;

    letter-spacing: 0.02em;

    font-weight: 600;

    text-decoration: none;

    display: inline-block;

    border: none;

    cursor: pointer;

}

.btn-chip.editar {
    background: var(--gold-soft);
    color: var(--gold-deep);
    border: 1px solid var(--gold);
}

.btn-chip.editar:hover {
    background: var(--gold);
    color: var(--navy);
}

.btn-chip.excluir {
    background: var(--red-soft);
    color: var(--red);
    border: 1px solid #f3b7bd;
}

.btn-chip.excluir:hover {
    background: var(--red);
    color: #ffffff;
}

.turfe-empty-row {

    padding: 26px;

    text-align: center;

    color: var(--ink-dim);

    font-size: 14px;

}

@media (max-width: 600px) {

    .turfe-hero-sm {
        padding: 22px 22px;
    }

    .turfe-form-card {
        padding: 22px 20px 26px;
    }

}

    </style>
</head>

<body>
    <div class="container">
        <?php
          require_once "includes/menu.php";
          require_once "Controller/Cidade.php";
          require_once "Funcoes/Funcoes.php";
          require_once "Controller/Usuario.php";

          //Verifica sessao e usuario
          $usuario = new Usuario();
          session_start();

          if ($_SESSION['logado'] == "logar") {
              $usuario->verificaLogado($_SESSION['func']);
          } else {
              header("Location: index.php");
                  exit;
          }
        
          $cliente = new Cidade();
          $objfcn = new Funcoes();
          $func = [];
          $clientes = $cliente->selecionarCidade();

          // Selecionar
          if(isset($_POST['enviar'])){
            $_POST['id_user'] = $_SESSION['id'];
            if($cliente->gravarCidade($_POST) == 'ok'){
              header("Location: cidade.php");
            }else{
              echo "Não gravou";
            }
          }

          // Alterar
          if(isset($_POST["btnAlterar"])) {

            if($cliente->editarCliente($_POST) == "ok") {
              header("Location: cidade.php");
              exit;
            }else{
              echo "<div class='turfe-alert danger'>Erro ao alterar.</div>";
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
                  header("Location: cliente.php");
                  exit;
                }else{
                  echo "<div class='turfe-alert danger'>Erro ao excluir.</div>";
                }
                break;
            }
          }

        ?>

        <div class="turfe-hero-sm">

            <p class="rail">Sistema TDS &middot; cadastro</p>

            <h1>Cadastro de cidades</h1>

            <p>Gerencie as cidades disponíveis para vincular aos clientes do sistema.</p>

        </div>

        <div class="turfe-form-card">

          <span class="form-eyebrow">
            <?= (isset($_GET["acao"]) && $_GET["acao"] == "edit") ? "editando cidade" : "nova cidade"; ?>
          </span>

          <h2>
            <?= (isset($_GET["acao"]) && $_GET["acao"] == "edit") ? "Alterar cidade" : "Dados da cidade"; ?>
          </h2>

          <form action="" method="post">

              <div class="turfe-field">
                  <label for="exampleInputEmail1" class="form-label">Nome</label>
                  <input type="text" name="nome" class="form-control" value="<?= isset($func["nome"]) ? $func["nome"] : "" ?>">
              </div>

              <input
              type="hidden"
              name="func"
              value="<?= isset($func["id"]) ? $objfcn->base64($func["id"],1) : "" ?>">

              <div class="turfe-actions">

              <?php if (isset($_GET["acao"]) && $_GET["acao"] == "edit") { ?>

                <input
                    type="submit"
                    class="btn-turfe-warning"
                    name="btnAlterar"
                    value="Alterar">

                <a href="cidade.php" class="btn-turfe-secondary">Cancelar</a>

              <?php } else { ?>

                <input
                    type="submit"
                    class="btn-turfe-primary"
                    name="enviar"
                    value="Cadastrar">

              <?php } ?>

              </div>

          </form>

        </div>

        <div class="turfe-section-title">
          <h2>Cidades cadastradas</h2>
          <span class="rule"></span>
          <span class="count-tag"><?= count($clientes); ?> cidades</span>
        </div>

        <div class="turfe-table-wrap">

        <table class="turfe-table">
          <thead>
            <tr>
              <th scope="col">Código</th>
              <th scope="col">Nome</th>
              <th scope="col">Editar</th>
              <th scope="col">Deletar</th>
            </tr>
          </thead>
          <tbody>
          <?php

            if (!empty($clientes)) {

            foreach($clientes as $rst){

           ?>
            <tr>
              <th scope="row"><span class="id-chip">#<?php echo $rst['id']; ?></span></th>
              <td><?php echo $rst['nome']; ?></td>
              <td>
                <a
                  class="btn-chip editar"
                  href="?acao=edit&func=<?= $objfcn->base64($rst["id"],1); ?>">
                  Editar
                </a>
              </td>
              <td>
                <a
                  class="btn-chip excluir"
                  href="?acao=delet&func=<?= $objfcn->base64($rst["id"],1); ?>"
                  onclick="return confirm('Deseja realmente excluir essa cidade?')">

                  Excluir

                </a>

              </td>
            </tr>
            <?php
            }

            } else {
            ?>

            <tr>
              <td colspan="4" class="turfe-empty-row">Nenhuma cidade cadastrada ainda.</td>
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