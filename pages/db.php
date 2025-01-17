<?php
// CONFIGURAÇÃO DAS CREDENCIAIS DE CONEXÃO COM O BANCO DE DADOS
$host = 'localhost';  // ENDEREÇO DO SERVIDOR
$user = 'root';       // USUÁRIO PADRÃO DO XAMPP
$pass = '';           // SENHA PADRÃO VAZIA NO XAMPP
$db   = 'Stock_db';   // NOME DO BANCO DE DADOS

// CRIA A CONEXÃO COM O BANCO DE DADOS USANDO O MYSQLI
$conn = new mysqli($host, $user, $pass, $db);

// VERIFICA SE HOUVE ERRO NA CONEXÃO
if ($conn->connect_error) {
    // SE HOUVER ERRO, EXIBE A MENSAGEM DE FALHA NA CONEXÃO
    die("Falha na conexão: " . $conn->connect_error);
}
?>

