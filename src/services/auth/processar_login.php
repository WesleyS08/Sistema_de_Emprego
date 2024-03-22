<?php
include "../conexão_com_banco.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $google_token = $_POST['google_token'];

    if (!empty($google_token)) {
        // Verifica se o token do Google já está presente no banco de dados
        $sql_token = "SELECT * FROM Tb_Pessoas WHERE Token = '$google_token'";
        $result_token = mysqli_query($_con, $sql_token);

        if (mysqli_num_rows($result_token) > 0) {
            // O token do Google já está presente no banco de dados, obtém o tipo de usuário
            $row = mysqli_fetch_assoc($result_token);
            $id_pessoa = $row['Id_Pessoas'];

            // Verifica se o usuário é um candidato
            $sql_candidato = "SELECT * FROM Tb_Candidato WHERE Tb_Pessoas_Id = '$id_pessoa'";
            $result_candidato = mysqli_query($_con, $sql_candidato);

            // Verifica se o usuário é uma empresa
            $sql_empresa = "SELECT * FROM Tb_Empresa WHERE Tb_Pessoas_Id = '$id_pessoa'";
            $result_empresa = mysqli_query($_con, $sql_empresa);

            if (mysqli_num_rows($result_candidato) > 0) {
                // Usuário é um candidato, redireciona para a página de candidato
                header("Location: pagina_do_candidato.php");
                exit;
            } elseif (mysqli_num_rows($result_empresa) > 0) {
                // Usuário é uma empresa, redireciona para a página de empresa
                header("Location: ../../../../../HomeRecrutador/homeRecrutador.html");
                exit;
            } else {
                $aviso = "Usuário não encontrado. Por favor, tente novamente.";
            }
        } else {
            // Se o token do Google não estiver presente no banco de dados, redireciona para o login normal
            header("Location: ../../../../../Login/login.html");
            exit;
        }
    } else {
        // Se o token do Google não estiver presente, continua com a verificação de email e senha
        if (!empty($email) && !empty($senha)) {
            $senha_sha1 = sha1($senha);

            // Verifica se o usuário é um candidato
            $sql_candidato = "SELECT * FROM Tb_Pessoas JOIN Tb_Candidato ON Tb_Pessoas.Id_Pessoas = Tb_Candidato.Tb_Pessoas_Id WHERE Email = '$email' AND Senha = '$senha_sha1'";
            $result_candidato = mysqli_query($_con, $sql_candidato);

            // Verifica se o usuário é uma empresa
            $sql_empresa = "SELECT * FROM Tb_Pessoas JOIN Tb_Empresa ON Tb_Pessoas.Id_Pessoas = Tb_Empresa.Tb_Pessoas_Id WHERE Email = '$email' AND Senha = '$senha_sha1'";
            $result_empresa = mysqli_query($_con, $sql_empresa);

            if (mysqli_num_rows($result_candidato) > 0) {
                // Usuário é um candidato, redireciona para a página de candidato
                header("Location: pagina_do_candidato.php");
                exit;
            } elseif (mysqli_num_rows($result_empresa) > 0) {
                // Usuário é uma empresa, redireciona para a página de empresa
                header("Location: ../../../../../HomeRecrutador/homeRecrutador.html");
                exit;
            } else {
                $aviso = "Usuário não encontrado ou senha incorreta. Por favor, tente novamente.";
            }
        } else {
            $aviso = "Por favor, preencha todos os campos.";
        }
    }
} else {
    $aviso = "Ocorreu um erro ao processar o formulário.";
}

header("Location: login.php?aviso=" . urlencode($aviso));
exit;
?>
