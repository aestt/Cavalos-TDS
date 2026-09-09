<!doctype html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Entrar - Sistema TDS</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Barlow+Condensed:wght@500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <?php
      require_once "Includes/bootstrap.php";
      require_once "Controller/Usuario.php";
      require_once "Funcoes/Funcoes.php";

      $usuario = new Usuario();

      if(isset($_POST['enviar'])){
        $usuario->logarUsuario($_POST);
      }else{
    
      }

      ?>

  <style>

/* =========================================================
   TOKENS — mesma paleta clara de dia de corrida do dashboard
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
    --shadow:       0 10px 30px rgba(20, 60, 35, 0.12);

    --font-display: 'Fraunces', Georgia, serif;
    --font-heading: 'Barlow Condensed', Arial, sans-serif;
    --font-body:    'Inter', Arial, sans-serif;

}

* {
    box-sizing: border-box;
}

html, body {
    height: 100%;
}

body {

    margin: 0;

    min-height: 100vh;

    display: flex;

    align-items: center;

    justify-content: center;

    padding: 24px;

    font-family: var(--font-body);

    color: var(--ink);

    background:
        radial-gradient(circle at 12% 15%, rgba(224, 164, 23, 0.16) 0%, transparent 45%),
        radial-gradient(circle at 88% 85%, rgba(12, 122, 62, 0.14) 0%, transparent 45%),
        var(--bg);

}

/* =========================================================
   CARTAZ / PAINEL DE LARGADA
========================================================= */

.login-wrap {

    width: 100%;

    max-width: 900px;

    display: grid;

    grid-template-columns: 1.05fr 1fr;

    border-radius: 14px;

    overflow: hidden;

    box-shadow: var(--shadow);

    background: var(--panel);

}

.login-poster {

    position: relative;

    padding: 44px 38px;

    background:
        repeating-linear-gradient(
            135deg,
            rgba(255, 255, 255, 0.06) 0px,
            rgba(255, 255, 255, 0.06) 14px,
            transparent 14px,
            transparent 28px
        ),
        linear-gradient(150deg, var(--grass) 0%, var(--grass-dark) 100%);

    color: #ffffff;

    display: flex;

    flex-direction: column;

    justify-content: space-between;

}

.login-poster::after {

    content: "";

    position: absolute;

    right: -50px;

    bottom: -70px;

    width: 220px;

    height: 220px;

    border-radius: 50%;

    background: rgba(224, 164, 23, 0.2);

}

.login-poster .brand {

    position: relative;

    display: flex;

    align-items: center;

    gap: 10px;

    font-family: var(--font-heading);

    letter-spacing: 0.08em;

    font-size: 13px;

    color: var(--gold-soft);

}

.login-poster .brand .post {

    width: 30px;

    height: 30px;

    border-radius: 50%;

    background: var(--gold);

    color: var(--navy);

    display: flex;

    align-items: center;

    justify-content: center;

    font-family: var(--font-display);

    font-weight: 700;

    font-size: 13px;

    flex-shrink: 0;

}

.login-poster h1 {

    position: relative;

    font-family: var(--font-display);

    font-weight: 600;

    font-size: 34px;

    line-height: 1.15;

    margin: 26px 0 10px;

}

.login-poster p {

    position: relative;

    font-size: 14.5px;

    color: rgba(255, 255, 255, 0.85);

    max-width: 32ch;

    margin: 0;

}

.login-poster .rail-list {

    position: relative;

    list-style: none;

    margin: 30px 0 0;

    padding: 0;

    display: flex;

    flex-direction: column;

    gap: 10px;

}

.login-poster .rail-list li {

    display: flex;

    align-items: center;

    gap: 10px;

    font-size: 13.5px;

    color: rgba(255, 255, 255, 0.9);

}

.login-poster .rail-list .dot {

    width: 7px;

    height: 7px;

    border-radius: 50%;

    background: var(--gold);

    flex-shrink: 0;

}

/* =========================================================
   FORMULÁRIO
========================================================= */

.login-form-side {

    padding: 46px 42px;

    display: flex;

    flex-direction: column;

    justify-content: center;

}

.login-form-side .eyebrow {

    font-family: var(--font-heading);

    font-size: 12.5px;

    letter-spacing: 0.06em;

    color: var(--grass-dark);

    background: var(--grass-soft);

    display: inline-block;

    padding: 4px 12px;

    border-radius: 100px;

    margin-bottom: 14px;

    width: fit-content;

}

.login-form-side h2 {

    font-family: var(--font-display);

    font-weight: 600;

    font-size: 26px;

    color: var(--navy);

    margin: 0 0 6px;

}

.login-form-side .lead {

    font-size: 14px;

    color: var(--ink-dim);

    margin: 0 0 26px;

}

.login-alert {

    display: flex;

    align-items: center;

    gap: 10px;

    padding: 11px 14px;

    border-radius: 8px;

    background: #fbe4e6;

    border: 1px solid #f3b7bd;

    color: var(--red);

    font-size: 13.5px;

    font-weight: 600;

    margin-bottom: 22px;

}

.field-group {

    margin-bottom: 18px;

}

.field-group label {

    display: block;

    font-family: var(--font-heading);

    font-size: 12.5px;

    letter-spacing: 0.03em;

    color: var(--ink-dim);

    margin-bottom: 6px;

}

.field-group input {

    width: 100%;

    padding: 12px 14px;

    border: 1px solid var(--line);

    border-radius: 8px;

    font-family: var(--font-body);

    font-size: 15px;

    color: var(--ink);

    background: #fbfcf9;

    transition: border-color 0.15s ease, box-shadow 0.15s ease;

}

.field-group input::placeholder {
    color: #9aa79f;
}

.field-group input:focus {

    outline: none;

    border-color: var(--grass);

    box-shadow: 0 0 0 3px rgba(12, 122, 62, 0.15);

    background: #ffffff;

}

.btn-entrar {

    width: 100%;

    padding: 13px 16px;

    border: none;

    border-radius: 8px;

    background: var(--navy);

    color: #ffffff;

    font-family: var(--font-heading);

    font-size: 15.5px;

    letter-spacing: 0.03em;

    font-weight: 600;

    cursor: pointer;

    margin-top: 6px;

    transition: background 0.15s ease, transform 0.05s ease;

}

.btn-entrar:hover {
    background: var(--grass-dark);
}

.btn-entrar:active {
    transform: translateY(1px);
}

.login-foot-note {

    margin-top: 20px;

    font-size: 12.5px;

    color: var(--ink-dim);

    text-align: center;

}

@media (max-width: 760px) {

    .login-wrap {
        grid-template-columns: 1fr;
    }

    .login-poster {
        padding: 34px 28px;
    }

    .login-form-side {
        padding: 34px 28px;
    }

}

  </style>

  <body>

    <div class="login-wrap">

      <div class="login-poster">

        <div>

          <div class="brand">
            <span class="post">TDS</span>
            <span>Sistema TDS &middot; Login</span>
          </div>

          <h1>Acompanhe cada páreo do início à apuração final.</h1>

          <p>
            Entre para abrir voltas, registrar apostas e conferir o
            resultado de cada jogador em tempo real.
          </p>

        </div>

        <ul class="rail-list">

          <li><span class="dot"></span> Voltas abertas e fechadas em um só painel</li>

          <li><span class="dot"></span> Apuração automática de apostas e prêmios</li>

          <li><span class="dot"></span> Histórico completo por jogador</li>

        </ul>

      </div>

      <div class="login-form-side">

        <span class="eyebrow">área do organizador</span>

        <h2>Entrar na conta</h2>

        <p class="lead">Use seu e-mail e senha para acessar o painel.</p>

<?php if (isset($_POST['enviar'])): ?>

        <div class="login-alert">
            Não foi possível entrar. Confira o e-mail e a senha e tente novamente.
        </div>

<?php endif; ?>

        <form action="" method="post">

            <div class="field-group">

                <label for="exampleInputEmail1">Email</label>

                <input
                    type="email"
                    name="email"
                    id="exampleInputEmail1"
                    class="form-control"
                    aria-describedby="emailHelp"
                    placeholder="Seu email"
                >

            </div>

            <div class="field-group">

                <label for="exampleInputPassword1">Senha</label>

                <input
                    type="password"
                    name="senha"
                    id="exampleInputPassword1"
                    class="form-control"
                    placeholder="Senha"
                >

            </div>

            <button type="submit" name="enviar" class="btn-entrar">Entrar</button>

        </form>

        <p class="login-foot-note">Acesso restrito aos organizadores do sistema TDS.</p>

      </div>

    </div>

    <?php require_once "Includes/javascript.php"; ?>
  </body>
</html>
