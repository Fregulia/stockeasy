<?php
// INICIALIZA A SESSÃO E INCLUI O DB
session_start();
include 'db.php';

$id = $_SESSION['usuario_id'];
$usuario_id = $id; // ARMAZENA O ID DO USUÁRIO EM UMA VARIÁVEL LOCAL
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Movimentações</title>

  <link rel="stylesheet" href="../styles/preloads.css">
  <link rel="stylesheet" href="../styles/movimentacoes.css">
  <script src="../scripts/preload.js" defer></script>

</head>

<body>
  <!-- HEADER -->
  <header id="header-container"></header>

  <main>
    <!-- TÍTULO PRINCIPAL DA PÁGINA -->
    <div class="welcome">
      <h1>Movimentações</h1>
    </div>

    <div class="conteudo">
      <div class="form_movimentações">
        <!-- FORMULÁRIO PARA REGISTRAR MOVIMENTAÇÕES -->
        <form method="POST" action="registrar_movimentacao.php">
        
          <!-- CAMPO PARA SELEÇÃO DE PRODUTO -->
          <label for="produto">
            <h3>Produto</h3>
          </label>
          <select name="produto_id" id="produto" required>
            <?php
            // CONSULTA O BANCO DE DADOS PARA TER OS PRODUTOS ASSOCIADOS AO USUÁRIO
            // PREPARAÇÃO DA CONSULTA
            $query = "SELECT id, nome FROM produtos WHERE usuario_id = ?";
            $stmt = $conn->prepare($query);
            if ($stmt === false) {
              // CASO HOUVER UM ERRO NA PREPARAÇÃO DA CONSULTA, MOSTRA UMA MENSAGEM DE ERRO
              die('Erro na preparação da consulta: ' . $conn->error);
            }
            $stmt->bind_param('i', $usuario_id); // VINCULA O ID DO USUÁRIO À CONSULTA
            $stmt->execute(); // EXECUTA A CONSULTA
            $result = $stmt->get_result(); // OBTÉM O RESULTADO DA CONSULTA

            // LOOP PARA EXIBIR OS PRODUTOS EM UM MENU DE OPÇÕES
            while ($row = $result->fetch_assoc()) {
              echo "<option value='" . $row['id'] . "'>" . $row['nome'] . "</option>";
            }
            
            $stmt->close(); // FECHA A CONEXÃO COM A CONSULTA
            ?>
          </select>

          <!-- CAMPO PARA DE QUANTIDADE -->
          <label for="quantidade">
            <h3>Quantidade</h3>
          </label>
          <input type="number" name="quantidade" id="quantidade" required>

          <!-- CAMPO DO TIPO DE MOVIMENTAÇÃO -->
          <label for="tipo_movimentacao">
            <h3>Tipo de Movimentação</h3>
          </label>
          <select name="tipo_movimentacao" id="tipo_movimentacao" required>
            <!-- OPÇÕES PARA TIPO DE MOVIMENTAÇÃO: ENTRADA OU SAÍDA -->
            <option value="entrada">Entrada</option>
            <option value="saida">Saída</option>
          </select>

          <!-- BOTÃO DE ENVIO DO FORMULÁRIO -->
          <button type="submit">Registrar Movimentação</button>
        </form>

      </div>
    </div>

  </main>

  <!-- RODAPÉ -->
  <footer id="footer-container"></footer>
</body>

</html>
