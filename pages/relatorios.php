<?php
session_start();
include 'db.php';

$usuario_id = $_SESSION['usuario_id']; // RECUPERA O ID DO USUÁRIO LOGADO

// VARIÁVEIS PARA ARMAZENAR RESULTADOS E FILTROS
$tipo_relatorio = $_POST['tipo_relatorio'] ?? 'movimentacoes'; // DEFINIR O TIPO DE RELATÓRIO SELECIONADO
$data_inicio = $_POST['data_inicio'] ?? null; // DATA INÍCIO SE FOR INFORMADA
$data_fim = $_POST['data_fim'] ?? null; // DATA FIM SE FOR INFORMADA
$resultados = []; // INICIALIZA O ARRAY PARA ARMAZENAR RESULTADOS

// SOMENTE PROCESSA A CONSULTA SE O FORMULÁRIO FOI ENVIADO
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // SQL BASE PARA CADA TIPO DE RELATÓRIO
  if ($tipo_relatorio === 'movimentacoes') {
    // CONSULTA PARA O RELATÓRIO DE MOVIMENTAÇÕES
    $sql = "SELECT p.nome AS produto, p.categoria_id, m.quantidade, m.tipo_movimentacao, m.data_movimentacao
                FROM movimentacoes m
                JOIN produtos p ON m.produto_id = p.id
                WHERE p.usuario_id = ?"; // FILTRA PELO ID DO USUÁRIO
    if ($data_inicio && $data_fim) {
      $sql .= " AND m.data_movimentacao BETWEEN ? AND ?"; // ADICIONA FILTRO DE DATA SE AMBAS FOREM INFORMADAS
    }
  } elseif ($tipo_relatorio === 'estoque') {
    // CONSULTA PARA O RELATÓRIO DE ESTOQUE
    $sql = "SELECT nome AS produto, categoria_id, quantidade 
                FROM produtos 
                WHERE usuario_id = ?"; // FILTRA PELO ID DO USUÁRIO
  } elseif ($tipo_relatorio === 'produtos') {
    // CONSULTA PARA O RELATÓRIO DE PRODUTOS
    $sql = "SELECT nome AS produto, categoria_id, preco, quantidade 
                FROM produtos 
                WHERE usuario_id = ?"; // FILTRA PELO ID DO USUÁRIO
  }

  // PREPARA E EXECUTA A CONSULTA
  $stmt = $conn->prepare($sql); // PREPARA A CONSULTA
  if ($tipo_relatorio === 'movimentacoes' && $data_inicio && $data_fim) {
    // BIND DOS PARÂMETROS PARA O TIPO DE RELATÓRIO 'MOVIMENTACOES' COM FILTROS DE DATA
    $stmt->bind_param("iss", $usuario_id, $data_inicio, $data_fim); 
  } else {
    // BIND PARA OS DEMAIS TIPOS DE RELATÓRIO
    $stmt->bind_param("i", $usuario_id); 
  }

  $stmt->execute(); // EXECUTA A CONSULTA
  $result = $stmt->get_result(); // PEGA O RESULTADO DA CONSULTA

  // ARMAZENA OS RESULTADOS PARA EXIBIÇÃO
  while ($row = $result->fetch_assoc()) {
    $resultados[] = $row; // ADICIONA O RESULTADO NA LISTA DE RESULTADOS
  }

  $stmt->close(); // FECHA A CONEXÃO COM A CONSULTA
}
$conn->close(); // FECHA A CONEXÃO COM O BANCO DE DADOS

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Relatórios</title>
  <link rel="stylesheet" href="../styles/preloads.css">
  <link rel="stylesheet" href="../styles/home.css">
  <link rel="stylesheet" href="../styles/relatorios.css">
  <script src="../scripts/preload.js" defer></script>
</head>

<body>
  <header id="header-container"></header>

  <main>
    <!-- TÍTULO PRINCIPAL -->
    <div class="welcome">
      <h1>Gerar Relatórios</h1> 
    </div>

    <div class="conteudo">
      <div class="formulario_relatorio">
        <form method="POST" action="">
          <label for="tipo_relatorio">Tipo de Relatório:</label>
          <select name="tipo_relatorio" id="tipo_relatorio">
            <!-- OPÇÕES PARA SELECIONAR O TIPO DE RELATÓRIO -->
            <option value="movimentacoes" <?= $tipo_relatorio === 'movimentacoes' ? 'selected' : '' ?>>Movimentações</option>
            <option value="estoque" <?= $tipo_relatorio === 'estoque' ? 'selected' : '' ?>>Estoque</option>
            <option value="produtos" <?= $tipo_relatorio === 'produtos' ? 'selected' : '' ?>>Produtos</option>
          </select>
          <br><br>

          <label for="data_inicio">Data Início:</label>
          <input type="date" name="data_inicio" id="data_inicio" value="<?= htmlspecialchars($data_inicio) ?>"> <!-- CAMPO PARA DATA INÍCIO -->
          <br><br>

          <label for="data_fim">Data Fim:</label>
          <input type="date" name="data_fim" id="data_fim" value="<?= htmlspecialchars($data_fim) ?>"> <!-- CAMPO PARA DATA FIM -->
          <br><br>

          <button type="submit">Gerar Relatório</button> <!-- BOTÃO PARA GERAR O RELATÓRIO -->
        </form>
      </div>

      <div class="resultados">
        <div class="titulo_resultados">
          <h2>Resultados</h2> <!-- TÍTULO DOS RESULTADOS -->
        </div>

        <div class="tabela_resultados">
          <table>
            <thead>
              <?php if ($tipo_relatorio === 'movimentacoes'): ?>
                <tr class="cabecalho_tabela">
                  <th>Produto</th>
                  <th>Categoria</th>
                  <th>Quantidade</th>
                  <th>Tipo de Movimentação</th>
                  <th>Data da Movimentação</th>
                </tr>
              <?php elseif ($tipo_relatorio === 'estoque'): ?>
                <tr class="cabecalho_tabela">
                  <th>Produto</th>
                  <th>Categoria</th>
                  <th>Quantidade</th>
                </tr>
              <?php elseif ($tipo_relatorio === 'produtos'): ?>
                <tr class="cabecalho_tabela">
                  <th>Produto</th>
                  <th>Categoria</th>
                  <th>Preço</th>
                  <th>Quantidade</th>
                </tr>
              <?php endif; ?>
            </thead>
            <tbody>
              <?php foreach ($resultados as $row): ?>
                <tr>
                  <?php foreach ($row as $key => $value): ?>
                    <?php
                    if ($key === 'categoria_id') {
                      // CONVERSÃO DO ID DA CATEGORIA PARA NOME
                      switch ($value) {
                        case 1:
                          $value = 'Eletrônicos'; // ELETRÔNICOS
                          break;
                        case 2:
                          $value = 'Eletrodomésticos'; // ELETRODOMÉSTICOS
                          break;
                        case 3:
                          $value = 'Alimentos'; // ALIMENTOS
                          break;
                        case 4:
                          $value = 'Materiais de Construção'; // MATERIAIS DE CONSTRUÇÃO
                          break;
                        default:
                          $value = 'Outro'; // OUTRO
                          break;
                      }
                    }
                    ?>
                    <td><?= htmlspecialchars($value) ?></td> <!-- EXIBE O VALOR CORRESPONDENTE -->
                  <?php endforeach; ?>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </main>

  <footer id="footer-container"></footer>
</body>

</html>
