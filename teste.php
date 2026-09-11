<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap demo</title>
    <?php require_once "includes/bootstrap.php"; ?>
</head>

<body>
    <div class="container">
        <h2>Cadastro Teste</h2>
        <?php 
            require_once "includes/menu.php";
            require_once "Controller/Teste.php";


            $cliente = new Teste();

            if(isset($_POST['enviar']) ){
                if($cliente->gravarCliente($_POST) == 'ok'){
                    header("Location: teste.php");
                }else{
                    echo "Não gravou";
                }
            }
         ?>
        <form method="post" action="">
            <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">Nome</label>
                <input type="text" name="nome" class="form-control" id="exampleInputEmail1"
                    aria-describedby="emailHelp">
            </div>
            <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">Email</label>
                <input type="text" name="email" class="form-control" id="exampleInputEmail1"
                    aria-describedby="emailHelp">
            </div>
              <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">CPF</label>
                <input type="text" name="cpf" class="form-control" id="exampleInputEmail1"
                    aria-describedby="emailHelp">
            </div>
            <div class="mb-3">
                <label for="exampleInputPassword1" class="form-label">Telefone</label>
                <input type="text" name="telefone" class="form-control" id="exampleInputPassword1">
            </div>
            <div class="mb-3">
                <label for="exampleFormControlTextarea1" class="form-label">Obs</label>
                <textarea name="observacao" class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
            </div>
            <button type="submit" name="enviar" class="btn btn-primary">Cadastrar</button>
        </form>

        <table class="table">
  <thead>
    <tr>
      <th scope="col">Código</th>
      <th scope="col">Nome</th>
      <th scope="col">CPF</th>
      <th scope="col">E-mail</th>
      <th scope="col">Telefone</th>
      <th scope="col">Observações</th>
      <th scope="col">Deletar</th>
      <th scope="col">Editar</th>
    </tr>
  </thead>
  <tbody>
        <?php 
            $cliente->selecionarCliente();
            foreach($cliente->selecionarCliente() as $rst){
      ?>
    <tr>
      <th scope="row"><?php  echo $rst['id']; ?></th>
      <td> <?php  echo $rst['nome']; ?>  </td>
      <td> <?php  echo $rst['cpf']; ?>  </td>
      <td> <?php  echo $rst['email']; ?></td>
      <td> <?php  echo $rst['telefone']; ?></td>
      <td> <?php  echo $rst['observacao']; ?></td>
      <td><button type="button" class="btn btn-danger">Deletar</button></td>
      <td> <button type="button" class="btn btn-info">Editar</button>  </td>
    </tr>

        <?php
               // echo $rst['nome'] . $rst['cpf'] . $rst['email'] . $rst['telefone'] . $rst['observacao'] ;
            }
        ?>

  </tbody>
</table>

        <?php require_once "includes/rodape.php"; ?>
    </div>

    <?php require_once "includes/javascript.php"; ?>
</body>

</html>