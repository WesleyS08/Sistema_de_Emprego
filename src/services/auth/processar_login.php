<?php

session_start();

include "../conexão_com_banco.php";

$aviso = ""; // Inicialize a variável aviso

// Verifica se a conexão com o banco de dados foi estabelecida com sucesso
if ($_con === false) {
    die ("Erro de conexão: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $senha = $_POST['senha'];

    // Verifica se os campos de e-mail e senha foram preenchidos
    if (!empty ($email) && !empty ($senha)) {
        $senha_sha1 = sha1($senha);

        $_SESSION['emailsenssion'] = $email;
        $_SESSION['senhasenssion'] = $senha;

        // Verifica se o usuário é um candidato
        $sql_candidato = "SELECT * FROM Tb_Pessoas JOIN Tb_Candidato ON Tb_Pessoas.Id_Pessoas = Tb_Candidato.Tb_Pessoas_Id WHERE Email = '$email' AND Senha = '$senha_sha1'";
        $result_candidato = mysqli_query($_con, $sql_candidato);

        // Verifica se o usuário é uma empresa
        $sql_empresa = "SELECT * FROM Tb_Pessoas JOIN Tb_Empresa ON Tb_Pessoas.Id_Pessoas = Tb_Empresa.Tb_Pessoas_Id WHERE Email = '$email' AND Senha = '$senha_sha1'";
        $result_empresa = mysqli_query($_con, $sql_empresa);

        // Verifica se a consulta SQL foi bem-sucedida
        if ($result_candidato === false || $result_empresa === false) {
            $aviso = "Erro ao executar consulta SQL: " . mysqli_error($_con);
        } else {
            if (mysqli_num_rows($result_candidato) > 0) {
                $_SESSION['tipo_usuario'] = 'candidato';
                header("Location: pagina_do_candidato.php");
                exit;
            } elseif (mysqli_num_rows($result_empresa) > 0) {
                $_SESSION['tipo_usuario'] = 'empresa';
                // Usuário é uma empresa, redireciona para a página de empresa
                header("Location: ../../../../../HomeRecrutador/homeRecrutador.html");
                exit;
            } else {
                $aviso = "Usuário não encontrado ou senha incorreta. Por favor, tente novamente.";
            }
        }
    } else {
        $aviso = "Por favor, preencha todos os campos.";
    }
}

// Exibe o aviso se houver algum
if (!empty ($aviso)) {
    echo $aviso;
}
?>