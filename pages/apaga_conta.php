<?php
session_start();
include 'db.php';

if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] != 2) {
    $usuario_id = $_SESSION['usuario_id']; // A variável de sessão contém o ID do usuário

    // Iniciar uma transação para garantir que as operações sejam atômicas
    $conexao->begin_transaction();

    try {
        // Excluir as movimentações relacionadas ao usuário e aos seus produtos
        $sql_movimentacoes = "DELETE FROM movimentacoes WHERE usuario_id = ?";
        $stmt = $conexao->prepare($sql_movimentacoes);
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();

        // Excluir os produtos relacionados ao usuário
        $sql_produto = "DELETE FROM produtos WHERE usuario_id = ?";
        $stmt_produto = $conexao->prepare($sql_produto);
        $stmt_produto->bind_param("i", $usuario_id);
        $stmt_produto->execute();

        // Excluir a conta do usuário
        $sql_usuario = "DELETE FROM usuarios WHERE id = ?";
        $stmt_usuario = $conexao->prepare($sql_usuario);
        $stmt_usuario->bind_param("i", $usuario_id);
        $stmt_usuario->execute();

        // Commit da transação
        $conexao->commit();

        // Exibir o alerta e redirecionar para a página de índice
        echo "<script>
                alert('Conta e produtos excluídos com sucesso!');
                window.location.href = 'index.php';
              </script>";
    } catch (Exception $e) {
        // Exibir detalhes do erro para depuração
        $conexao->rollback();
        echo "<script>
                alert('Erro ao excluir a conta ou produtos. Detalhes: " . $e->getMessage() . "');
                window.location.href = 'index.php';
              </script>";
    }
} else if ($_SESSION['usuario_id'] == 2) {
    echo "<script>
    alert('Você está não pode excluir a conta admin!');
    window.location.href = 'profile.php';
  </script>";
} else {
    echo "<script>
            alert('Você não está logado!');
            window.location.href = 'index.php';
          </script>";
}
