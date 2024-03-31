<?php
session_start();

include "../conexão_com_banco.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $senha = $_POST['senha'];

    if (!empty($email) && !empty($senha)) {
        $senha_sha1 = sha1($senha);

        $sql_pessoa = "SELECT * FROM Tb_Pessoas WHERE Email = '$email' AND Senha = '$senha_sha1'";
        $result_pessoa = mysqli_query($_con, $sql_pessoa);

        if ($result_pessoa === false) {
            // Tratar erros de consulta SQL
            $aviso = "Erro ao executar consulta SQL: " . mysqli_error($_con);
        } else {
            if (mysqli_num_rows($result_pessoa) > 0) {
                $row = mysqli_fetch_assoc($result_pessoa);
                $_SESSION['nome_usuario'] = $row['Nome'];
                // Verificar se é empresa ou candidato
                $sql_empresa = "SELECT * FROM Tb_Empresa WHERE Tb_Pessoas_Id = '" . $row['Id_Pessoas'] . "'";
                $result_empresa = mysqli_query($_con, $sql_empresa);

                $sql_candidato = "SELECT * FROM Tb_Candidato WHERE Tb_Pessoas_Id = '" . $row['Id_Pessoas'] . "'";
                $result_candidato = mysqli_query($_con, $sql_candidato);

                if (mysqli_num_rows($result_empresa) > 0) {
                    $_SESSION['tipo_usuario'] = 'empresa';
                    $_SESSION['email_session'] = $email;
                    header("Location: ../../views/HomeRecrutador/homeRecrutador.php");                    
                    exit;
                } elseif (mysqli_num_rows($result_candidato) > 0) {
                    $_SESSION['tipo_usuario'] = 'candidato';
                    $_SESSION['email_session'] = $email;
                    header("Location: ../../views/HomeCandidato/homeCandidato.html");
                    exit;
                } else {
                    // Se o tipo de usuário não puder ser determinado, redirecione para a página de login com um aviso
                    header("Location: ../../views/Login/login.html?aviso=" . urlencode("Tipo de usuário desconhecido."));
                    exit;
                }
            } else {
                header("Location: ../../views/Login/login.html?aviso=" . urlencode("Usuário não encontrado ou senha incorreta. Por favor, tente novamente.."));
                exit;
            }
        }
    } else {
        header("Location: ../../views/Login/login.html?aviso=" . urlencode("Algo deu errado. Tente novamente."));
        exit;
    }
}
?>
