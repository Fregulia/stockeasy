<?php
// INICIA A SESSÃO E INCLUI O DB
session_start();
include 'db.php';

// RECUPERA OS DADOS DA SESSÃO
$id = $_SESSION['usuario_id'];
$nome = $_SESSION['nome'];
$email = $_SESSION['email'];

// VERIFICA SE O FORMULÁRIO FOI ENVIADO VIA POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // OBTÉM OS DADOS DO FORMULÁRIO DE CADASTRO
  $nome = $_POST['nome'];
  $descricao = $_POST['descricao'];
  $preco = $_POST['preco'];
  $quantidade = $_POST['qtd'];
  $categoria = $_POST['categoria'];

  // MAPEA A CATEGORIA SELECIONADA PARA UM ID DE CATEGORIA CORRESPONDENTE
  switch ($categoria) {
    case 'opcao1':
      $categoria_id = 1; // ELETRÔNICOS
      break;
    case 'opcao2':
      $categoria_id = 2; // ELETRODOMÉSTICOS
      break;
    case 'opcao3':
      $categoria_id = 3; // ALIMENTOS
      break;
    case 'opcao4':
      $categoria_id = 4; // MATERIAIS DE CONSTRUÇÃO
      break;
    default:
      $categoria_id = 0; // VALOR PADRÃO OU ERRO
      break;
  }

  // SQL PARA INSERIR OS DADOS DO PRODUTO NO BANCO DE DADOS
  $sql = "INSERT INTO produtos (nome, descricao, preco, quantidade, categoria_id, usuario_id)
            VALUES (?, ?, ?, ?, ?, ?)";

  // PREPARA A CONSULTA 
  if ($stmt = $conn->prepare($sql)) {
    $usuario_id = $id; // OBTÉM O ID DO USUÁRIO LOGADO

    // BIND DOS PARÂMETROS PARA A CONSULTA
    $stmt->bind_param("ssdiis", $nome, $descricao, $preco, $quantidade, $categoria_id, $usuario_id);

    // EXECUTA A CONSULTA DE INSERÇÃO NO BANCO DE DADOS
    if ($stmt->execute()) {
      echo "Produto cadastrado com sucesso!"; // CONFIRMA QUE O PRODUTO FOI CADASTRADO
      header("Location: produtos.php"); // REDIRECIONA PARA A PÁGINA DE PRODUTOS
    } else {
      // CASO OCORRA ERRO AO EXECUTAR A CONSULTA
      echo "Erro ao cadastrar o produto: " . $stmt->error;
    }

    // FECHA A DECLARAÇÃO DO PREPARED STATEMENT
    $stmt->close();
  } else {
    // CASO OCORRA ERRO AO PREPARAR A CONSULTA
    echo "Erro na preparação da consulta: " . $conn->error;
  }
}

// LISTA OS PRODUTOS CADASTRADOS PELO USUÁRIO
$usuario_id = $id; // OBTÉM O ID DO USUÁRIO
$sql = "SELECT * FROM produtos WHERE usuario_id = ? LIMIT 10"; // CONSULTA OS PRODUTOS DO USUÁRIO, LIMITANDO A 10 RESULTADOS
$stmt = $conn->prepare($sql); // PREPARA A CONSULTA NO BANCO
$stmt->bind_param("i", $usuario_id);
$stmt->execute(); // EXECUTA A CONSULTA
$result = $stmt->get_result(); // OBTÉM O RESULTADO DA CONSULTA
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Produtos - StockEasy</title>

  <link rel="stylesheet" href="../styles/produtos.css">
  <link rel="stylesheet" href="../styles/home.css">
  <link rel="stylesheet" href="../styles/preloads.css">
  <script src="../scripts/preload.js" defer></script>
</head>

<body>
  <!-- CABEÇALHO -->
  <header id="header-container"></header>

  <main>
    <!-- TÍTULO DA PÁGINA DE PRODUTOS -->
    <div class="welcome">
      <h1>Produtos</h1>
    </div>

    <div class="conteudo">
      <div class="produtos__cadastrados">
        <!-- TÍTULO DA SEÇÃO DE PRODUTOS CADASTRADOS -->
        <h2>Produtos Cadastrados</h2>

        <!-- TABELA DE PRODUTOS CADASTRADOS -->
        <div class="tabela__produtos">
          <?php
          // VERIFICA SE EXISTEM PRODUTOS CADASTRADOS PARA O USUÁRIO
          if ($result->num_rows > 0) {
            echo "<table>"; // INÍCIO DA TABELA
            echo "<tr><th>ID</th><th>Nome</th><th>Descrição</th><th>Preço</th><th>Quantidade</th><th>Categoria</th><th>Ações</th></tr>";

            // ITERA SOBRE OS RESULTADOS E EXIBE OS PRODUTOS
            while ($row = $result->fetch_assoc()) {
              // MAPEAMENTO DA CATEGORIA PARA TEXTO
              switch ($row['categoria_id']) {
                case 1:
                  $categoria = 'Eletrônicos';
                  break;
                case 2:
                  $categoria = 'Eletrodomésticos';
                  break;
                case 3:
                  $categoria = 'Alimentos';
                  break;
                case 4:
                  $categoria = 'Materiais de Construção';
                  break;
                default:
                  $categoria = 'Desconhecida';
                  break;
              }

              // EXIBE OS DADOS DO PRODUTO NA TABELA
              echo "<tr>
                <td>" . $row['id'] . "</td>
                <td>" . $row['nome'] . "</td>
                <td>" . $row['descricao'] . "</td>
                <td>" . $row['preco'] . "</td>
                <td>" . $row['quantidade'] . "</td>
                <td>" . $categoria . "</td>
                <td><a href='deletar_produto.php?id=" . $row['id'] . "'>Excluir</a></td>
              </tr>";
            }
            echo "</table>"; // FIM DA TABELA
          } else {
            echo "Nenhum produto encontrado."; // MENSAGEM CASO NÃO EXISTA PRODUTO CADASTRADO
          }
          ?>
        </div>

        <div class="cadastrar__produto">
          <h2>Cadastrar Novo Produto</h2>

          <div class="formulario__cadastro">
            <form method="POST">
              <div class="dados__produto">
                <label class="titulo__dado">
                  <h3>Nome do Produto</h3>
                </label>
                <input class="valor__dado" type="text" name="nome" required><br>

                <label class="titulo__dado">
                  <h3>Descrição do Produto</h3>
                </label>
                <input class="valor__dado" type="text" name="descricao" required><br>

                <label class="titulo__dado">
                  <h3>Preço do Produto (R$)</h3>
                </label>
                <input class="valor__dado" type="number" name="preco" required><br>

                <label class="titulo__dado">
                  <h3>Quantidade</h3>
                </label>
                <input class="valor__dado" type="number" name="qtd" required><br>

                <label class="titulo__dado" for="opcao">
                  <h3>Categoria</h3>
                </label>
                <select id="categoria_produto" name="categoria">
                  <option value="opcao1">Eletrônicos</option>
                  <option value="opcao2">Eletrodomésticos</option>
                  <option value="opcao3">Alimentos</option>
                  <option value="opcao4">Materiais de Construção</option>
                </select>

                <button id="enviar" type="submit">
                  <h3>Cadastrar</h3>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </main>

</body>

</html>