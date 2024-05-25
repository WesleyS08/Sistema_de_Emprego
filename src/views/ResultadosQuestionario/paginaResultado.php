<?php 
include "../../services/conexão_com_banco.php";




?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado do Questionário</title>
    <link rel="stylesheet" type="text/css" href="../../assets/styles/verificacoes.css">
    <link rel="stylesheet" type="text/css" href="../../assets/styles/homeStyles.css">
</head>
<body>
    <nav>
        <a href="../../../index.php">
            <img id="logo" src="../../assets/images/logos_empresa/logo_sias.png" alt="Logo da Empresa">
        </a>
    </nav>
    <div class="container">
        <div class="content">
            <h1>Questionário finalizado!</h1>
            <p>Você acabou de responder o questionário, veja a sua pontuação abaixo:</p>
            <?php
                if (isset($_GET['pontuacaoTotal'])) {
                    $pontuacaoTotal = $_GET['pontuacaoTotal'];
                    echo "<p>Sua pontuação total: $pontuacaoTotal</p>";
                } else {
                    echo "<p>Não foi possível recuperar sua pontuação.</p>";
                }
            ?>
            <a href="../TodosTestes/todosTestes.php" style="color: rgb(0, 0, 0); text-decoration: underline;">Ir para Meus Testes</a>
        </div>
    </div>    
    <script src="https://cdn.lordicon.com/lordicon.js"></script>
</body>
</html>