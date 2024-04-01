<?php
include "../../services/conexão_com_banco.php";
session_start();

$aviso = ""; 

if ($_con === false) {
    die ("Erro de conexão: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $google_token = $_POST['token'];
    $email_google = $_POST['email_google'];

    if (!empty($google_token) && !empty($email_google)) {
        // Verifica se o token do Google já está presente no banco de dados para o email fornecido
        $sql_token = "SELECT * FROM Tb_Pessoas WHERE Email = '$email_google'";
        $result_token = mysqli_query($_con, $sql_token);

        if (mysqli_num_rows($result_token) > 0) {
            // O email já está presente no banco de dados, verifica se há um token associado a ele
            $row = mysqli_fetch_assoc($result_token);
            $nome_usuario = $row['Nome']; // Obtém o nome do usuário do banco de dados

            if (empty($row['Token'])) {
                // Se não houver um token associado a esse email, salva o token no banco de dados
                $sql_update_token = "UPDATE Tb_Pessoas SET Token = '$google_token' WHERE Email = '$email_google'";
                mysqli_query($_con, $sql_update_token);
            }

            // Verifica se o usuário é uma empresa
            $sql_empresa = "SELECT * FROM Tb_Empresa WHERE Tb_Pessoas_Id = '" . $row['Id_Pessoas'] . "'";
            $result_empresa = mysqli_query($_con, $sql_empresa);

            // Verifica se o usuário é um candidato
            $sql_candidato = "SELECT * FROM Tb_Candidato WHERE Tb_Pessoas_Id = '" . $row['Id_Pessoas'] . "'";
            $result_candidato = mysqli_query($_con, $sql_candidato);

            if (mysqli_num_rows($result_empresa) > 0) {
                $_SESSION['google_session'] = $email_google;
                $_SESSION['google_usuario'] = 'empresa';
                $_SESSION['nome_usuario'] = $nome_usuario; 
                header("Location: ../../views/HomeRecrutador/homeRecrutador.php");
                exit;
            } elseif (mysqli_num_rows($result_candidato) > 0) {
                $_SESSION['google_session'] = $email_google;
                $_SESSION['google_usuario'] = 'candidato';
                $_SESSION['nome_usuario'] = $nome_usuario; 
                header("Location: ../../views/homeCandidato/homeCandidato.html"); // O que faz essa linha? lol
                exit;
            } else {
                // Se o tipo de usuário não puder ser determinado, redirecione para a página de login com um aviso
                header("Location: ../../views/Login/login.html?aviso=" . urlencode("Tipo de usuário desconhecido."));
                exit;
            }
        } else {
            // Se o email não foi encontrado no sistema, redirecione para a página de login com o aviso
            header("Location: ../../views/Login/login.html?aviso=" . urlencode("Email não encontrado no sistema."));
            exit;
        }
    } else {
        // Se os campos estiverem vazios, redirecione para a página de login com o aviso
        header("Location: ../../views/Login/login.html?aviso=" . urlencode("Por favor, preencha todos os campos."));
        exit;
    }
}
?>
