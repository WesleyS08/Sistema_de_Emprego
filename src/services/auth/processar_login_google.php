<?php
include "../conexão_com_banco.php";

$aviso = ""; 


if ($_con === false) {
    die("Erro de conexão: " . mysqli_connect_error());
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

            if (empty($row['Token'])) {
                // Se não houver um token associado a esse email, salva o token no banco de dados
                $sql_update_token = "UPDATE Tb_Pessoas SET Token = '$google_token' WHERE Email = '$email_google'";
                mysqli_query($_con, $sql_update_token);
            }

            // Verifica se o usuário é um candidato
            $sql_candidato = "SELECT * FROM Tb_Candidato WHERE Tb_Pessoas_Id = '" . $row['Id_Pessoas'] . "'";
            $result_candidato = mysqli_query($_con, $sql_candidato);

            // Verifica se o usuário é uma empresa
            $sql_empresa = "SELECT * FROM Tb_Empresa WHERE Tb_Pessoas_Id = '" . $row['Id_Pessoas'] . "'";
            $result_empresa = mysqli_query($_con, $sql_empresa);

            if (mysqli_num_rows($result_candidato) > 0) {
    
                header("Location: pagina_do_candidato.php");
                exit;
            } elseif (mysqli_num_rows($result_empresa) > 0) {
            
                header("Location: ../../../../../HomeRecrutador/homeRecrutador.html");
                exit;
            } else {
                // Se o email estiver presente, mas não for nem candidato nem empresa, redirecione para a página de login com o aviso
                header("Location: ../../../../../Login/login.html?aviso=" . urlencode("Algo deu errado. Tente novamente."));
                exit;
            }
        } else {
            // Se o email não foi encontrado no sistema, redirecione para a página de login com o aviso
            header("Location: ../../../../../Login/login.html?aviso=" . urlencode("Email não encontrado no sistema."));
            exit;
        }
    } else {
        // Se os campos estiverem vazios, redirecione para a página de login com o aviso
        header("Location: ../../../../../Login/login.html?aviso=" . urlencode("Por favor, preencha todos os campos."));
        exit;
    }
} 
?>
