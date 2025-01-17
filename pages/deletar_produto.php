<?php
// INCLUÍ A CONEXÃO COM O BANCO
include('db.php');

// VERIFICA SE O PARÂMETRO 'id' TÁ PRESENTE NA URL
if (isset($_GET['id'])) {
    // RECUPERA O ID DO PRODUTO DA URL
    $produto_id = $_GET['id'];

    // INICIA A TRANSAÇÃO PARA GARANTIR QUE AMBAS AS EXCLUSÕES OCORRAM
    $conn->begin_transaction();

    try {
        // PREPARA A CONSULTA SQL PARA DELETAR AS MOVIMENTAÇÕES ASSOCIADAS AO PRODUTO
        $deleteMovimentacaoSql = "DELETE FROM movimentacoes WHERE produto_id = ?";
        
        // PREPARA A CONSULTA
        if ($stmt = $conn->prepare($deleteMovimentacaoSql)) {
            // ASSOCIA O PARÂMETRO (produto_id) À CONSULTA
            $stmt->bind_param("i", $produto_id);

            // EXECUTA A CONSULTA
            if (!$stmt->execute()) {
                // LIDA COM ERROS NA EXECUÇÃO
                throw new Exception("Erro ao deletar movimentações: " . $conn->error);
            }
            // FECHA A CONSULTA DE MOVIMENTAÇÕES
            $stmt->close();
        } 
        else {
            // LIDA COM ERROS NA PREPARAÇÃO DA CONSULTA DE MOVIMENTAÇÕES
            throw new Exception("Erro na preparação da consulta de movimentações: " . $conn->error);
        }

        // PREPARA A CONSULTA SQL PARA DELETAR O PRODUTO
        $deleteProdutoSql = "DELETE FROM produtos WHERE id = ?";

        // PREPARA A CONSULTA
        if ($stmt = $conn->prepare($deleteProdutoSql)) {
            // ASSOCIA O PARÂMETRO (produto_id) À CONSULTA
            $stmt->bind_param("i", $produto_id);

            // EXECUTA A CONSULTA
            if (!$stmt->execute()) {
                // LIDA COM ERROS NA EXECUÇÃO
                throw new Exception("Erro ao deletar o produto: " . $conn->error);
            }
            // FECHA A CONSULTA DO PRODUTO
            $stmt->close();

        } 
        else {
            // LIDA COM ERROS NA PREPARAÇÃO DA CONSULTA DE PRODUTO
            throw new Exception("Erro na preparação da consulta de produto: " . $conn->error);
        }

        // COMMIT DA TRANSAÇÃO, SE AMBAS AS EXCLUSÕES FOREM BEM-SUCEDIDAS
        $conn->commit();

        // MENSAGEM DE SUCESSO
        echo "Produto e suas movimentações deletados com sucesso!";
        // REDIRECIONA PARA A PÁGINA QUE LISTA OS PRODUTOS
        header("Location: produtos.php");
        exit;

    } catch (Exception $e) {
        // EM CASO DE ERRO, DESFAZ A TRANSAÇÃO
        $conn->rollback();
        // EXIBE A MENSAGEM DE ERRO
        echo "Erro: " . $e->getMessage();
    }

    // FECHA A CONEXÃO COM O BANCO
    $conn->close();

} 
else {
    // MENSAGEM DE ERRO CASO O ID NÃO SEJA ESPECIFICADO
    echo "ID do produto não especificado.";
}
?>

