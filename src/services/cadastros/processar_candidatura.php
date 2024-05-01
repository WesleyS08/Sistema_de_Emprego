<?php
include "../conexão_com_banco.php";
session_start();

// Verifica se o usuário está autenticado e obtém o email do usuário
if (isset($_SESSION['email_session'])) {
    $emailUsuario = $_SESSION['email_session'];
} elseif (isset($_SESSION['google_session'])) {
    $emailUsuario = $_SESSION['google_session'];
} else {
    // Adicione lógica para lidar com autenticação como empresa, se necessário
    $autenticadoComoEmpresa = false;
}

// Verifica se o email do usuário está presente na sessão
if (!empty($emailUsuario)) {
    // Obtém o CPF do candidato com base no email
    $queryCandidato = "SELECT CPF FROM Tb_Candidato WHERE Tb_Pessoas_Id IN (SELECT Id_Pessoas FROM Tb_Pessoas WHERE Email = ?)";
    $stmtCandidato = $_con->prepare($queryCandidato);
    $stmtCandidato->bind_param("s", $emailUsuario);
    $stmtCandidato->execute();
    $resultCandidato = $stmtCandidato->get_result();
    if ($rowCandidato = $resultCandidato->fetch_assoc()) {
        $cpfCandidato = $rowCandidato['CPF'];
    } else {
        // Lidar com o caso em que o CPF do candidato não foi encontrado
        exit("CPF do candidato não encontrado.");
    }
    $stmtCandidato->close();

    // Obtém o CNPJ da empresa com base no ID da vaga
    if (isset($_GET['id_anuncio'])) {
        $idAnuncio = intval($_GET['id_anuncio']);
        $queryEmpresa = "SELECT Tb_Empresa_CNPJ FROM Tb_Vagas WHERE Tb_Anuncios_Id = ?";
        $stmtEmpresa = $_con->prepare($queryEmpresa);
        $stmtEmpresa->bind_param("i", $idAnuncio);
        $stmtEmpresa->execute();
        $resultEmpresa = $stmtEmpresa->get_result();
        if ($rowEmpresa = $resultEmpresa->fetch_assoc()) {
            $cnpjEmpresa = $rowEmpresa['Tb_Empresa_CNPJ'];
        } else {
            // Lidar com o caso em que o CNPJ da empresa não foi encontrado para o ID da vaga fornecido
            exit("CNPJ da empresa não encontrado para o ID da vaga fornecido.");
        }
        $stmtEmpresa->close();
    } else {
        // Lidar com o caso em que o ID do anúncio não foi fornecido na URL
        exit("ID do anúncio não foi fornecido na URL.");
    }


    date_default_timezone_set('America/Sao_Paulo');
    // Inserir a candidatura no banco de dados
    $dataAtual = date('Y-m-d H:i:s');
    $queryInserir = "INSERT INTO Tb_Inscricoes (Tb_Vagas_Tb_Anuncios_Id, Tb_Vagas_Tb_Empresa_CNPJ, Tb_Candidato_CPF, Data_de_Inscricao) 
                    VALUES (?, ?, ?, ?)";
    $stmtInserir = $_con->prepare($queryInserir);
    $stmtInserir->bind_param("isss", $idAnuncio, $cnpjEmpresa, $cpfCandidato, $dataAtual);
    if ($stmtInserir->execute()) {
        header("Location: ../../views/Vaga/vaga.php?id=" . $idAnuncio);
        exit();
    } else {
        // Lidar com o caso em que ocorreu um erro durante a inserção da candidatura
        echo "Erro ao realizar a candidatura.";
    }
    $stmtInserir->close();

    // Fecha a conexão com o banco de dados
    $_con->close();
} else {
    echo "O email do usuário não está na sessão.";
}
?>
