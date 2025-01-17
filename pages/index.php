<?php
// INICIA A SESSÃO E INCLUI O BANCO DE DADOS
session_start();
include 'db.php';

// VERIFICA SE O FORMULÁRIO FOI ENVIADO PELO MÉTODO POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // RECEBE O EMAIL E A SENHA DO FORMULÁRIO
  $email = $_POST['email'];
  $senha = $_POST['senha'];

  // PREPARA A CONSULTA PARA BUSCAR O USUÁRIO PELO EMAIL
  $sql = "SELECT id, nome, senha FROM usuarios WHERE email = ?";
  $stmt = $conn->prepare($sql); // PREPARA A CONSULTA NO BANCO DE DADOS
  $stmt->bind_param("s", $email); // FAZ O VÍNCULO DO EMAIL COM A CONSULTA
  $stmt->execute(); // EXECUTA A CONSULTA NO BANCO DE DADOS
  $result = $stmt->get_result(); // OBTÉM O RESULTADO DA CONSULTA

  // VERIFICA SE O USUÁRIO FOI ENCONTRADO
  if ($user = $result->fetch_assoc()) {
    // VERIFICA SE A SENHA ESTÁ CORRETA
    if (password_verify($senha, $user['senha'])) {
      // ARMAZENA AS INFORMAÇÕES DO USUÁRIO NA SESSÃO
      $_SESSION['usuario_id'] = $user['id'];
      $_SESSION['nome'] = $user['nome'];
      $_SESSION['email'] = $user['email'];
      header("Location: home.php"); // REDIRECIONA PARA A PÁGINA PRINCIPAL
      exit; // INTERROMPE A EXECUÇÃO DO SCRIPT
    }
    // SE A SENHA ESTIVER INCORRETA, EXIBE UMA MENSAGEM
    echo "<script>alert('Senha incorreta!');</script>";
  } else {
    // SE O USUÁRIO NÃO FOR ENCONTRADO, EXIBE UMA MENSAGEM
    echo "<script>alert('Usuário não encontrado!');</script>";
  }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - StockEasy</title>
  <link rel="stylesheet" href="../styles/index.css">
</head>

<body>
  <main>
    <!-- NOME DA MARCA COM LINK PARA A PÁGINA INICIAL -->
    <div class="marca__nome">
      <a href="index.php" class="marca">StockEasy</a>
    </div>

    <div class="cadastro">
      <!-- TÍTULO DA PÁGINA DE LOGIN -->
      <div class="titulo">
        <h2>Login</h2>
      </div>

      <div class="formulario">
        <!-- FORMULÁRIO DE LOGIN COM MÉTODO POST PARA ENVIAR DADOS -->
        <form method="POST">
          <div class="dados">
            <!-- ENTRADA PARA O EMAIL -->
            <div class="labels">
              <label class="label_login">Email</label>
            </div>
            <input type="email" name="email" placeholder="Digite Seu Email" required><br>

            <!-- ENTRADA PARA A SENHA -->
            <label class="label_login">Senha</label>
            <input type="password" name="senha" placeholder="Digite Sua Senha" required><br>

            <!-- BOTÃO PARA ENVIAR -->
            <button id="enviar" type="submit">Entrar</button>
          </div>

          <!-- LINK PARA REDIRECIONAR O USUÁRIO PARA A PÁGINA DE CADASTRO -->
          <a id="cadastrar" href="cadastro.php">Não tem uma conta? Cadastre-se</a>
        </form>
      </div>

    </div>
    
  </main>

</body>

</html>