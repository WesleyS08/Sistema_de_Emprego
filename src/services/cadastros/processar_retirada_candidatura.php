<?php
include "../conexão_com_banco.php";
session_start();

// Verifica se o usuário está autenticado e obtém o email do usuário
if (isset($_SESSION['email_session'])) {
    $emailUsuario = $_SESSION['email_session'];
} elseif (isset($_SESSION['google_session'])) {
    $emailUsuario = $_SESSION['google_session'];
} else {
    exit("O email do usuário não está na sessão.");
}

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
        exit("CPF do candidato não encontrado.");
    }
    $stmtCandidato->close();

    // Obtém o ID do anúncio
    if (isset($_GET['id_anuncio'])) {
        $idAnuncio = intval($_GET['id_anuncio']);
    } else {
        exit("ID do anúncio não foi fornecido na URL.");
    }

    // Remover a candidatura do banco de dados
    $queryRemover = "DELETE FROM Tb_Inscricoes WHERE Tb_Vagas_Tb_Anuncios_Id = ? AND Tb_Candidato_CPF = ?";
    $stmtRemover = $_con->prepare($queryRemover);
    $stmtRemover->bind_param("is", $idAnuncio, $cpfCandidato);
    if ($stmtRemover->execute()) {
        // Redirecionar de volta à página da vaga
        header("Location: ../../views/Vaga/vaga.php?id=" . $idAnuncio);
        exit();
    } else {
        echo "Erro ao remover a candidatura.";
    }
    $stmtRemover->close();

    $_con->close();
} else {
    echo "O email do usuário não está na sessão.";
}
?>