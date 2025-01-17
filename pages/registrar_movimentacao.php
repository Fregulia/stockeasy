<?php
session_start();
include 'db.php';  // Inclui a conexão com o banco de dados

// Recebe os dados do formulário
$produto_id = $_POST['produto_id'];
$quantidade = $_POST['quantidade'];
$tipo_movimentacao = $_POST['tipo_movimentacao'];
$usuario_id = $_SESSION['usuario_id'];

// Ajuste do estoque de acordo com o tipo de movimentação
if ($tipo_movimentacao == 'entrada') {
    $query = "UPDATE produtos SET quantidade = quantidade + ? WHERE id = ?";
} else {
    $query = "UPDATE produtos SET quantidade = quantidade - ? WHERE id = ?";
}

// Preparar a consulta
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $quantidade, $produto_id);

// Executar a consulta
$stmt->execute();

// Registrar a movimentação na tabela de movimentações
$query_movimentacao = "INSERT INTO movimentacoes (produto_id, quantidade, tipo_movimentacao, usuario_id) 
                       VALUES (?, ?, ?, ?)";
$stmt_movimentacao = $conn->prepare($query_movimentacao);
$stmt_movimentacao->bind_param("iisi", $produto_id, $quantidade, $tipo_movimentacao, $usuario_id);

// Executar o registro da movimentação
$stmt_movimentacao->execute();

// Redirecionar de volta para a página de movimentações
header("Location: movimentacoes.php");
exit();
?>
