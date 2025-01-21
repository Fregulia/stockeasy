<?php
// Conexão com o banco de dados
include 'db.php';

// Verifica se o ID foi passado na URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    // Busca os dados do produto pelo ID
    $query = "SELECT * FROM produtos WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verifica se o produto existe
    if ($result->num_rows > 0) {
        $produto = $result->fetch_assoc();
    } else {
        echo "Produto não encontrado.";
        exit();
    }
} else {
    echo "ID inválido.";
    exit();
}

// Atualiza os dados do produto
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $preco = $_POST['preco'];
    $quantidade = $_POST['quantidade'];

    // Atualiza o produto no banco de dados
    $updateQuery = "UPDATE produtos SET nome = ?, descricao = ?, preco = ?, quantidade = ? WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("ssdii", $nome, $descricao, $preco, $quantidade, $id);

    if ($stmt->execute()) {
        echo "Produto atualizado com sucesso!";
        header("Location: produtos.php");
        exit();
    } else {
        echo "Erro ao atualizar o produto: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Produto</title>
    <script src="../scripts/preload.js"></script>
    <link rel="stylesheet" href="../styles/preloads.css">
    <link rel="stylesheet" href="../styles/editar_produto.css">
</head>

<body>
    <header id="header-container"></header>

    <main>
        <div class="welcome">
            <h1>Editar Produto</h1>
        </div>

        <div class="conteudo">
            <div class="titulo_formulario">
                <h2>Informações do Produto</h2>
            </div>

            <div class="formulario">
                <form method="POST">
                    <label for="nome">Nome</label>
                    <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($produto['nome']) ?>" required>
                    <br>

                    <label for="descricao">Descrição</label>
                    <textarea id="descricao" name="descricao" required><?= htmlspecialchars($produto['descricao']) ?></textarea>
                    <br>

                    <label for="preco">Preço</label>
                    <input type="number" id="preco" name="preco" value="<?= htmlspecialchars($produto['preco']) ?>" step="0.01" required>
                    <br>

                    <label for="quantidade">Quantidade</label>
                    <input type="number" id="quantidade" name="quantidade" value="<?= htmlspecialchars($produto['quantidade']) ?>" required>
                    <br>

                    <button type="submit">SALVAR</button>
                </form>
            </div>

        </div>

    </main>



    <footer id="footer-container"></footer>
</body>

</html>