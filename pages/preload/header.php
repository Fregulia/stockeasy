<?php
// INICIA A SESSÃO E PEGA O ID DO USUARIO
session_start();
$usuario_id = $_SESSION['usuario_id'];
?>
<header>
    <div class="container__header">
        <!-- NAVEGAÇÃO DO CABEÇALHO -->
        <nav class="nav__cabecalho">
            <!-- NOME DA MARCA COM LINK PARA A PAGINA PRINCIPAL -->
            <div class="marca__nome">
                <a href="home.php" class="marca">StockEasy</a>
            </div>

            <!-- BOTÕES DE NAVEGAÇÃO -->
            <div class="botoes__nav">
                <ul>
                    <li>
                        <a href="produtos.php">Produtos</a>
                    </li>
                    <li>
                        <a href="movimentacoes.php">Movimentações</a>
                    </li>
                    <li>
                        <a href="relatorios.php">Relatórios</a>
                    </li>
                    <li>
                        <a href="logout.php">Sair</a>
                    </li>
                </ul>
            </div>

            <!-- ÁREA DE PERFIL DO USUÁRIO COM LINK -->
            <div class="perfil">
                <a href="profile.php">
                    <?php
                    // EXIBE A IMAGEM DE PERFIL DO USUÁRIO, UTILIZANDO O ID DA SESSÃO
                    echo '<img width="75px" id="img_perfil" src="../imgs/uploads/' . $usuario_id . '.jpeg" alt="Imagem de Perfil">';
                    ?>
                </a>
            </div>
        </nav>
    </div>
</header>
