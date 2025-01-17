<?php
// INCLUI O ARQUIVO DO BANCO DE DADOS
include 'db.php';

// VERIFICA SE O MÉTODO DA REQUISIÇÃO É POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // RECEBE OS DADOS DO FORMULÁRIO
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    // FAZ O HASH DA SENHA ANTES DE ARMAZENÁ-LA NO BANCO
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

    // COMANDO SQL PARA INSERIR OS DADOS NO BANCO
    $sql = "INSERT INTO usuarios (nome, email, senha) VALUES ('$nome', '$email', '$senha')";

    // EXECUTA O COMANDO SQL E VERIFICA SE A INSERÇÃO FOI BEM-SUCEDIDA
    if ($conn->query($sql) === TRUE) {
        // SE SUCESSO, EXIBE UMA MENSAGEM E REDIRECIONA PARA A PÁGINA DE LOGIN
        echo "<script>alert('Cadastro realizado com sucesso!'); window.location='index.php';</script>";
    } else {
        // SE ERRO, EXIBE A MENSAGEM DE ERRO
        echo "Erro: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<link rel="stylesheet" href="../styles/index.css">

<head>
    <title>Cadastro - StockEasy</title>
    <script>
        // FUNÇÃO PARA VALIDAR O FORMULÁRIO ANTES DE ENVIAR
        function validarFormulario() {
            let senha = document.getElementById('senha').value; // PEGANDO O VALOR DA SENHA
            let confirmacao = document.getElementById('confirmar_senha').value; // PEGANDO O VALOR DA CONFIRMAÇÃO DE SENHA

            // VERIFICA SE AS SENHAS NÃO SÃO IGUAIS
            if (senha !== confirmacao) {
                alert("As senhas não coincidem!"); // MENSAGEM DE ERRO
                return false; // IMPEDIR O ENVIO DO FORMULÁRIO
            }
            return true; // PERMITIR O ENVIO DO FORMULÁRIO
        }
    </script>
</head>

<body>
    <main>
        <!-- NOME DA MARCA COM LINK PARA A PÁGINA PRINCIPAL -->
        <div class="marca__nome">
            <a href="index.php" class="marca">StockEasy</a>
        </div>

        <div class="cadastro">
            <!-- TÍTULO DA PÁGINA DE CADASTRO -->
            <div class="titulo">
                <h2>Cadastro</h2>
            </div>
            <!-- FORMULÁRIO DE CADASTRO QUE CHAMA VALIDAÇÃO ANTES DE ENVIAR -->
            <form method="POST" onsubmit="return validarFormulario();">
                <!-- CAMPO PARA O NOME -->
                <label class="label_login">Nome</label>
                <input placeholder="Digite Seu Nome" type="text" name="nome" required><br>

                <!-- CAMPO PARA O EMAIL -->
                <div class="labels label_login"><label>Email</label></div>
                <input placeholder="Digite Seu Email" type="email" name="email" required><br>

                <!-- CAMPO PARA A SENHA -->
                <div class="labels label_login"><label>Senha</label></div>
                <input placeholder="Digite Sua Senha"  type="password" id="senha" name="senha" required><br>

                <!-- CAMPO PARA CONFIRMAR A SENHA -->
                <div class="labels label_login"><label>Confirmar Senha</label></div>
                <input placeholder="Confime Sua Senha" type="password" id="confirmar_senha" required><br>

                <!-- BOTÃO PARA ENVIAR -->
                <button id="enviar" type="submit">Cadastrar</button>
            </form>
            <!-- LINK PARA REDIRECIONAR O USUÁRIO PARA A PÁGINA DE LOGIN -->
            <a href="index.php">Já tem uma conta? Faça login</a>
        </div>
    </main>
</body>

</html>
