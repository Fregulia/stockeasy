<?php
// INCLUI DB E INICIA SESSAO
include 'db.php';
session_start();

// RECUPERA DADOS DO USUARIO NA SESSÃO
$nome = $_SESSION['nome'];
$usuario_id = $_SESSION['usuario_id'];

// VERIFICA SE O FORMULÁRIO FOI ENVIADO VIA POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // OBTÉM OS DADOS DO FORMULÁRIO
  $novo_nome = isset($_POST['nome']) ? $_POST['nome'] : ''; // NOME DO USUÁRIO
  $novo_email = isset($_POST['email']) ? $_POST['email'] : ''; // E-MAIL DO USUÁRIO

  // VERIFICA SE OS CAMPOS FORAM PREENCHIDOS
  if (!empty($novo_nome) || !empty($novo_email)) {
    // PREPARA A CONSULTA SQL PARA ATUALIZAR OS DADOS NO BANCO DE DADOS
    $updateQuery = "UPDATE usuarios SET "; // INICIO DA CONSULTA DE ATUALIZAÇÃO
    $params = []; // ARMAZENA OS PARÂMETROS PARA A CONSULTA
    $types = ''; // ARMAZENA OS TIPOS DE DADOS DOS PARÂMETROS

    // SE O NOME FOI FORNECIDO, ATUALIZA O CAMPO NOME
    if (!empty($novo_nome)) {
      $updateQuery .= "nome = ?, "; // ADICIONA O CAMPO NOME À CONSULTA
      $params[] = $novo_nome; // ADICIONA O NOVO NOME À LISTA DE PARÂMETROS
      $types .= 's';  // TIPO STRING PARA O NOME
    }

    // SE O E-MAIL FOI FORNECIDO, ATUALIZA O CAMPO E-MAIL
    if (!empty($novo_email)) {
      $updateQuery .= "email = ?, "; // ADICIONA O CAMPO E-MAIL À CONSULTA
      $params[] = $novo_email; // ADICIONA O NOVO E-MAIL À LISTA DE PARÂMETROS
      $types .= 's';  // TIPO STRING PARA O E-MAIL
    }

    // REMOVE A VÍRGULA EXTRA NO FINAL DA CONSULTA
    $updateQuery = rtrim($updateQuery, ', ') . " WHERE id = ?"; // FINALIZA A CONSULTA

    // ADICIONA O ID DO USUÁRIO PARA A CONDIÇÃO WHERE
    $params[] = $usuario_id; // ADICIONA O ID DO USUÁRIO COMO PARÂMETRO
    $types .= 'i';  // DEFINE COMO INTEIRO

    // PREPARA A CONSULTA SQL
    $stmt = $conn->prepare($updateQuery); // PREPARA A CONSULTA COM A CONEXÃO

    // VINCULA OS PARÂMETROS À CONSULTA
    $stmt->bind_param($types, ...$params); // VINCULA OS PARÂMETROS COM A CONSULTA

    // EXECUTA A CONSULTA
    if ($stmt->execute()) {
      // SUCESSO NA EXECUÇÃO DA CONSULTA
    } else {
      // CASO OCORRA ERRO AO EXECUTAR A CONSULTA
      echo "Erro ao atualizar os dados: " . $stmt->error; // MENSAGEM DE ERRO
    }

    // FECHA A DECLARAÇÃO DO PREPARED STATEMENT
    $stmt->close();
  }
}

// OBTÉM OS DADOS ATUAIS DO USUÁRIO PARA PRÉ-PREENCHER O FORMULÁRIO
$sql = "SELECT nome, email FROM usuarios WHERE id = ?"; // CONSULTA PARA O NOME E E-MAIL DO USUÁRIO
$stmt = $conn->prepare($sql); // PREPARA A CONSULTA
$stmt->bind_param('i', $usuario_id); // VINCULA O ID DO USUÁRIO COMO PARÂMETRO
$stmt->execute(); // EXECUTA A CONSULTA
$stmt->bind_result($nome, $email); // VINCULA OS RESULTADOS DA CONSULTA ÀS VARIÁVEIS
$stmt->fetch(); // OBTÉM O RESULTADO DA CONSULTA
$stmt->close(); // FECHA O PREPARED STATEMENT

// FECHA A CONEXÃO COM O BANCO DE DADOS
$conn->close();

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8"> 
  <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
  <title>Perfil</title> 

  <link rel="stylesheet" href="../styles/preloads.css"> 
  <link rel="stylesheet" href="../styles/profile.css"> 
  <script src="../scripts/preload.js" defer></script> 
</head>

<body>
  <!-- CABEÇALHO -->
  <header id="header-container"></header> 

  <main>
    <div class="welcome">
      <?php echo "<h1>Perfil de $nome</h1>" ?> <!-- EXIBE O NOME DO USUÁRIO -->
    </div>

    <div class="conteudo">
      <div class="foto_perfil">
        <h2>Envie sua Foto de Perfil</h2> <!-- TÍTULO DA SEÇÃO DE FOTO DE PERFIL -->

        <form id="uploadForm" enctype="multipart/form-data" method="POST" action="upload.php">
          <label for="profilePic">Selecione sua foto de perfil</label><br> <!-- RÓTULO PARA SELEÇÃO DE FOTO -->
          <input type="file" id="profilePic" name="profilePic" accept="image/*" required><br> <!-- CAMPO DE SELEÇÃO DE FOTO -->
          <button type="submit">Enviar</button> <!-- BOTÃO DE ENVIO -->
        </form>
      </div>

      <div class="informacoes_pessoais">
        <h2>Edite Suas Informações Pessoais</h2> <!-- TÍTULO DA SEÇÃO DE EDIÇÃO DE INFORMAÇÕES -->

        <form class="form_dados" method="POST" action=""> <!-- FORMULÁRIO DE EDIÇÃO -->
          <label for="nome" style="margin-top: 10%;">Nome</label><br> <!-- RÓTULO PARA O CAMPO NOME -->
          <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($nome); ?>"><br><br> <!-- CAMPO DE NOME -->

          <label for="email">E-mail</label><br> <!-- RÓTULO PARA O CAMPO E-MAIL -->
          <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>"><br><br> <!-- CAMPO DE E-MAIL -->

          <input type="submit" value="Atualizar"> <!-- BOTÃO DE SUBMISSÃO DO FORMULÁRIO -->
        </form>
      </div>
    </div>
  </main>

  <footer id="footer-container"></footer> <!-- RODAPÉ DA PÁGINA -->
</body>

</html>
