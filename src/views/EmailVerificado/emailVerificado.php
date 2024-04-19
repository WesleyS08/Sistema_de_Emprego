<?php
include "../../services/conexão_com_banco.php";

// Verificar se o parâmetro 'id' está presente na URL
if (isset($_GET['id'])) {
    // Obter o ID do usuário da URL
    $idPessoa = $_GET['id'];

    // Atualizar o banco de dados para marcar o usuário como verificado
    $queryAtualizarVerificado = "UPDATE Tb_Pessoas SET Verificado = 1 WHERE Id_Pessoas = $idPessoa";
    if (mysqli_query($_con, $queryAtualizarVerificado)) {
    } else {
        echo "Erro ao verificar o email: " . mysqli_error($_con);
    }
} else {
    echo "ID de usuário não encontrado na URL.";
}

// Fechar a conexão com o banco de dados
mysqli_close($_con);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificação</title>
    <link rel="stylesheet" type="text/css" href="../../assets/styles/verificacaoEmailStyle.css">
    <link rel="stylesheet" type="text/css" href="../../assets/styles/homeStyles.css">
</head>
<body>
    <nav>
        <img id="logo" src="../../assets/images/logos_empresa/logo_sias.png">
    </nav>
    <div class="container">
        <div class="content">
            <h1>Email verificado!</h1>
            <lord-icon
                src="https://cdn.lordicon.com/oqdmuxru.json"
                trigger="in"
                delay="500"
                state="in-check"
                colors="primary:#000000"
                style="width:200px;height:200px">
            </lord-icon>            
            <a href="../Login/login.html"><button>Ir para login</button></a>
        </div>
    </div>    
    <script src="https://cdn.lordicon.com/lordicon.js"></script>
</body>
</html>