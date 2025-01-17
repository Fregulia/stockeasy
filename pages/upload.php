<?php
session_start();
$usuario_id = $_SESSION['usuario_id']; // Diretório onde as imagens serão salvas
$targetDir = "../imgs/uploads/";
if (!is_dir($targetDir)) {
    mkdir($targetDir, 0777, true); // Cria o diretório se não existir
}

// Verificar se há um arquivo enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['profilePic'])) {
    $file = $_FILES['profilePic'];

    // Validar erros no upload
    if ($file['error'] !== UPLOAD_ERR_OK) {
        echo "Erro ao enviar o arquivo.";
        exit;
    }

    // Validar tipo de arquivo (somente imagens)
    $allowedTypes = ['image/jpeg'];
    if (!in_array($file['type'], $allowedTypes)) {
        echo "Apenas arquivos JPEG são permitidos.";
        exit;
    }

    // Gerar um nome único para o arquivo
    $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $fileName = $usuario_id . "." . $fileExtension;

    // Caminho completo para salvar a imagem
    $targetFile = $targetDir . $fileName;

    // Verificar se o arquivo já existe e sobrescrever
    if (file_exists($targetFile)) {
        unlink($targetFile); // Excluir o arquivo antigo
    }

    // Mover o arquivo para o diretório de destino
    if (move_uploaded_file($file['tmp_name'], $targetFile)) {
        echo "Upload realizado com sucesso!";
        echo "<br><img src='$targetFile' style='width: 150px; height: 150px; border-radius: 50%;'>";
    } else {
        echo "Erro ao salvar o arquivo.";
    }
} else {
    echo "Nenhum arquivo foi enviado.";
}
?>

