<?php
session_start();

include "../conexão_com_banco.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $senha = $_POST['senha'];

    if (!empty($email) && !empty($senha)) {
        $senha_sha1 = sha1($senha);

        $sql_candidato = "SELECT * FROM Tb_Pessoas JOIN Tb_Candidato ON Tb_Pessoas.Id_Pessoas = Tb_Candidato.Tb_Pessoas_Id WHERE Email = '$email' AND Senha = '$senha_sha1'";
        $result_candidato = mysqli_query($_con, $sql_candidato);

        $sql_empresa = "SELECT * FROM Tb_Pessoas JOIN Tb_Empresa ON Tb_Pessoas.Id_Pessoas = Tb_Empresa.Tb_Pessoas_Id WHERE Email = '$email' AND Senha = '$senha_sha1'";
        $result_empresa = mysqli_query($_con, $sql_empresa);

        if ($result_candidato === false || $result_empresa === false) {
            // Tratar erros de consulta SQL
            $aviso = "Erro ao executar consulta SQL: " . mysqli_error($_con);
        } else {
            if (mysqli_num_rows($result_candidato) > 0) {
                $_SESSION['tipo_usuario'] = 'candidato';
                // Consulta para obter o nome do usuário
                $row = mysqli_fetch_assoc($result_candidato);
                $_SESSION['nome_usuario'] = $row['Nome']; // Supondo que o nome do usuário está na coluna 'Nome'
                header("Location: pagina_do_candidato.php");
                exit;
            } elseif (mysqli_num_rows($result_empresa) > 0) {
                $_SESSION['tipo_usuario'] = 'empresa';
                // Consulta para obter o nome do usuário
                $row = mysqli_fetch_assoc($result_empresa);
                $_SESSION['nome_usuario'] = $row['Nome']; // Supondo que o nome do usuário está na coluna 'Nome'
                header("Location: ../../../../../HomeRecrutador/homeRecrutador.php");
                exit;
            } else {
                $aviso = "Usuário não encontrado ou senha incorreta. Por favor, tente novamente.";
            }
        }
    } else {
        $aviso = "Por favor, preencha todos os campos.";
    }
}

if (!empty($aviso)) {
    echo $aviso;
}
?>
