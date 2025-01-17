<?php
session_start(); 
session_destroy(); // DESTRÓI A SESSÃO
header("Location: index.php"); // REDIRECIONA PRA PÁGINA DE LOGIN
exit;
?>
