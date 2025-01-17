<?php
// INICIA A SESSÃO E INCLUI O DB
session_start();
include 'db.php';

// RECUPERA OS DADOS DO USUÁRIO DA SESSÃO
$nome = $_SESSION['nome'];
$email = $_SESSION['email'];
$usuario_id = $_SESSION['usuario_id'];

// CONSULTA SQL PARA BUSCAR OS PRODUTOS DO USUÁRIO LOGADO (LIMITADO A 10)
$query = "SELECT nome, quantidade FROM produtos WHERE usuario_id = ? LIMIT 10";
$stmt = $conn->prepare($query); // PREPARA A CONSULTA SQL
if ($stmt === false) {
    die('Erro na preparação da consulta: ' . $conn->error); // VERIFICA ERRO NA PREPARAÇÃO
}

// ASSOCIA O VALOR 'USUARIO_ID' A CONSULTA COMO SENDO INTEIRO
$stmt->bind_param('i', $usuario_id);
$stmt->execute(); // EXECUTA
$result = $stmt->get_result(); // OBTÉM O RESULTADO DA CONSULTA

$labels = []; // ARRAY PARA ARMAZENAR OS NOMES DOS PRODUTOS
$quantidades = []; // ARRAY PARA ARMAZENAR AS QUANTIDADES DOS PRODUTOS

// LOOP PARA PREENCHER OS ARRAYS COM OS DADOS DO BANCO DE DADOS
while ($row = $result->fetch_assoc()) {
    $labels[] = $row['nome']; // ADICIONA O NOME DO PRODUTO AO ARRAY 'LABELS'
    $quantidades[] = $row['quantidade']; // ADICIONA A QUANTIDADE AO ARRAY 'QUANTIDADES'
}

// CONSULTA SQL PARA BUSCAR AS ÚLTIMAS 5 MOVIMENTAÇÕES DO USUÁRIO LOGADO
$sql = "SELECT m.id, p.nome AS produto, m.quantidade, m.tipo_movimentacao, m.data_movimentacao 
        FROM movimentacoes m
        INNER JOIN produtos p ON m.produto_id = p.id
        WHERE m.usuario_id = ? 
        ORDER BY m.data_movimentacao DESC 
        LIMIT 5";

$stmt = $conn->prepare($sql); // PREPARA A CONSULTA SQL
$stmt->bind_param("i", $usuario_id); // ASSOCIA O PARÂMETRO 'USUARIO_ID' A CONSULTA
$stmt->execute(); // EXECUTA
$result = $stmt->get_result(); // OBTÉM O RESULTADO

$movimentacoes = []; // ARRAY PARA ARMAZENAR AS MOVIMENTAÇÕES

// PREENCHE O ARRAY DE MOVIMENTAÇÕES COM OS DADOS RECUPERADOS DO BANCO
while ($row = $result->fetch_assoc()) {
    $movimentacoes[] = $row; // ADICIONA A MOVIMENTAÇÃO AO ARRAY 'MOVIMENTACOES'
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - StockEasy</title>

    <link rel="stylesheet" href="../styles/preloads.css">
    <link rel="stylesheet" href="../styles/dashboard.css">
    <script src="../scripts/preload.js" defer></script>
</head>

<body>
    <header id="header-container"></header>

    <main>
        <!-- TÍTULO PRINCIPAL DA PÁGINA -->
        <div class="welcome">
            <h1>Dashboard</h1>
        </div>

        <div class="conteudo">
            <!-- SEÇÃO DO GRÁFICO DE PRODUTOS -->
            <div class="grafico_estoque">
                <!-- TÍTULO DO GRÁFICO -->
                <div class="label">
                    <h2>Proporção dos Produtos no Estoque</h2>
                </div>

                <!-- CONTAINER DO GRÁFICO -->
                <div class="grafico">
                    <!-- DIV PARA O GRÁFICO DE PRODUTOS -->
                    <div id="produtoChart"></div>

                    <!-- SCRIPT PARA INCLUIR O PLOTLY (GRÁFICO DE PIZZA) -->
                    <script src="https://cdn.plot.ly/plotly-latest.min.js"></script>
                    <script>
                        // DEFINIÇÃO DOS DADOS DO GRÁFICO DE PRODUTOS (USANDO PHP PARA PASSAR OS VALORES)
                        var data = [{
                            values: <?php echo json_encode($quantidades); ?>,  // QUANTIDADE DOS PRODUTOS
                            labels: <?php echo json_encode($labels); ?>,  // NOMES DOS PRODUTOS
                            type: 'pie'
                        }];

                        // DEFINIÇÃO DO LAYOUT DO GRÁFICO
                        var layout = {
                            title: 'Ocupação do Estoque', // TÍTULO DO GRÁFICO
                            showlegend: true // EXIBIÇÃO DA LEGENDA
                        };

                        // CRIAÇÃO DO GRÁFICO COM PLOTLY
                        Plotly.newPlot('produtoChart', data, layout);
                    </script>
                </div>
            </div>

            <!-- SEÇÃO DAS ÚLTIMAS MOVIMENTAÇÕES -->
            <div class="dashboard">
                <!-- TÍTULO DA SEÇÃO DE MOVIMENTAÇÕES -->
                <h1 class="dashboard-title">Últimas Movimentações</h1>

                <!-- CONDIÇÃO PARA VERIFICAR SE EXISTEM MOVIMENTAÇÕES -->
                <?php if (count($movimentacoes) > 0): ?>
                    <!-- TABELA PARA EXIBIR AS MOVIMENTAÇÕES -->
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Produto</th>
                                <th>Quantidade</th>
                                <th>Tipo</th>
                                <th>Data</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- LOOP PARA EXIBIR CADA MOVIMENTAÇÃO NA TABELA -->
                            <?php foreach ($movimentacoes as $mov): ?>
                                <tr>
                                    <!-- EXIBE O ID DA MOVIMENTAÇÃO -->
                                    <td><?php echo $mov['id']; ?></td>
                                    
                                    <!-- EXIBE O NOME DO PRODUTO DA MOVIMENTAÇÃO -->
                                    <td><?php echo $mov['produto']; ?></td>
                                    
                                    <!-- EXIBE A QUANTIDADE DA MOVIMENTAÇÃO -->
                                    <td><?php echo $mov['quantidade']; ?></td>
                                    
                                    <!-- EXIBE O TIPO DA MOVIMENTAÇÃO (INICIANDO A PRIMEIRA LETRA EM MAIÚSCULO) -->
                                    <td><?php echo ucfirst($mov['tipo_movimentacao']); ?></td>
                                    
                                    <!-- EXIBE A DATA E HORA DA MOVIMENTAÇÃO -->
                                    <td><?php echo date("d/m/Y H:i", strtotime($mov['data_movimentacao'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <!-- MENSAGEM CASO NÃO HOUVER MOVIMENTAÇÕES -->
                    <p>Nenhuma movimentação encontrada.</p>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- RODAPÉ DA PÁGINA -->
    <footer id="footer-container"></footer>

    <!-- SCRIPT DE PRELOAD -->
    <script src="../scripts/preload.js"></script>
</body>

</html>

